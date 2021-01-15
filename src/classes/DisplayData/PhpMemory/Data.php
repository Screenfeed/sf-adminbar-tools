<?php
/**
 * Class to display the php memory in the adminbar.
 * This class provides the data to display.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\PhpMemory;

use WP_Admin_Bar;
use Screenfeed\AdminbarTools\DisplayData\AbstractUI;
use Screenfeed\AdminbarTools\DisplayData\DataInterface;
use function Screenfeed\AdminbarTools\get_constant;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class that provides the php memory data to display in the adminbar.
 *
 * @since 4.0.0
 */
class Data implements DataInterface {

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
	}

	/**
	 * Returns some data related to memory.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $args Unused optional arguments.
	 * @return array<mixed>
	 */
	public function get_data( $args = [] ) {
		return [
			'memory_usage'        => memory_get_usage(),
			'wp_memory_limit'     => wp_convert_hr_to_bytes( (string) get_constant( 'WP_MEMORY_LIMIT' ) ),
			'wp_max_memory_limit' => wp_convert_hr_to_bytes( (string) get_constant( 'WP_MAX_MEMORY_LIMIT' ) ),
		];
	}
}
