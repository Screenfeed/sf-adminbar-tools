<?php
/**
 * Class to display base items in the adminbar.
 * This class provides the data to display.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\BaseItems;

use WP_Admin_Bar;
use Screenfeed\AdminbarTools\DisplayData\AbstractUI;
use Screenfeed\AdminbarTools\DisplayData\DataInterface;
use function Screenfeed\AdminbarTools\get_constant;
use function Screenfeed\AdminbarTools\get_global;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class that provides the data for the base items in the adminbar.
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
	 * Returns the number of queries on the page, the time to generate it, and the php version in use.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $args Unused optional arguments.
	 * @return array<mixed>
	 */
	public function get_data( $args = [] ) {
		return [
			'num_queries' => get_num_queries(),
			'timer'       => timer_stop(),
			'php_version' => get_constant( 'PHP_VERSION' ),
			'wp_version'  => get_global( 'wp_version' ),
		];
	}
}
