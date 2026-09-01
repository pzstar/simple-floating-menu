<?php

/**
 * The builder screen for one menu.
 *
 * The buttons on the left, the selected button's settings on the right, and
 * the menu's own design above them. Replaces the post editor for this type,
 * which has nothing useful to offer a menu.
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

class SFM_Standalone_Page {

    /**
     * How many entries of each post type the content picker offers.
     *
     * @since    1.4.0
     */
    const PICKER_LIMIT = 200;

    /**
     * Guards the page pickers' two requests.
     *
     * @since    1.4.0
     */
    const AJAX_NONCE = 'sfm-backend-ajax-nonce';

    /**
     * Guards the design panel's own save.
     *
     * @since    1.4.0
     */
    const DESIGN_NONCE = 'sfm_standalone_save_design';

    const PAGE_SLUG = 'sfm-menu-builder';

    const NONCE_ACTION = 'sfm_standalone_builder';

    const NONCE_NAME = 'sfm_standalone_builder_nonce';

    public function __construct() {
        add_action('admin_menu', array($this, 'add_page'), 12);
        add_action('admin_head', array($this, 'hide_page'));
        add_action('admin_enqueue_scripts', array($this, 'scripts'));
        add_action('admin_post_sfm_standalone_save', array($this, 'handle_save'));
        add_action('wp_ajax_sfm_standalone_save_design', array($this, 'handle_save_design'));
        add_action('wp_ajax_sfm_get_posts_by_query', array($this, 'get_posts_by_query'));
        add_action('wp_ajax_sfm_set_selected_options', array($this, 'set_selected_options'));
        add_action('admin_post_sfm_standalone_create', array($this, 'handle_create'));

        /* The post editor is not where one of these is edited, nor where one
           is made: Add New would otherwise open a bare editor and leave an
           empty auto draft behind every time it was clicked. */
        add_action('load-post.php', array($this, 'redirect_edit'));
        add_action('load-post-new.php', array($this, 'redirect_new'));
        add_filter('parent_file', array($this, 'parent_file'));
    }

    /**
     * The builder's address for one menu.
     *
     * @param  int $post_id
     * @return string
     */
    public static function url($post_id = 0) {
        $args = array('page' => self::PAGE_SLUG);

        if ($post_id) {
            $args['menu'] = intval($post_id);
        }

        return add_query_arg($args, admin_url('admin.php'));
    }

    /**
     * @return string
     */
    public static function list_url() {
        return add_query_arg('post_type', SFM_Standalone::POST_TYPE, admin_url('edit.php'));
    }

    /**
     * @return bool
     */
    public static function is_screen() {
        return isset($_GET['page']) && $_GET['page'] === self::PAGE_SLUG;
    }

    /**
     * What a page picker offers for what has been typed into it.
     *
     * The premium plugin's own picker, brought across as it stands: the same
     * two requests, the same shape of answer, so a menu is picked out by page
     * the same way in both.
     *
     * @since 1.4.0
     */
    public function get_posts_by_query() {
        check_ajax_referer(self::AJAX_NONCE, 'wp_nonce');

        if (!current_user_can('manage_options')) {
            return;
        }

        $search_string = isset($_POST['q']) ? sanitize_text_field(wp_unslash($_POST['q'])) : '';
        $post_type = isset($_POST['post_type']) ? sanitize_key($_POST['post_type']) : 'post';
        $results = array();

        add_filter('posts_where', array($this, 'title_filter'), 10, 2);
        $query = new WP_Query(array(
            'title_filter' => $search_string,
            'post_status' => 'publish',
            'post_type' => $post_type,
            'posts_per_page' => -1,
        ));
        remove_filter('posts_where', array($this, 'title_filter'), 10, 2);
        wp_reset_postdata();

        if (!isset($query->posts)) {
            return;
        }

        foreach ($query->posts as $post) {
            $results[] = array(
                'id' => $post->ID,
                'text' => $post->post_title,
            );
        }

        wp_send_json(array('results' => $results));
    }

    /**
     * Narrows a picker's search to titles.
     *
     * @param  string   $where
     * @param  WP_Query $wp_query
     * @return string
     */
    public function title_filter($where, $wp_query) {
        global $wpdb;

        if ($search_term = $wp_query->get('title_filter')) {
            $where .= ' AND ' . $wpdb->posts . '.post_title LIKE \'%' . esc_sql($wpdb->esc_like($search_term)) . '%\'';
        }

        return $where;
    }

    /**
     * The entries a picker is already set to, so it can name them.
     *
     * @since 1.4.0
     */
    public function set_selected_options() {
        check_ajax_referer(self::AJAX_NONCE, 'wp_nonce');

        if (!current_user_can('manage_options')) {
            return;
        }

        $post_type = isset($_POST['post_type']) ? sanitize_key($_POST['post_type']) : 'post';
        $selected_ids = isset($_POST['selected_ids']) ? sanitize_text_field(wp_unslash($_POST['selected_ids'])) : '';
        $results = array();

        $query = new WP_Query(array(
            'post_type' => $post_type,
            'post__in' => array_map('absint', explode(',', $selected_ids)),
            'posts_per_page' => -1,
        ));
        wp_reset_postdata();

        if (!isset($query->posts)) {
            return;
        }

        foreach ($query->posts as $post) {
            $results[] = array(
                'id' => $post->ID,
                'text' => $post->post_title,
            );
        }

        wp_send_json(array('results' => $results));
    }

    /**
     * @since 1.4.0
     */
    public function add_page() {
        add_submenu_page(
            SFM_Standalone_Admin::PARENT_SLUG,
            esc_html__('Edit Menu', 'simple-floating-menu'),
            esc_html__('Edit Menu', 'simple-floating-menu'),
            'manage_options',
            self::PAGE_SLUG,
            array($this, 'render')
        );
    }

    /**
     * Keeps the builder out of the sidebar; it is reached from the list.
     *
     * @since 1.4.0
     */
    public function hide_page() {
        remove_submenu_page(SFM_Standalone_Admin::PARENT_SLUG, self::PAGE_SLUG);
    }

    /**
     * @param  string $parent_file
     * @return string
     */
    public function parent_file($parent_file) {
        return self::is_screen() ? SFM_Standalone_Admin::PARENT_SLUG : $parent_file;
    }

    /**
     * Sends the post editor to the builder instead.
     *
     * @since 1.4.0
     */
    public function redirect_edit() {
        $post_id = isset($_GET['post']) ? intval($_GET['post']) : 0;

        if (!$post_id || get_post_type($post_id) != SFM_Standalone::POST_TYPE) {
            return;
        }

        wp_safe_redirect(self::url($post_id));
        exit;
    }

    /**
     * Sends Add New to the list, where the add panel is.
     *
     * @since 1.4.0
     */
    public function redirect_new() {
        $type = isset($_GET['post_type']) ? sanitize_key($_GET['post_type']) : '';

        if ($type != SFM_Standalone::POST_TYPE) {
            return;
        }

        wp_safe_redirect(add_query_arg('sfm_add', '1', self::list_url()));
        exit;
    }

    /**
     * @since 1.4.0
     */
    public function scripts() {
        if (!self::is_screen()) {
            return;
        }

        wp_enqueue_style('wp-color-picker');

        /* The design settings sit in the WordPress media modal, which is the
           frame the premium plugin's own stylesheet is written against. */
        wp_enqueue_style('media-views');

        /* Select2 turns the content picker into a search box, the library the
           premium plugin dresses that control with. */
        wp_enqueue_style('jquery-select2', SFM_URL . 'assets/css/select2.min.css', array(), '4.1.0');
        wp_enqueue_script('jquery-select2', SFM_URL . 'assets/js/select2.min.js', array('jquery'), '4.1.0', true);

        /* The premium plugin's own conditional fields plugin, which the
           display panel's lists are shown and hidden by. */
        wp_enqueue_script('jquery-condition', SFM_URL . 'assets/js/jquery-condition.js', array('jquery'), SFM_VERSION, true);

        /* Chosen stays for the typography selects, as it does in premium. */
        wp_enqueue_style('chosen', SFM_URL . 'assets/css/chosen.css', array(), SFM_VERSION);
        wp_enqueue_script('chosen', SFM_URL . 'assets/js/chosen.jquery.js', array('jquery'), SFM_VERSION, true);

        /* The five icon fonts, so a button's glyph shows in the list and in its
           editor rather than as an empty square. */
        wp_enqueue_style('fontawesome-6.3.0', SFM_URL . 'assets/css/fontawesome-6.3.0.css', array(), SFM_VERSION);
        wp_enqueue_style('eleganticons', SFM_URL . 'assets/css/eleganticons.css', array(), SFM_VERSION);
        wp_enqueue_style('iconfont', SFM_URL . 'assets/css/icofont.css', array(), SFM_VERSION);
        wp_enqueue_style('materialdesignicons', SFM_URL . 'assets/css/materialdesignicons.css', array(), SFM_VERSION);
        wp_enqueue_style('essentialicon', SFM_URL . 'assets/css/essentialicon.css', array(), SFM_VERSION);
        wp_enqueue_style('sfm-standalone-builder', SFM_URL . 'standalone/css/builder.css', array('wp-color-picker'), SFM_VERSION);

        /* The design modal picks a template with the same picker the menu list
           offers, so the stylesheet is needed here too. */
        SFM_Standalone_Templates::enqueue();

        wp_enqueue_script(
            'sfm-standalone-builder',
            SFM_URL . 'standalone/js/builder.js',
            array('jquery', 'jquery-ui-sortable', 'jquery-ui-slider', 'wp-color-picker', 'jquery-select2', 'jquery-condition', 'chosen'),
            SFM_VERSION,
            true
        );

        wp_localize_script('sfm-standalone-builder', 'sfmBuilder', array(
            'confirmRemove' => esc_html__('Remove this button?', 'simple-floating-menu'),
            'confirmLeave' => esc_html__('This menu has unsaved changes.', 'simple-floating-menu'),
            'untitled' => esc_html__('Untitled button', 'simple-floating-menu'),
            'searchContent' => esc_html__('Search your content', 'simple-floating-menu'),
            'menu' => isset($_GET['menu']) ? intval($_GET['menu']) : 0,
            'designNonce' => wp_create_nonce(self::DESIGN_NONCE),
            'designSaved' => esc_html__('Design saved.', 'simple-floating-menu'),
            'designFailed' => esc_html__('The design could not be saved.', 'simple-floating-menu'),
            'libraries' => SFM_Standalone_Store::icon_libraries(),
            'searchIcons' => esc_html__('Search icons', 'simple-floating-menu'),
            'ajaxNonce' => wp_create_nonce(self::AJAX_NONCE),
            'search' => esc_html__('Search', 'simple-floating-menu'),
            'noIcons' => esc_html__('No icons match that.', 'simple-floating-menu'),
        ));
    }

    /**
     * @since 1.4.0
     */
    public function render() {
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to edit menus.', 'simple-floating-menu'));
        }

        $post_id = isset($_GET['menu']) ? intval($_GET['menu']) : 0;
        $post = $post_id ? get_post($post_id) : null;

        if (!$post || $post->post_type != SFM_Standalone::POST_TYPE) {
            echo '<div class="wrap"><h1>' . esc_html__('Menu not found', 'simple-floating-menu') . '</h1>';
            printf('<p><a href="%s">%s</a></p></div>', esc_url(self::list_url()), esc_html__('Back to menus', 'simple-floating-menu'));
            return;
        }

        $settings = SFM_Standalone_Store::get_settings($post->ID);
        $buttons = SFM_Standalone_Store::get_buttons($post->ID);

        require __DIR__ . '/views/builder.php';
    }

    /**
     * Creates an empty menu and opens it.
     *
     * @since 1.4.0
     */
    public function handle_create() {
        check_admin_referer('sfm_standalone_create');

        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You are not allowed to create menus.', 'simple-floating-menu'));
        }

        $name = isset($_POST['sfm_menu_name']) ? sanitize_text_field(wp_unslash($_POST['sfm_menu_name'])) : '';

        if ($name === '') {
            wp_safe_redirect(add_query_arg('sfm_notice', 'name', self::list_url()));
            exit;
        }

        $post_id = wp_insert_post(array(
            'post_type' => SFM_Standalone::POST_TYPE,
            'post_title' => $name,
            /* A draft until somebody publishes it, so a half built menu
               cannot appear on the site by surprise. */
            'post_status' => 'draft',
        ), true);

        if (is_wp_error($post_id) || !$post_id) {
            wp_safe_redirect(add_query_arg('sfm_notice', 'failed', self::list_url()));
            exit;
        }

        SFM_Standalone_Store::save_settings($post_id, Simple_Floating_Menu::default_settings());
        SFM_Standalone_Store::save_buttons($post_id, array());

        wp_safe_redirect(add_query_arg('sfm_notice', 'created', self::url($post_id)));
        exit;
    }

    /**
     * @since 1.4.0
     */
    /**
     * Saves the design panels on their own.
     *
     * The premium plugin saves its design settings without leaving the page, so
     * this does the same: only the panels' own fields are sent, merged over
     * what is stored, and run through the plugin's sanitiser. The buttons are
     * carried along untouched so the sanitiser has the whole menu to work on.
     *
     * @since    1.4.0
     */
    public function handle_save_design() {
        check_ajax_referer(self::DESIGN_NONCE, 'nonce');

        $post_id = isset($_POST['menu']) ? intval($_POST['menu']) : 0;

        if (!$post_id || !current_user_can('manage_options') || get_post_type($post_id) != SFM_Standalone::POST_TYPE) {
            wp_send_json_error(array('message' => __('You are not allowed to edit this menu.', 'simple-floating-menu')));
        }

        $parsed = array();
        parse_str(isset($_POST['data']) ? wp_unslash($_POST['data']) : '', $parsed);

        $posted = isset($parsed['sfm']) && is_array($parsed['sfm']) ? $parsed['sfm'] : array();

        $defaults = Simple_Floating_Menu::default_settings();
        $stored = SFM_Standalone_Store::get_raw_settings($post_id);
        $stored = is_array($stored) ? $stored : array();

        $settings = array_merge($defaults, $stored);

        /* A grouped field posts only the keys the panel renders. */
        foreach (array('tooltip_font', 'button_shadow', 'tooltip_padding') as $group) {
            if (isset($posted[$group]) && is_array($posted[$group])) {
                $posted[$group] = array_merge($settings[$group], $posted[$group]);
            }
        }

        $settings = array_merge($settings, $posted);

        /* The sanitiser reads the buttons, so it is given the real ones. */
        $settings['buttons'] = SFM_Standalone_Store::get_buttons($post_id);

        $clean = Simple_Floating_Menu::get_instance()->sanitize_form($settings);

        SFM_Standalone_Store::save_settings($post_id, $clean);

        /* The card outside the modal names them the way the panels do. */
        $templates = SFM_Standalone_Templates::all();
        $positions = SFM_Standalone_Store::positions();
        $shapes = SFM_Standalone_Store::shapes();

        wp_send_json_success(array(
            'facts' => array_values(array_filter(array(
                isset($templates[$clean['template']]) ? $templates[$clean['template']]['name'] : '',
                isset($positions[$clean['position']]) ? $positions[$clean['position']] : '',
                isset($shapes[$clean['style']]) ? $shapes[$clean['style']] : '',
            ))),
        ));
    }

    public function handle_save() {
        $post_id = isset($_POST['menu']) ? intval($_POST['menu']) : 0;

        check_admin_referer(self::NONCE_ACTION, self::NONCE_NAME);

        if (!$post_id || !current_user_can('manage_options') || get_post_type($post_id) != SFM_Standalone::POST_TYPE) {
            wp_die(esc_html__('You are not allowed to edit this menu.', 'simple-floating-menu'));
        }

        $posted = isset($_POST['sfm']) && is_array($_POST['sfm']) ? wp_unslash($_POST['sfm']) : array();

        /* The plugin's own sanitiser, so a standalone menu is cleaned by
           exactly the same rules as the settings screen's menu. */
        $plugin = Simple_Floating_Menu::get_instance();
        $defaults = Simple_Floating_Menu::default_settings();

        /* Over what is stored rather than over the defaults: a picker that
           fills itself in only once its panel has been opened posts nothing
           until then, and that must leave what it holds alone. */
        $stored = SFM_Standalone_Store::get_raw_settings($post_id);
        $settings = array_merge($defaults, is_array($stored) ? $stored : array());

        /* A grouped field posts only the keys its panel renders, and a plain
           merge would drop the rest of the group. */
        foreach (array('tooltip_font', 'button_shadow', 'tooltip_padding') as $group) {
            if (isset($posted[$group]) && is_array($posted[$group])) {
                $posted[$group] = array_merge($settings[$group], $posted[$group]);
            }
        }

        $settings = $plugin->sanitize_form(array_merge($settings, $posted));

        /* The buttons come back out of the plugin's own sanitiser rather than
           being cleaned a second way here. The store still runs over them, to
           guarantee every button keeps an id of its own: the id is what its
           colours are keyed to in the CSS. */
        SFM_Standalone_Store::save_buttons($post_id, isset($settings['buttons']) ? $settings['buttons'] : array());
        SFM_Standalone_Store::save_settings($post_id, $settings);

        $title = isset($_POST['sfm_menu_title']) ? sanitize_text_field(wp_unslash($_POST['sfm_menu_title'])) : '';
        $status = isset($_POST['sfm_menu_status']) && $_POST['sfm_menu_status'] === 'publish' ? 'publish' : 'draft';

        wp_update_post(array(
            'ID' => $post_id,
            'post_title' => $title !== '' ? $title : get_the_title($post_id),
            'post_status' => $status,
        ));

        wp_safe_redirect(add_query_arg('sfm_notice', 'saved', self::url($post_id)));
        exit;
    }
}
