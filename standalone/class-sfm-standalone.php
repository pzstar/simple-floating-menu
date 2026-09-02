<?php

/**
 * Standalone menus.
 *
 * Menus that are their own thing rather than the single bar configured on the
 * settings screen. Everything this feature needs is inside this folder, and it
 * is wired in by the single require in simple-floating-menu.php. Remove that
 * line and the plugin behaves exactly as it did before.
 *
 * What is left behind in the rest of the plugin are two inert seams: the
 * optional arguments on sfm_dymanic_styles() and on floating_menu_html(),
 * neither of which does anything without this folder loaded.
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

class SFM_Standalone {

    /**
     * The post type one menu is stored as.
     *
     * Deliberately the same name the premium plugin uses. Somebody who builds
     * menus here and then upgrades keeps the menus they built, rather than
     * finding an empty list and their work stranded under a type nothing
     * reads any more.
     */
    const POST_TYPE = 'sffm-navigation';

    /**
     * Post meta holding the ordered button list.
     *
     * The premium plugin's own key, holding the premium plugin's own shape.
     * Upgrading is then not a conversion at all: the premium plugin finds the
     * menu already in the form it reads. SFM_Standalone_Store translates
     * between that shape and this plugin's flat one, and is the only place
     * either key is touched.
     */
    const ITEMS_META = '_sffm_items';

    /** Post meta holding everything that is not a button. */
    const CONFIG_META = '_sffm_config';

    /**
     * Whether the premium plugin is running.
     *
     * It owns this post type once it is here, and does everything below in a
     * fuller form, so this half of the plugin stands aside rather than
     * registering the same screens and the same type twice.
     *
     * @return bool
     */
    public static function premium_active() {
        return defined('SFFM_VERSION')
            || class_exists('Super_Floating_Flying_Menu')
            || class_exists('SFFM_Standalone');
    }

    /**
     * Boots the feature once every plugin has had a chance to load.
     *
     * The check cannot happen at file load: this plugin's folder sorts before
     * the premium one's, so at that moment the premium plugin has not defined
     * anything to find. Everything below hooks init or later, so waiting for
     * plugins_loaded costs nothing.
     */
    public static function boot() {
        add_action('plugins_loaded', array(__CLASS__, 'init'), 5);
    }

    /**
     * Boots the feature.
     */
    public static function init() {
        if (self::premium_active()) {
            return;
        }

        require_once __DIR__ . '/class-sfm-standalone-store.php';
        require_once __DIR__ . '/class-sfm-standalone-cpt.php';
        require_once __DIR__ . '/class-sfm-standalone-frontend.php';

        new SFM_Standalone_Cpt();
        new SFM_Standalone_Frontend();

        if (is_admin()) {
            require_once __DIR__ . '/class-sfm-standalone-templates.php';
            require_once __DIR__ . '/class-sfm-standalone-admin.php';
            require_once __DIR__ . '/class-sfm-standalone-page.php';
            require_once __DIR__ . '/class-sfm-standalone-transfer.php';

            new SFM_Standalone_Admin();
            new SFM_Standalone_Page();
            new SFM_Standalone_Transfer();
        }
    }

    /**
     * The class a menu's markup and its CSS agree on.
     *
     * The settings screen's menu has no such class, so its rules stay
     * unqualified and nothing about it changes.
     *
     * @param  int $post_id
     * @return string
     */
    public static function scope($post_id) {
        return 'sfm-menu-' . intval($post_id);
    }
}

SFM_Standalone::boot();
