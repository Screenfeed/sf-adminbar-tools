<?php
/**
 * Small helpers to work with constants.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Checks if a constant is defined.
 *
 * @since 4.0.0
 *
 * @param  string $constant_name Name of the constant to check.
 * @return bool
 */
function has_constant( $constant_name ) {
	return defined( $constant_name );
}

/**
 * Returns a constant's value.
 *
 * @since 4.0.0
 *
 * @param  string $constant_name Name of the constant to check.
 * @param  mixed  $default       Optional. Default value to return if constant is not defined.
 * @return mixed
 */
function get_constant( $constant_name, $default = null ) {
	if ( ! has_constant( $constant_name ) ) {
		return $default;
	}

	return constant( $constant_name );
}
