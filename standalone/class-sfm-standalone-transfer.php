<?php

/**
 * Exporting and importing one menu.
 *
 * A file written here carries a menu's design and its buttons, so a setup can
 * be moved to another site without building it again.
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

class SFM_Standalone_Transfer {

    const FORMAT = 'sfm-standalone-menu';

    const FORMAT_VERSION = 1;

    /** Anything larger is refused before it is parsed. */
    const MAX_UPLOAD_BYTES = 1048576;

    public function __construct() {
        add_action('admin_post_sfm_standalone_export', array($this, 'handle_export'));
        add_action('admin_post_sfm_standalone_import', array($this, 'handle_import'));

        add_filter('post_row_actions', array($this, 'row_actions'), 10, 2);
        add_action('admin_notices', array($this, 'notice'), 9);
    }

    /**
     * Adds Export to each menu's row.
     *
     * @param  array   $actions
     * @param  WP_Post $post
     * @return array
     */
    public function row_actions($actions, $post) {
        if ($post->post_type != SFM_Standalone::POST_TYPE || !current_user_can('manage_options')) {
            return $actions;
        }

        $actions['sfm_export'] = sprintf(
            '<a href="%s">%s</a>',
            esc_url(wp_nonce_url(
                add_query_arg(array('action' => 'sfm_standalone_export', 'menu' => $post->ID), admin_url('admin-post.php')),
                'sfm_standalone_export_' . $post->ID
            )),
            esc_html__('Export', 'simple-floating-menu')
        );

        return $actions;
    }

    /**
     * The upload half of the add panel.
     *
     * @since 1.4.0
     */
    public static function render_import_form() {
        ?>
        <form method="post" enctype="multipart/form-data" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <input type="hidden" name="action" value="sfm_standalone_import"/>
            <?php wp_nonce_field('sfm_standalone_import'); ?>

            <?php
            /* The input covers the whole box, so dropping a file and clicking to
               browse are the same control. Everything below it is the picture
               drawn behind it; the script only swaps which half is shown. */
            ?>
            <div class="sfm-drop">
                <input type="file" name="sfm_import_file" class="sfm-drop-input" required
                       accept=".json,application/json"
                       aria-label="<?php esc_attr_e('Choose a menu export file', 'simple-floating-menu'); ?>"/>

                <div class="sfm-drop-idle">
                    <span class="sfm-drop-badge" aria-hidden="true">
                        <span class="dashicons dashicons-upload"></span>
                    </span>
                    <strong><?php esc_html_e('Drop a menu export here', 'simple-floating-menu'); ?></strong>
                    <span class="sfm-drop-sub">
                        <?php
                        printf(
                            /* translators: %s: the words that read as a link. */
                            esc_html__('or %s from your computer', 'simple-floating-menu'),
                            '<b>' . esc_html__('choose a file', 'simple-floating-menu') . '</b>'
                        );
                        ?>
                    </span>
                    <span class="sfm-drop-limit">
                        <?php
                        printf(
                            /* translators: %s: the largest file accepted, already formatted. */
                            esc_html__('JSON, up to %s', 'simple-floating-menu'),
                            esc_html(size_format(self::MAX_UPLOAD_BYTES))
                        );
                        ?>
                    </span>
                </div>

                <div class="sfm-drop-file" hidden>
                    <span class="sfm-drop-file-icon dashicons dashicons-media-text" aria-hidden="true"></span>
                    <span class="sfm-drop-file-body">
                        <span class="sfm-drop-file-title"></span>
                        <span class="sfm-drop-file-meta"></span>
                    </span>
                    <button type="button" class="sfm-drop-clear"
                            aria-label="<?php esc_attr_e('Choose a different file', 'simple-floating-menu'); ?>">
                        <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
                    </button>
                </div>
            </div>

            <p class="sfm-drop-error" role="alert" hidden></p>

            <div class="sfm-add-actions">
                <button type="submit" class="button button-primary sfm-import-submit">
                    <?php esc_html_e('Import Menu', 'simple-floating-menu'); ?>
                </button>
            </div>

            <p class="description">
                <?php
                printf(
                    /* translators: %s: the Export row action. */
                    esc_html__('A .json file exported from the %s link on this list.', 'simple-floating-menu'),
                    '<strong>' . esc_html__('Export', 'simple-floating-menu') . '</strong>'
                );
                ?>
            </p>
        </form>
        <?php
    }

    /**
     * @since 1.4.0
     */
    public function notice() {
        $screen = get_current_screen();

        if (!$screen || $screen->base != 'edit' || $screen->post_type != SFM_Standalone::POST_TYPE) {
            return;
        }

        if (empty($_GET['sfm_notice'])) {
            return;
        }

        $notices = array(
            'imported' => array('success', __('Menu imported. It is a draft until you publish it.', 'simple-floating-menu')),
            'name' => array('error', __('Give the menu a name first.', 'simple-floating-menu')),
            'failed' => array('error', __('The menu could not be created. Nothing was changed.', 'simple-floating-menu')),
            'invalid' => array('error', __('That file is not a menu export this plugin recognises. Nothing was changed.', 'simple-floating-menu')),
            'upload' => array('error', __('The file could not be read. Nothing was changed.', 'simple-floating-menu')),
        );

        $key = sanitize_key($_GET['sfm_notice']);

        if (!isset($notices[$key])) {
            return;
        }

        printf(
            '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
            esc_attr($notices[$key][0]),
            esc_html($notices[$key][1])
        );
    }

    /**
     * Sends one menu as a file.
     *
     * @since 1.4.0
     */
    public function handle_export() {
        $post_id = isset($_GET['menu']) ? intval($_GET['menu']) : 0;

        check_admin_referer('sfm_standalone_export_' . $post_id);

        if (!$post_id || !current_user_can('manage_options') || get_post_type($post_id) != SFM_Standalone::POST_TYPE) {
            wp_die(esc_html__('You are not allowed to export this menu.', 'simple-floating-menu'));
        }

        $payload = array(
            'format' => self::FORMAT,
            'version' => self::FORMAT_VERSION,
            'title' => get_the_title($post_id),
            'settings' => SFM_Standalone_Store::get_raw_settings($post_id),
            'buttons' => SFM_Standalone_Store::get_buttons($post_id),
        );

        $name = sanitize_title(get_the_title($post_id));
        $name = $name !== '' ? $name : 'floating-menu';

        nocache_headers();
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $name . '.json');

        echo wp_json_encode($payload);
        exit;
    }

    /**
     * Makes a menu from an uploaded file.
     *
     * @since 1.4.0
     */
    public function handle_import() {
        check_admin_referer('sfm_standalone_import');

        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to import menus.', 'simple-floating-menu'));
        }

        if (empty($_FILES['sfm_import_file']['tmp_name']) || !empty($_FILES['sfm_import_file']['error'])) {
            $this->back('upload');
        }

        /* Checked before the file is read, not after. */
        if ($_FILES['sfm_import_file']['size'] > self::MAX_UPLOAD_BYTES) {
            $this->back('upload');
        }

        $raw = file_get_contents($_FILES['sfm_import_file']['tmp_name']);
        $data = json_decode($raw, true);

        if (!is_array($data) || !isset($data['format']) || $data['format'] !== self::FORMAT
            || !isset($data['buttons']) || !is_array($data['buttons'])) {
            $this->back('invalid');
        }

        $title = isset($data['title']) && $data['title'] !== ''
            ? sanitize_text_field($data['title'])
            : __('Imported Menu', 'simple-floating-menu');

        $post_id = wp_insert_post(array(
            'post_type' => SFM_Standalone::POST_TYPE,
            'post_title' => $title,
            /* Arrives switched off, so it cannot appear on the site before
               anyone has looked at it. */
            'post_status' => 'draft',
        ), true);

        if (is_wp_error($post_id) || !$post_id) {
            $this->back('failed');
        }

        $settings = isset($data['settings']) && is_array($data['settings']) ? $data['settings'] : array();
        SFM_Standalone_Store::save_settings($post_id, array_merge(Simple_Floating_Menu::default_settings(), $settings));
        SFM_Standalone_Store::save_buttons($post_id, $data['buttons']);

        wp_safe_redirect(add_query_arg('sfm_notice', 'imported', SFM_Standalone_Page::url($post_id)));
        exit;
    }

    /**
     * @param string $notice
     */
    private function back($notice) {
        wp_safe_redirect(add_query_arg('sfm_notice', $notice, SFM_Standalone_Page::list_url()));
        exit;
    }
}
