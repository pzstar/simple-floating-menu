<?php

/**
 * Reading and writing standalone menus.
 *
 * A menu's settings are deliberately the same shape as the settings screen's
 * own, so the renderer, the CSS builder and the sanitiser all work on them
 * without a second version of any of it.
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

class SFM_Standalone_Store {

    /**
     * Every menu that should appear on the site.
     *
     * A draft is a menu somebody is still working on, so only published ones
     * are returned.
     *
     * @return WP_Post[]
     */
    public static function live_menus() {
        return get_posts(array(
            'post_type' => SFM_Standalone::POST_TYPE,
            'post_status' => 'publish',
            'numberposts' => -1,
            'orderby' => 'menu_order title',
            'order' => 'ASC',
            'suppress_filters' => false,
        ));
    }

    /**
     * A menu's settings, merged over the plugin's defaults.
     *
     * @param  int $post_id
     * @return array
     */
    public static function get_settings($post_id) {
        $stored = get_post_meta($post_id, SFM_Standalone::CONFIG_META, true);
        $stored = is_array($stored) ? $stored : array();

        $settings = array_merge(Simple_Floating_Menu::default_settings(), $stored);

        /* The buttons live in their own row, so they are put back here where
           the renderer expects to find them. */
        $settings['buttons'] = self::get_buttons($post_id);

        /* Neither belongs to a standalone menu: it is switched on by being
           published, and it is edited from its own screen. */
        $settings['enable_sfm'] = 'yes';
        $settings['enable_sfm_setting'] = 'no';

        return $settings;
    }

    /**
     * The settings exactly as stored, with no defaults merged in.
     *
     * @param  int $post_id
     * @return array
     */
    public static function get_raw_settings($post_id) {
        $stored = get_post_meta($post_id, SFM_Standalone::CONFIG_META, true);

        return is_array($stored) ? $stored : array();
    }

    /**
     * Stores a menu's settings.
     *
     * @param int   $post_id
     * @param array $settings
     */
    public static function save_settings($post_id, $settings) {
        unset($settings['buttons']);

        update_post_meta($post_id, SFM_Standalone::CONFIG_META, $settings);
    }

    /**
     * A menu's buttons, in order.
     *
     * @param  int $post_id
     * @return array
     */
    public static function get_buttons($post_id) {
        $buttons = get_post_meta($post_id, SFM_Standalone::ITEMS_META, true);

        return is_array($buttons) ? $buttons : array();
    }

    /**
     * Stores a menu's buttons.
     *
     * @param int   $post_id
     * @param array $buttons
     */
    public static function save_buttons($post_id, $buttons) {
        update_post_meta($post_id, SFM_Standalone::ITEMS_META, self::sanitize_buttons($buttons));
    }

    /**
     * Cleans a button list coming from a form or an import.
     *
     * Every button gets an id of its own, because the id is what its colours
     * are keyed to in the CSS; two buttons sharing one would be coloured the
     * same however they were set.
     *
     * @param  array $buttons
     * @return array
     */
    public static function sanitize_buttons($buttons) {
        if (!is_array($buttons)) {
            return array();
        }

        $defaults = Simple_Floating_Menu::default_settings();
        $shape = $defaults['buttons'][0];
        $valid_icons = self::valid_icons();

        $clean = array();
        $seen = array();

        foreach ($buttons as $button) {
            if (!is_array($button)) {
                continue;
            }

            $id = isset($button['id']) ? sanitize_html_class($button['id']) : '';

            if ($id === '' || isset($seen[$id])) {
                $id = uniqid('sfm-');
            }

            $seen[$id] = true;
            $row = array('id' => $id);

            foreach ($shape as $key => $default) {
                if ($key === 'id') {
                    continue;
                }

                $value = isset($button[$key]) ? $button[$key] : $default;

                if ($key === 'url') {
                    $row[$key] = esc_url_raw($value);
                } elseif ($key === 'open_new_tab' || $key === 'tooltip_enable') {
                    $row[$key] = (bool) $value;
                } elseif ($key === 'action') {
                    $row[$key] = in_array($value, self::valid_actions(), true) ? $value : 'default';
                } elseif ($key === 'scroll_sectionid') {
                    $row[$key] = sanitize_html_class(ltrim((string) $value, '#'));
                } elseif ($key === 'classes') {
                    $row[$key] = implode(' ', array_filter(array_map('sanitize_html_class', explode(' ', (string) $value))));
                } elseif ($key === 'icon') {
                    $row[$key] = in_array($value, $valid_icons, true) ? $value : '';
                } elseif (strpos($key, 'color') !== false) {
                    $row[$key] = self::sanitize_color($value, $default);
                } else {
                    $row[$key] = sanitize_text_field($value);
                }
            }

            $clean[] = $row;
        }

        return $clean;
    }

    /**
     * The eight edges a menu can sit on, with the words used for them.
     *
     * @since    1.4.0
     * @return   array
     */
    public static function positions() {
        return array(
            'top-left' => __('Top left', 'simple-floating-menu'),
            'top-middle' => __('Top middle', 'simple-floating-menu'),
            'top-right' => __('Top right', 'simple-floating-menu'),
            'middle-left' => __('Middle left', 'simple-floating-menu'),
            'middle-right' => __('Middle right', 'simple-floating-menu'),
            'bottom-left' => __('Bottom left', 'simple-floating-menu'),
            'bottom-middle' => __('Bottom middle', 'simple-floating-menu'),
            'bottom-right' => __('Bottom right', 'simple-floating-menu'),
        );
    }

    /**
     * The shapes a button can take, with the words used for them.
     *
     * @since    1.4.0
     * @return   array
     */
    public static function shapes() {
        return array(
            'sfm-rect' => __('Square', 'simple-floating-menu'),
            'sfm-round' => __('Round', 'simple-floating-menu'),
            'sfm-triangle' => __('Triangle', 'simple-floating-menu'),
            'sfm-rhombus' => __('Rhombus', 'simple-floating-menu'),
            'sfm-pentagon' => __('Pentagon', 'simple-floating-menu'),
            'sfm-hexagon' => __('Hexagon', 'simple-floating-menu'),
            'sfm-star' => __('Star', 'simple-floating-menu'),
            'sfm-rabbet' => __('Rabbet', 'simple-floating-menu'),
            'sfm-oval' => __('Oval', 'simple-floating-menu'),
        );
    }

    /**
     * What a button can be made to do.
     *
     * @since    1.4.0
     * @return   array
     */
    public static function valid_actions() {
        return array('default', 'scroll_sectionid', 'scroll_to_top', 'scroll_to_bottom');
    }

    /**
     * The icon fonts the plugin ships, grouped for the picker.
     *
     * The same five libraries the settings screen offers, honouring the same
     * filters, so hiding one hides it in both places. This is the picker's
     * view; valid_icons() stays the whole set, or a saved icon from a library
     * somebody later switched off would be wiped the next time its menu was
     * saved.
     *
     * @since    1.4.0
     * @return   array
     */
    public static function icon_libraries() {
        $libraries = array(
            'icofont' => array(
                'name' => __('Ico Font', 'simple-floating-menu'),
                'show' => apply_filters('sfm_show_ico_font', true),
                'icons' => sfm_icofont_icon_array(),
            ),
            'fontawesome' => array(
                'name' => __('Font Awesome', 'simple-floating-menu'),
                'show' => apply_filters('sfm_show_font_awesome', true),
                'icons' => sfm_font_awesome_icon_array(),
            ),
            'essential' => array(
                'name' => __('Essential Icon', 'simple-floating-menu'),
                'show' => apply_filters('sfm_show_essential_icon', true),
                'icons' => sfm_essential_icon_array(),
            ),
            'material' => array(
                'name' => __('Material Icon', 'simple-floating-menu'),
                'show' => apply_filters('sfm_show_material_icon', true),
                'icons' => sfm_materialdesignicons_array(),
            ),
            'elegant' => array(
                'name' => __('Elegant Icon', 'simple-floating-menu'),
                'show' => apply_filters('sfm_show_elegant_icon', true),
                'icons' => sfm_eleganticons_array(),
            ),
        );

        $offered = array();

        foreach ($libraries as $slug => $library) {
            if (!$library['show'] || !$library['icons']) {
                continue;
            }

            $offered[$slug] = array(
                'name' => $library['name'],
                'icons' => $library['icons'],
            );
        }

        return $offered;
    }

    /**
     * Every icon the plugin ships, as one list.
     *
     * @return array
     */
    public static function valid_icons() {
        static $icons = null;

        if ($icons === null) {
            $icons = array_merge(
                sfm_font_awesome_icon_array(),
                sfm_materialdesignicons_array(),
                sfm_essential_icon_array(),
                sfm_icofont_icon_array(),
                sfm_eleganticons_array()
            );
        }

        return $icons;
    }

    /**
     * A colour, or the default when it is not one.
     *
     * @param  mixed  $color
     * @param  string $fallback
     * @return string
     */
    public static function sanitize_color($color, $fallback = '') {
        $color = is_string($color) ? trim($color) : '';

        if ($color === '') {
            return '';
        }

        if (preg_match('/^#([0-9a-f]{3}|[0-9a-f]{4}|[0-9a-f]{6}|[0-9a-f]{8})$/i', $color)) {
            return $color;
        }

        if (preg_match('/^rgba?\(\s*\d+\s*,\s*\d+\s*,\s*\d+\s*(,\s*[\d.]+\s*)?\)$/i', $color)) {
            return $color;
        }

        return $fallback;
    }
}
