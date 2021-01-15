<?php
/**
 * Interface to use to sanitize and validate options.
 *
 * @package Screenfeed/autowpoptions
 */

namespace Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Sanitization;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Interface to use to sanitize and validate options.
 *
 * @since 1.0.0
 */
interface SanitizationInterface {

	/**
	 * Returns the prefix used in hook names.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_prefix();

	/**
	 * Returns the identifier used in the hook names.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_identifier();

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
	public function get_default_values();

	/**
	 * Returns the values used when the option is empty.
	 *
	 * @since 1.0.0
	 *
	 * @return array<mixed>
	 */
	public function get_reset_values();

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
	public function sanitize_and_validate( $key, $value, $default = null );

	/**
	 * Validates the options before storing the values.
	 * Basic sanitization and validation have been made, value by value.
	 * It is useful when we want to change a value depending on another one.
	 *
	 * @since 1.0.0
	 *
	 * @param  array<mixed> $values The option values.
	 * @return array<mixed>
	 */
	public function sanitize_and_validate_on_update( array $values );
}
