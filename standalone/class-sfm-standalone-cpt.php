<?php

/**
 * The post type one standalone menu is stored as.
 *
 * Not public: a menu is not a page on the site, it is something that appears
 * on the pages of the site, so it has no permalink of its own.
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

class SFM_Standalone_Cpt {

    public function __construct() {
        add_action('init', array($this, 'register'));
        add_action('before_delete_post', array($this, 'clean_up'));
    }

    /**
     * @since 1.4.0
     */
    public function register() {
        register_post_type(SFM_Standalone::POST_TYPE, array(
            'labels' => array(
                'name' => esc_html__('Floating Menus', 'simple-floating-menu'),
                'singular_name' => esc_html__('Floating Menu', 'simple-floating-menu'),
                'add_new_item' => esc_html__('Add Menu', 'simple-floating-menu'),
                'edit_item' => esc_html__('Edit Menu', 'simple-floating-menu'),
                'search_items' => esc_html__('Search Menus', 'simple-floating-menu'),
                'not_found' => esc_html__('No menus yet.', 'simple-floating-menu'),
                'not_found_in_trash' => esc_html__('No menus in the trash.', 'simple-floating-menu'),
            ),
            'public' => false,
            'show_ui' => true,
            /* Listed under the plugin's own menu, not as a top level entry. */
            'show_in_menu' => false,
            'supports' => array('title'),
            'capability_type' => 'page',
            'map_meta_cap' => true,
            'has_archive' => false,
            'rewrite' => false,
            'query_var' => false,
            'exclude_from_search' => true,
            'delete_with_user' => false,
        ));
    }

    /**
     * Removes a menu's settings when the menu itself is deleted.
     *
     * @param int $post_id
     */
    public function clean_up($post_id) {
        if (get_post_type($post_id) != SFM_Standalone::POST_TYPE) {
            return;
        }

        delete_post_meta($post_id, SFM_Standalone::ITEMS_META);
        delete_post_meta($post_id, SFM_Standalone::CONFIG_META);
    }
}
