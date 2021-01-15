<?php
/**
 * Class containing the UI to display on the user's profile admin page.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

use ScreenfeedAdminbarTools_Mustache_Engine as Template_Engine;
use Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Options;
use Screenfeed\AdminbarTools\Traits\InputFieldsTrait;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * User's profile UI.
 *
 * @since 4.0.0
 */
class ProfileUI {
	use InputFieldsTrait;

	/**
	 * The page that displays the settings.
	 * This is not an actual page name/slug.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	const SETTINGS_PAGE = 'adminbar-tools';

	/**
	 * Plugin name.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $plugin_name;

	/**
	 * Constructor.
	 *
	 * @since  4.0
	 *
	 * @param  string          $plugin_name Plugin name.
	 * @param  Options         $options     An instance of Options.
	 * @param  Template_Engine $templates   Instance of the template engine.
	 * @return void
	 */
	public function __construct( $plugin_name, $options, $templates ) {
		$this->plugin_name = $plugin_name;
		$this->options     = $options;
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
		add_action( 'load-profile.php', [ $this, 'add_settings_fields' ] );
		add_action( 'show_user_profile', [ $this, 'show_user_fields' ], 11 );
		add_action( 'personal_options_update', [ $this, 'update_user_options' ] );
	}

	/**
	 * Adds setting sections and fields in the user's profile page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function add_settings_fields() {
		$section = 'sfabt';

		add_settings_section( $section, $this->plugin_name, '__return_false', static::SETTINGS_PAGE );

		// Admins list.
		add_settings_field( 'admins', __( "Who's gonna use this plugin?", 'sf-adminbar-tools' ), [ $this, 'admins_field' ], static::SETTINGS_PAGE, $section );

		// Kill Heartbeat.
		add_settings_field(
			'no-autosave',
			__( 'Console spamming', 'sf-adminbar-tools' ),
			[ $this, 'checkboxes_field' ],
			static::SETTINGS_PAGE,
			$section,
			[
				'option'      => 'sfabt-no-autosave',
				'choices'     => [
					1 => __( 'Disable posts autosave, authentication check, and everything related to Heartbeat (for you only).', 'sf-adminbar-tools' ),
				],
				'value'       => (int) get_user_meta( get_current_user_id(), 'sfabt-no-autosave', true ),
				'description' => __( "When you're on a post edit screen, WordPress keeps a track of your current status very frequently with ajax calls. This can be boring if you're working in your JavaScript console, so you can disable it here.", 'sf-adminbar-tools' ),
			]
		);

		/**
		 * Fires after adding setting sections and fields, allowing to add more of them.
		 */
		do_action( 'sfabt_settings' );
	}

	/**
	 * Prints the setting sections and fields in the user's profile page.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function show_user_fields() {
		do_settings_sections( static::SETTINGS_PAGE );
	}

	/**
	 * Prints the coworkers list field.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function admins_field() {
		$wp_roles = get_global( 'wp_roles' );

		// Get roles.
		$roles         = [];
		$user_capacity = sfabt_get_user_capacity();

		if ( isset( $wp_roles->role_objects[ $user_capacity ] ) ) {
			$roles[] = $user_capacity;
		} else {
			foreach ( $wp_roles->role_objects as $role => $object ) {
				if ( ! empty( $object->capabilities[ $user_capacity ] ) ) {
					$roles[] = $role;
				}
			}
		}

		if ( empty( $roles ) ) {
			$this->print_template(
				'paragraph',
				[
					'text' => sprintf(
						/* translators: 1 is a user capacity. */
						__( 'ERROR: could not find roles with the capability "%s".', 'sf-adminbar-tools' ),
						$user_capacity
					),
				]
			);
			return;
		}

		// Display users ordered by role.
		$inputs    = [];
		$user_id   = get_current_user_id();
		$coworkers = $this->options->get( 'coworkers' );
		$name_attr = sprintf( '%s[coworkers][]', esc_attr( $this->options->get_storage()->get_full_name() ) );

		foreach ( $roles as $role ) {
			$users = get_users( [ 'role' => $role ] );

			if ( empty( $users ) ) {
				continue;
			}

			$role_block = [
				'description_before' => [],
				'choices'            => [],
			];

			if ( isset( $wp_roles->role_names[ $role ] ) ) {
				$role_block['description_before'][] = [
					'text' => translate_user_role( $wp_roles->role_names[ $role ] ),
				];
			} else {
				$role_block['description_before'][] = [
					'text' => $role,
				];
			}

			foreach ( $users as $user ) {
				$avatar = get_avatar( $user->ID, 16, '' );
				$label  = $user_id === $user->ID ? __( 'Me', 'sf-adminbar-tools' ) : $user->display_name;

				$role_block['choices'][] = [
					'name'  => $name_attr,
					'value' => (int) $user->ID,
					'atts'  => checked( isset( $coworkers[ $user->ID ] ), true, false ),
					'label' => sprintf( '%s %s', $avatar, esc_html( $label ) ),
				];
			}

			$inputs[] = $role_block;
		}//end foreach

		if ( empty( $inputs ) ) {
			$this->print_template(
				'paragraph',
				[
					'text' => sprintf(
						/* translators: 1 is a user capacity. */
						__( 'ERROR: could not find users with the capability "%s".', 'sf-adminbar-tools' ),
						$user_capacity
					),
				]
			);
			return;
		}

		$this->print_template(
			'checkbox-list',
			[
				'multiple?' => count( $inputs ) > 1,
				'list'      => $inputs,
			]
		);
	}

	/**
	 * Updates user options on form submit.
	 *
	 * @since 4.0.0
	 *
	 * @param  int $user_id The user ID.
	 * @return void
	 */
	public function update_user_options( $user_id ) {
		$options = filter_input( INPUT_POST, $this->options->get_storage()->get_full_name(), FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		if ( empty( $options ) || empty( $user_id ) || ! get_constant( 'IS_PROFILE_PAGE' ) ) {
			return;
		}

		$nonce_value = filter_input( INPUT_POST, '_wpnonce' );

		if ( empty( $nonce_value ) || ! wp_verify_nonce( $nonce_value, 'update-user_' . $user_id ) ) {
			return;
		}

		$this->update_user_metas( $user_id, $options );

		$this->options->set( $options );
	}

	/**
	 * Updates user options on form submit.
	 *
	 * @since 4.0.0
	 *
	 * @param  int          $user_id The user ID.
	 * @param  array<mixed> $options The options sent.
	 * @return void
	 */
	private function update_user_metas( $user_id, $options ) {
		/**
		 * Filters the options to use in `filter_var_array()` for custom user metas sent on form submit.
		 *
		 * @see https://www.php.net/manual/en/function.filter-var-array.php
		 *
		 * @param array<array|int> $filters Options to use in `filter_var_array()`. Array keys are the user meta name, array values are either a filter type, or an array optionally specifying the filter, flags and options.
		 */
		$filters = apply_filters( 'sfabt_saved_user_metas_filters', [] );
		$filters = array_filter(
			(array) $filters,
			function ( $value, $key ) {
				if ( ! is_string( $key ) ) {
					return false;
				}

				$trim_key = trim( $key );

				if ( $trim_key !== $key || empty( $trim_key ) ) {
					return false;
				}

				return is_array( $value ) || is_int( $value );
			},
			ARRAY_FILTER_USE_BOTH
		);
		$filters = array_merge(
			$filters,
			[
				'sfabt-no-autosave' => [
					'filter'  => FILTER_VALIDATE_INT,
					'options' => [ 'min_range' => 1 ],
				],
			]
		);
		$options = filter_var_array( $options, $filters );

		if ( ! is_array( $options ) ) {
			return;
		}

		// Save user metas.
		foreach ( $filters as $meta_name => $filter ) {
			if ( isset( $options[ $meta_name ] ) ) {
				update_user_meta( $user_id, $meta_name, 1 );
			} else {
				delete_user_meta( $user_id, $meta_name );
			}
		}
	}
}
