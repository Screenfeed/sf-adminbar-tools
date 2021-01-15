<?php
/**
 * Class that checks for plugin's requirements.
 * php version 5.2
 *
 * @package Screenfeed/sf-adminbar-tools
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class to check if the current WordPress and PHP versions meet our requirements.
 * This class must be compatible with php 5.2.
 *
 * @since 4.0.0
 */
class SFABT_Requirements_Check {

	/**
	 * Tells is the instanciation went fine.
	 *
	 * @var   bool
	 * @since 4.0.0
	 */
	private $instance_ok = true;

	/**
	 * Plugin Name.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $plugin_name;

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $plugin_version;

	/**
	 * Required WordPress version.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $wp_version;

	/**
	 * Required PHP version.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $php_version;

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param array<string> $args {
	 *     Arguments to populate the class properties.
	 *
	 *     @type string $plugin_name    Plugin name.
	 *     @type string $plugin_version Plugin version.
	 *     @type string $wp_version     Required WordPress version.
	 *     @type string $php_version    Required PHP version.
	 * }
	 */
	public function __construct( $args ) {
		if ( ! is_array( $args ) ) {
			$this->instance_ok = false;
			return;
		}

		foreach ( array( 'plugin_name', 'plugin_version', 'wp_version', 'php_version' ) as $setting ) {
			if ( ! isset( $args[ $setting ] ) || ! is_string( $args[ $setting ] ) ) {
				$this->instance_ok = false;
				return;
			}

			$this->$setting = $args[ $setting ];
		}
	}

	/**
	 * Checks if all requirements are ok, if not, display a notice and the rollback.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function check() {
		if ( ! $this->instance_ok ) {
			return false;
		}

		return $this->php_passes() && $this->wp_passes();
	}

	/**
	 * Checks if the current PHP version is equal or superior to the required PHP version.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function php_passes() {
		return version_compare( PHP_VERSION, $this->php_version ) >= 0;
	}

	/**
	 * Checks if the current WordPress version is equal or superior to the required WP version.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function wp_passes() {
		if ( empty( $GLOBALS['wp_version'] ) ) {
			return false;
		}

		return version_compare( $GLOBALS['wp_version'], $this->wp_version ) >= 0;
	}

	/**
	 * Adds an admin notice to the queue, saying that the plugin cannot work in this environment.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function add_notice() {
		if ( ! $this->instance_ok ) {
			return;
		}

		add_action( 'admin_notices', array( $this, 'print_notice' ) );
	}

	/**
	 * Prints an admin nortice, saying that the plugin cannot work in this environment.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function print_notice() {
		if ( ! $this->instance_ok ) {
			return;
		}

		if ( ! current_user_can( sfabt_get_user_capacity() ) ) {
			return;
		}

		sfabt_load_translations();

		$required = array();

		if ( ! $this->php_passes() ) {
			$required[] = 'PHP ' . $this->php_version;
		}

		if ( ! $this->wp_passes() ) {
			$required[] = 'WordPress ' . $this->wp_version;
		}

		$required = wp_sprintf_l( '%l', $required );

		echo '<div class="notice notice-error"><p>';
		printf(
			/* translators: %1$s = Plugin name, %2$s = Plugin version, $3$s is something like "PHP 5.6" or "PHP 5.6 and WordPress 4.0". */
			esc_html__( 'To function properly, %1$s %2$s requires at least %3$s.', 'sf-adminbar-tools' ),
			'<strong>' . esc_html( $this->plugin_name ) . '</strong>',
			esc_html( $this->plugin_version ),
			esc_html( $required )
		);
		echo '</p></div>';
	}
}
