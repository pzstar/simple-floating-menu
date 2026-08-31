<?php

/**
 * Putting standalone menus on the site.
 *
 * Each one is printed by the plugin's own renderer and described by the
 * plugin's own CSS builder, passed that menu's settings and a class of its
 * own. The menu from the settings screen is untouched and still prints
 * itself.
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

class SFM_Standalone_Frontend {

    public function __construct() {
        if (is_admin()) {
            return;
        }

        /* After the plugin's own styles, so a menu's rules can rely on the
           stylesheet they narrow. */
        add_action('wp_enqueue_scripts', array($this, 'styles'), 20);
        add_action('wp_footer', array($this, 'render'));
    }

    /**
     * The menus that will print on this request.
     *
     * @return WP_Post[]
     */
    private function menus() {
        return apply_filters('sfm_standalone_menus', SFM_Standalone_Store::live_menus());
    }

    /**
     * One block of CSS covering every menu on the page.
     *
     * @since 1.4.0
     */
    public function styles() {
        $css = '';

        foreach ($this->menus() as $menu) {
            $settings = SFM_Standalone_Store::get_settings($menu->ID);

            if (empty($settings['buttons'])) {
                continue;
            }

            $css .= sfm_dymanic_styles($settings, SFM_Standalone::scope($menu->ID));
        }

        if ($css === '') {
            return;
        }

        /* The handle the plugin already registers, so this lands with the
           rest of the menu's CSS rather than as another request. */
        if (wp_style_is('sfm-style', 'enqueued') || wp_style_is('sfm-style', 'registered')) {
            wp_add_inline_style('sfm-style', $css);
            return;
        }

        wp_register_style('sfm-standalone', false, array(), SFM_VERSION);
        wp_enqueue_style('sfm-standalone');
        wp_add_inline_style('sfm-standalone', $css);
    }

    /**
     * @since 1.4.0
     */
    public function render() {
        if (defined('REST_REQUEST') && REST_REQUEST) {
            return;
        }

        if (!class_exists('Simple_Floating_Menu_Frontend')) {
            return;
        }

        /* Built without running its constructor on purpose: that constructor
           hooks wp_footer and wp_enqueue_scripts, so a second instance would
           print the settings screen's menu all over again. Only the one
           method is wanted here. */
        $reflection = new ReflectionClass('Simple_Floating_Menu_Frontend');
        $printer = $reflection->newInstanceWithoutConstructor();

        foreach ($this->menus() as $menu) {
            $settings = SFM_Standalone_Store::get_settings($menu->ID);

            if (empty($settings['buttons'])) {
                continue;
            }

            $printer->floating_menu_html($settings, SFM_Standalone::scope($menu->ID));
        }
    }
}
