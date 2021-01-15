<?php
/**
 * Class to display the value of a variable (or anything else).
 * This class provides the data to display.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\SomeVar;

use Closure;
use Screenfeed\AdminbarTools\DisplayData\DataInterface;
use function Screenfeed\AdminbarTools\get_global;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class that provides the value of the variables to display in the adminbar.
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
		add_filter( 'sfabt_displayable_vars', [ $this, 'add_wp_query' ], 5 );
		add_filter( 'sfabt_displayable_vars', [ $this, 'add_profile_data' ], 5 );
		add_filter( 'sfabt_displayable_vars', [ $this, 'add_user_data' ], 5 );
	}

	/**
	 * Returns the list of the variables and their value.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $args Optional arguments not in use here.
	 * @return array<Closure>
	 */
	public function get_data( $args = [] ) {
		/**
		 * Filter the list of variables that can be displayed in the lightbox.
		 *
		 * @since 4.0.0
		 *
		 * @param array<Closure> $vars Each entry must have the variable's name as key. The entry value is an anonymous function returning the variable's value.
		 */
		$vars = (array) apply_filters( 'sfabt_displayable_vars', [] );

		return array_filter(
			$vars,
			function ( $value, $key ) {
				return is_string( $key ) && is_callable( $value ) && $value instanceof Closure;
			},
			ARRAY_FILTER_USE_BOTH
		);
	}

	/**
	 * Filters the list of variables that can be displayed in the lightbox to add `$wp_query` on frontend.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<Closure> $vars Each entry must have the variable's name as key. The entry value is an anonymous function returning the variable's value.
	 * @return array<Closure>
	 */
	public function add_wp_query( $vars ) {
		if ( is_admin() ) {
			return $vars;
		}

		$vars = (array) $vars;

		$vars['$wp_query'] = function () {
			$query = $GLOBALS['wp_query'];

			if ( ! isset( $query->queried_object ) ) {
				$query->queried_object = $query->get_queried_object();
			}

			if ( ! isset( $query->queried_object_id ) ) {
				$query->queried_object_id = $query->get_queried_object_id();
			}

			return $query;
		};

		return $vars;
	}

	/**
	 * Filters the list of variables that can be displayed in the lightbox to add the current user data on the user profile page.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<Closure> $vars Each entry must have the variable's name as key. The entry value is an anonymous function returning the variable's value.
	 * @return array<Closure>
	 */
	public function add_profile_data( $vars ) {
		$pagenow = get_global( 'pagenow' );

		if ( 'profile.php' !== $pagenow ) {
			return $vars;
		}

		$vars = (array) $vars;

		$vars['$userdata'] = function () {
			return get_userdata( get_current_user_id() );
		};

		return $vars;
	}

	/**
	 * Filters the list of variables that can be displayed in the lightbox to add the user data on all user profile pages.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<Closure> $vars Each entry must have the variable's name as key. The entry value is an anonymous function returning the variable's value.
	 * @return array<Closure>
	 */
	public function add_user_data( $vars ) {
		$pagenow = get_global( 'pagenow' );

		if ( 'user-edit.php' !== $pagenow ) {
			return $vars;
		}

		$vars = (array) $vars;

		$vars['$userdata'] = function () {
			if ( ! current_user_can( 'edit_users' ) ) {
				return __( "Sorry, you are not allowed to see this user's data.", 'sf-adminbar-tools' );
			}

			if ( is_multisite() && ! current_user_can( 'manage_network_users' ) && ! apply_filters( 'enable_edit_any_user_configuration', true ) ) {
				return __( "Sorry, you are not allowed to see this user's data.", 'sf-adminbar-tools' );
			}

			$user_id = filter_input(
				INPUT_GET,
				'user_id',
				FILTER_VALIDATE_INT,
				[
					'options' => [
						'min_range' => 0,
					],
				]
			);

			if ( empty( $user_id ) ) {
				return null;
			}

			if ( ! current_user_can( 'edit_user', $user_id ) ) {
				return __( "Sorry, you are not allowed to see this user's data.", 'sf-adminbar-tools' );
			}

			return get_userdata( $user_id );
		};

		return $vars;
	}
}
