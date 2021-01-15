<?php
/**
 * API functions.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

use Screenfeed\AdminbarTools\Dependencies\League\Container\Container;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Returns the dependency injection container.
 *
 * @since 4.0.0
 *
 * @return Container|null
 */
function get_container() {
	/**
	 * Filter the dependency injection container.
	 *
	 * @since 4.0.0
	 *
	 * @return Container|null
	 */
	$container = apply_filters( 'sfabt_container', null );

	if ( ! $container instanceof Container ) {
		return null;
	}

	return $container;
}
