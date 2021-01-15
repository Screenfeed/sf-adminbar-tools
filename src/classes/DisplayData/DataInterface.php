<?php
/**
 * Interface to use to display data to the user.
 * This interface is used to provide the data to display.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * AdminItems.
 *
 * @since 4.0.0
 */
interface DataInterface {

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init();

	/**
	 * Returns the data to display.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $args Some optional arguments that may be used to select a set of data for example.
	 * @return array<mixed>
	 */
	public function get_data( $args = [] );
}
