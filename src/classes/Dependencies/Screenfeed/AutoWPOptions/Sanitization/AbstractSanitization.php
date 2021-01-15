<?php
/**
 * Abstract class to use to sanitize and validate options.
 *
 * @package Screenfeed/autowpoptions
 */

namespace Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Sanitization;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Abstract class to use to sanitize and validate options.
 *
 * @since 1.0.0
 */
abstract class AbstractSanitization implements SanitizationInterface {

	/**
	 * Current plugin version.
	 * It is stored with the options, and may be used during an upgrade process.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	protected $version;

	/**
	 * Prefix used in hook names.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	protected $prefix;

	/**
	 * Suffix used in hook names.
	 *
	 * @var   string
	 * @since 1.0.0
	 */
	protected $identifier;

	/**
	 * The default values.
	 * These are the "zero state" values.
	 * Don't use null as value. `cached` and `version` are reserved keys, do not use them.
	 *
	 * @var   array<mixed>
	 * @since 1.0.0
	 */
	protected $default_values;

	/**
	 * The values used when they are set the first time or reset.
	 * Values identical to default values are not listed.
	 * `cached` and `version` are reserved keys, do not use them.
	 *
	 * @var   array<mixed>
	 * @since 1.0.0
	 */
	protected $reset_values = [];

	/**
	 * The constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $version Current plugin version.
	 * @return void
	 */
	public function __construct( $version ) {
		$this->version        = $version;
		$this->default_values = array_merge(
			[
				'version' => '',
			],
			$this->default_values
		);
	}

	/**
	 * Returns the prefix used in hook names.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_prefix() {
		return $this->prefix;
	}

	/**
	 * Returns the identifier used in the hook names.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_identifier() {
		return $this->identifier;
	}

	/** ----------------------------------------------------------------------------------------- */
	/** DEFAULT + RESET VALUES ================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Returns default option values.
	 *
	 * @since 1.0.0
	 *
	 * @return array<mixed>
	 */
	public function get_default_values() {
		$default_values = $this->default_values;

		if ( ! empty( $default_values['cached'] ) ) {
			unset( $default_values['cached'] );
			return $default_values;
		}

		$prefix     = $this->get_prefix();
		$identifier = $this->get_identifier();

		/**
		 * Allows to add more default option values.
		 *
		 * @since 1.0.0
		 *
		 * @param array $new_values     New default option values.
		 * @param array $default_values Plugin default option values.
		 */
		$new_values = apply_filters( "{$prefix}_default_{$identifier}_values", [], $default_values );
		$new_values = is_array( $new_values ) ? $new_values : [];

		if ( ! empty( $new_values ) ) {
			// Don't allow new values to overwrite the plugin values.
			$new_values = array_diff_key( $new_values, $default_values );
		}

		if ( ! empty( $new_values ) ) {
			$default_values       = array_merge( $default_values, $new_values );
			$this->default_values = $default_values;
		}

		$this->default_values['cached'] = 1;

		return $default_values;
	}

	/**
	 * Returns the values used when the option is empty.
	 *
	 * @since 1.0.0
	 *
	 * @return array<mixed>
	 */
	public function get_reset_values() {
		$reset_values = $this->reset_values;

		if ( ! empty( $reset_values['cached'] ) ) {
			unset( $reset_values['cached'] );
			return $reset_values;
		}

		$default_values = $this->get_default_values();
		$reset_values   = array_merge( $default_values, $reset_values );
		$prefix         = $this->get_prefix();
		$identifier     = $this->get_identifier();

		/**
		 * Allows to filter the "reset" option values.
		 *
		 * @since 1.0.0
		 *
		 * @param array $reset_values Plugin reset option values.
		 */
		$new_values = apply_filters( "{$prefix}_reset_{$identifier}_values", $reset_values );

		if ( ! empty( $new_values ) && is_array( $new_values ) ) {
			$reset_values = array_merge( $reset_values, $new_values );
		}

		$this->reset_values           = $reset_values;
		$this->reset_values['cached'] = 1;

		return $reset_values;
	}

	/** ----------------------------------------------------------------------------------------- */
	/** SANITIZATION, VALIDATION ================================================================ */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Sanitizes and validates an option value.
	 * This is used when getting the value from storage, and also before storing.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key     The option key.
	 * @param  mixed  $value   The value.
	 * @param  mixed  $default The default value.
	 * @return mixed
	 */
	public function sanitize_and_validate( $key, $value, $default = null ) {
		if ( ! isset( $default ) ) {
			$default_values = $this->get_default_values();
			$default        = $default_values[ $key ];
		}

		// Cast the value.
		$value = $this->cast( $value, $default );

		if ( $value === $default ) {
			return $value;
		}

		// Version.
		if ( 'version' === $key ) {
			return sanitize_text_field( $value );
		}

		return $this->sanitize_and_validate_value( $key, $value, $default );
	}

	/**
	 * Sanitizes and validates an option value. Basic casts have been made.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key     The option key.
	 * @param  mixed  $value   The value.
	 * @param  mixed  $default The default value.
	 * @return mixed
	 */
	abstract protected function sanitize_and_validate_value( $key, $value, $default );

	/**
	 * Sanitizes and validates the options.
	 * This is used before storing them.
	 *
	 * @since 1.0.0
	 *
	 * @param  array<mixed> $values The option values.
	 * @return array<mixed>
	 */
	public function sanitize_and_validate_on_update( array $values ) {
		$default_values = $this->get_default_values();

		if ( ! empty( $values ) ) {
			foreach ( $default_values as $key => $default ) {
				if ( isset( $values[ $key ] ) ) {
					$values[ $key ] = $this->sanitize_and_validate( $key, $values[ $key ], $default );
				}
			}
		}

		$values = array_intersect_key( $values, $default_values );

		// Version.
		if ( empty( $values['version'] ) ) {
			$values['version'] = $this->version;
		}

		return $this->validate_values_on_update( $values );
	}

	/**
	 * Validates the options before storing the values.
	 * Basic sanitization and validation have been made, value by value.
	 * It is useful when we want to change a value depending on another one.
	 *
	 * @since 1.0.0
	 *
	 * @param  array<mixed> $values The option value.
	 * @return array<mixed>
	 */
	abstract protected function validate_values_on_update( array $values );

	/** ----------------------------------------------------------------------------------------- */
	/** TOOLS =================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Casts a value, depending on its default value type.
	 *
	 * @since 1.0.0
	 *
	 * @param  mixed $value   The value to cast.
	 * @param  mixed $default The default value.
	 * @return mixed
	 */
	protected function cast( $value, $default ) {
		if ( is_array( $default ) ) {
			return is_array( $value ) ? $value : [];
		}

		if ( is_int( $default ) ) {
			return (int) $value;
		}

		if ( is_bool( $default ) ) {
			return (bool) $value;
		}

		if ( is_float( $default ) ) {
			return round( (float) $value, 3 );
		}

		return $value;
	}
}
