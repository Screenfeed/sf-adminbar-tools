<?php
/**
 * Class to display base items in the adminbar.
 * This class prints the UI.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\BaseItems;

use WP_Admin_Bar;
use Screenfeed\AdminbarTools\DisplayData\AbstractUI;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class to display base items in the adminbar.
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
		add_action( 'admin_bar_menu', [ $this, 'add_nodes' ], 0 );
		ob_start( [ $this, 'adjust_late_values' ] );
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
		/**
		 * Fires before adding new adminbar nodes.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance.
		 */
		do_action( 'sfabt_add_nodes_before', $wp_admin_bar );

		$data = $this->data->get_data();

		// GROUP LEVEL 0: The main group.
		$wp_admin_bar->add_group(
			[
				'id'   => 'sfabt-tools',
				'meta' => [
					'class' => 'ab-top-secondary',
				],
			]
		);

		// ITEM LEVEL 0: The main item (requests and page load time).
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-tools',
				'id'     => 'sfabt-main',
				'title'  => $this->render_template(
					'adminbar/late-adjust-wrap',
					[
						'late_adjust_tag'  => 'main item',
						'late_adjust_text' => sprintf(
							/* translators: 1 is a formatted number (don't use %d), 2 is a number of seconds (float). */
							__( '%1$s q. - %2$s s', 'sf-adminbar-tools' ),
							number_format_i18n( (int) $data['num_queries'] ),
							$data['timer']
						),
					]
				),
			]
		);

		/**
		 * Fires before adding 1st level adminbar nodes.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance.
		 */
		do_action( 'sfabt_add_nodes_inside', $wp_admin_bar );

		// ITEM LEVEL 1: php version.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-main',
				'id'     => 'sfabt-php-version',
				'title'  => $this->render_template(
					'adminbar/php-version',
					[
						'text' => sprintf(
							/* translators: 1 is the PHP version in use. */
							__( 'php %s', 'sf-adminbar-tools' ),
							$data['php_version']
						),
					]
				),
			]
		);

		// ITEM LEVEL 1: WP version.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-main',
				'id'     => 'sfabt-wp-version',
				'title'  => $this->render_template(
					'adminbar/wp-version',
					[
						'text' => sprintf(
							/* translators: 1 is the WordPress version in use. */
							__( 'WP %s', 'sf-adminbar-tools' ),
							$data['wp_version']
						),
					]
				),
			]
		);

		/**
		 * Fires after adding new adminbar nodes.
		 *
		 * @param WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance.
		 */
		do_action( 'sfabt_add_nodes_after', $wp_admin_bar );
	}

	/**
	 * Adjusts the values that are not fully available when the adminbar is printed.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $buffer The page buffer.
	 * @return string
	 */
	public function adjust_late_values( $buffer ) {
		if ( ! is_string( $buffer ) || empty( $buffer ) ) {
			return $buffer;
		}

		$data = $this->data->get_data();

		// Main item.
		$pattern     = $this->render_template(
			'adminbar/late-adjust-wrap',
			[
				'late_adjust_tag'  => 'main item',
				'late_adjust_text' => '(.+)',
			]
		);
		$replacement = $this->render_template(
			'adminbar/main',
			[
				'text' => sprintf(
					/* translators: 1 is a formatted number (don't use %d), 2 is a number of seconds (float). */
					__( '%1$s q. - %2$s s', 'sf-adminbar-tools' ),
					number_format_i18n( (int) $data['num_queries'] ),
					$data['timer']
				),
			]
		);

		$new_buffer = preg_replace( "@{$pattern}@U", $replacement, $buffer );

		if ( ! empty( $new_buffer ) ) {
			$buffer = $new_buffer;
		}

		return $buffer;
	}
}
