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
        $menus = array();

        foreach (SFM_Standalone_Store::live_menus() as $menu) {
            if (self::is_visible(SFM_Standalone_Store::get_settings($menu->ID))) {
                $menus[] = $menu;
            }
        }

        return apply_filters('sfm_standalone_menus', $menus);
    }

    /**
     * Whether this menu is allowed on the page being viewed.
     *
     * The premium plugin's Display panel decides this the same way, from the
     * same settings, so a menu behaves the same in both.
     *
     * @since  1.4.0
     * @param  array $settings
     * @return bool
     */
    public static function is_visible($settings) {
        $condition = isset($settings['display_condition']) ? $settings['display_condition'] : 'show_all';

        if ($condition == 'hide_all') {
            return false;
        }

        if ($condition != 'show_selected' && $condition != 'hide_selected') {
            return true;
        }

        $matched = self::matches_selection($settings);

        return $condition == 'show_selected' ? $matched : !$matched;
    }

    /**
     * Whether the page being viewed is one of the ones picked out.
     *
     * @param  array $settings
     * @return bool
     */
    private static function matches_selection($settings) {
        if (is_404()) {
            return self::is_on($settings, 'error_pages');
        }

        if (is_search()) {
            return self::is_on($settings, 'search_pages');
        }

        /* Asked before the blog, because a site showing its posts on the front
           page is both, and the front page is the more particular of the two. */
        if (is_front_page()) {
            return self::is_on($settings, 'front_pages');
        }

        if (is_home()) {
            return self::is_on($settings, 'blog_pages');
        }

        if (is_singular()) {
            $post = get_queried_object();

            if (!$post instanceof WP_Post) {
                return false;
            }

            $types = isset($settings['cpt_pages']) ? (array) $settings['cpt_pages'] : array();
            $pages = isset($settings['specific_pages']) ? array_map('intval', (array) $settings['specific_pages']) : array();

            return in_array($post->post_type, $types) || in_array((int) $post->ID, $pages, true);
        }

        if (is_archive()) {
            if (self::is_on($settings, 'archive_pages')) {
                return true;
            }

            $archives = isset($settings['specific_archive']) ? (array) $settings['specific_archive'] : array();

            return in_array(self::archive_post_type(), $archives);
        }

        return false;
    }

    /**
     * The post type the archive being viewed lists.
     *
     * A taxonomy archive is named by what the taxonomy is attached to, and the
     * date and author archives list posts, so each answers as its type rather
     * than as nothing at all.
     *
     * @return string
     */
    private static function archive_post_type() {
        $object = get_queried_object();

        if ($object instanceof WP_Post_Type) {
            return $object->name;
        }

        if ($object instanceof WP_Term) {
            $taxonomy = get_taxonomy($object->taxonomy);

            if ($taxonomy && !empty($taxonomy->object_type)) {
                return reset($taxonomy->object_type);
            }
        }

        return is_date() || is_author() ? 'post' : '';
    }

    /**
     * @param  array  $settings
     * @param  string $key
     * @return bool
     */
    private static function is_on($settings, $key) {
        return isset($settings[$key]) && $settings[$key] == 'on';
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
