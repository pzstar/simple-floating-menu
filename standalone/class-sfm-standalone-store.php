<?php

/**
 * Reading and writing standalone menus.
 *
 * A standalone menu is stored in the shape the premium plugin stores its own
 * menus in, under the same two meta keys. Upgrading is then not a conversion
 * at all: the premium plugin finds its own data already there and reads it.
 *
 * Inside this plugin the settings keep the flat shape the settings screen uses,
 * so the renderer, the CSS builder and the sanitiser all work on them without a
 * second version of any of it. This class is the only door to the data, so the
 * translation lives here and nowhere else.
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

class SFM_Standalone_Store {

    /**
     * The name each of this plugin's settings goes by in the premium one.
     *
     * @since  1.4.0
     * @return array
     */
    private static function config_map() {
        return array(
            'position' => 'temp_1_position',
            'orientation' => 'temp_1_orientation',
            'top_offset' => 'temp_1_offset_top',
            'bottom_offset' => 'temp_1_offset_bottom',
            'left_offset' => 'temp_1_offset_left',
            'right_offset' => 'temp_1_offset_right',
            'button_height' => 'button_size',
            'icon_size' => 'icon_size',
            'button_spacing' => 'tab_spacing',
            'zindex' => 'zindex',
            'scroll_offset' => 'scroll_offset',
            'button_bg_color' => 'button_bg_color',
            'button_bg_color_hover' => 'button_hover_bg_color',
            'button_icon_color' => 'button_icon_color',
            'button_icon_color_hover' => 'button_hover_icon_color',
            'tooltip_bg_color' => 'tooltip_bg_color',
            'tooltip_bg_color_hover' => 'tooltip_hover_bg_color',
            'tooltip_text_color' => 'tooltip_text_color',
            'tooltip_text_color_hover' => 'tooltip_hover_text_color',
            'tooltip_border_radius' => 'tooltip_border_radius',
        );
    }

    /**
     * The typography settings, which this plugin nests and the premium one
     * spreads across keys of its own.
     *
     * @since  1.4.0
     * @return array
     */
    private static function font_map() {
        return array(
            'family' => 'tooltip_font_family',
            'style' => 'tooltip_font_style',
            'transform' => 'tooltip_text_transform',
            'decoration' => 'tooltip_text_decoration',
            'size' => 'tooltip_font_size',
            'line_height' => 'tooltip_line_height',
            'letter_spacing' => 'tooltip_letter_spacing',
        );
    }

    /**
     * The three templates, and the nine button shapes, as the premium plugin
     * names them.
     *
     * @since  1.4.0
     * @return array
     */
    private static function name_map() {
        return array(
            'template' => array(
                'sfm-template-1' => 'floatmenu-template-1',
                'sfm-template-2' => 'floatmenu-template-2',
                'sfm-template-3' => 'floatmenu-template-3',
            ),
            'style' => array(
                'sfm-rect' => 'square',
                'sfm-round' => 'round',
                'sfm-triangle' => 'triangle',
                'sfm-rhombus' => 'rhombus',
                'sfm-pentagon' => 'pentagon',
                'sfm-hexagon' => 'hexagon',
                'sfm-star' => 'star',
                'sfm-rabbet' => 'rabbet',
                'sfm-oval' => 'oval',
            ),
        );
    }

    /**
     * The name each of a button's settings goes by in the premium plugin,
     * where they sit under a "floatingmenu" key of their own.
     *
     * @since  1.4.0
     * @return array
     */
    private static function item_map() {
        return array(
            'button_bg_color' => 'bg_color',
            'button_bg_color_hover' => 'hover_bg_color',
            'button_icon_color' => 'icon_color',
            'button_icon_color_hover' => 'hover_icon_color',
            'tooltip_bg_color' => 'tooltip_bg_color',
            'tooltip_bg_color_hover' => 'tooltip_hover_bg_color',
            'tooltip_text_color' => 'tooltip_text_color',
            'tooltip_text_color_hover' => 'tooltip_hover_text_color',
            'tool_tip_text' => 'tooltip_text',
            'action' => 'action',
            'scroll_sectionid' => 'scroll_sectionid',
        );
    }

    /**
     * The settings this plugin has and the premium one does not.
     *
     * @since  1.4.0
     * @return array
     */
    private static function own_keys() {
        return array('sfm_load_google_font_locally');
    }

    /**
     * Turn this plugin's flat settings into the premium plugin's nested ones.
     *
     * Only the keys both plugins have are written. The premium plugin fills
     * everything else from its own defaults when it reads the menu, so a
     * partial configuration is exactly what it expects.
     *
     * @since  1.4.0
     * @param  array $settings
     * @return array
     */
    public static function to_stored_config($settings) {
        $names = self::name_map();
        $float = array();

        $template = isset($settings['template']) ? $settings['template'] : '';
        $float['template'] = isset($names['template'][$template]) ? $names['template'][$template] : 'floatmenu-template-1';

        $style = isset($settings['style']) ? $settings['style'] : '';
        $float['temp_1_tab_shape'] = isset($names['style'][$style]) ? $names['style'][$style] : 'round';

        foreach (self::config_map() as $from => $to) {
            if (isset($settings[$from])) {
                $float[$to] = $settings[$from];
            }
        }

        /* Both plugins keep a shadow's parts together. The premium one carries
           a spread as well, which this plugin does not offer. */
        if (isset($settings['button_shadow']) && is_array($settings['button_shadow'])) {
            $shadow = $settings['button_shadow'];

            $float['button_shadow'] = array(
                'x' => isset($shadow['x']) ? $shadow['x'] : '',
                'y' => isset($shadow['y']) ? $shadow['y'] : '',
                'blur' => isset($shadow['blur']) ? $shadow['blur'] : '',
                'spread' => isset($shadow['spread']) ? $shadow['spread'] : '',
                'color' => isset($shadow['color']) ? $shadow['color'] : '',
            );
        }

        /* This plugin calls it padding and keeps the sides in one array; the
           premium one calls it spacing and keeps a key per side. */
        if (isset($settings['tooltip_padding']) && is_array($settings['tooltip_padding'])) {
            foreach (array('top', 'right', 'bottom', 'left') as $side) {
                if (isset($settings['tooltip_padding'][$side])) {
                    $float['tooltip_spacing_' . $side] = $settings['tooltip_padding'][$side];
                }
            }
        }

        if (isset($settings['tooltip_font']) && is_array($settings['tooltip_font'])) {
            foreach (self::font_map() as $from => $to) {
                if (isset($settings['tooltip_font'][$from])) {
                    $float[$to] = $settings['tooltip_font'][$from];
                }
            }
        }

        $config = array(
            'menu_enabled' => true,
            'menu_type' => 'floating_menu',
            'floatmenu' => $float,
        );

        /* Settings the premium plugin has no equivalent for ride alongside
           rather than inside "floatmenu", which is the only key it reads. They
           are invisible to it, and still here if this plugin is put back. */
        foreach (self::own_keys() as $key) {
            if (isset($settings[$key])) {
                $config['sfm'][$key] = $settings[$key];
            }
        }

        return $config;
    }

    /**
     * Turn the premium plugin's nested settings back into this plugin's flat
     * ones. The reverse of self::to_stored_config().
     *
     * @since  1.4.0
     * @param  array $stored
     * @return array
     */
    public static function to_flat_config($stored) {
        $float = isset($stored['floatmenu']) && is_array($stored['floatmenu']) ? $stored['floatmenu'] : array();
        $names = self::name_map();
        $flat = array();

        $template = array_search(isset($float['template']) ? $float['template'] : '', $names['template'], true);
        if ($template !== false) {
            $flat['template'] = $template;
        }

        $style = array_search(isset($float['temp_1_tab_shape']) ? $float['temp_1_tab_shape'] : '', $names['style'], true);
        if ($style !== false) {
            $flat['style'] = $style;
        }

        foreach (self::config_map() as $to => $from) {
            if (isset($float[$from])) {
                $flat[$to] = $float[$from];
            }
        }

        if (isset($float['button_shadow']) && is_array($float['button_shadow'])) {
            $flat['button_shadow'] = $float['button_shadow'];

            /* The premium plugin spreads a shadow and this one does not. An
               empty spread is dropped rather than carried, so a menu saved
               here keeps the shape this plugin's own settings have; a spread
               somebody set over there survives untouched. */
            if (isset($flat['button_shadow']['spread']) && $flat['button_shadow']['spread'] === '') {
                unset($flat['button_shadow']['spread']);
            }
        }

        foreach (array('top', 'right', 'bottom', 'left') as $side) {
            if (isset($float['tooltip_spacing_' . $side])) {
                $flat['tooltip_padding'][$side] = $float['tooltip_spacing_' . $side];
            }
        }

        foreach (self::font_map() as $to => $from) {
            if (isset($float[$from])) {
                $flat['tooltip_font'][$to] = $float[$from];
            }
        }

        /* The premium plugin sizes a button on one axis, and so does the
           builder here: its width field mirrors the size field rather than
           being set on its own. Squaring it off on the way in is what keeps a
           menu the same shape either side of an upgrade. */
        if (isset($flat['button_height'])) {
            $flat['button_width'] = $flat['button_height'];
        }

        if (isset($stored['sfm']) && is_array($stored['sfm'])) {
            foreach (self::own_keys() as $key) {
                if (isset($stored['sfm'][$key])) {
                    $flat[$key] = $stored['sfm'][$key];
                }
            }
        }

        return $flat;
    }

    /**
     * Turn this plugin's flat buttons into the premium plugin's menu items.
     *
     * The premium plugin builds an id for each item by arithmetic on this one,
     * so it has to be a small whole number. This plugin keys a button's CSS on
     * its id instead, which is why the flat side gets a name built from the
     * number rather than the number itself.
     *
     * @since  1.4.0
     * @param  array $buttons
     * @return array
     */
    public static function to_stored_items($buttons) {
        $items = array();
        $index = 0;

        foreach ($buttons as $button) {
            if (!is_array($button)) {
                continue;
            }

            $index++;

            $icon = isset($button['icon']) ? $button['icon'] : '';
            $new_tab = !empty($button['open_new_tab']);
            $label = isset($button['label']) ? $button['label'] : '';
            $tooltip = isset($button['tool_tip_text']) ? $button['tool_tip_text'] : '';

            $float = array(
                'icon_type' => 'available_icon',
                'available_icon' => $icon,
                'tooltip_enable' => !empty($button['tooltip_enable']) ? 'on' : 'off',
                'target_blank' => $new_tab ? 'on' : 'off',
            );

            foreach (self::item_map() as $from => $to) {
                if (isset($button[$from])) {
                    $float[$to] = $button[$from];
                }
            }

            /* Carried over so the icon survives a switch to the fly menu, which
               this plugin has no screen for but the premium one does. */
            $fly = array();

            if ($icon !== '') {
                $fly = array('icon_type' => 'available_icon', 'available_icon' => $icon);
            }

            $items[] = array(
                'id' => $index,
                'label' => $label !== '' ? $label : $tooltip,
                'url' => isset($button['url']) ? $button['url'] : '',
                'parent' => '0',
                'target' => $new_tab ? '_blank' : '',
                'classes' => isset($button['classes']) ? $button['classes'] : '',
                'attr_title' => isset($button['attr_title']) ? $button['attr_title'] : '',
                'xfn' => '',
                'settings' => array('floatingmenu' => $float, 'flymenu' => $fly),
            );
        }

        return $items;
    }

    /**
     * Turn the premium plugin's menu items back into this plugin's flat
     * buttons. The reverse of self::to_stored_items().
     *
     * @since  1.4.0
     * @param  array $items
     * @return array
     */
    public static function to_flat_buttons($items) {
        $buttons = array();
        $index = 0;

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $index++;

            $float = isset($item['settings']['floatingmenu']) && is_array($item['settings']['floatingmenu'])
                ? $item['settings']['floatingmenu']
                : array();

            $id = isset($item['id']) ? (int) $item['id'] : $index;

            $button = array(
                'id' => 'sfm-item-' . ($id > 0 ? $id : $index),
                'label' => isset($item['label']) ? $item['label'] : '',
                'icon' => isset($float['available_icon']) ? $float['available_icon'] : '',
                'url' => isset($item['url']) ? $item['url'] : '',
                'classes' => isset($item['classes']) ? $item['classes'] : '',
                'attr_title' => isset($item['attr_title']) ? $item['attr_title'] : '',
                'open_new_tab' => isset($item['target']) && $item['target'] === '_blank',
                'tooltip_enable' => !isset($float['tooltip_enable']) || $float['tooltip_enable'] === 'on',
            );

            foreach (self::item_map() as $to => $from) {
                if (isset($float[$from])) {
                    $button[$to] = $float[$from];
                }
            }

            $buttons[] = $button;
        }

        return $buttons;
    }

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
        $settings = array_merge(Simple_Floating_Menu::default_settings(), self::get_raw_settings($post_id));

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

        return is_array($stored) ? self::to_flat_config($stored) : array();
    }

    /**
     * Stores a menu's settings.
     *
     * @param int   $post_id
     * @param array $settings
     */
    public static function save_settings($post_id, $settings) {
        unset($settings['buttons']);

        update_post_meta($post_id, SFM_Standalone::CONFIG_META, self::to_stored_config($settings));
    }

    /**
     * A menu's buttons, in order.
     *
     * @param  int $post_id
     * @return array
     */
    public static function get_buttons($post_id) {
        $items = get_post_meta($post_id, SFM_Standalone::ITEMS_META, true);

        return is_array($items) ? self::to_flat_buttons($items) : array();
    }

    /**
     * Stores a menu's buttons.
     *
     * @param int   $post_id
     * @param array $buttons
     */
    public static function save_buttons($post_id, $buttons) {
        $clean = self::sanitize_buttons($buttons);

        update_post_meta($post_id, SFM_Standalone::ITEMS_META, self::to_stored_items($clean));
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
