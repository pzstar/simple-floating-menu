<?php
if (!class_exists('Simple_Floating_Menu_Frontend')) {

    /**
     * Turns a set of link attributes into an escaped attribute string.
     *
     * @since 1.4.0
     * @param array $link
     * @return string
     */
    function sfm_link_attributes($link) {
        $attributes = '';

        foreach ($link as $name => $value) {
            if ($name === 'class') {
                $value = implode(' ', array_filter($value));

                if ($value === '') {
                    continue;
                }
            }

            $value = $name === 'href' ? esc_url($value) : esc_attr($value);
            $attributes .= ' ' . $name . '="' . $value . '"';
        }

        return $attributes;
    }

    class Simple_Floating_Menu_Frontend {

        /**
         * Initialize the plugin.
         */
        public function __construct() {
            add_action('wp_footer', array($this, 'floating_menu_html'));

            // Add necesary CSS/JS
            add_action('wp_enqueue_scripts', array($this, 'load_scripts'));
        }

        public function load_scripts() {
            wp_enqueue_style('fontawesome-6.3.0', SFM_URL . 'assets/css/fontawesome-6.3.0.css', array(), SFM_VERSION);
            wp_enqueue_style('eleganticons', SFM_URL . 'assets/css/eleganticons.css', array(), SFM_VERSION);
            wp_enqueue_style('essentialicon', SFM_URL . 'assets/css/essentialicon.css', array(), SFM_VERSION);
            wp_enqueue_style('iconfont', SFM_URL . 'assets/css/icofont.css', array(), SFM_VERSION);
            wp_enqueue_style('materialdesignicons', SFM_URL . 'assets/css/materialdesignicons.css', array(), SFM_VERSION);
            wp_enqueue_style('sfm-style', SFM_URL . 'assets/css/style.css', array(), SFM_VERSION);
            wp_add_inline_style('sfm-style', sfm_dymanic_styles());

            wp_enqueue_script('sfm-custom-scripts', SFM_URL . 'assets/js/custom-scripts.js', array(), SFM_VERSION, true);
            $fonts_url = self::sfm_fonts_url();
            $settings = Simple_Floating_Menu::get_settings();
            $load_font_locally = $settings['sfm_load_google_font_locally'];

            if ($fonts_url && $load_font_locally == 'yes') {
                include_once SFM_PATH . 'inc/wptt-webfont-loader.php';
                $fonts_url = wptt_get_webfont_url($fonts_url);
            }

            // Load Fonts if necessary.
            if ($fonts_url) {
                wp_enqueue_style('sfm-fonts', $fonts_url, array(), SFM_VERSION);
            }
        }

        /**
         * Prints one floating menu.
         *
         * Called with nothing it prints the menu from the settings screen, as
         * it always has. A standalone menu passes its own settings and a class
         * of its own, which is what its CSS is narrowed to.
         *
         * @param array|null  $settings
         * @param string      $scope
         */
        public function floating_menu_html($settings = null, $scope = '') {
            if (!(defined('REST_REQUEST') && REST_REQUEST)) {
                $class = array('sfm-floating-menu');
                $standalone = is_array($settings);
                $settings = $standalone ? $settings : Simple_Floating_Menu::get_settings();

                if ($scope !== '') {
                    $class[] = $scope;
                }
                $buttons = $settings['buttons'];
                $enable_sfm = $settings['enable_sfm'];
                $enable_sfm_setting = $settings['enable_sfm_setting'];
                $class[] = isset($settings['template']) && $settings['template'] ? $settings['template'] : 'sfm-template-1';
                $scroll_offset = isset($settings['scroll_offset']) ? (int) $settings['scroll_offset'] : 0;
                $class[] = isset($settings['position']) && $settings['position'] ? $settings['position'] : '';
                $class[] = isset($settings['style']) && $settings['style'] ? $settings['style'] : '';
                $class[] = isset($settings['orientation']) && $settings['orientation'] ? $settings['orientation'] : '';
                $sfm_show_menu = (is_admin() || $enable_sfm == 'yes') && $buttons;
                if (apply_filters('sfm_before_floating_menu_render', $sfm_show_menu)) {
                    ?>
                    <div class="<?php echo esc_attr(implode(' ', $class)); ?>" data-scroll-offset="<?php echo esc_attr($scroll_offset); ?>">
                        <?php if (!$standalone && current_user_can('administrator') && $enable_sfm_setting == 'yes') { ?>
                            <div class="sfm-button sfm-edit">
                                <div class="sfm-tool-tip"><a href="<?php echo esc_url(admin_url('admin.php?page=simple-floating-menu')); ?>"><?php echo esc_html__('Edit', 'simple-floating-menu') ?></a></div>
                                <a class="sfm-shape-button" target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=simple-floating-menu')); ?>"><i class="icofont-gear"></i></a>
                            </div>
                        <?php } ?>

                        <?php
                        foreach ($buttons as $button) {
                            $action = isset($button['action']) ? $button['action'] : 'default';

                            /* A scrolling button needs no address, so only a
                               plain link is dropped for want of one. */
                            if ($action === 'default' && empty($button['url'])) {
                                continue;
                            }

                            $unique_id = $button['id'];

                            /* Absent on a menu saved before the field existed,
                               and a tooltip was shown then. */
                            $show_tooltip = !isset($button['tooltip_enable']) || $button['tooltip_enable'];

                            /* The tooltip says what the label says unless it
                               has been given words of its own. */
                            $tooltip_text = isset($button['tool_tip_text']) && $button['tool_tip_text'] !== ''
                                ? $button['tool_tip_text']
                                : (isset($button['label']) ? $button['label'] : '');

                            /* Shared by the button and its tooltip, so clicking
                               either does the same thing. The shape class is the
                               button's alone and is added to it below. */
                            $link = array('class' => array());

                            if (!empty($button['attr_title'])) {
                                $link['title'] = $button['attr_title'];
                            }

                            if ($action === 'scroll_sectionid') {
                                $section = isset($button['scroll_sectionid']) ? ltrim((string) $button['scroll_sectionid'], '#') : '';
                                $link['href'] = $section === '' ? '#' : '#' . $section;
                                $link['class'][] = 'sfm-scroll-to-section';
                            } elseif ($action === 'scroll_to_top') {
                                $link['href'] = '#';
                                $link['class'][] = 'sfm-scroll-to-top';
                            } elseif ($action === 'scroll_to_bottom') {
                                $link['href'] = '#';
                                $link['class'][] = 'sfm-scroll-to-bottom';
                            } else {
                                $link['href'] = $button['url'];

                                if (!empty($button['open_new_tab'])) {
                                    $link['target'] = '_blank';
                                    $link['rel'] = 'noopener';
                                }
                            }

                            $button_class = array('sfm-button', $unique_id);

                            if (!empty($button['classes'])) {
                                $button_class[] = $button['classes'];
                            }

                            $attributes = sfm_link_attributes($link);

                            $shape = $link;
                            array_unshift($shape['class'], 'sfm-shape-button');
                            $shape_attributes = sfm_link_attributes($shape);
                            ?>
                            <div class="<?php echo esc_attr(implode(' ', $button_class)); ?>">
                                <?php if ($show_tooltip && $tooltip_text !== '') { ?>
                                    <div class="sfm-tool-tip"><a<?php echo $attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped above. ?>><?php echo esc_html($tooltip_text) ?></a></div>
                                <?php } ?>
                                <a<?php echo $shape_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built and escaped above. ?>><i class="<?php echo esc_attr($button['icon']) ?>"></i></a>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                    <?php
                }
            }
        }

        public static function sfm_fonts_url() {
            $fonts_url = '';
            $settings = Simple_Floating_Menu::get_settings();
            $subsets = 'latin,latin-ext';

            /*
             * Translators: To add an additional character subset specific to your language,
             * translate this to 'greek', 'cyrillic', 'devanagari' or 'vietnamese'. Do not translate into your own language.
             */
            $subset = esc_html_x('no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', 'simple-floating-menu');

            if ('cyrillic' == $subset) {
                $subsets .= ',cyrillic,cyrillic-ext';
            } elseif ('greek' == $subset) {
                $subsets .= ',greek,greek-ext';
            } elseif ('devanagari' == $subset) {
                $subsets .= ',devanagari';
            } elseif ('vietnamese' == $subset) {
                $subsets .= ',vietnamese';
            }
            $standard_font_families = sfm_get_standard_font_families();
            $all_font = array_merge(sfm_standard_font_array(), sfm_google_font_array());

            /* Every family on the page, not only the one the settings screen
               sets: a standalone menu carries a tooltip font of its own, and
               without this its CSS would name a font nothing had fetched. */
            $families = array();

            if (isset($settings['tooltip_font']['family'])) {
                $families[] = $settings['tooltip_font']['family'];
            }

            if (class_exists('SFM_Standalone_Store')) {
                foreach (SFM_Standalone_Store::live_menus() as $sfm_menu) {
                    $menu_settings = SFM_Standalone_Store::get_settings($sfm_menu->ID);

                    if (isset($menu_settings['tooltip_font']['family'])) {
                        $families[] = $menu_settings['tooltip_font']['family'];
                    }
                }
            }

            $wanted = array();

            foreach (array_unique($families) as $font_family) {
                if ($font_family === '' || in_array($font_family, $standard_font_families) || !isset($all_font[$font_family])) {
                    continue;
                }

                $variants = implode(',', array_keys($all_font[$font_family]['variants']));
                $wanted[] = $font_family . ':' . str_replace('italic', 'i', $variants);
            }

            if ($wanted) {
                $fonts_url = add_query_arg(array(
                    /* Google takes several families in one request, separated
                       by a pipe. */
                    'family' => urlencode(implode('|', $wanted)),
                    'subset' => urlencode($subsets),
                ), 'https://fonts.googleapis.com/css');
            }

            return $fonts_url;
        }

    }

}

if (!is_admin()) {
    new Simple_Floating_Menu_Frontend;
}
