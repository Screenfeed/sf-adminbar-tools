<?php
/**
 * Abstract class to display data to the user.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\DisplayData;

use ScreenfeedAdminbarTools_Mustache_Engine as Template_Engine;
use Screenfeed\AdminbarTools\DisplayData\AdminbarUIInterface;
use Screenfeed\AdminbarTools\DisplayData\UIInterface;
use Screenfeed\AdminbarTools\Traits\TemplateEngineTrait;
use WP_Admin_Bar;
use function Screenfeed\AdminbarTools\get_global;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Abstract class to display data to the user by using the template engine.
 *
 * @since 4.0.0
 */
abstract class AbstractUI implements AdminbarUIInterface, UIInterface {
	use TemplateEngineTrait;

	/**
	 * A Data instance.
	 *
	 * @var   DataInterface
	 * @since 4.0.0
	 */
	protected $data;

	/**
	 * Constructor.
	 *
	 * @since 4.0.0
	 *
	 * @param  DataInterface   $data      A Data instance.
	 * @param  Template_Engine $templates Instance of the template engine.
	 * @return void
	 */
	public function __construct( $data, $templates ) {
		$this->data = $data;
		$this->set_engine( $templates );
	}

	/**
	 * Adds an adminbar node.
	 *
	 * @since  4.0.0
	 *
	 * @param  array<mixed> $node_args    Arguments to create the admin bar node.
	 * @param  string       $template     The id/path to the template. Ex: 'foobar' or 'sub/foobar'.
	 * @param  array<mixed> $context_args Context arguments passed to the template.
	 * @return void
	 */
	protected function add_node( $node_args, $template, $context_args ) {
		$node_args['title'] = $this->render_template( $template, $context_args );

		get_global( 'wp_admin_bar' )->add_node( $node_args );
	}
}
