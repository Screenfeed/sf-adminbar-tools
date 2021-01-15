<?php
/**
 * Class to display the value of a variable (or anything else).
 * This class prints the UI.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\SomeVar;

use WP_Admin_Bar;
use Screenfeed\AdminbarTools\DisplayData\AbstractUI;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class to display the value of a variable (or anything else).
 *
 * @since 4.0.0
 */
class NodesUI extends AbstractUI {

	/**
	 * Key (action) used for the nonce.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	const NONCE_KEY = 'sfabt_get-var';

	/**
	 * Key used to send the name of the variable to print.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	const VAR_KEY = 'sfabt-var';

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->data->init();

		add_filter( 'sfabt_localize_script', [ $this, 'localize_script' ], 5 );

		if ( is_admin() ) {
			add_action( 'admin_print_footer_scripts', [ $this, 'print_lightbox_template' ] );
		} else {
			add_action( 'wp_print_footer_scripts', [ $this, 'print_lightbox_template' ] );
		}

		add_action( 'sfabt_add_nodes_inside', [ $this, 'add_nodes' ] );
		add_action( 'init', [ $this, 'dispatch_guide' ] );
	}

	/**
	 * Filters the JS file localization to add the nonce value and some translated strings.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $localize Vars to localize.
	 * @return array<mixed>
	 */
	public function localize_script( $localize ) {
		if ( empty( $this->data->get_data() ) ) {
			return $localize;
		}

		$localize['queryNonce'] = wp_create_nonce( static::NONCE_KEY );

		return $localize;
	}

	/**
	 * Prints the template to use in JS for the lightbox.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function print_lightbox_template() {
		$this->print_template(
			'js-template',
			[
				'template_id'   => 'adminbar-lightbox',
				'lightbox?'     => true,
				'wrapper_label' => esc_attr__( 'Close this modal window', 'sf-adminbar-tools' ),
				'button_title'  => esc_attr__( 'Click to reload the value', 'sf-adminbar-tools' ),
				'spinner_title' => esc_attr__( 'Loading...', 'sf-adminbar-tools' ),
			],
			function ( $template ) {
				return str_replace( [ "\t", "\n" ], '', $template );
			}
		);
	}

	/**
	 * Adds nodes to the admin bar that display the content of some variable.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 * @return void
	 */
	public function add_nodes( $wp_admin_bar ) {
		foreach ( $this->data->get_data() as $name => $value ) {
			$wp_admin_bar->add_node(
				[
					'parent' => 'sfabt-main',
					'id'     => 'sfabt-var-' . $name,
					'title'  => $this->render_template(
						'adminbar/some-var',
						[
							'var_name_attr' => esc_attr( $name ),
							'var_name'      => $name,
							'spinner_title' => esc_attr__( 'Loading...', 'sf-adminbar-tools' ),
						]
					),
					'meta'   => [
						'class'    => 'sfabt-var hide-if-no-js',
						'tabindex' => 0,
						'title'    => sprintf(
							/* translators: 1 is a variable's name. */
							__( "Display %s's value", 'sf-adminbar-tools' ),
							$name
						),
					],
				]
			);
		}//end foreach
	}

	/**
	 * Hooks the printer depending on the context.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function dispatch_guide() {
		if ( is_admin() ) {
			add_action( 'admin_init', [ $this, 'maybe_print_value' ], 0 );
		} else {
			add_action( 'wp', [ $this, 'maybe_print_value' ], 0 );
		}
	}

	/**
	 * Maybe prints the requested value.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function maybe_print_value() {
		$var_name = filter_input( INPUT_GET, static::VAR_KEY );
		$nonce    = filter_input( INPUT_GET, '_wpnonce' );

		if ( empty( $var_name ) || null === $nonce ) {
			return;
		}

		if ( ! $this->verify_nonce() ) {
			$this->print_value( __( 'Sorry, you are not allowed to do that.', 'sf-adminbar-tools' ) );
			return;
		}

		$vars = $this->data->get_data();

		if ( isset( $vars[ $var_name ] ) ) {
			$value = call_user_func( $vars[ $var_name ] );
		} else {
			$value = __( 'Variable not found.', 'sf-adminbar-tools' );
		}

		$this->print_value( $value );
	}

	/**
	 * Tells if printing values is allowed.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	private function verify_nonce() {
		if ( empty( $_GET['_wpnonce'] ) ) {
			return false;
		}

		$nonce_value = filter_input( INPUT_GET, '_wpnonce' );

		return (bool) wp_verify_nonce( $nonce_value, static::NONCE_KEY );
	}

	/**
	 * Prints a value and dies.
	 *
	 * @since 4.0.0
	 *
	 * @param  mixed $value The value to print.
	 * @return void
	 */
	private function print_value( $value ) {
		if ( ! headers_sent() ) {
			if ( is_404() ) {
				header( 'HTTP/1.0 200 OK' );
			}

			nocache_headers();
		}

		if ( ( is_string( $value ) && '' === trim( $value ) ) || null === $value || is_bool( $value ) ) {
			$callback = 'Screenfeed\AdminbarTools\var_export';
		} else {
			$callback = 'print_r';
		}

		echo esc_html( call_user_func( $callback, $value, true ) );
		die();
	}
}
