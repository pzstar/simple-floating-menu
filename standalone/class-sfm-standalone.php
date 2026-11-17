<?php

/**
 * Standalone menus.
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

class SFM_Standalone {

    /**
     * The post type one menu is stored as.
     */
    const POST_TYPE = 'sffm-navigation';

    /**
     * Post meta holding the ordered button list.
     */
    const ITEMS_META = '_sffm_items';

    /** Post meta holding everything that is not a button. */
    const CONFIG_META = '_sffm_config';

    /**
     * Whether the premium plugin is running.
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
     * @param  int $post_id
     * @return string
     */
    public static function scope($post_id) {
        return 'sfm-menu-' . intval($post_id);
    }
}

SFM_Standalone::boot();
