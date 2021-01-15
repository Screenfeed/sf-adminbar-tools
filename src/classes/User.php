<?php
/**
 * Coworker's class.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Coworker.
 *
 * @since 4.0.0
 */
class User {
	/**
	 * The user ID.
	 *
	 * @var   int
	 * @since 4.0.0
	 */
	private $user_id;

	/**
	 * An instance of Coworkers.
	 *
	 * @var   Coworkers
	 * @since 4.0.0
	 */
	private $coworkers;

	/**
	 * Constructor.
	 *
	 * @since  4.0
	 *
	 * @param  int|null  $user_id   A user ID. Default is the current user's ID.
	 * @param  Coworkers $coworkers An instance of Coworkers.
	 * @return void
	 */
	public function __construct( $user_id, $coworkers ) {
		if ( null === $user_id ) {
			$user_id = get_current_user_id();
		} elseif ( ! is_int( $user_id ) || 0 > $user_id ) {
			$user_id = 0;
		}

		$this->user_id   = $user_id;
		$this->coworkers = $coworkers;
	}

	/**
	 * Tells if the user can be added to the coworkers list.
	 *
	 * @since 4.0.0
	 *
	 * @return bool
	 */
	public function is_eligible() {
		return ! empty( $this->user_id ) && user_can( $this->user_id, sfabt_get_user_capacity() );
	}

	/**
	 * Tells if a user is in the coworkers list.
	 *
	 * @since 4.0.0
	 *
	 * @return int|bool The user ID if allowed. False otherwise.
	 */
	public function is_coworker() {
		if ( empty( $this->user_id ) ) {
			return false;
		}

		$coworkers = $this->coworkers->get_list();

		return ! empty( $coworkers[ $this->user_id ] ) && $this->is_eligible() ? $this->user_id : false;
	}

	/**
	 * Adds the user to the list of coworkers.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function register_as_coworker() {
		$this->coworkers->add_coworker( $this->user_id );
	}

	/**
	 * Removes the user from the list of coworkers.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function deregister_as_coworker() {
		$this->coworkers->remove_coworker( $this->user_id );
	}
}
