<?php
/**
 * Plugin Name: SF Admin Bar Tools
 * Plugin URI: https://www.screenfeed.fr/sf-abt/
 * Description: Adds some small development tools to the admin bar.
 * Version: 4.0
 * Requires PHP: 5.6
 * Author: Grégory Viguier
 * Author URI: https://www.screenfeed.fr/greg/
 * License: GPLv3
 * License URI: https://www.screenfeed.fr/gpl-v3.txt
 * Text Domain: sf-adminbar-tools
 * Domain Path: /languages/
 * php version 5.2
 *
 * @package Screenfeed/sf-adminbar-tools
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

define( 'SFABT_VERSION', '4.0' );

add_action( 'plugins_loaded', 'sfabt_plugin_init' );
/**
 * Initializes the plugin.
 *
 * @since 4.0.0
 *
 * @return void
 */
function sfabt_plugin_init() {
	// Nothing to do during autosave.
	if ( defined( 'DOING_AUTOSAVE' ) ) {
		return;
	}

	$plugin_dir  = plugin_dir_path( __FILE__ );
	$plugin_name = 'SF Admin Bar Tools';

	// Check for WordPress and PHP version.
	require_once $plugin_dir . '/src/class-sfabt-requirements-check.php';

	$requirement_checks = new SFABT_Requirements_Check(
		array(
			'plugin_name'    => $plugin_name,
			'plugin_version' => SFABT_VERSION,
			'wp_version'     => '4.7',
			'php_version'    => '5.6',
		)
	);

	if ( ! $requirement_checks->check() ) {
		$requirement_checks->add_notice();
		return;
	}

	// Init the plugin.
	require_once $plugin_dir . '/src/classes/Plugin.php';

	$plugin = call_user_func(
		array( 'Screenfeed\AdminbarTools\Plugin', 'construct' ),
		array(
			'plugin_file' => __FILE__,
			'plugin_name' => $plugin_name,
		)
	);

	$plugin->init();
}

/**
 * Returns the user capacity required to operate the plugin.
 *
 * @since 4.0.0
 *
 * @return string A user capacity or user role.
 */
function sfabt_get_user_capacity() {
	static $cap;

	if ( isset( $cap ) ) {
		return $cap;
	}

	if ( defined( 'SFABT_CAP' ) && is_string( SFABT_CAP ) && '' !== SFABT_CAP ) {
		$cap = SFABT_CAP;
	} else {
		$cap = 'administrator';
	}

	return $cap;
}

/**
 * Loads the plugin translations.
 * Previously named `sfabt_lang_init()`.
 *
 * @since 4.0.0
 *
 * @return void
 */
function sfabt_load_translations() {
	load_plugin_textdomain( 'sf-adminbar-tools', false, basename( dirname( __FILE__ ) ) . '/languages/' );
}
