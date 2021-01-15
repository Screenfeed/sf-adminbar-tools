<?php
/**
 * Class to display the value of some properties of the global var `$current_screen` in the adminbar.
 * This class provides the data to display.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\CurrentScreen;

use Screenfeed\AdminbarTools\DisplayData\DataInterface;
use function Screenfeed\AdminbarTools\get_global;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class that provides the properties of the global var `$current_screen` to display in the adminbar.
 *
 * @since 4.0.0
 */
class Data implements DataInterface {

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
	}

	/**
	 * Returns the value of some properties of `$current_screen`.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $args Optional arguments not in use here.
	 * @return array<string>
	 */
	public function get_data( $args = [] ) {
		$current_screen = get_global( 'current_screen' );

		if ( ! is_object( $current_screen ) ) {
			return [];
		}

		$data    = [];
		$screens = [
			'id',
			'base',
			'parent_base',
			'parent_file',
		];
		foreach ( $screens as $name ) {
			$value         = ! empty( $current_screen->$name ) ? $current_screen->$name : 'NULL';
			$data[ $name ] = $value;
		}

		return $data;
	}
}
