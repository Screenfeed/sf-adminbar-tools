<?php
/**
 * Class that disables some WP features.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

use ScreenfeedAdminbarTools_Mustache_Engine as Template_Engine;
use Screenfeed\AdminbarTools\Traits\TemplateEngineTrait;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Disables some WP Features.
 *
 * @since 4.0.0
 */
class DisableWPFeatures {
	use TemplateEngineTrait;

	/**
	 * Constructor.
	 *
	 * @since  4.0
	 *
	 * @param  Template_Engine $templates Instance of the template engine.
	 * @return void
	 */
	public function __construct( $templates ) {
		$this->set_engine( $templates );
	}

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		// Autosave + Heartbeat.
		add_action( 'load-post.php', [ $this, 'maybe_disable_autosave' ] );
		add_action( 'load-post-new.php', [ $this, 'maybe_disable_autosave' ] );
		// Post Lock, for ourselves.
		add_filter( 'update_post_metadata', [ $this, 'dont_set_post_lock' ], 10, 4 );
		add_filter( 'show_post_locked_dialog', '__return_false' );
	}

	/**
	 * Depending on the user setting, disables posts autosave.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function maybe_disable_autosave() {
		$wp_scripts = get_global( 'wp_scripts' );

		if ( ! get_user_meta( get_current_user_id(), 'sf-abt-no-autosave', true ) ) {
			return;
		}

		// Remove autosave and heartbeat from dependencies to not prevent other scripts from being enqueued.
		// Of course it can lead to troubles, but it's better than dequeueing all other scripts.
		if ( ! empty( $wp_scripts->registered ) ) {
			foreach ( $wp_scripts->registered as $s => $r ) {
				if ( ! empty( $r->deps ) ) {
					$pos = array_search( 'heartbeat', $r->deps, true );

					if ( false !== $pos ) {
						unset( $wp_scripts->registered[ $s ]->deps[ $pos ] );
					}

					$pos = array_search( 'autosave', $r->deps, true );

					if ( false !== $pos ) {
						unset( $wp_scripts->registered[ $s ]->deps[ $pos ] );
					}
				}
			}
		}

		// Remove autosave and heartbeat from the queue to not trigger notices.
		$wp_scripts->queue = array_values( array_diff( $wp_scripts->queue, [ 'autosave', 'heartbeat' ] ) );

		// Finally, remove autosave and heartbeat.
		wp_deregister_script( 'autosave' );
		wp_deregister_script( 'heartbeat' );

		// Remind the user.
		add_action( 'admin_notices', [ $this, 'disable_autosave_notice' ] );
	}

	/**
	 * Prints a notice alerting that autosave is disabled.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function disable_autosave_notice() {
		$post = get_global( 'post' );
		$tags = [
			'type'    => 'info',
			'message' => [
				[
					'text' => __( 'Autosave and post lock disabled. As long as you don\'t save your modifications, you can mess with this post!', 'sf-adminbar-tools' ),
				],
			],
		];

		$user_id = wp_check_post_lock( $post->ID );

		if ( ! empty( $user_id ) ) {
			$user = get_userdata( $user_id );

			if ( ! empty( $user ) ) {
				$tags['message'][] = [
					'text' => sprintf(
						/* translators: 1 is a user name. */
						__( '%s is currently editing this post.', 'sf-adminbar-tools' ),
						$user->display_name
					),
				];
			}
		}

		$this->render_template( 'admin-notice', $tags );
	}

	/**
	 * Makes sure the user is not set as the user who locks the post edition: we'll filter the '_edit_lock' meta value when it's updated.
	 *
	 * @since  4.0
	 *
	 * @param  null|bool $check      Whether to allow updating metadata for the given type.
	 * @param  int       $object_id  ID of the object metadata is for.
	 * @param  string    $meta_key   Metadata key.
	 * @param  mixed     $meta_value Metadata value. Must be serializable if non-scalar.
	 * @return null|bool
	 */
	public function dont_set_post_lock( $check, $object_id, $meta_key, $meta_value ) {
		if ( '_edit_lock' !== $meta_key || empty( $meta_value ) ) {
			return $check;
		}

		$meta_value      = explode( ':', $meta_value );
		$current_user_id = get_current_user_id();

		// No user ID or user ID is not mine.
		if ( empty( $meta_value[1] ) || $current_user_id !== (int) $meta_value[1] ) {
			return $check;
		}

		return false;
	}
}
