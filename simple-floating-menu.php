<?php
/**
 * Plugin Name: Simple Floating Menu
 * Plugin URI: https://github.com/pzstar/simple-floating-menu
 * Description: Simple Floating Menu adds a stylish designed menu in your website.
 * Version: 1.4.0
 * Author: HashThemes
 * Author URI:  https://hashthemes.com
 * Text Domain: simple-floating-menu
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /languages
 *
 */
if (!defined('ABSPATH'))
    exit;

define('SFM_VERSION', '1.4.0');
define('SFM_FILE', __FILE__);
define('SFM_PLUGIN_BASENAME', plugin_basename(SFM_FILE));
define('SFM_PATH', plugin_dir_path(SFM_FILE));
define('SFM_URL', plugins_url('/', SFM_FILE));

/**
 * Whether this plugin should load.
 *
 * Everything the plugin does is loaded through this, so another plugin can
 * stand it down without it having to be deactivated. The premium version does
 * exactly that: it draws the same menus, and the two running together would
 * give a site two of them and two copies of the icon fonts.
 *
 * The answer is asked for on plugins_loaded rather than here, so a plugin
 * loaded after this one still gets its say.
 *
 *     add_filter('sfm_load_plugin', '__return_false');
 *
 * @since 1.4.0
 * @return bool
 */
function sfm_load_plugin() {
    return (bool) apply_filters('sfm_load_plugin', true);
}

/**
 * Init the plugin.
 */
function simple_floating_menu() {
    if (!sfm_load_plugin()) {
        return;
    }

    require_once SFM_PATH . 'includes/class-simple-floating-menu.php';

    /* Standalone menus, kept entirely within the standalone folder.
       Remove this single line to remove the feature. */
    require_once SFM_PATH . 'standalone/class-sfm-standalone.php';

    Simple_Floating_Menu::get_instance();
}

/* Early, so the plugin's own hooks still land before anything needs them. */
add_action('plugins_loaded', 'simple_floating_menu', 0);
