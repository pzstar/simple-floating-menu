<?php

/**
 * The menu list.
 *
 * Sits under the plugin's own top level entry, beside the settings screen the
 * plugin has always had, so the single bar and these menus are found in the
 * same place.
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

class SFM_Standalone_Admin {

    /** The plugin's existing top level page. */
    const PARENT_SLUG = 'simple-floating-menu';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu'), 11);

        add_filter('manage_' . SFM_Standalone::POST_TYPE . '_posts_columns', array($this, 'columns'));
        add_action('manage_' . SFM_Standalone::POST_TYPE . '_posts_custom_column', array($this, 'column'), 10, 2);

        add_action('admin_enqueue_scripts', array($this, 'scripts'));
        add_action('admin_notices', array($this, 'add_panel'), 10);

        /* Keeps the plugin's entry highlighted while on these screens. */
        add_filter('parent_file', array($this, 'parent_file'));
    }

    /**
     * @since 1.4.0
     */
    public function add_menu() {
        add_submenu_page(
            self::PARENT_SLUG,
            esc_html__('Floating Menus', 'simple-floating-menu'),
            esc_html__('Floating Menus', 'simple-floating-menu'),
            'manage_options',
            'edit.php?post_type=' . SFM_Standalone::POST_TYPE
        );
    }

    /**
     * @param  string $parent_file
     * @return string
     */
    public function parent_file($parent_file) {
        $screen = get_current_screen();

        if ($screen && $screen->post_type == SFM_Standalone::POST_TYPE) {
            return self::PARENT_SLUG;
        }

        return $parent_file;
    }

    /**
     * Whether this request is for the menu list.
     *
     * @return bool
     */
    public static function is_list_screen() {
        $screen = function_exists('get_current_screen') ? get_current_screen() : null;

        return $screen && $screen->base == 'edit' && $screen->post_type == SFM_Standalone::POST_TYPE;
    }

    /**
     * @since 1.4.0
     */
    public function scripts() {
        if (!self::is_list_screen()) {
            return;
        }

        wp_enqueue_style('sfm-standalone-list', SFM_URL . 'standalone/css/list.css', array(), SFM_VERSION);


        wp_enqueue_script('sfm-standalone-list', SFM_URL . 'standalone/js/list.js', array(), SFM_VERSION, true);

        wp_localize_script('sfm-standalone-list', 'sfmList', array(
            'format' => SFM_Standalone_Transfer::FORMAT,
            'maxBytes' => SFM_Standalone_Transfer::MAX_UPLOAD_BYTES,
            'sizeLimit' => size_format(SFM_Standalone_Transfer::MAX_UPLOAD_BYTES),
            'untitled' => __('Untitled menu', 'simple-floating-menu'),
            'noButtons' => __('No buttons', 'simple-floating-menu'),
            'buttonOne' => __('1 button', 'simple-floating-menu'),
            /* translators: %d: how many buttons the file holds. */
            'buttonMany' => __('%d buttons', 'simple-floating-menu'),
            /* translators: %s: the largest file accepted, already formatted. */
            'tooBig' => __('That file is bigger than %s.', 'simple-floating-menu'),
            'notJson' => __('That file is not JSON.', 'simple-floating-menu'),
            'notRecognised' => __('That is not a menu exported from this plugin.', 'simple-floating-menu'),
            'unreadable' => __('That file could not be read.', 'simple-floating-menu'),
        ));
    }

    /**
     * @param  array $columns
     * @return array
     */
    public function columns($columns) {
        $date = isset($columns['date']) ? $columns['date'] : null;
        unset($columns['date']);

        $columns['sfm_buttons'] = esc_html__('Buttons', 'simple-floating-menu');
        $columns['sfm_template'] = esc_html__('Template', 'simple-floating-menu');

        if ($date !== null) {
            $columns['date'] = $date;
        }

        return $columns;
    }

    /**
     * @param string $column
     * @param int    $post_id
     */
    public function column($column, $post_id) {
        if ($column == 'sfm_buttons') {
            $count = count(SFM_Standalone_Store::get_buttons($post_id));

            printf(
                /* translators: %d: how many buttons the menu has. */
                esc_html(_n('%d button', '%d buttons', $count, 'simple-floating-menu')),
                intval($count)
            );

            return;
        }

        if ($column == 'sfm_template') {
            $settings = SFM_Standalone_Store::get_settings($post_id);
            $template = isset($settings['template']) ? $settings['template'] : '';
            $templates = SFM_Standalone_Templates::all();

            /* The words the picker uses, and the first of the template's own
               traits beside them, so the column says something about the menu
               rather than only numbering it. */
            $name = isset($templates[$template]) ? $templates[$template]['name'] : $template;
            $trait = isset($templates[$template]['facts'][0]) ? $templates[$template]['facts'][0] : '';

            printf(
                '<span class="sfm-pill">%s</span> <span class="sfm-muted">%s</span>',
                esc_html($name),
                esc_html($trait)
            );
        }
    }

    /**
     * The panel above the list for adding a menu.
     *
     * @since 1.4.0
     */
    public function add_panel() {
        if (!self::is_list_screen() || !current_user_can('manage_options')) {
            return;
        }
        ?>
        <details class="sfm-add" id="sfm-add-panel" <?php echo empty($_GET['sfm_add']) ? '' : 'open'; ?>>
            <summary>
                <span class="sfm-add-icon dashicons dashicons-plus-alt2" aria-hidden="true"></span>
                <span class="sfm-add-title"><?php esc_html_e('Add a menu', 'simple-floating-menu'); ?></span>
                <span class="sfm-add-hint"><?php esc_html_e('Start an empty one, or bring in a menu you exported', 'simple-floating-menu'); ?></span>
                <span class="sfm-add-chevron dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
            </summary>

            <div class="sfm-add-body">
                <section class="sfm-add-route">
                    <h3 class="sfm-add-route-title">
                        <span class="dashicons dashicons-plus" aria-hidden="true"></span>
                        <?php esc_html_e('New menu', 'simple-floating-menu'); ?>
                    </h3>

                    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                        <input type="hidden" name="action" value="sfm_standalone_create"/>
                        <?php wp_nonce_field('sfm_standalone_create'); ?>

                        <label class="screen-reader-text" for="sfm-new-name"><?php esc_html_e('Menu name', 'simple-floating-menu'); ?></label>
                        <input type="text" id="sfm-new-name" name="sfm_menu_name"
                               placeholder="<?php esc_attr_e('Name this menu', 'simple-floating-menu'); ?>" required/>

                        <div class="sfm-add-actions">
                            <button type="submit" class="button button-primary"><?php esc_html_e('Create Menu', 'simple-floating-menu'); ?></button>
                        </div>

                        <p class="description"><?php esc_html_e('A new menu starts as a draft, so nothing appears on your site until you publish it.', 'simple-floating-menu'); ?></p>
                    </form>
                </section>

                <section class="sfm-add-route">
                    <h3 class="sfm-add-route-title">
                        <span class="dashicons dashicons-upload" aria-hidden="true"></span>
                        <?php esc_html_e('From a file', 'simple-floating-menu'); ?>
                    </h3>

                    <?php SFM_Standalone_Transfer::render_import_form(); ?>
                </section>
            </div>
        </details>
        <?php
    }
}
