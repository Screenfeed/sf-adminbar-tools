<?php
/**
 * Coworkers' class.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

use Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Options;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Coworkers.
 *
 * @since 4.0.0
 */
class Coworkers {

	/**
	 * An instance of Options.
	 *
	 * @var   Options
	 * @since 4.0.0
	 */
	private $options;

	/**
	 * Constructor.
	 *
	 * @since  4.0
	 *
	 * @param  Options $options An instance of Options.
	 * @return void
	 */
	public function __construct( $options ) {
		$this->options = $options;
	}

	/**
	 * Returns the coworkers list.
	 *
	 * @since 4.0.0
	 *
	 * @return array<int> User IDs as array keys and array values.
	 */
	public function get_list() {
		return $this->options->get( 'coworkers' );
	}

	/**
	 * Tells if the coworkers list still contains at least one eligible user.
	 *
	 * @since 4.0.0
	 *
	 * @return int|bool The user ID if allowed. False otherwise.
	 */
	public function have_eligible() {
		$have = false;

		foreach ( $this->get_list() as $user_id ) {
			if ( ( new User( $user_id, $this ) )->is_eligible() ) {
				$have = true;
				break;
			}
		}

		return $have;
	}

	/**
	 * Adds a user to the list of coworkers.
	 *
	 * @since 4.0.0
	 *
	 * @param  int $user_id A user ID. Default is the current user's ID.
	 * @return void
	 */
	public function add_coworker( $user_id = null ) {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		} elseif ( ! is_int( $user_id ) || 0 > $user_id ) {
			$user_id = 0;
		}

		if ( empty( $user_id ) ) {
			return;
		}

		$options = $this->options->get_all();

		$options['coworkers'][] = $user_id;

		$this->options->set( $options );
	}

	/**
	 * Removes a user from the list of coworkers.
	 *
	 * @since 4.0.0
	 *
	 * @param  int $user_id A user ID. Default is the current user's ID.
	 * @return void
	 */
	public function remove_coworker( $user_id = null ) {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		} elseif ( ! is_int( $user_id ) || 0 > $user_id ) {
			$user_id = 0;
		}

		if ( empty( $user_id ) ) {
			return;
		}

		$options = $this->options->get_all();

		if ( ! isset( $options['coworkers'][ $user_id ] ) ) {
			return;
		}

		unset( $options['coworkers'][ $user_id ] );

		$this->options->set( $options );
	}
}
