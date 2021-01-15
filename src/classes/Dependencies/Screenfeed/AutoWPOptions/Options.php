<?php
/**
 * Class that handles the plugin options.
 *
 * @package Screenfeed/autowpoptions
 */

namespace Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions;

use Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Storage\StorageInterface;
use Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Sanitization\SanitizationInterface;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class that handles the plugin options.
 *
 * @since 1.0.0
 */
class Options {

	/**
	 * An instance of StorageInterface.
	 *
	 * @var   StorageInterface
	 * @since 1.0.0
	 */
	private $storage;

	/**
	 * An instance of SanitizationInterface.
	 *
	 * @var   SanitizationInterface
	 * @since 1.0.0
	 */
	private $sanitization;

	/**
	 * The constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param  StorageInterface      $storage      An instance of StorageInterface.
	 * @param  SanitizationInterface $sanitization An instance of SanitizationInterface.
	 * @return void
	 */
	public function __construct( StorageInterface $storage, SanitizationInterface $sanitization ) {
		$this->storage      = $storage;
		$this->sanitization = $sanitization;
	}

	/**
	 * Launches the hooks.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'sanitize_option_' . $this->storage->get_full_name(), [ $this->sanitization, 'sanitize_and_validate_on_update' ], 50 );
	}

	/**
	 * Returns the storage instance.
	 *
	 * @since 1.0.0
	 *
	 * @return StorageInterface
	 */
	public function get_storage() {
		return $this->storage;
	}

	/** ----------------------------------------------------------------------------------------- */
	/** GET/SET/DELETE OPTION(S) ================================================================ */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Returns an option.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key The option name.
	 * @return mixed       The option value. Null if the key does not exist.
	 */
	public function get( $key ) {
		$default_values = $this->sanitization->get_default_values();

		if ( ! isset( $default_values[ $key ] ) ) {
			return null;
		}

		$default    = $default_values[ $key ];
		$prefix     = $this->sanitization->get_prefix();
		$identifier = $this->sanitization->get_identifier();

		/**
		 * Pre-filters any option before read.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed $value   Value to return instead of the option value. Default null to skip it.
		 * @param mixed $default The default value.
		 */
		$value = apply_filters( "pre_get_{$prefix}_{$identifier}_{$key}", null, $default );

		if ( isset( $value ) ) {
			return $value;
		}

		// Get all values.
		$values = $this->get_all();

		// Sanitize and validate the value.
		$value = $this->sanitization->sanitize_and_validate( $key, $values[ $key ], $default );

		/**
		 * Filters any option after read.
		 *
		 * @since 1.0.0
		 *
		 * @param mixed $value   Value of the option.
		 * @param mixed $default The default value. Default false.
		*/
		return apply_filters( "get_{$prefix}_{$identifier}_{$key}", $value, $default );
	}

	/**
	 * Returns all options (no cast, no sanitization, no validation).
	 * Reset values are returned if the whole option does not exist.
	 * Default values are added for the keys that are missing.
	 *
	 * @since 1.0.0
	 *
	 * @return array<mixed> The options.
	 */
	public function get_all() {
		$values = $this->storage->get();

		if ( empty( $values ) ) {
			return $this->sanitization->get_reset_values();
		}

		$default = $this->sanitization->get_default_values();
		$values  = array_merge( $default, $values );
		return array_intersect_key( $values, $default );
	}

	/**
	 * Sets one or multiple options.
	 * Empty fields are not deleted.
	 *
	 * @since 1.0.0
	 *
	 * @param  array<mixed> $values An array of option name / option value pairs.
	 * @return void
	 */
	public function set( array $values ) {
		$values = array_merge( $this->get_all(), $values );
		$values = array_intersect_key( $values, $this->sanitization->get_default_values() );

		$this->storage->set( $values );
	}

	/**
	 * Deletes one or multiple options.
	 *
	 * @since 1.0.0
	 *
	 * @param  array<string>|string $keys An array of option names or a single option name.
	 * @return void
	 */
	public function delete( $keys ) {
		$values = $this->storage->get();

		if ( ! $values ) {
			if ( false !== $values ) {
				$this->storage->delete();
			}
			return;
		}

		$keys   = array_flip( (array) $keys );
		$values = array_diff_key( $values, $keys );

		$this->storage->set( $values );
	}

	/**
	 * Deletes all options.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function delete_all() {
		$this->storage->delete();
	}

	/**
	 * Checks if the option with the given name exists.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $key The option name.
	 * @return bool
	 */
	public function has( $key ) {
		return null !== $this->get( $key );
	}
}
