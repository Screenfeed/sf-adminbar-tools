<?php
/**
 * What to do when the plugin is uninstalled.
 * php version 5.2
 *
 * @package Screenfeed/sf-adminbar-tools
 */

defined( 'ABSPATH' ) || exit; // @phpstan-ignore-line

delete_option( 'sfabt_settings' );
delete_site_option( 'sfabt_settings' );
delete_metadata( 'user', 0, 'sfabt-no-autosave', null, true );
