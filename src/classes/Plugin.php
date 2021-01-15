<?php
/**
 * Plugin's main class.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools;

use Exception;
use TypeError;
use ScreenfeedAdminbarTools_Mustache_Engine as Mustache_Engine;
use ScreenfeedAdminbarTools_Mustache_Loader_ArrayLoader as Mustache_Loader_ArrayLoader;
use ScreenfeedAdminbarTools_Mustache_Loader_CascadingLoader as Mustache_Loader_CascadingLoader;
use ScreenfeedAdminbarTools_Mustache_Loader_FilesystemLoader as Mustache_Loader_FilesystemLoader;
use Screenfeed\AdminbarTools\Dependencies\League\Container\Container;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Plugin's main class.
 *
 * @since 4.0.0
 */
final class Plugin {

	/**
	 * Tells is the instanciation went fine.
	 *
	 * @var   bool
	 * @since 4.0.0
	 */
	private $instance_ok = true;

	/**
	 * Arguments passed to the constructor.
	 *
	 * @var   array<string> {
	 *     @type string $plugin_file Absolute path to the plugin.
	 *     @type string $plugin_name Plugin name.
	 * }
	 * @since 4.0.0
	 */
	private $plugin_args;

	/**
	 * Instance of Container class.
	 *
	 * @var   Container
	 * @since 4.0.0
	 */
	private $container;

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<string> $plugin_args {
	 *     An array of arguments.
	 *
	 *     @type string $plugin_file Absolute path to the plugin.
	 *     @type string $plugin_name Plugin name.
	 * }
	 * @return void
	 */
	public function __construct( $plugin_args ) {
		if ( ! is_array( $plugin_args ) || ! isset( $plugin_args['plugin_file'] ) || ! is_string( $plugin_args['plugin_file'] ) ) {
			$this->instance_ok = false;
			return;
		}

		$this->plugin_args = $plugin_args;
	}

	/**
	 * Allows to create an instance with a static method.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<string> $plugin_args {
	 *     An array of arguments.
	 *
	 *     @type string $plugin_file Absolute path to the plugin.
	 *     @type string $plugin_name Plugin name.
	 * }
	 * @return self
	 */
	public static function construct( $plugin_args ) {
		return new self( $plugin_args );
	}

	/**
	 * Initializes the plugin.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		if ( ! $this->instance_ok ) {
			return;
		}

		$this->load();

		// Some hooks.
		add_action( 'init', [ $this, 'init_context' ], 1 );
	}

	/**
	 * Initializes the plugin after WP is loaded.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init_context() {
		if ( ! $this->instance_ok ) {
			return;
		}

		$pagenow = get_global( 'pagenow' );

		// Check we have at least one coworker. If not, add the current user if eligible.
		$coworkers = $this->container->get( 'coworkers' );
		$user      = $this->container->get( 'current_user' );

		if ( ! $coworkers->have_eligible() && $user->is_eligible() && ! $user->is_coworker() ) {
			$user->register_as_coworker();
		}

		// Stop here for non-coworkers.
		if ( ! $user->is_coworker() ) {
			return;
		}

		$show = $this->container->get( 'environment_tools' )->is_admin_bar_showing();

		if ( $show ) {
			$this->container->get( 'assets' )->init();
			$this->container->get( 'display_base_items_nodes' )->init();
		}

		if ( is_admin() ) {
			// Admin area.
			$this->container->get( 'admin_ui' )->init();
			$this->container->get( 'disable_wp_features' )->init();

			if ( $show ) {
				$this->container->get( 'display_admin_hooks_nodes' )->init();
				$this->container->get( 'display_pagenow_nodes' )->init();
				$this->container->get( 'display_current_screen_nodes' )->init();
			}

			if ( 'profile.php' === $pagenow && get_constant( 'IS_PROFILE_PAGE' ) ) {
				// User's profile.
				$this->container->get( 'profile_ui' )->init();
			}
		} elseif ( $show ) {
			// Frontend.
			$this->container->get( 'display_templates_nodes' )->init();
		}

		if ( $show ) {
			$this->container->get( 'display_php_memory_nodes' )->init();
			$this->container->get( 'display_debug_nodes' )->init();
			$this->container->get( 'display_var_nodes' )->init();
		}

		sfabt_load_translations();

		/**
		 * Fires when SFABT is fully loaded.
		 *
		 * @since 4.0.0
		 *
		 * @param Plugin $plugin Instance of this class.
		 */
		do_action( 'sfabt_loaded', $this );
	}

	/**
	 * Includes plugin files and fills in the container.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	private function load() {
		// Autoload.
		$plugin_path = trailingslashit( plugin_dir_path( $this->plugin_args['plugin_file'] ) );

		if ( file_exists( $plugin_path . 'vendor/autoload.php' ) ) {
			require_once $plugin_path . 'vendor/autoload.php';
		}

		// DI container.
		$this->container = new Container();

		$container = $this->container;
		add_filter(
			'sfabt_container',
			function () use ( $container ) {
				return $container;
			}
		);

		// Store some values.
		$this->container->add( 'plugin_path', $plugin_path );
		$this->container->add( 'plugin_url', trailingslashit( plugin_dir_url( $this->plugin_args['plugin_file'] ) ) );
		$this->container->add( 'plugin_basename', plugin_basename( $this->plugin_args['plugin_file'] ) );
		$this->container->add( 'plugin_name', $this->plugin_args['plugin_name'] );

		// Require function files.
		$functions_path = $plugin_path . 'src/functions/';

		require_once $functions_path . 'api.php';
		require_once $functions_path . 'constants.php';
		require_once $functions_path . 'debug.php';
		require_once $functions_path . 'globals.php';

		// Register classes.
		$this->container->share( 'environment_tools', 'Screenfeed\AdminbarTools\EnvironmentTools' );
		$this->container->share(
			'templates',
			function () use ( $plugin_path ) {
				$loaders = [];

				/**
				 * Allows to add custom folders to look for templates.
				 *
				 * @since 4.0.0
				 *
				 * @param array<string> $template_folder_paths A list of paths to folders containing templates.
				 */
				$template_folder_paths = (array) apply_filters( 'sfabt_template_folder_paths', [] );

				if ( ! empty( $template_folder_paths ) ) {
					$template_folder_paths = array_filter( $template_folder_paths, 'is_string' );
					$template_folder_paths = array_map( 'trailingslashit', $template_folder_paths );
					$template_folder_paths = array_filter( $template_folder_paths, [ $this, 'file_exists' ] );
					$template_folder_paths = array_filter( $template_folder_paths );

					if ( ! empty( $template_folder_paths ) ) {
						foreach ( $template_folder_paths as $template_folder_path ) {
							$loaders[] = new Mustache_Loader_FilesystemLoader( $template_folder_path );
						}
					}
				}

				$loaders[] = new Mustache_Loader_ArrayLoader(
					// phpcs:disable WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow, WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
					[
						'adminbar/admin-hook-count'    => '{{ text }}',
						'adminbar/admin-hooks'         => '{{ text }}',
						'adminbar/admin-hooks-context' => '{{ text }}',
						'adminbar/current-screen'      => '{{ text }}',
						'adminbar/current-screen-prop' => '{{ text }}',
						'adminbar/debug'               => '{{ text }}',
						'adminbar/debug-child'         => '{{ text }}',
						'adminbar/main'                => '{{ text }}',
						'adminbar/memory-child'        => '{{ text }}',
						'adminbar/memory-used'         => '{{ text }}',
						'adminbar/php-version'         => '{{ text }}',
						'adminbar/varnow'              => '{{ text }}',
						'adminbar/varnow-child'        => '{{ text }}',
						'adminbar/wp-version'         => '{{ text }}',
					]
					// phpcs:enable WordPress.Arrays.MultipleStatementAlignment.LongIndexSpaceBeforeDoubleArrow, WordPress.Arrays.MultipleStatementAlignment.DoubleArrowNotAligned
				);
				$loaders[] = new Mustache_Loader_FilesystemLoader( $plugin_path . 'views' );

				return new Mustache_Engine(
					[
						'loader' => new Mustache_Loader_CascadingLoader( $loaders ),
						'escape' => function ( $value ) {
							return esc_html( $value );
						},
					]
				);
			}
		);

		$environment_tools = $this->container->get( 'environment_tools' );
		$templates         = $this->container->get( 'templates' );
		$plugin_basename   = $this->container->get( 'plugin_basename' );
		$plugin_name       = $this->container->get( 'plugin_name' );

		$this->container->share( 'options_sanitization', 'Screenfeed\AdminbarTools\Options\OptionSanitization' )
			->withArgument( get_constant( 'SFABT_VERSION' ) );

		$options_sanitization = $this->container->get( 'options_sanitization' );
		$prefix               = $options_sanitization->get_prefix();
		$identifier           = $options_sanitization->get_identifier();

		$this->container->share( 'options_storage', 'Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Storage\WpOption' )
			->withArgument( "{$prefix}_{$identifier}" ) // The option name is `sfabt_settings`.
			->withArgument( $environment_tools->is_plugin_active_for_network( $plugin_basename ) );
		$this->container->share( 'options', 'Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Options' )
			->withArgument( $this->container->get( 'options_storage' ) )
			->withArgument( $options_sanitization );

		$options = $this->container->get( 'options' );

		$this->container->share( 'coworkers', 'Screenfeed\AdminbarTools\Coworkers' )
			->withArgument( $options );
		$this->container->share( 'current_user', 'Screenfeed\AdminbarTools\User' )
			->withArgument( null )
			->withArgument( $this->container->get( 'coworkers' ) );

		$this->container->share( 'disable_wp_features', 'Screenfeed\AdminbarTools\DisableWPFeatures' )
			->withArgument( $templates );

		$nodes = [
			'base_items'     => 'BaseItems',
			'admin_hooks'    => 'AdminHooks',
			'current_screen' => 'CurrentScreen',
			'pagenow'        => 'Pagenow',
			'php_memory'     => 'PhpMemory',
			'debug'          => 'Debug',
			'var'            => 'SomeVar',
			'templates'      => 'Templates',
		];
		foreach ( $nodes as $name => $namespace ) {
			$this->container->share( "display_{$name}_data", "Screenfeed\AdminbarTools\DisplayData\\{$namespace}\Data" );
			$this->container->share( "display_{$name}_nodes", "Screenfeed\AdminbarTools\DisplayData\\{$namespace}\NodesUI" )
				->withArgument( $this->container->get( "display_{$name}_data" ) )
				->withArgument( $templates );
		}

		$this->container->share( 'assets', 'Screenfeed\AdminbarTools\Assets' )
			->withArgument( $this->container->get( 'plugin_url' ) );
		$this->container->share( 'admin_ui', 'Screenfeed\AdminbarTools\AdminUI' )
			->withArgument( $templates )
			->withArgument( $plugin_basename )
			->withArgument( $plugin_path )
			->withArgument( $plugin_name );
		$this->container->share( 'profile_ui', 'Screenfeed\AdminbarTools\ProfileUI' )
			->withArgument( $plugin_name )
			->withArgument( $options )
			->withArgument( $templates );

		/**
		 * Fires when all classes have been registered into the container.
		 *
		 * @since 4.0.0
		 *
		 * @param Plugin $plugin Instance of this class.
		 */
		do_action( 'sfabt_registered', $this );
	}

	/**
	 * Checks if a file or directory exists.
	 *
	 * @since  4.0.0
	 *
	 * @param  string $file Path to file or directory.
	 * @return bool         Whether $file exists or not.
	 */
	private function file_exists( $file ) {
		try {
			return file_exists( $file );
		} catch ( Exception $e ) {
			return false;
		}
	}
}
