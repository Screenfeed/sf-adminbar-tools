<?php
/**
 * Class to display admin hooks in the adminbar.
 * This class provides the data to display.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\AdminHooks;

use Screenfeed\AdminbarTools\DisplayData\DataInterface;
use function Screenfeed\AdminbarTools\get_global;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class that provides the admin hooks to display in the adminbar.
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
	 * Returns a list of some oftenly used admin hooks.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $args {
	 *     See below.
	 *
	 *     @var string $context Which set of data to get: 'before_headers', 'after_headers', or 'in_footer'.
	 * }
	 * @return array<string|array>
	 */
	public function get_data( $args = [] ) {
		$context = ! empty( $args['context'] ) ? $args['context'] : '';

		switch ( $context ) {
			case 'before_headers':
				return $this->get_admin_hooks_before_headers();

			case 'after_headers':
				return $this->get_admin_hooks_after_headers();

			case 'in_footer':
				return $this->get_admin_hooks_in_footer();

			default:
				return [];
		}
	}

	/**
	 * Returns a list of some oftenly used admin hooks that are triggered before headers.
	 *
	 * @since 4.0.0
	 *
	 * @return array<string|array>
	 */
	private function get_admin_hooks_before_headers() {
		$plugin_page = get_global( 'plugin_page' );
		$page_hook   = get_global( 'page_hook' );
		$pagenow     = get_global( 'pagenow' );
		$typenow     = get_global( 'typenow' );
		$taxnow      = get_global( 'taxnow' );

		$hooks = [
			'muplugins_loaded'    => 'muplugins_loaded',
			'plugins_loaded'      => 'plugins_loaded',
			'setup_theme'         => 'setup_theme',
			'after_setup_theme'   => 'after_setup_theme',
			'auth_cookie_valid'   => [
				'auth_cookie_valid',
				[
					'$cookie_elements' => 'array',
					'$user'            => 'WP_User object',
				],
			],
			'set_current_user'    => 'set_current_user',
			'init'                => 'init',
			'widgets_init'        => 'widgets_init',
			'wp_loaded'           => 'wp_loaded',
			'auth_redirect'       => [
				'auth_redirect',
				[
					'$user_id' => 'int',
				],
			],
			'admin_menu'          => [
				( is_network_admin() ? 'network_' : ( is_user_admin() ? 'user_' : '' ) ) . 'admin_menu',
				[
					"''" => 'empty string',
				],
			],
			'admin_init'          => 'admin_init',
			'admin_bar_init'      => 'admin_bar_init',
			'add_admin_bar_menus' => 'add_admin_bar_menus',
			'current_screen'      => [
				'current_screen',
				[
					'$current_screen' => 'WP_Screen object',
				],
			],
		];

		if ( ! empty( $plugin_page ) ) {
			if ( ! empty( $page_hook ) ) {
				$hooks['load-hook-plugin'] = 'load-' . $page_hook;
			} else {
				$hooks['load-hook-plugin'] = 'load-' . $plugin_page;
			}
		} elseif ( ! isset( $_GET['import'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$hooks['load-hook-pagenow'] = 'load-' . $pagenow;

			// Some old "load-*" hooks exist for backward compatibility.
			$old = false;

			if ( 'page' === $typenow ) {
				// New/Edit page.
				if ( 'post-new.php' === $pagenow ) {
					$old = 'page-new';
				} elseif ( 'post.php' === $pagenow ) {
					$old = 'page';
				}
			} elseif ( 'edit-tags.php' === $pagenow ) {
				// List taxonomy terms.
				if ( 'category' === $taxnow ) {
					$old = 'categories';
				} elseif ( 'link_category' === $taxnow ) {
					$old = 'edit-link-categories';
				}
			} elseif ( 'term.php' === $pagenow ) {
				// New/Edit taxonomy term.
				$old = 'edit-tags';
			}

			if ( ! empty( $old ) ) {
				$hooks['load-hook-pagenow-old'] = "load-$old.php";
			}
		}//end if

		if ( empty( $plugin_page ) && ! isset( $_GET['import'] ) && ! empty( $_REQUEST['action'] ) && is_string( $_REQUEST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			$hooks['load-hook-action'] = 'admin_action_' . wp_strip_all_tags( wp_unslash( $_REQUEST['action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended, @phpstan-ignore-line
		}

		return $hooks;
	}

	/**
	 * Returns a list of some oftenly used admin hooks that are triggered after headers.
	 *
	 * @since 4.0.0
	 *
	 * @return array<string|array>
	 */
	private function get_admin_hooks_after_headers() {
		$hook_suffix = get_global( 'hook_suffix' );
		$plugin_page = get_global( 'plugin_page' );
		$page_hook   = get_global( 'page_hook' );

		$hooks = [
			'admin_enqueue_scripts' => [
				'admin_enqueue_scripts',
				[
					'$hook_suffix' => $hook_suffix,
				],
			],
		];

		if ( ! empty( $hook_suffix ) ) {
			$new_hooks = [
				'admin_print_styles-' . $hook_suffix,
				'admin_print_styles',
				'admin_print_scripts-' . $hook_suffix,
				'admin_print_scripts',
				'wp_print_scripts',
				'admin_head-' . $hook_suffix,
			];
		} else {
			$new_hooks = [
				'admin_print_styles',
				'admin_print_scripts',
				'wp_print_scripts',
			];
		}

		$hooks = array_merge( $hooks, array_filter( (array) array_combine( $new_hooks, $new_hooks ) ) );

		$hooks = array_merge(
			$hooks,
			[
				'admin_head'        => 'admin_head',
				'adminmenu'         => 'adminmenu',
				'in_admin_header'   => 'in_admin_header',
				'admin_bar_menu'    => [
					'admin_bar_menu',
					[
						'&$wp_admin_bar' => get_class( $GLOBALS['wp_admin_bar'] ) . ' object',
					],
				],
				'admin_notices'     => ( is_network_admin() ? 'network_' : ( is_user_admin() ? 'user_' : '' ) ) . 'admin_notices',
				'all_admin_notices' => 'all_admin_notices',
			]
		);

		if ( ! empty( $plugin_page ) && ! empty( $page_hook ) ) {
			$hooks[ $page_hook ] = $page_hook;
		}

		return $hooks;
	}

	/**
	 * Returns a list of some oftenly used admin hooks that are triggered in footer.
	 *
	 * @since 4.0.0
	 *
	 * @return array<string|array>
	 */
	private function get_admin_hooks_in_footer() {
		$hook_suffix = get_global( 'hook_suffix' );

		return [
			'in_admin_footer'                           => 'in_admin_footer',
			'admin_footer'                              => [
				'admin_footer',
				[
					'\'\'' => 'empty string',
				],
			],
			"admin_print_footer_scripts-{$hook_suffix}" => "admin_print_footer_scripts-{$hook_suffix}",
			'admin_print_footer_scripts'                => 'admin_print_footer_scripts',
			"admin_footer-{$hook_suffix}"               => "admin_footer-{$hook_suffix}",
			'shutdown'                                  => 'shutdown',
		];
	}
}
