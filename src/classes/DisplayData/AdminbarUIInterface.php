<?php
/**
 * Interface to use to display data to the user in the adminbar.
 * This interface is used to add nodes to the adminbar.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData;

use WP_Admin_Bar;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Interface to use to display data to the user in the adminbar.
 *
 * @since 4.0.0
 */
interface AdminbarUIInterface {

	/**
	 * Adds nodes to the adminbar.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 * @return void
	 */
	public function add_nodes( $wp_admin_bar );
}
