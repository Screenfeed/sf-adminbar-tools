<?php
/**
 * Class to display the template and template parts used on frontend in the adminbar.
 * This class provides the data to display.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData\Templates;

use Screenfeed\AdminbarTools\DisplayData\DataInterface;
use function Screenfeed\AdminbarTools\get_constant;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Class that provides the list of the template parts to display in the adminbar.
 * This stores all the template parts used in frontend and provide a way to retrieve this list.
 *
 * @since 4.0.0
 */
class Data implements DataInterface {

	/**
	 * Array storing the template parts.
	 *
	 * @var   array<mixed> {
	 *     @type string $path Path to the template part.
	 *     @type array  $args Array of arguments passed to the template part (WP 5.5.0).
	 * }
	 * @since 4.0.0
	 */
	private $template_parts = [];

	/**
	 * Some paths related to WP.
	 *
	 * @var   array<string> {
	 *     @type string $wp_includes.
	 *     @type string $stylesheet.
	 *     @type string $template.
	 * }
	 * @since 4.0.0
	 */
	private $wp_paths = [];

	/**
	 * Template part used for comments.
	 *
	 * @var   string
	 * @since 4.0.0
	 */
	private $comments_template_file = '';

	/**
	 * Launches hooks.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	public function init() {
		$this->wp_paths = [
			'wp_includes' => trailingslashit( wp_normalize_path( get_constant( 'ABSPATH' ) . get_constant( 'WPINC' ) ) ),
			'stylesheet'  => trailingslashit( wp_normalize_path( get_constant( 'STYLESHEETPATH' ) ) ),
			'template'    => trailingslashit( wp_normalize_path( get_constant( 'TEMPLATEPATH' ) ) ),
		];

		add_action( 'all', [ $this, 'store_template_parts' ], 10, 4 );
		add_filter( 'comments_template', [ $this, 'store_comments_template' ], PHP_INT_MAX );
	}

	/**
	 * Returns the list of template parts used on the page.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $args Optional arguments not in use here.
	 * @return array<array<mixed>> List of template part names.
	 */
	public function get_data( $args = [] ) {
		return $this->template_parts;
	}

	/**
	 * Filters all hooks to get the template parts.
	 *
	 * @since 4.0.0
	 *
	 * @param  string       $tag  The name of the hook.
	 * @param  string       $slug The slug name for the generic template.
	 * @param  string|null  $name The name of the specialized template.
	 * @param  array<mixed> $args Additional arguments passed to the template. Since WP 5.5.0.
	 * @return void
	 */
	public function store_template_parts( $tag, $slug = '', $name = null, $args = [] ) {
		$this->comments_template_file = '';

		$other_actions = [
			'get_header'  => 'header',
			'get_sidebar' => 'sidebar',
			'get_footer'  => 'footer',
		];

		if ( isset( $other_actions[ $tag ] ) ) {
			$name = $slug;
			$slug = $other_actions[ $tag ];
		} elseif ( 'wc_get_template_part' === $tag || 'wc_get_template' === $tag ) {
			// WooCommerce.
			$this->store_template_part( $slug, $args );
			return;
		} elseif ( 'comments_template' === $tag ) {
			// This info may be useful later in `$this->store_comments_template()`.
			$this->comments_template_file = wp_normalize_path( $slug );
			return;
		} elseif ( 0 !== strpos( $tag, 'get_template_part_' ) ) {
			return;
		}

		if ( empty( $slug ) ) {
			return;
		}

		$templates = [];
		$name      = (string) $name;

		if ( '' !== $name ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";

		$located = locate_template( $templates, false, false );

		if ( ! $located && isset( $other_actions[ $tag ] ) ) {
			$located = $this->wp_paths['wp_includes'] . "theme-compat/{$slug}.php";
		}

		$this->store_template_part( $located, $args );
	}

	/**
	 * Filters the `comments_template` hook to get the comments template part.
	 *
	 * @since 4.0.0
	 *
	 * @param  string $include The path to the theme template file.
	 * @return string
	 */
	public function store_comments_template( $include ) {
		if ( file_exists( $include ) ) {
			// Equals `STYLESHEETPATH . $file` or a filtered custom value.
			$located = $include;
		} else {
			// See `$this->store_template_parts()`.
			$located = $this->comments_template_file;

			if ( empty( $located ) || 0 !== strpos( $located, $this->wp_paths['stylesheet'] ) ) {
				// Should not happen unless the world falls appart.
				$this->comments_template_file = '';
				return $include;
			}

			$located = str_replace( $this->wp_paths['stylesheet'], $this->wp_paths['template'], $located );

			if ( ! file_exists( $located ) ) {
				$located = $this->wp_paths['wp_includes'] . 'theme-compat/comments.php';
			}
		}

		$this->store_template_part( $located );
		$this->comments_template_file = '';

		return $include;
	}

	/**
	 * Stores a template part.
	 *
	 * @since 4.0.0
	 *
	 * @param  string       $located A path to a template part.
	 * @param  array<mixed> $args    Array of arguments passed to the template part (WP 5.5.0).
	 * @return void
	 */
	private function store_template_part( $located, $args = [] ) {
		if ( empty( $located ) || ! is_string( $located ) ) {
			return;
		}

		$args = (array) $args;
		ksort( $args );

		$this->template_parts[] = [
			'path' => $located,
			'args' => $args,
		];
	}
}
