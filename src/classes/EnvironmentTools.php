<?php
/**
 * Environment tools' class.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Environment tools.
 *
 * @since 4.0.0
 */
class EnvironmentTools {

	/**
	 * Returns the main blog's ID.
	 *
	 * @since 4.0.0
	 *
	 * @return int
	 */
	public function get_main_blog_id() {
		static $blog_id;

		if ( isset( $blog_id ) ) {
			return $blog_id;
		}

		$current_site = get_global( 'current_site' );

		if ( ! is_multisite() ) {
			$blog_id = 1;
		} elseif ( ! empty( $current_site->blog_id ) ) {
			$blog_id = absint( $current_site->blog_id );
		} elseif ( has_constant( 'BLOG_ID_CURRENT_SITE' ) ) {
			$blog_id = absint( get_constant( 'BLOG_ID_CURRENT_SITE' ) );
		} elseif ( has_constant( 'BLOGID_CURRENT_SITE' ) ) {
			// deprecated.
			$blog_id = absint( get_constant( 'BLOG_ID_CURRENT_SITE' ) );
		}

		$blog_id = ! empty( $blog_id ) ? $blog_id : 1;

		return $blog_id;
	}

	/**
	 * Determines whether the plugin is active for the entire network.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $plugin Path to the plugin file relative to the plugins directory (plugin basename).
	 * @return bool
	 */
	public function is_plugin_active_for_network( $plugin ) {
		require_once get_constant( 'ABSPATH' ) . 'wp-admin/includes/plugin.php';

		return is_plugin_active_for_network( $plugin );
	}

	/**
	 * Determines whether the admin bar should be showing.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function is_admin_bar_showing() {
		$show_admin_bar = get_global( 'show_admin_bar' );
		$pagenow        = get_global( 'pagenow' );

		if ( function_exists( 'is_admin_bar_showing' ) ) {
			return (bool) is_admin_bar_showing();
		}

		if ( defined( 'XMLRPC_REQUEST' ) || defined( 'DOING_AJAX' ) || defined( 'IFRAME_REQUEST' ) ) {
			return false;
		}

		if ( function_exists( 'wp_is_json_request' ) && wp_is_json_request() ) {
			return false;
		}

		if ( is_embed() ) {
			return false;
		}

		if ( is_admin() ) {
			return true;
		}

		if ( ! isset( $show_admin_bar ) ) {
			if ( ! is_user_logged_in() || 'wp-login.php' === $pagenow ) {
				$show_admin_bar = false; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			} else {
				$show_admin_bar = _get_admin_bar_pref(); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			}
		}

		$show_admin_bar = (bool) apply_filters( 'show_admin_bar', $show_admin_bar ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		return $show_admin_bar;
	}
}
