<?php
/**
 * Class to display admin hooks in the adminbar.
 * This class prints the UI.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\AdminHooks;

use WP_Admin_Bar;
use Screenfeed\AdminbarTools\DisplayData\AbstractUI;
use function Screenfeed\AdminbarTools\get_global;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class to display admin hooks in the adminbar.
 *
 * @since 4.0.0
 */
class NodesUI extends AbstractUI {

	/**
	 * ID of the main node.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $main_node_id = 'sfabt-admin-hooks';

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'sfabt_add_nodes_inside', [ $this, 'add_nodes' ], 5 );
		ob_start( [ $this, 'adjust_late_values' ] );
	}

	/**
	 * Adds nodes displaying some oftenly used admin hooks.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 * @return void
	 */
	public function add_nodes( $wp_admin_bar ) {
		// ITEM LEVEL 1: admin hooks menu.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-main',
				'id'     => $this->main_node_id,
				'title'  => $this->render_template(
					'adminbar/admin-hooks',
					[
						'text' => __( 'Admin hooks', 'sf-adminbar-tools' ),
					]
				),
			]
		);

		$contexts = [
			'before_headers' => __( 'Hooks before headers', 'sf-adminbar-tools' ),
			'after_headers'  => __( 'Hooks after headers', 'sf-adminbar-tools' ),
			'in_footer'      => __( 'Hooks in footer', 'sf-adminbar-tools' ),
		];

		// ITEMS LEVEL 2 + ITEMS LEVEL 3.
		foreach ( $contexts as $context => $label ) {
			$this->add_nodes_for_context( $wp_admin_bar, $context, $label );
		}
	}

	/**
	 * Adjusts the values that are not fully available when the adminbar is printed.
	 * Here we're looking for hooks (adminbar LIs) that display the `late-adjust-wrap` template,
	 * meaning that the hook has not fired yet because it happens after the adminbar is printed.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $buffer The page buffer.
	 * @return string
	 */
	public function adjust_late_values( $buffer ) {
		$wp_actions = get_global( 'wp_actions' );
		$wp_filter  = get_global( 'wp_filter' );

		if ( ! is_string( $buffer ) || empty( $buffer ) ) {
			return $buffer;
		}

		$pattern = $this->render_template(
			'adminbar/late-adjust-wrap',
			[
				'late_adjust_tag'  => '([a-z_.-]+) admin hook',
				'late_adjust_text' => '&times;',
			]
		);

		preg_match_all( "@{$pattern}@", $buffer, $matches, PREG_SET_ORDER );

		if ( empty( $matches ) ) {
			return $buffer;
		}

		foreach ( $matches as $match ) {
			if ( ! isset( $wp_actions[ $match[1] ] ) ) {
				continue;
			}

			if ( empty( $wp_filter[ $match[1] ] ) ) {
				$count = 0;
			} else {
				$count = array_sum( array_map( 'count', $wp_filter[ $match[1] ]->callbacks ) );
			}

			$count = $this->render_template(
				'adminbar/admin-hook-count',
				[
					'text' => $count,
				]
			);

			$buffer = str_replace( $match[0], $count, $buffer );
		}

		return $buffer;
	}

	/**
	 * Adds nodes displaying some oftenly used admin hooks.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 * @param  string       $context      Which set of data to get: 'before_headers', 'after_headers', or 'in_footer'.
	 * @param  string       $label        Label to use for this group of nodes.
	 * @return void
	 */
	private function add_nodes_for_context( $wp_admin_bar, $context, $label ) {
		$node_id = sprintf( 'sfabt-%s-hooks', $context );

		// ITEM LEVEL 2: menu.
		$wp_admin_bar->add_node(
			[
				'parent' => $this->main_node_id,
				'id'     => $node_id,
				'title'  => $this->render_template(
					'adminbar/admin-hooks-context',
					[
						'text' => $label,
					]
				),
			]
		);

		// ITEMS LEVEL 3: items.
		foreach ( $this->data->get_data( [ 'context' => $context ] ) as $id => $hook ) {
			$wp_admin_bar->add_node(
				[
					'parent' => $node_id,
					'id'     => 'sfabt-' . $id,
					'title'  => $this->get_admin_hook_content( $hook ),
					'meta'   => [ 'class' => 'has-intel' ],
				]
			);
		}
	}

	/**
	 * Returns the UI to display for an admin hook.
	 *
	 * @since 4.0.0
	 *
	 * @param  string|array<mixed> $hook   The name of the hook. Can be an array like `[ $hook, $params ]`.
	 * @param  array<mixed>        $params Parameters passed to the hook.
	 * @return string
	 */
	private function get_admin_hook_content( $hook, $params = [] ) {
		$wp_actions = get_global( 'wp_actions' );
		$wp_filter  = get_global( 'wp_filter' );

		if ( is_array( $hook ) ) {
			$hook_name = $hook[0];
			$params    = $hook[1];
		} else {
			$hook_name = $hook;
			$params    = [];
		}

		if ( isset( $wp_actions[ $hook_name ] ) ) {
			if ( empty( $wp_filter[ $hook_name ] ) ) {
				$count = [ 0 ];
			} else {
				$count = array_sum( array_map( 'count', $wp_filter[ $hook_name ]->callbacks ) );
				$count = [ $count ];
			}
		} else {
			$count = false;
		}

		if ( is_array( $params ) && ! empty( $params ) ) {
			$nbr_params = count( $params );
			$params_txt = [];

			foreach ( $params as $param => $value ) {
				$params_txt[] = sprintf(
					/* translators: 1 is the name of a parameter, 2 is its value. */
					__( '%1$s (%2$s)', 'sf-adminbar-tools' ),
					$param,
					$value
				);
			}

			$data_attr = $nbr_params > 1 ? ' data-nbrparams="' . $nbr_params . '"' : '';
			$params    = [
				'title' => esc_attr(
					sprintf(
						/* translators: 1 is a list of parameters and their value. */
						_n( 'Parameter: %s', 'Parameters: %s', $nbr_params, 'sf-adminbar-tools' ),
						implode( ', ', $params_txt )
					)
				),
			];
		} else {
			$data_attr = '';
			$params    = false;
		}//end if

		return $this->render_template(
			'adminbar/admin-hook',
			[
				'count?'           => $count,
				'params?'          => $params,
				'min_width'        => strlen( $hook_name ),
				'hook_name_value'  => esc_attr( $hook_name ),
				'atts'             => $data_attr,
				'late_adjust_tag'  => "{$hook_name} admin hook",
				'late_adjust_text' => '&times;',
			]
		);
	}
}
