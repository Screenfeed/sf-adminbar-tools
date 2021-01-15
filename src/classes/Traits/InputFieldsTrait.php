<?php
/**
 * Trait that contains basic input fields for the setting pages.
 * php version 5.6
 *
 * @package Screenfeed/sf-adminbar-tools
 */

namespace Screenfeed\AdminbarTools\Traits;

use Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Options;
use Screenfeed\AdminbarTools\Traits\TemplateEngineTrait;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Trait that contains basic input fields for the setting pages.
 *
 * @since 4.0.0
 */
trait InputFieldsTrait {
	use TemplateEngineTrait;

	/**
	 * An instance of Options.
	 *
	 * @var   Options
	 * @since 4.0.0
	 */
	private $options;

	/**
	 * Prints a list of checkboxes.
	 *
	 * @since 4.0.0
	 *
	 * @param  array<mixed> $args {
	 *     A list of arguments.
	 *
	 *     @type string        $option      The option name.
	 *     @type array<string> $choices     A list of choices: 1 value = 1 checkbox. Array keys are checkbox values, array values are checkbox labels.
	 *     @type mixed         $value       The current value.
	 *     @type string        $description Optional. A description text to print after the list of checkboxes.
	 * }
	 * @return void;
	 */
	public function checkboxes_field( $args ) {
		$multiple  = count( $args['choices'] ) > 1;
		$name_attr = sprintf( '%s[%s]%s', $this->options->get_storage()->get_full_name(), $args['option'], $multiple ? '[]' : '' );
		$name_attr = esc_attr( $name_attr );
		$list      = [
			'choices'           => [],
			'description_after' => [],
		];

		if ( ! empty( $args['description'] ) ) {
			$list['description_after'][] = [
				'text' => $args['description'],
			];
		}

		foreach ( $args['choices'] as $value => $label ) {
			$list['choices'][] = [
				'name'  => $name_attr,
				'value' => esc_attr( $value ),
				'atts'  => checked( $args['value'], $value, false ),
				'label' => esc_html( $label ),
			];
		}

		$this->print_template(
			'checkbox-list',
			[
				'multiple?' => $multiple,
				'list'      => $list,
			]
		);
	}
}
