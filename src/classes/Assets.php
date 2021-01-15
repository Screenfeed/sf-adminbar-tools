<?php
/**
 * Class that enqueues CSS and JS assets.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

use WP_Admin_Bar;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Enqueues CSS and JS assets.
 *
 * @since 4.0.0
 */
class Assets {

	/**
	 * The plugin's URL.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $plugin_url;

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $plugin_url The plugin's URL.
	 * @return void
	 */
	public function __construct( $plugin_url ) {
		$this->plugin_url = $plugin_url;
	}

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ], 999 );
		add_action( 'wp_print_styles', [ $this, 'enqueue_assets' ], 999 );
	}

	/**
	 * Enqueues CSS and JS assets.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		$is_debug = (bool) get_constant( 'SCRIPT_DEBUG' );
		$min      = $is_debug ? '' : '.min';
		$version  = $is_debug ? get_constant( 'SFABT_VERSION' ) : time();

		wp_enqueue_style( 'sfabt', $this->plugin_url . 'assets/css/sfabt' . $min . '.css', [], $version, 'screen' );
		wp_enqueue_script( 'sfabt', $this->plugin_url . 'assets/js/sfabt' . $min . '.js', [ 'jquery' ], $version, true );

		$localize = [
			'debug' => $is_debug,
		];
		/**
		 * Filter the JS file localization.
		 *
		 * @since 4.0.0
		 *
		 * @param array<mixed> $localize Vars to localize.
		 */
		$localize = (array) apply_filters( 'sfabt_localize_script', $localize );

		if ( ! empty( $localize ) ) {
			wp_localize_script( 'sfabt', 'sfabtContext', $localize );
		}
	}
}
