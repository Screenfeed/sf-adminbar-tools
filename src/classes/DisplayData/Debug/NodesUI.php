<?php
/**
 * Class to display the debug info (debug related constants, etc) in the adminbar.
 * This class prints the UI.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\Debug;

use WP_Admin_Bar;
use Screenfeed\AdminbarTools\DisplayData\AbstractUI;
use function Screenfeed\AdminbarTools\get_constant;
use function Screenfeed\AdminbarTools\has_constant;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class to display the debug info (debug related constants, etc) in the adminbar.
 *
 * @since 4.0.0
 */
class NodesUI extends AbstractUI {

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'sfabt_add_nodes_inside', [ $this, 'add_nodes' ], 5 );
	}

	/**
	 * Adds nodes to the adminbar.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 * @return void
	 */
	public function add_nodes( $wp_admin_bar ) {
		$data = $this->data->get_data();

		// ITEM LEVEL 1: WP debug.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-main',
				'id'     => 'sfabt-wp-debug',
				'title'  => $this->render_template(
					'adminbar/debug',
					[
						'text' => $data['debug'] ? __( 'WP_DEBUG is enabled', 'sf-adminbar-tools' ) : __( 'WP_DEBUG is disabled', 'sf-adminbar-tools' ),
					]
				),
			]
		);

		// ITEM LEVEL 2: script debug.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-wp-debug',
				'id'     => 'sfabt-script-debug',
				'title'  => $this->render_template(
					'adminbar/debug-child',
					[
						'text' => sprintf(
							/* translators: 1 and 2 can be... anything. */
							__( '%1$s: %2$s', 'sf-adminbar-tools' ),
							'SCRIPT_DEBUG',
							$data['script_debug'] ? 'true' : 'false'
						),
					]
				),
			]
		);

		// ITEM LEVEL 2: debug log.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-wp-debug',
				'id'     => 'sfabt-debug-log',
				'title'  => $this->render_template(
					'adminbar/debug-child',
					[
						'text' => sprintf(
							/* translators: 1 and 2 can be... anything. */
							__( '%1$s: %2$s', 'sf-adminbar-tools' ),
							'WP_DEBUG_LOG',
							$data['debug_log'] ? 'true' : 'false'
						),
					]
				),
			]
		);

		// ITEM LEVEL 2: debug display.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-wp-debug',
				'id'     => 'sfabt-debug-display',
				'title'  => $this->render_template(
					'adminbar/debug-child',
					[
						'text' => sprintf(
							/* translators: 1 and 2 can be... anything. */
							__( '%1$s: %2$s', 'sf-adminbar-tools' ),
							'WP_DEBUG_DISPLAY',
							$data['debug_display'] ? 'true' : 'false'
						),
					]
				),
			]
		);

		// ITEM LEVEL 2: error riporting.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-wp-debug',
				'id'     => 'sfabt-error-reporting',
				'title'  => $this->render_template(
					'adminbar/debug-child',
					[
						'text' => sprintf(
							/* translators: 1 is the error reporting level. */
							__( 'Error reporting %s', 'sf-adminbar-tools' ),
							$this->error_to_string( $data['error_reporting'] )
						),
					]
				),
			]
		);
	}

	/** ----------------------------------------------------------------------------------------- */
	/** TOOLS =================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Returns the error level as a string.
	 *
	 * @since 4.0.0
	 *
	 * @param  int $value An error level.
	 * @return string
	 */
	private function error_to_string( $value ) {
		$level_names = [
			(int) get_constant( 'E_ERROR' )           => 'E_ERROR',
			(int) get_constant( 'E_WARNING' )         => 'E_WARNING',
			(int) get_constant( 'E_PARSE' )           => 'E_PARSE',
			(int) get_constant( 'E_NOTICE' )          => 'E_NOTICE',
			(int) get_constant( 'E_CORE_ERROR' )      => 'E_CORE_ERROR',
			(int) get_constant( 'E_CORE_WARNING' )    => 'E_CORE_WARNING',
			(int) get_constant( 'E_COMPILE_ERROR' )   => 'E_COMPILE_ERROR',
			(int) get_constant( 'E_COMPILE_WARNING' ) => 'E_COMPILE_WARNING',
			(int) get_constant( 'E_USER_ERROR' )      => 'E_USER_ERROR',
			(int) get_constant( 'E_USER_WARNING' )    => 'E_USER_WARNING',
			(int) get_constant( 'E_USER_NOTICE' )     => 'E_USER_NOTICE',
		];

		if ( has_constant( 'E_STRICT' ) ) {
			$level_names[ (int) get_constant( 'E_STRICT' ) ] = 'E_STRICT';
		}

		$levels = [];
		$e_all  = get_constant( 'E_ALL' );

		if ( ( $value & $e_all ) === $e_all ) {
			$levels[] = 'E_ALL';
			$value   &= ~ $e_all;
		}

		foreach ( $level_names as $level => $name ) {
			$level = $level;

			if ( ( $value & $level ) === $level ) {
				$levels[] = $name;
			}
		}

		return implode( ' | ', $levels );
	}
}
