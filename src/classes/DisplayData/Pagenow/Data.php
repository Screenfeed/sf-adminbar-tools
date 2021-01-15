<?php
/**
 * Class to display the value of the global vars `$pagenow`, `$typenow`, and `$taxnow` in the adminbar.
 * This class provides the data to display.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\Pagenow;

use Screenfeed\AdminbarTools\DisplayData\DataInterface;
use function Screenfeed\AdminbarTools\get_global;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class that provides the value of `$pagenow`, `$typenow`, and `$taxnow` to display in the adminbar.
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
	 * Returns the values of `$pagenow`, `$typenow`, and `$taxnow`.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $args Optional arguments not in use here.
	 * @return array<string|null>
	 */
	public function get_data( $args = [] ) {
		$pagenow = get_global( 'pagenow' );
		$typenow = get_global( 'typenow' );
		$taxnow  = get_global( 'taxnow' );

		if ( empty( $pagenow ) && empty( $typenow ) && empty( $taxnow ) ) {
			return [];
		}

		return [
			'pagenow' => $pagenow,
			'typenow' => $typenow,
			'taxnow'  => $taxnow,
		];
	}
}
