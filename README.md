# SF Admin Bar Tools

A WordPress plugin that adds some small development tools to the admin bar.

Requires **php 5.6** and **WordPress 4.7**.

## What this plugin does

The plugin adds a new tab in your admin bar with simple but useful indications and tools.

* Displays the number of queries in your page and the amount of time to generate the page.
* Displays the php memory usage and php memory limits (constants `WP_MEMORY_LIMIT` and `WP_MAX_MEMORY_LIMIT`).
* displays the php version and WP version.
* Displays `WP_DEBUG`, `SCRIPT_DEBUG`, `WP_DEBUG_LOG`, `WP_DEBUG_DISPLAY`, and error reporting values.

### In your site front-end

* Lists the template and all template parts used in the current page (template parts added with `get_template_part()`). Compatible with WooCommerce's templates.
* `$wp_query`: this will open a lightbox displaying the content of `$wp_query`. Click the lightbox title to reload the value, click outside the lightbox to close it.

### In your site administration

* Admin hooks: lists some oftenly used hooks (like `admin_init`). The indicator to the right of the line tells you how many times the hook has been triggered by a callback. A "P" means the hook has a parameter: hover it for more details. Click a hook (on its text) to auto-select its code, for example: click *admin_init* to select `add_action( 'admin_init', '' );`.
* `$current_screen`: displays the value of 4 properties of this object: `id`, `base`, `parent_base`, `parent_file`.
* `$...now`: displays the value of the well-known variables `$pagenow`, `$typenow`, and `$taxnow`.
* On a user profile page, `$userdata`: : this will open a lightbox displaying the user's data.

You can decide who's gonna use this plugin (go to your profile page for all the settings). This way, the plugin's items won't show up to other users (your client for example).  
Also, a new menu item `Code Tester` will appear. There you are able to do some tests with your code.
