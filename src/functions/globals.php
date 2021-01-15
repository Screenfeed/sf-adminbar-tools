<?php
/**
 * Small helpers to work with globals without using the keyword `global`.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Checks if a global is defined.
 *
 * @since 4.0.0
 *
 * @param  string $global_name Name of the global to check.
 * @return bool
 */
function has_global( $global_name ) {
	return isset( $GLOBALS[ $global_name ] );
}

/**
 * Returns a global's value.
 *
 * @since 4.0.0
 *
 * @param  string $global_name Name of the global to check.
 * @param  mixed  $default     Optional. Default value to return if global is not defined.
 * @return mixed
 */
function get_global( $global_name, $default = null ) {
	if ( ! has_global( $global_name ) ) {
		return $default;
	}

	return $GLOBALS[ $global_name ];
}
