<?php
/**
 * Small helpers to help debug.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * A more readable var_dump().
 * Handy in `tests.php`.
 *
 * @since 4.0.0
 *
 * @param  mixed $data   Any data.
 * @param  bool  $return Return the result instead of printing it.
 * @return void|mixed
 */
function var_dump( $data, $return = false ) {
	ob_start();
	call_user_func( '\var_dump', $data );
	$data = ob_get_clean();

	if ( ! empty( $data ) ) {
		// Remove the line number.
		$data = (string) preg_replace( '@<small>/.+\.php:\d+:</small>@U', '', $data );
	}

	if ( ! empty( $return ) ) {
		return $data;
	}

	echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * A more readable var_export().
 * Handy in `tests.php`.
 *
 * @since 4.0.0
 *
 * @param  mixed $data   Any data.
 * @param  bool  $return Return the result instead of printing it.
 * @return void|mixed
 */
function var_export( $data, $return = false ) {
	$data = is_string( $data ) ? $data : call_user_func( '\var_export', $data, true );

	if ( ! empty( $data ) ) {
		// More readable arrays.
		$data = (string) preg_replace( '/=>\s+array/', '=> array', $data );
		$data = (string) preg_replace( '/=>\s+\(object\)\s+array/', '=> (object) array', $data );
		$data = (string) preg_replace( '/__set_state\(array/', '__set_state( array', $data );
		$data = (string) preg_replace( '/\)\)/', ') )', $data );
		$data = (string) preg_replace( '/array\s*\(/', 'array(', $data );
		$data = (string) preg_replace( '/array\(\s+\)/', 'array()', $data );
	}

	if ( ! empty( $return ) ) {
		return $data;
	}

	echo $data; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
