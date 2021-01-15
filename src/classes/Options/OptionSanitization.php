<?php
/**
 * Abstract class to use to sanitize and validate options.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\Options;

use Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Sanitization\AbstractSanitization;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Abstract class to use to sanitize and validate options.
 *
 * @since 4.0.0
 */
class OptionSanitization extends AbstractSanitization {

	/**
	 * Prefix used in hook names.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	protected $prefix = 'sfabt';

	/**
	 * Suffix used in hook names.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	protected $identifier = 'settings';

	/**
	 * The default values.
	 * These are the "zero state" values.
	 * Don't use null as value.
	 *
	 * @var   array<mixed>
	 * @since 4.0.0
	 */
	protected $default_values = [
		'coworkers' => [],
	];

	/** ----------------------------------------------------------------------------------------- */
	/** SANITIZATION, VALIDATION ================================================================ */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Sanitizes and validates an option value. Basic casts have been made.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $key     The option key.
	 * @param  mixed  $value   The value.
	 * @param  mixed  $default The default value.
	 * @return mixed
	 */
	protected function sanitize_and_validate_value( $key, $value, $default ) {
		switch ( $key ) {
			case 'coworkers':
				$value = is_array( $value ) ? array_unique( array_map( 'absint', $value ) ) : [];
				$value = ! empty( $value ) ? array_combine( $value, $value ) : [];
				return $value;

			default:
				return false;
		}
	}

	/**
	 * Validates all options before storing them. Basic sanitization and validation have been made, row by row.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $values The option value.
	 * @return array<mixed>
	 */
	protected function validate_values_on_update( array $values ) {
		return $values;
	}
}
