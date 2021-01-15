<?php
/**
 * Interface to use to display data to the user.
 * This interface is used to initiate the process, like launching hooks.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Interface to use to display data to the user.
 *
 * @since 4.0.0
 */
interface UIInterface {

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init();
}
