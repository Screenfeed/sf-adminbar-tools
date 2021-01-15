<?php
/**
 * Class to display the template and template parts used on frontend in the adminbar.
 * This class prints the UI.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\Templates;

use WP_Admin_Bar;
use Screenfeed\AdminbarTools\DisplayData\AbstractUI;
use function Screenfeed\AdminbarTools\get_constant;
use function Screenfeed\AdminbarTools\get_global;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class to display the template and template parts used on frontend in the adminbar.
 *
 * @since 4.0.0
 */
class NodesUI extends AbstractUI {

	/**
	 * A list of paths to WP directories, like the plugins directory, indexed by their type.
	 *
	 * @var   array<string>
	 * @since 4.0.0
	 */
	private $wp_directories = [];

	/**
	 * A list of symlinked paths, indexed by their local path.
	 *
	 * @var   array<string>|null
	 * @since 4.0.0
	 */
	private $wp_symlinked_paths = null;

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->data->init();

		// The adminbar must be printed in the footer to be able to catch all template parts.
		remove_action( 'wp_body_open', 'wp_admin_bar_render', 0 );

		add_action( 'sfabt_add_nodes_inside', [ $this, 'add_nodes' ], 1 );
		add_action( 'sfabt_add_nodes_inside', [ $this, 'add_template_part_nodes' ], 1 );
	}

	/**
	 * Adds a node displaying the current template in use.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 * @return void
	 */
	public function add_nodes( $wp_admin_bar ) {
		$template = get_global( 'template' );

		if ( empty( $template ) ) {
			return;
		}

		$path_descr = $this->get_template_path_description( $template );

		$wp_admin_bar->add_node(
			[
				'parent' => 'sfabt-main',
				'id'     => 'sfabt-template',
				'title'  => $this->render_template(
					'adminbar/template',
					[
						'relative_path'     => $path_descr['relative_path'],
						'path_description?' => ! empty( $path_descr['description'] ) ? [ 'text' => $path_descr['description'] ] : false,
					]
				),
				'meta'   => [
					'title' => __( 'Template', 'sf-adminbar-tools' ),
				],
			]
		);
	}

	/**
	 * Adds nodes displaying the template parts in use.
	 *
	 * @since 4.0.0
	 *
	 * @param  WP_Admin_Bar $wp_admin_bar WP_Admin_Bar instance, passed by reference.
	 * @return void
	 */
	public function add_template_part_nodes( $wp_admin_bar ) {
		if ( ! is_object( $wp_admin_bar->get_node( 'sfabt-template' ) ) ) {
			return;
		}

		$i           = 0;
		$last_data   = [];
		$last_count  = 1;
		$all_located = $this->data->get_data();

		foreach ( $all_located as $index => $template_data ) {
			if ( isset( $all_located[ $index + 1 ] ) && $all_located[ $index + 1 ] === $template_data ) {
				// If not the last item of the list, and the next item is identical to this one.
				$last_data = $template_data;
				++$last_count;
				continue;
			}

			// Passed this point, the item will be printed.
			$counter = false;

			if ( $template_data === $last_data ) {
				// This is the last one of a series of identical items.
				$counter = [ 'total_count' => $last_count ];
			}

			$last_data  = [];
			$last_count = 1;
			$path_descr = $this->get_template_path_description( $template_data['path'] );

			if ( ! empty( $template_data['args'] ) ) {
				$template_args = [
					'label' => esc_attr__( 'Template arguments:', 'sf-adminbar-tools' ),
					'code'  => esc_attr( call_user_func( 'Screenfeed\AdminbarTools\var_export', $template_data['args'], true ) ),
				];
			} else {
				$template_args = false;
			}

			$wp_admin_bar->add_node(
				[
					'parent' => 'sfabt-template',
					'id'     => "sfabt-template-part-{$i}",
					'title'  => $this->render_template(
						'adminbar/template',
						[
							'relative_path'     => $path_descr['relative_path'],
							'path_description?' => ! empty( $path_descr['description'] ) ? [ 'text' => $path_descr['description'] ] : false,
							'counter?'          => $counter,
							'template_args?'    => $template_args,
						]
					),
					'meta'   => [
						'title' => __( 'Template part', 'sf-adminbar-tools' ),
					],
				]
			);
			++$i;
		}//end foreach
	}

	/** ----------------------------------------------------------------------------------------- */
	/** TOOLS =================================================================================== */
	/** ----------------------------------------------------------------------------------------- */

	/**
	 * "Translates" a file path into what it targets: theme, plugin, MU plugin, etc.
	 * It also says if the path is symlinked.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $template_path A template path.
	 * @param  bool   $symlinked     True when the targetted file is symlinked. False otherwise.
	 * @return array<string>         {
	 *     @type string $relative_path Path relative to $template_path.
	 *     @type string $description   Descrption of the path.
	 * }
	 */
	private function get_template_path_description( $template_path, $symlinked = false ) {
		$template_path = wp_normalize_path( $template_path );

		if ( is_child_theme() ) {
			// Child theme.
			if ( 0 === strpos( $template_path, $this->get_wp_directory( 'stylesheet' ) ) ) {
				return $this->get_template_path_text( $this->get_wp_directory( 'stylesheet' ), $template_path, __( 'child theme', 'sf-adminbar-tools' ), 'theme', $symlinked );
			}

			if ( 0 === strpos( $template_path, $this->get_wp_directory( 'template' ) ) ) {
				return $this->get_template_path_text( $this->get_wp_directory( 'template' ), $template_path, __( 'parent theme', 'sf-adminbar-tools' ), 'theme', $symlinked );
			}
		} elseif ( 0 === strpos( $template_path, $this->get_wp_directory( 'stylesheet' ) ) ) {
			// Theme.
			return $this->get_template_path_text( $this->get_wp_directory( 'stylesheet' ), $template_path, __( 'theme', 'sf-adminbar-tools' ), 'theme', $symlinked );
		}

		if ( 0 === strpos( $template_path, $this->get_wp_directory( 'theme_compat' ) ) ) {
			// Theme compat.
			return $this->get_template_path_text( $this->get_wp_directory( 'theme_compat' ), $template_path, __( 'theme compat', 'sf-adminbar-tools' ), 'theme', $symlinked );
		}

		if ( 0 === strpos( $template_path, $this->get_wp_directory( 'plugins' ) ) ) {
			// Plugin.
			return $this->get_template_path_text( $this->get_wp_directory( 'plugins' ), $template_path, __( 'plugin', 'sf-adminbar-tools' ), 'plugin', $symlinked );
		}

		if ( 0 === strpos( $template_path, $this->get_wp_directory( 'mu_plugins' ) ) ) {
			// MU Plugin.
			return $this->get_template_path_text( $this->get_wp_directory( 'mu_plugins' ), $template_path, __( 'Must-Use plugin', 'sf-adminbar-tools' ), 'plugin', $symlinked );
		}

		if ( ! $symlinked ) {
			// Maybe symlinked.
			foreach ( $this->get_wp_symlinked_paths() as $local_path => $symlink_path ) {
				if ( 0 === strpos( $template_path, $symlink_path ) ) {
					return $this->get_template_path_description( str_replace( $symlink_path, $local_path, $template_path ), true );
				}
			}
		}

		return [
			'relative_path' => str_replace( $this->get_wp_directory( 'abspath' ), '', $template_path ),
			'description'   => '',
		];
	}

	/**
	 * "Translates" a file path into what it targets: theme, plugin, MU plugin, etc.
	 * It also says if the path is symlinked.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $template_dir  Directory containing the template. Not necessarily direct directory.
	 * @param  string $template_path A template path.
	 * @param  string $description   The descriptive text.
	 * @param  string $type          'theme' or 'plugin'.
	 * @param  bool   $symlinked     True when the targetted file is symlinked. False otherwise.
	 * @return array<string>         {
	 *     @type string $relative_path Path relative to $template_path.
	 *     @type string $description   Descrption of the path.
	 * }
	 */
	private function get_template_path_text( $template_dir, $template_path, $description, $type, $symlinked ) {
		$return = [
			'relative_path' => str_replace( $template_dir, '', $template_path ),
			'description'   => $description,
		];

		if ( ! $symlinked ) {
			return $return;
		}

		if ( 'theme' === $type ) {
			/* translators: 1 is "theme", "child theme", etc. */
			$return['description'] = sprintf( _x( '%s, symlinked', 'theme', 'sf-adminbar-tools' ), $description );
		} else {
			/* translators: 1 is "plugin" or "Must-Use plugin". */
			$return['description'] = sprintf( _x( '%s, symlinked', 'plugin', 'sf-adminbar-tools' ), $description );
		}

		return $return;
	}

	/**
	 * Returns the path to a WP directory, like the plugins directory.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $directory_type The type of directory. Possible values are 'stylesheet', 'template', 'theme_compat', 'plugins', 'mu_plugins', 'abspath'.
	 * @return string
	 */
	private function get_wp_directory( $directory_type ) {
		if ( isset( $this->wp_directories[ $directory_type ] ) ) {
			return $this->wp_directories[ $directory_type ];
		}

		switch ( $directory_type ) {
			case 'stylesheet':
				$this->wp_directories[ $directory_type ] = wp_normalize_path( trailingslashit( get_stylesheet_directory() ) );
				return $this->wp_directories[ $directory_type ];

			case 'template':
				$this->wp_directories[ $directory_type ] = wp_normalize_path( trailingslashit( get_template_directory() ) );
				return $this->wp_directories[ $directory_type ];

			case 'theme_compat':
				$this->wp_directories[ $directory_type ] = wp_normalize_path( get_constant( 'ABSPATH' ) . get_constant( 'WPINC' ) . '/theme-compat/' );
				return $this->wp_directories[ $directory_type ];

			case 'plugins':
				$this->wp_directories[ $directory_type ] = wp_normalize_path( get_constant( 'WP_PLUGIN_DIR' ) . '/' );
				return $this->wp_directories[ $directory_type ];

			case 'mu_plugins':
				$this->wp_directories[ $directory_type ] = wp_normalize_path( get_constant( 'WPMU_PLUGIN_DIR' ) . '/' );
				return $this->wp_directories[ $directory_type ];

			case 'abspath':
				$this->wp_directories[ $directory_type ] = wp_normalize_path( get_constant( 'ABSPATH' ) );
				return $this->wp_directories[ $directory_type ];

			default:
				return '';
		}//end switch
	}

	/**
	 * Returns the list of symlinked paths, indexed by their local path.
	 *
	 * @since 4.0.0
	 *
	 * @return array<string>
	 */
	private function get_wp_symlinked_paths() {
		$wp_plugin_paths = get_global( 'wp_plugin_paths' );

		if ( is_array( $this->wp_symlinked_paths ) ) {
			return $this->wp_symlinked_paths;
		}

		$this->wp_symlinked_paths = [];

		foreach ( $wp_plugin_paths as $local_path => $symlink_path ) {
			$this->wp_symlinked_paths[ wp_normalize_path( $local_path ) ] = wp_normalize_path( $symlink_path );
		}

		return $this->wp_symlinked_paths;
	}
}
