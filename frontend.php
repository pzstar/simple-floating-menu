<?php
if (!class_exists('Simple_Floating_Menu_Frontend')) {

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

        public function floating_menu_html() {
            if (!(defined('REST_REQUEST') && REST_REQUEST)) {
                $class = array('sfm-floating-menu');
                $settings = Simple_Floating_Menu::get_settings();
                $buttons = $settings['buttons'];
                $enable_sfm = $settings['enable_sfm'];
                $enable_sfm_setting = $settings['enable_sfm_setting'];
                $class[] = isset($settings['position']) && $settings['position'] ? $settings['position'] : '';
                $class[] = isset($settings['style']) && $settings['style'] ? $settings['style'] : '';
                $class[] = isset($settings['orientation']) && $settings['orientation'] ? $settings['orientation'] : '';
                $sfm_show_menu = (is_admin() || $enable_sfm == 'yes') && $buttons;
                if (apply_filters('sfm_before_floating_menu_render', $sfm_show_menu)) {
                    ?>
                    <div class="<?php echo esc_attr(implode(' ', $class)); ?>">
                        <?php if (current_user_can('administrator') && $enable_sfm_setting == 'yes') { ?>
                            <div class="sfm-button sfm-edit">
                                <div class="sfm-tool-tip"><a href="<?php echo admin_url('admin.php?page=simple-floating-menu') ?>"><?php echo esc_html__('Edit', 'simple-floating-menu') ?></a></div>
                                <a class="sfm-shape-button" target="_blank" href="<?php echo admin_url('admin.php?page=simple-floating-menu') ?>"><i class="icofont-gear"></i></a>
                            </div>
                        <?php } ?>

                        <?php
                        foreach ($buttons as $button) {
                            if ($button['url']) {
                                $target = isset($button['open_new_tab']) && $button['open_new_tab'] ? 'target="_blank"' : '';
                                $unique_id = $button['id'];
                                ?>
                                <div class="sfm-button <?php echo esc_attr($unique_id); ?>">
                                    <?php if ($button['tool_tip_text']) { ?>
                                        <div class="sfm-tool-tip"><a <?php echo $target; ?> href="<?php echo esc_url($button['url']) ?>"><?php echo esc_html($button['tool_tip_text']) ?></a></div>
                                    <?php } ?>
                                    <a class="sfm-shape-button" <?php echo $target; ?> href="<?php echo esc_url($button['url']) ?>"><i class="<?php echo esc_attr($button['icon']) ?>"></i></a>
                                </div>
                                <?php
                            }
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
            $subset = _x('no-subset', 'Add new subset (greek, cyrillic, devanagari, vietnamese)', 'simple-floating-menu');

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

            if (isset($settings['tooltip_font']['family'])) {
                $font_family = $settings['tooltip_font']['family'];
                if (!in_array($font_family, $standard_font_families)) {
                    $variants_array = $all_font[$font_family]['variants'];
                    $variants_keys = array_keys($variants_array);
                    $variants = implode(',', $variants_keys);
                    $fonts = $font_family . ':' . str_replace('italic', 'i', $variants);
                    $fonts_url = add_query_arg(array(
                        'family' => urlencode($fonts),
                        'subset' => urlencode($subsets),
                            ), 'https://fonts.googleapis.com/css');
                }
            }

            return $fonts_url;
        }

    }

}

if (!is_admin()) {
    new Simple_Floating_Menu_Frontend;
}
