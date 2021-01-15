<?php
/**
 * Class to display the debug info (debug related constants, etc) in the adminbar.
 * This class provides the data to display.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\Debug;

use WP_Admin_Bar;
use Screenfeed\AdminbarTools\DisplayData\AbstractUI;
use Screenfeed\AdminbarTools\DisplayData\DataInterface;
use function Screenfeed\AdminbarTools\get_constant;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class that provides the debug info (debug related constants, etc) to display in the adminbar.
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
	 * Returns the value of some data related to debug, like `WP_DEBUG`.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $args Unused optional arguments.
	 * @return array<mixed>
	 */
	public function get_data( $args = [] ) {
		return [
			'debug'           => get_constant( 'WP_DEBUG' ),
			'script_debug'    => get_constant( 'SCRIPT_DEBUG' ),
			'debug_log'       => get_constant( 'WP_DEBUG_LOG' ),
			'debug_display'   => get_constant( 'WP_DEBUG_DISPLAY' ),
			'error_reporting' => error_reporting(), // phpcs:ignore WordPress.PHP.DevelopmentFunctions.prevent_path_disclosure_error_reporting, WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting
		];
	}
}
