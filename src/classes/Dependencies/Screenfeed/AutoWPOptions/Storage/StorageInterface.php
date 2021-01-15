<?php
/**
 * Interface to use to define an options storage.
 *
 * @package Screenfeed/autowpoptions
 */

namespace Screenfeed\AdminbarTools\Dependencies\Screenfeed\AutoWPOptions\Storage;

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

/**
 * Interface to use to define an options storage.
 *
 * @since 1.0.0
 */
interface StorageInterface {

	/**
	 * Returns the type of the storage, like `wp_option`, `file`, etc.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_type();

	/**
	 * Returns the "name" of the option that stores the settings.
	 * Depending on the storage type, it can be an option name, a file path, etc.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_full_name();

	/**
	 * Returns the network ID of the option.
	 *
	 * @since 1.0.0
	 *
	 * @return int
	 */
	public function get_network_id();

	/**
	 * Tells if the option is a network option.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public function is_network_option();

	/**
	 * Returns the value of all options.
	 *
	 * @since 1.0.0
	 *
	 * @return array<mixed>|false The options. False if not set yet. An empty array if invalid.
	 */
	public function get();

	/**
	 * Updates the options.
	 *
	 * @since 1.0.0
	 *
	 * @param array<mixed> $values An array of option name / option value pairs.
	 *
	 * @return bool True if the value was updated, false otherwise.
	 */
	public function set( array $values );

	/**
	 * Deletes all options.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if the option was deleted, false otherwise.
	 */
	public function delete();
}
