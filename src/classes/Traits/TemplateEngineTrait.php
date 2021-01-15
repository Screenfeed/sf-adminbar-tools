<?php
/**
 * Trait that simplifies a bit the use of the template engine.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\Traits;

use Closure;
use ScreenfeedAdminbarTools_Mustache_Engine as Template_Engine;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Trait that simplifies a bit the use of the template engine.
 * It also allows to filter everything that is passed to the engine.
 *
 * @since 4.0.0
 */
trait TemplateEngineTrait {

	/**
	 * Instance of the template engine.
	 *
	 * @var   Template_Engine
	 * @since 4.0.0
	 */
	private $engine;

	/**
	 * Sets the engine template.
	 *
	 * @since 4.0.0
	 *
	 * @param  Template_Engine $templates Instance of the template engine.
	 * @return void
	 */
	protected function set_engine( $templates ) {
		$this->engine = $templates;
	}

	/**
	 * Renders a template.
	 *
	 * @since 4.0.0
	 *
	 * @param  string         $template     The id/path to the template. Ex: 'foobar' or 'sub/foobar'.
	 * @param  array<mixed>   $context_args Context arguments passed to the template.
	 * @param  string|Closure $post_filter  Callback to perform after rendering the template.
	 * @return string
	 */
	protected function render_template( $template, $context_args = [], $post_filter = null ) {
		/**
		 * Filters the name (target) of the template being rendered.
		 *
		 * @since 4.0.0
		 *
		 * @param  string         $template     The id/path to the template. Ex: 'foobar' or 'sub/foobar'.
		 * @param  array<mixed>   $context_args Context arguments passed to the template.
		 * @param  string|Closure $post_filter  Callback to perform after rendering the template.
		 */
		$template = (string) apply_filters( 'sfabt_template_name', $template, $context_args, $post_filter );

		if ( '' === $template ) {
			return '';
		}

		/**
		 * Filters the context arguments of the template being rendered.
		 *
		 * @since 4.0.0
		 *
		 * @param  array<mixed>   $context_args Context arguments passed to the template.
		 * @param  string         $template     The id/path to the template. Ex: 'foobar' or 'sub/foobar'.
		 * @param  string|Closure $post_filter  Callback to perform after rendering the template.
		 */
		$context_args = (array) apply_filters( 'sfabt_template_args', $context_args, $template, $post_filter );

		/**
		 * Filters the post-render callback of the template being rendered.
		 *
		 * @since 4.0.0
		 *
		 * @param  string|Closure $post_filter  Callback to perform after rendering the template.
		 * @param  string         $template     The id/path to the template. Ex: 'foobar' or 'sub/foobar'.
		 * @param  array<mixed>   $context_args Context arguments passed to the template.
		 */
		$post_filter = apply_filters( 'sfabt_template_post_filter', $post_filter, $template, $context_args );

		$render = $this->engine->render( $template, $context_args );

		if ( ! empty( $post_filter ) && is_callable( $post_filter ) ) {
			$render = call_user_func( $post_filter, $render );
		}

		/**
		 * Filters the rendering of a template.
		 *
		 * @since 4.0.0
		 *
		 * @param  string         $render      The template rendering.
		 * @param  string         $template     The id/path to the template. Ex: 'foobar' or 'sub/foobar'.
		 * @param  array<mixed>   $context_args Context arguments passed to the template.
		 * @param  string|Closure $post_filter  Callback to perform after rendering the template.
		 */
		return (string) apply_filters( 'sfabt_template_render', $render, $template, $context_args, $post_filter );
	}

	/**
	 * Prints a template.
	 *
	 * @since 4.0.0
	 *
	 * @param  string         $template     The id/path to the template. Ex: 'foobar' or 'sub/foobar'.
	 * @param  array<mixed>   $context_args Context arguments passed to the template.
	 * @param  string|Closure $post_filter  Callback to perform after rendering the template.
	 * @return void
	 */
	protected function print_template( $template, $context_args = [], $post_filter = null ) {
		echo $this->render_template( $template, $context_args, $post_filter ); //phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
}
