<?php
/**
 * Class to display the php memory in the adminbar.
 * This class prints the UI.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\PhpMemory;

use WP_Admin_Bar;
use Screenfeed\AdminbarTools\DisplayData\AbstractUI;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class to display the php memory in the adminbar.
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

		// ITEM LEVEL 1: php memory used.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-main',
				'id'     => 'sfabt-memory',
				'title'  => $this->render_template(
					'adminbar/late-adjust-wrap',
					[
						'late_adjust_tag'  => 'php memory used',
						'late_adjust_text' => sprintf(
							/* translators: 1 is a formatted number (don't use %d). */
							__( 'Memory used: %s', 'sf-adminbar-tools' ),
							$this->size_format( $data['memory_usage'], 2 )
						),
					]
				),
			]
		);

		// ITEM LEVEL 2: WP_MEMORY_LIMIT.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-memory',
				'id'     => 'sfabt-wp-memory-limit',
				'title'  => $this->render_template(
					'adminbar/memory-child',
					[
						'text' => sprintf(
							/* translators: 1 and 2 can be... anything. */
							__( '%1$s: %2$s', 'sf-adminbar-tools' ),
							'WP_MEMORY_LIMIT',
							$this->size_format( $data['wp_memory_limit'], 2 )
						),
					]
				),
			]
		);

		// ITEM LEVEL 2: WP_MAX_MEMORY_LIMIT.
		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-memory',
				'id'     => 'sfabt-wp-max-memory-limit',
				'title'  => $this->render_template(
					'adminbar/memory-child',
					[
						'text' => sprintf(
							/* translators: 1 and 2 can be... anything. */
							__( '%1$s: %2$s', 'sf-adminbar-tools' ),
							'WP_MAX_MEMORY_LIMIT',
							$this->size_format( $data['wp_max_memory_limit'], 2 )
						),
					]
				),
			]
		);
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

		// Memory used.
		$pattern     = $this->render_template(
			'adminbar/late-adjust-wrap',
			[
				'late_adjust_tag'  => 'php memory used',
				'late_adjust_text' => '(.+)',
			]
		);
		$replacement = $this->render_template(
			'adminbar/memory-used',
			[
				'text' => sprintf(
					/* translators: 1 is a formatted number (don't use %d). */
					__( 'Memory used: %s', 'sf-adminbar-tools' ),
					$this->size_format( $data['memory_usage'], 2 )
				),
			]
		);

		$new_buffer = preg_replace( "@{$pattern}@U", $replacement, $buffer );

		if ( ! empty( $new_buffer ) ) {
			$buffer = $new_buffer;
		}

		return $buffer;
	}

	/** ----------------------------------------------------------------------------------------- */
	/** TOOLS =================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * Converts float number to format, not based on the locale.
	 *
	 * @since 4.0.0
	 *
	 * @param  float|int $bytes    The number to convert.
	 * @param  int       $decimals Optional. Precision of the number of decimal places. Default 0.
	 * @return string|bool         Converted number in string format. False on error.
	 */
	private function size_format( $bytes, $decimals = 0 ) {
		$quant = [
			'TB' => TB_IN_BYTES,
			'GB' => GB_IN_BYTES,
			'MB' => MB_IN_BYTES,
			'kB' => KB_IN_BYTES,
			'B'  => 1,
		];

		if ( 0 === $bytes ) {
			return number_format_i18n( 0, $decimals ) . ' B';
		}

		foreach ( $quant as $unit => $mag ) {
			if ( (float) $bytes >= $mag ) {
				return number_format_i18n( $bytes / $mag, $decimals ) . ' ' . $unit;
			}
		}

		return false;
	}
}
