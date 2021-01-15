<?php
/**
 * Class to display the value of the global vars `$pagenow`, `$typenow`, and `$taxnow` in the adminbar.
 * This class prints the UI.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\Pagenow;

use WP_Admin_Bar;
use Screenfeed\AdminbarTools\DisplayData\AbstractUI;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class to display the value of the global vars `$pagenow`, `$typenow`, and `$taxnow` in the adminbar.
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
	 * Adds nodes displaying the value of `$pagenow`, `$typenow`, and `$taxnow`.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 * @return void
	 */
	public function add_nodes( $wp_admin_bar ) {
		$data = $this->data->get_data();

		if ( empty( $data ) ) {
			return;
		}

		// ITEM LEVEL 1: $...now menu.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-main',
				'id'     => 'sfabt-now',
				'title'  => $this->render_template(
					'adminbar/varnow',
					[
						'text' => '$...now',
					]
				),
			]
		);

		// ITEMS LEVEL 2: $...now items.
		foreach ( $data as $name => $value ) {
			$value = ! empty( $value ) ? esc_html( $value ) : 'NULL';

			$wp_admin_bar->add_node(
				[
					'parent' => 'sfabt-now',
					'id'     => 'sfabt-' . $name,
					'title'  => $this->render_template(
						'adminbar/varnow-child',
						[
							/* translators: 1 and 2 can be... anything. */
							'text' => sprintf( __( '%1$s: %2$s', 'sf-adminbar-tools' ), '$' . $name, $value ),
						]
					),
				]
			);
		}
	}
}
