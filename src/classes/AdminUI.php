<?php
/**
 * Class containing the admin UI.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

use ScreenfeedAdminbarTools_Mustache_Engine as Template_Engine;
use Screenfeed\AdminbarTools\Traits\TemplateEngineTrait;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Admin UI.
 *
 * @since 4.0.0
 */
class AdminUI {
	use TemplateEngineTrait;

	/**
	 * Path to the plugin file relative to the plugins directory (plugin basename).
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $plugin_basename;

	/**
	 * Path to the plugin's directory.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $plugin_path;

	/**
	 * Plugin name.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $plugin_name;

	/**
	 * Constructor.
	 *
	 * @since  4.0
	 *
	 * @param  Template_Engine $templates       Instance of the template engine.
	 * @param  string          $plugin_basename Path to the plugin file relative to the plugins directory (plugin basename).
	 * @param  string          $plugin_path     Path to the plugin's directory.
	 * @param  string          $plugin_name     Plugin name.
	 * @return void
	 */
	public function __construct( $templates, $plugin_basename, $plugin_path, $plugin_name ) {
		$this->set_engine( $templates );
		$this->plugin_basename = $plugin_basename;
		$this->plugin_path     = $plugin_path;
		$this->plugin_name     = $plugin_name;
	}

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_filter( 'plugin_action_links_' . $this->plugin_basename, [ $this, 'settings_action_link' ], 10 );
		add_filter( 'network_admin_plugin_action_links_' . $this->plugin_basename, [ $this, 'settings_action_link' ], 10 );
		add_action( 'admin_menu', [ $this, 'add_tests_menu_item' ], PHP_INT_MAX - 1 );
		add_action( 'admin_menu', [ $this, 'add_all_settings_menu_item' ], PHP_INT_MAX );
	}

	/**
	 * Adds a link in the plugin's row (plugins list) to the user's profile.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<string> $links An array of plugin action links.
	 * @return array<string>
	 */
	public function settings_action_link( $links ) {
		$links['settings'] = $this->render_template(
			'link',
			[
				'href' => esc_url( self_admin_url( 'profile.php' ) . '#sf-adminbar-tools' ),
				'text' => __( 'Profile', 'sf-adminbar-tools' ),
			]
		);
		return $links;
	}

	/**
	 * Adds a link to the "All Settings" page if not already present.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function add_all_settings_menu_item() {
		$submenu         = get_global( 'submenu' );
		$add_all_options = true;

		if ( ! empty( $submenu['options-general.php'] ) ) {
			foreach ( $submenu['options-general.php'] as $option ) {
				if ( 'options.php' === $option[2] ) {
					$add_all_options = false;
					break;
				}
			}
		}

		if ( $add_all_options ) {
			add_options_page( __( 'All Settings' ), __( 'All Settings' ), sfabt_get_user_capacity(), 'options.php' );
		}
	}

	/**
	 * Adds a link to the tests page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function add_tests_menu_item() {
		add_menu_page(
			sprintf(
				/* translators: 1 is the plugin name. */
				__( "%s' tests page", 'sf-adminbar-tools' ),
				$this->plugin_name
			),
			__( 'Code Tester', 'sf-adminbar-tools' ),
			sfabt_get_user_capacity(),
			'sfabt-code-tester',
			[ $this, 'display_tests_page_contents' ]
		);
	}

	/**
	 * Displays the page contents.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function display_tests_page_contents() {
		$title      = get_global( 'title' );
		$tests_file = $this->plugin_path . 'tests.php';

		if ( ! file_exists( $tests_file ) ) {
			$this->print_template(
				'tests',
				[
					'title'    => $title,
					'message?' => [
						'html' => esc_html__( 'Tests file not found.', 'sf-adminbar-tools' ),
					],
				]
			);
			return;
		}

		ob_start();
		include $tests_file;
		$tests = ob_get_clean();

		if ( '' === $tests || false === $tests ) {
			$this->print_template(
				'tests',
				[
					'title'    => $title,
					'message?' => [
						'html' => sprintf(
							/* translators: 1 is a file name, 2 is HTML tag name. */
							esc_html__( 'Here you can test everything you want with your code, simply by editing the file %1$s in the plugin and printing stuff: it will be escaped and wrapped in a %2$s tag automatically.', 'sf-adminbar-tools' ),
							'<code>tests.php</code>',
							'<code>&lt;pre/&gt;</code>'
						),
					],
				]
			);
			return;
		}

		if ( strpos( $tests, '<pre' ) !== 0 ) {
			// Classic code output.
			$this->print_template(
				'tests',
				[
					'title'         => $title,
					'code_non_pre?' => [
						'code' => $tests,
					],
				]
			);
			return;
		}

		// Xdebug.
		$this->print_template(
			'tests',
			[
				'title'    => $title,
				'code_pre' => wp_kses(
					$tests,
					[
						'b'     => [],
						'font'  => [
							'color' => true,
						],
						'i'     => [],
						'pre'   => [
							'class' => true,
							'dir'   => true,
						],
						'small' => [],
					]
				),
			]
		);
	}
}
