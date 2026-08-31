<?php

/**
 * The starting designs a new menu can be built from.
 *
 * The premium plugin ships a catalogue of ready made menus; this one has the
 * three templates the front end can draw. The picker they are chosen with is
 * the same one, so the choice reads the same in both.
 *
 * @link       https://hashthemes.com
 * @since      1.4.0
 *
 * @package    Simple_Floating_Menu
 * @subpackage Simple_Floating_Menu/standalone
 */
class SFM_Standalone_Templates {

    /**
     * Every template, keyed by the class the front end puts on the menu.
     *
     * @since    1.4.0
     * @return   array
     */
    public static function all() {
        return array(
            'sfm-template-1' => array(
                'name' => __('Template 1', 'simple-floating-menu'),
                'facts' => array(
                    __('Pointer tooltip', 'simple-floating-menu'),
                    __('Classic', 'simple-floating-menu'),
                ),
            ),
            'sfm-template-2' => array(
                'name' => __('Template 2', 'simple-floating-menu'),
                'facts' => array(
                    __('Pointer tooltip', 'simple-floating-menu'),
                    __('Grows on hover', 'simple-floating-menu'),
                ),
            ),
            'sfm-template-3' => array(
                'name' => __('Template 3', 'simple-floating-menu'),
                'facts' => array(
                    __('Flush tooltip', 'simple-floating-menu'),
                    __('No pointer', 'simple-floating-menu'),
                ),
            ),
        );
    }

    /**
     * The preview picture for one template.
     *
     * @since    1.4.0
     * @param    string   $key
     * @return   string
     */
    public static function thumbnail($key) {
        $file = 'assets/images/templates/template' . str_replace('sfm-template-', '', $key) . '.jpg';

        return file_exists(SFM_PATH . $file) ? SFM_URL . $file : '';
    }

    /**
     * Load the picker's stylesheet.
     *
     * @since    1.4.0
     */
    public static function enqueue() {
        wp_enqueue_style('sfm-template-picker', SFM_URL . 'standalone/css/template-picker.css', array(), SFM_VERSION);
    }

    /**
     * The picker itself.
     *
     * @since    1.4.0
     * @param    array   $args
     */
    public static function render_picker($args = array()) {
        $args = wp_parse_args($args, array(
            'name' => 'sfm_template',
            'selected' => '',
            'id' => 'sfm-template-picker',
        ));

        $offered = self::all();
        $selected = isset($offered[$args['selected']]) ? $args['selected'] : key($offered);
        ?>
        <div class="sfm-tp" id="<?php echo esc_attr($args['id']); ?>">
            <div class="sfm-tp-grid">
                <?php foreach ($offered as $key => $entry) {
                    $input_id = $args['id'] . '-' . $key;
                    $thumbnail = self::thumbnail($key);
                    ?>
                    <div class="sfm-tp-card">
                        <input type="radio" class="sfm-tp-input"
                               id="<?php echo esc_attr($input_id); ?>"
                               name="<?php echo esc_attr($args['name']); ?>"
                               value="<?php echo esc_attr($key); ?>"
                               <?php checked($key, $selected); ?>/>

                        <label class="sfm-tp-label" for="<?php echo esc_attr($input_id); ?>">
                            <span class="sfm-tp-art" aria-hidden="true">
                                <?php if ($thumbnail) { ?>
                                    <img src="<?php echo esc_url($thumbnail); ?>" alt=""/>
                                <?php } ?>
                            </span>

                            <span class="sfm-tp-name"><?php echo esc_html($entry['name']); ?></span>

                            <span class="sfm-tp-facts">
                                <?php foreach ($entry['facts'] as $fact) { ?>
                                    <span class="sfm-tp-fact"><?php echo esc_html($fact); ?></span>
                                <?php } ?>
                            </span>
                        </label>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php
    }

}
