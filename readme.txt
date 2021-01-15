=== Admin Bar Tools ===
Contributors: GregLone
Tags: debug, query, development, testing, tests
Requires at least: 4.7
Tested up to: 5.6.0
Stable tag: trunk
License: GPLv3

Adds some small development tools to the admin bar.

== Description ==
The plugin adds a new tab in your admin bar with simple but useful indications and tools.

* Displays the number of queries in your page and the amount of time to generate the page.
* Displays the php memory usage and php memory limits (constants `WP_MEMORY_LIMIT` and `WP_MAX_MEMORY_LIMIT`).
* displays the php version and WP version.
* Displays `WP_DEBUG`, `SCRIPT_DEBUG`, `WP_DEBUG_LOG`, `WP_DEBUG_DISPLAY`, and error reporting values.

**In your site front-end:**

* Lists the template and all template parts used in the current page (template parts added with `get_template_part()`). Compatible with WooCommerce's templates.
* `$wp_query`: this will open a lightbox displaying the content of `$wp_query`. Click the lightbox title to reload the value, click outside the lightbox to close it.

**In your site administration:**

* Admin hooks: lists some oftenly used hooks (like `admin_init`). The indicator to the right of the line tells you how many times the hook has been triggered by a callback. A "P" means the hook has a parameter: hover it for more details. Click a hook (on its text) to auto-select its code, for example: click *admin_init* to select `add_action( 'admin_init', '' );`.
* `$current_screen`: displays the value of 4 properties of this object: `id`, `base`, `parent_base`, `parent_file`.
* `$...now`: displays the value of the well-known variables `$pagenow`, `$typenow`, and `$taxnow`.
* On a user profile page, `$userdata`: : this will open a lightbox displaying the user's data.

You can decide who's gonna use this plugin (go to your profile page for all the settings). This way, the plugin's items won't show up to other users (your client for example).
Also, a new menu item `Code Tester` will appear. There you are able to do some tests with your code.

== Installation ==

1. Extract the plugin folder from the downloaded ZIP file.
2. Upload the `sf-admin-bar-tools` folder to your *wp-content/plugins/* directory.
3. Activate the plugin from the "Plugins" page.

== Frequently Asked Questions ==

None, yet.

== Screenshots ==

1. Admin side: list the most important hooks in the admin area.
2. Admin side: click a hook, you're ready to copy/paste.
3. Front side: see the `WP_Query` object value.
4. Front side: see the template and list all template parts used in the current page.
5. The settings in your profile page.
6. The code tester area.

== Changelog ==

= 4.0.0 =
* 2021/01/15
* The plugin has been totally rewritten. It requires at least php 5.6 and WP 4.7, and is ready for WP 5.6.
* Items' arrangement is a bit more clear, less cryptic.
* Templates: if arguments are passed to the template via `get_template_part()` (new in WP 5.5), a "P" will appear at the right of the row: hovering this "P" will display these arguments.
* Added: the value of the constants `WP_MAX_MEMORY_LIMIT`, `SCRIPT_DEBUG`, `WP_DEBUG_LOG`, and `WP_DEBUG_DISPLAY`. Also the WordPress' version.
* About the lightbox displaying the value of `$wp_query`: this lightbow is now also used to display a user's data on a user's profile page (admin area). This lightbox can be filtered/extended to display anything you want in frontend or admin.
* A new menu item "Code Tester" to test your code rapidly.
* Improvement: this tool being displayed in the admin bar, some values were inaccurate or incomplete (everything happening after the admin bar wasn't taken into account). This has been fixed for the number of requests, page load time, admin hooks, and memory usage.
* Fix: the plugin forces the admin bar to be printed at the bottom of the page on frontend, instead of using the new hook `wp_body_open`. This is done to be able to list all template parts.
* The plugin is now using a template loader that can be filtered to customize the way everything is displayed.

= 3.0.4 =
* 2016/11/27
* Ready for WP 4.7.
* Fixed php warnings related to the new `WP_Hook` class. Thanks Sébastien Serre for alerting me.

= 3.0.3 =
* 2016/04/03
* Ready for WP 4.5.
* Code quality improvements.

= 3.0.2 =
* 2015/11/07
* Bugfix: avoid annoying message caused by `is_embed()` in WP 4.4.0.

= 3.0.1 =
* 2015/06/08
* Bugfix: avoid php notices when no template parts are found.
* Improvement: the "Hide WP SEO" checkbox also removes the fields in taxonomy screens now.
* Removed all unused old files. SVN, I hate you so much.

= 3.0 =
* 2015/03/30
* Two years without any update: it's time to rebuild everything from the ground with unicorns and kittens!
* The main focus of this release is to repair broken things and remove obsolete features. It's a major rewrite.
* New: in front-end, list the template and all template parts used in the current page. Compatible with WooCommerce.
* New: if WP SEO is installed, you can remove all its columns et metaboxes (they bore me).
* New: if WPML is installed, you will have a link to the "hidden tools" (dangerous weapons that will blow up your site if you don't know what you do (　ﾟДﾟ)＜!!).
* Removed: the admin bar can no longer be shrinked.
* Removed: coworking feature. Did somebody use it? It was a big mess for only this "tiny" thing.
* Changed: the settings are in your profile page. Some of them are now user preferences.
* Improved: more hooks listed in the admin area.
* Improved: display the number of times the hooks are hit (for real this time).
* Improved: hook code selection.
* Improved: the "disable auto-save" feature now works with new WordPress releases. It also removes auto-lock, auth-check ("XXX is currently editing this post"), and all the things related to Heartbeat.
* Todo: meh.

= 2.1.1 =
* 2013/01/26
* Bugfix in settings page (a missing BR tag)

= 2.1 =
* 2013/01/26
* New: Auto "subscribe" when the plugin is activated. No need to rush to the settings page after activation now.
* New tool: `pre_print_r()`. It's a kind of improved `print_r()` to use where you need: wrap with a `<pre>` tag, choose how to display it (or not) to other users with 2 parameters.
* New: add your own options in the settings page. See the two action hooks 'sf-abt-settings' and 'sf-abt-preferences'. Now there's a new system to deal with the plugin options, see the 'sf_abt_default_options', 'sf_abt_sanitization_functions' and 'sf_abt_sanitize_settings' filters.
* New section "Personal preferences" in the plugin settings page, with the two following options:
* The cowork tree and statuses are refreshed every 5 minutes and on window focus. Now you can disable this.
* When you're on a post edit screen, WordPress autosave your post every minute. Now you can disable this.
* New: Enable the "All Options" options menu.
* Enhancement: if you use the Debug Bar plugin, its admin bar item has an icon on a small screen now (icon from http://gentleface.com/free_icon_set.html).
* Fix: in rares occasions, the admin submenus were displayed under content.
* Fix: use `wp_get_theme()` only if exists (WP 3.4).
* Fix: check WordPress version.

= 2.0.1 =
* 2012/10/17
* Bugfix in settings page

= 2.0 =
* 2012/10/16 - Major release
* Bugfix: jQuery is now launched correctly in themes where it's not already present.
* Enhancement: the main item is now located at the far right of the admin bar. I think it's more convenient for the "retract" functionality.
* Enhancement: now there's a small indicator for the "Fix/unfix admin menu" functionality.
* Enhancement: the $wp_query lightbox works on a 404 page.
* New tool: cowork.
* New indicators: php memory, php version, WP_DEBUG state, error_reporting level, current front-end template.
* New tool: hooks list in administration.
* Thanks a lot to juliobox for some of the awesome ideas :)

= 1.0.1 =
* 2012/06/16
* Minor CSS fix for WP 3.4: the floated admin menu was partially hidden under the admin bar.

= 1.0 =
* 2012/06/10 - First public release

== Upgrade Notice ==
