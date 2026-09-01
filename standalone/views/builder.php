<?php

/**
 * The builder screen.
 *
 * @var WP_Post $post
 * @var array   $settings
 * @var array   $buttons
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

$sfm_positions = SFM_Standalone_Store::positions();

$sfm_shapes = SFM_Standalone_Store::shapes();

$sfm_templates = array(
    'sfm-template-1' => __('Template 1', 'simple-floating-menu'),
    'sfm-template-2' => __('Template 2', 'simple-floating-menu'),
    'sfm-template-3' => __('Template 3', 'simple-floating-menu'),
);

/* The menu's own colours, which a button may override one by one. */
$sfm_menu_colors = array(
    'button' => array(
        'button_bg_color' => __('Background Color', 'simple-floating-menu'),
        'button_bg_color_hover' => __('Background Color (Hover)', 'simple-floating-menu'),
        'button_icon_color' => __('Icon Color', 'simple-floating-menu'),
        'button_icon_color_hover' => __('Icon Color (Hover)', 'simple-floating-menu'),
    ),
    'tooltip' => array(
        'tooltip_bg_color' => __('Background Color', 'simple-floating-menu'),
        'tooltip_bg_color_hover' => __('Background Color (Hover)', 'simple-floating-menu'),
        'tooltip_text_color' => __('Text Color', 'simple-floating-menu'),
        'tooltip_text_color_hover' => __('Text Color (Hover)', 'simple-floating-menu'),
    ),
);

$sfm_shadow_fields = array(
    'x' => __('X', 'simple-floating-menu'),
    'y' => __('Y', 'simple-floating-menu'),
    'blur' => __('Blur', 'simple-floating-menu'),
);

$sfm_numbers = array(
    'button_height' => array(__('Button Size (px)', 'simple-floating-menu'), 40, 200),
    'button_width' => array(__('Button Width (px)', 'simple-floating-menu'), 40, 200),
    'icon_size' => array(__('Font/Image Icon Size (px)', 'simple-floating-menu'), 10, 60),
    'icon_position' => array(__('Icon Offset (px)', 'simple-floating-menu'), -40, 40),
    'button_spacing' => array(__('Spacing Between Buttons (px)', 'simple-floating-menu'), 0, 200),
    'top_offset' => array(__('Offset from Top (px)', 'simple-floating-menu'), 0, 200),
    'bottom_offset' => array(__('Offset from Bottom (px)', 'simple-floating-menu'), 0, 200),
    'left_offset' => array(__('Offset from Left (px)', 'simple-floating-menu'), 0, 200),
    'right_offset' => array(__('Offset from Right (px)', 'simple-floating-menu'), 0, 200),
);

$sfm_colors = array(
    'button_bg_color' => __('Background', 'simple-floating-menu'),
    'button_bg_color_hover' => __('Background (hover)', 'simple-floating-menu'),
    'button_icon_color' => __('Icon', 'simple-floating-menu'),
    'button_icon_color_hover' => __('Icon (hover)', 'simple-floating-menu'),
    'tooltip_bg_color' => __('Tooltip Background', 'simple-floating-menu'),
    'tooltip_bg_color_hover' => __('Tooltip Background (hover)', 'simple-floating-menu'),
    'tooltip_text_color' => __('Tooltip Text', 'simple-floating-menu'),
    'tooltip_text_color_hover' => __('Tooltip Text (hover)', 'simple-floating-menu'),
);
?>
<div class="wrap sfm-page sfm-page-builder">

    <h1 class="screen-reader-text"><?php
        /* translators: %s: the menu being edited. */
        printf(esc_html__('Edit Menu: %s', 'simple-floating-menu'), esc_html($post->post_title));
    ?></h1>
    <hr class="wp-header-end"/>

    <?php
    if (!empty($_GET['sfm_notice'])) {
        $sfm_notices = array(
            'created' => array('success', __('Menu created. Add its buttons below.', 'simple-floating-menu')),
            'saved' => array('success', __('Menu saved.', 'simple-floating-menu')),
            'imported' => array('success', __('Menu imported.', 'simple-floating-menu')),
        );
        $sfm_key = sanitize_key($_GET['sfm_notice']);

        if (isset($sfm_notices[$sfm_key])) {
            printf(
                '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
                esc_attr($sfm_notices[$sfm_key][0]),
                esc_html($sfm_notices[$sfm_key][1])
            );
        }
    }
    ?>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" id="sfm-builder-form" class="sfm-builder">
        <input type="hidden" name="action" value="sfm_standalone_save"/>
        <input type="hidden" name="menu" value="<?php echo esc_attr($post->ID); ?>"/>
        <?php wp_nonce_field(SFM_Standalone_Page::NONCE_ACTION, SFM_Standalone_Page::NONCE_NAME); ?>

        <div class="sfm-topbar">
            <a class="sfm-topbar-back" href="<?php echo esc_url(SFM_Standalone_Page::list_url()); ?>">
                <span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span>
                <?php esc_html_e('Menus', 'simple-floating-menu'); ?>
            </a>

            <label class="sfm-topbar-name">
                <span class="screen-reader-text"><?php esc_html_e('Menu name', 'simple-floating-menu'); ?></span>
                <input type="text" id="sfm-menu-title" name="sfm_menu_title"
                       value="<?php echo esc_attr($post->post_title); ?>"
                       placeholder="<?php esc_attr_e('Menu name', 'simple-floating-menu'); ?>" required/>
            </label>

            <span class="sfm-badge sfm-badge-float"><?php esc_html_e('Floating Menu', 'simple-floating-menu'); ?></span>

            <label class="sfm-toggle" title="<?php esc_attr_e('Show this menu on the front end', 'simple-floating-menu'); ?>">
                <input type="checkbox" name="sfm_menu_status" value="publish" <?php checked($post->post_status, 'publish'); ?>/>
                <span class="sfm-toggle-track" aria-hidden="true"><span class="sfm-toggle-knob"></span></span>
                <span class="sfm-toggle-text"><?php esc_html_e('Enabled', 'simple-floating-menu'); ?></span>
            </label>

            <div class="sfm-topbar-actions">
                <button type="submit" class="button button-primary sfm-save"><?php esc_html_e('Save Menu', 'simple-floating-menu'); ?></button>
            </div>
        </div>

        <?php
        /* The design card. Everything that decides how the menu looks sits
           behind this one button, the way it does in the premium plugin, so the
           button list below is not competing with a wall of settings. */
        $sfm_template_number = str_replace('sfm-template-', '', $settings['template']);
        $sfm_thumbnail = SFM_PATH . 'assets/images/templates/template' . $sfm_template_number . '.jpg';
        $sfm_facts = array(
            isset($sfm_templates[$settings['template']]) ? $sfm_templates[$settings['template']] : '',
            isset($sfm_positions[$settings['position']]) ? $sfm_positions[$settings['position']] : '',
            isset($sfm_shapes[$settings['style']]) ? $sfm_shapes[$settings['style']] : '',
        );
        $sfm_facts = array_filter($sfm_facts);
        ?>
        <div class="sfm-design">
            <div class="sfm-design-preview" aria-hidden="true">
                <?php if (file_exists($sfm_thumbnail)) { ?>
                    <img src="<?php echo esc_url(SFM_URL . 'assets/images/templates/template' . $sfm_template_number . '.jpg'); ?>" alt=""/>
                <?php } else { ?>
                    <span class="dashicons dashicons-admin-customizer"></span>
                <?php } ?>
            </div>

            <div class="sfm-design-body">
                <h2><?php esc_html_e('Design', 'simple-floating-menu'); ?></h2>

                <?php if ($sfm_facts) { ?>
                    <p class="sfm-design-facts">
                        <?php foreach ($sfm_facts as $sfm_fact) { ?>
                            <span class="sfm-design-fact"><?php echo esc_html($sfm_fact); ?></span>
                        <?php } ?>
                    </p>
                <?php } ?>

                <p class="sfm-design-note">
                    <?php esc_html_e('Template, placement, button shape and size, shadow and tooltip type. Saved together with this menu.', 'simple-floating-menu'); ?>
                </p>
            </div>

            <div class="sfm-design-action">
                <button type="button" id="sfm-design-trigger" class="sfm-design-button">
                    <span class="dashicons dashicons-admin-customizer" aria-hidden="true"></span>
                    <?php esc_html_e('Edit Design Settings', 'simple-floating-menu'); ?>
                </button>
            </div>
        </div>

        <div class="sfm-builder-body">

            <div class="sfm-pane sfm-pane-list">
                <div class="sfm-pane-head">
                    <h2><?php esc_html_e('Buttons', 'simple-floating-menu'); ?></h2>
                    <button type="button" class="button button-small sfm-add-button">
                        <span class="dashicons dashicons-plus-alt2" aria-hidden="true"></span>
                        <?php esc_html_e('Add', 'simple-floating-menu'); ?>
                    </button>
                </div>

                <ul class="sfm-list" id="sfm-list">
                    <?php foreach ($buttons as $sfm_index => $sfm_button) {
                        require __DIR__ . '/button-row.php';
                    } ?>
                </ul>

                <p class="sfm-list-empty" <?php echo $buttons ? 'hidden' : ''; ?>>
                    <?php esc_html_e('No buttons yet. Add one, or pick something from your content below.', 'simple-floating-menu'); ?>
                </p>

                <div class="sfm-pane-foot">
                    <label class="screen-reader-text" for="sfm-content-picker"><?php esc_html_e('Add from content', 'simple-floating-menu'); ?></label>
                    <select class="sfm-picker" id="sfm-content-picker"
                            data-placeholder="<?php esc_attr_e('Search your content', 'simple-floating-menu'); ?>">
                        <option value=""><?php esc_html_e('Add from content&hellip;', 'simple-floating-menu'); ?></option>
                        <?php
                        foreach (get_post_types(array('public' => true), 'objects') as $sfm_type) {
                            $sfm_entries = get_posts(array(
                                'post_type' => $sfm_type->name,
                                'post_status' => 'publish',
                                'numberposts' => SFM_Standalone_Page::PICKER_LIMIT,
                                'orderby' => 'title',
                                'order' => 'ASC',
                            ));

                            if (!$sfm_entries) {
                                continue;
                            }
                            ?>
                            <optgroup label="<?php echo esc_attr($sfm_type->labels->name); ?>">
                                <?php foreach ($sfm_entries as $sfm_entry) { ?>
                                    <option value="<?php echo esc_url(get_permalink($sfm_entry->ID)); ?>"><?php
                                        echo esc_html($sfm_entry->post_title !== '' ? $sfm_entry->post_title : __('(no title)', 'simple-floating-menu'));
                                    ?></option>
                                <?php } ?>
                            </optgroup>
                            <?php
                        }
                        ?>
                    </select>

                    <p class="sfm-hint">
                        <?php esc_html_e('Drag to reorder. A floating menu shows every button in one row.', 'simple-floating-menu'); ?>
                    </p>
                </div>
            </div>

            <div class="sfm-pane sfm-pane-detail" id="sfm-detail">
                <?php foreach ($buttons as $sfm_index => $sfm_button) {
                    require __DIR__ . '/button-editor.php';
                } ?>

                <p class="sfm-detail-empty"><?php esc_html_e('Select a button on the left to edit it.', 'simple-floating-menu'); ?></p>
            </div>
        </div>

        <?php
        /* The design settings, in the premium plugin's own frame: the WordPress
           media modal shell it uses, a title naming the open panel, a menu
           column, the panels, and a toolbar. */
        ?>
        <div id="sfm-mega-menu" class="sfm-mega-menu" hidden>
            <div class="sfm-modal media-modal wp-core-ui">
                <button type="button" class="button-link media-modal-close sfm-modal-close" id="sfm-design-close">
                    <span class="media-modal-icon"><span class="screen-reader-text"><?php esc_html_e('Close', 'simple-floating-menu'); ?></span></span>
                </button>

                <div class="media-modal-content">
                    <div class="sfm-frame-title">
                        <h1 id="sfm-modal-title"><?php esc_html_e('Layout Settings', 'simple-floating-menu'); ?></h1>
                    </div>

                    <div class="sfm-frame-wrap">
                        <div class="sfm-frame-menu">
                            <div class="sfm-menu">
                                <a href="#" class="sfm-tab active" data-tab="layout"
                                   data-title="<?php esc_attr_e('Layout Settings', 'simple-floating-menu'); ?>"><?php esc_html_e('Layout', 'simple-floating-menu'); ?></a>
                                <a href="#" class="sfm-tab" data-tab="general"
                                   data-title="<?php esc_attr_e('General Settings', 'simple-floating-menu'); ?>"><?php esc_html_e('General', 'simple-floating-menu'); ?></a>
                                <a href="#" class="sfm-tab" data-tab="design"
                                   data-title="<?php esc_attr_e('Design Settings', 'simple-floating-menu'); ?>"><?php esc_html_e('Design', 'simple-floating-menu'); ?></a>
                                <a href="#" class="sfm-tab" data-tab="display"
                                   data-title="<?php esc_attr_e('Display Settings', 'simple-floating-menu'); ?>"><?php esc_html_e('Display', 'simple-floating-menu'); ?></a>
                            </div>
                        </div>

                        <div class="sfm-frame-content">
                            <div class="sfm-content">

                                <div class="sfm-panel active" data-panel="layout">
                                    <div class="sfm-settings-row">
                                        <label><?php esc_html_e('Select Template', 'simple-floating-menu'); ?></label>
                                        <div class="sfm-settings-fields">
                                            <?php
                                            SFM_Standalone_Templates::render_picker(array(
                                                'name' => 'sfm[template]',
                                                'id' => 'sfm-design-template-picker',
                                                'selected' => $settings['template'],
                                            ));
                                            ?>
                                        </div>
                                    </div>

                                    <div class="sfm-settings-row">
                                        <label><?php esc_html_e('Button Shape', 'simple-floating-menu'); ?></label>
                                        <div class="sfm-settings-fields">
                                            <select name="sfm[style]">
                                                <?php foreach ($sfm_shapes as $sfm_value => $sfm_label) { ?>
                                                    <option value="<?php echo esc_attr($sfm_value); ?>" <?php selected($settings['style'], $sfm_value); ?>><?php echo esc_html($sfm_label); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="sfm-panel" data-panel="general" hidden>
                                    <div class="sfm-settings-row">
                                        <label><?php esc_html_e('Menu Position', 'simple-floating-menu'); ?></label>
                                        <div class="sfm-settings-fields">
                                            <div class="sfm-settings-list-row">
                                                <div class="sfm-settings-list">
                                                    <select id="sfm-position" name="sfm[position]">
                                                        <?php foreach ($sfm_positions as $sfm_value => $sfm_label) { ?>
                                                            <option value="<?php echo esc_attr($sfm_value); ?>" <?php selected($settings['position'], $sfm_value); ?>><?php echo esc_html($sfm_label); ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <div class="sfm-settings-list">
                                                    <ul class="sfm-two-column-row">
                                                        <?php
                                                        $sfm_offset_sides = array(
                                                            'top_offset' => 'top-left,top-middle,top-right',
                                                            'bottom_offset' => 'bottom-left,bottom-middle,bottom-right',
                                                            'left_offset' => 'top-left,middle-left,bottom-left',
                                                            'right_offset' => 'top-right,middle-right,bottom-right',
                                                        );

                                                        foreach ($sfm_offset_sides as $sfm_key => $sfm_when) {
                                                            $sfm_spec = $sfm_numbers[$sfm_key];
                                                            $sfm_shown = in_array($settings['position'], explode(',', $sfm_when), true);
                                                            ?>
                                                            <li class="sfm-settings-list" data-when-position="<?php echo esc_attr($sfm_when); ?>" <?php echo $sfm_shown ? '' : 'hidden'; ?>>
                                                                <label for="sfm-<?php echo esc_attr($sfm_key); ?>"><?php echo esc_html($sfm_spec[0]); ?></label>
                                                                <div class="sfm-settings-fields">
                                                                    <input type="number" id="sfm-<?php echo esc_attr($sfm_key); ?>" name="sfm[<?php echo esc_attr($sfm_key); ?>]"
                                                                           min="<?php echo esc_attr($sfm_spec[1]); ?>" max="<?php echo esc_attr($sfm_spec[2]); ?>" step="1"
                                                                           value="<?php echo esc_attr($settings[$sfm_key]); ?>"/>
                                                                    <p class="sfm-description"><?php esc_html_e('Leave empty for default value', 'simple-floating-menu'); ?></p>
                                                                </div>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php $sfm_corners = 'top-left,top-right,bottom-left,bottom-right'; ?>
                                    <div class="sfm-settings-row" data-when-position="<?php echo esc_attr($sfm_corners); ?>" <?php echo in_array($settings['position'], explode(',', $sfm_corners), true) ? '' : 'hidden'; ?>>
                                        <label><?php esc_html_e('Orientation', 'simple-floating-menu'); ?></label>
                                        <div class="sfm-settings-fields">
                                            <select name="sfm[orientation]">
                                                <option value="horizontal" <?php selected($settings['orientation'], 'horizontal'); ?>><?php esc_html_e('Horizontal', 'simple-floating-menu'); ?></option>
                                                <option value="vertical" <?php selected($settings['orientation'], 'vertical'); ?>><?php esc_html_e('Vertical', 'simple-floating-menu'); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="sfm-settings-row">
                                        <label><?php esc_html_e('Button Sizes', 'simple-floating-menu'); ?></label>
                                        <div class="sfm-settings-fields">
                                            <div class="sfm-settings-list-row">
                                                <div class="sfm-settings-list">
                                                    <ul class="sfm-two-column-row">
                                                        <?php foreach (array('button_height', 'icon_size') as $sfm_key) {
                                                            $sfm_spec = $sfm_numbers[$sfm_key]; ?>
                                                            <li class="sfm-settings-list">
                                                                <label for="sfm-<?php echo esc_attr($sfm_key); ?>"><?php echo esc_html($sfm_spec[0]); ?></label>
                                                                <div class="sfm-settings-fields">
                                                                    <input type="number" id="sfm-<?php echo esc_attr($sfm_key); ?>" name="sfm[<?php echo esc_attr($sfm_key); ?>]"
                                                                           min="<?php echo esc_attr($sfm_spec[1]); ?>" max="<?php echo esc_attr($sfm_spec[2]); ?>" step="1"
                                                                           value="<?php echo esc_attr($settings[$sfm_key]); ?>"/>
                                                                    <p class="sfm-description"><?php esc_html_e('Leave empty for default value', 'simple-floating-menu'); ?></p>
                                                                </div>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            </div>

                                            <?php
                                            /* This plugin sizes buttons on two axes and can nudge the
                                               icon; premium offers one size and no nudge. These carry
                                               the rest through so a menu already using them is not
                                               flattened, and the width follows the size when it is set. */
                                            ?>
                                            <input type="hidden" id="sfm-button_width" name="sfm[button_width]" value="<?php echo esc_attr($settings['button_width']); ?>"/>
                                            <input type="hidden" name="sfm[icon_position]" value="<?php echo esc_attr($settings['icon_position']); ?>"/>
                                        </div>
                                    </div>

                                    <div class="sfm-settings-row">
                                        <label><?php esc_html_e('Spacing Between Buttons (px)', 'simple-floating-menu'); ?></label>
                                        <div class="sfm-settings-fields">
                                            <input type="number" name="sfm[button_spacing]"
                                                   min="<?php echo esc_attr($sfm_numbers['button_spacing'][1]); ?>" max="<?php echo esc_attr($sfm_numbers['button_spacing'][2]); ?>" step="1"
                                                   value="<?php echo esc_attr($settings['button_spacing']); ?>"/>
                                            <p class="sfm-description"><?php esc_html_e('Leave empty for default value', 'simple-floating-menu'); ?></p>
                                        </div>
                                    </div>

                                    <div class="sfm-settings-row">
                                        <label><?php esc_html_e('Scroll Top Offset (px)', 'simple-floating-menu'); ?></label>
                                        <div class="sfm-settings-fields">
                                            <input type="number" name="sfm[scroll_offset]" min="0" max="500" step="1"
                                                   value="<?php echo esc_attr($settings['scroll_offset']); ?>"/>
                                            <p class="sfm-description"><?php esc_html_e('The amount of top spacing when link is clicked to link to scroll to the section', 'simple-floating-menu'); ?></p>
                                        </div>
                                    </div>

                                    <div class="sfm-settings-row">
                                        <label><?php esc_html_e('Z-Index', 'simple-floating-menu'); ?></label>
                                        <div class="sfm-settings-fields">
                                            <input type="number" name="sfm[zindex]" step="1" value="<?php echo esc_attr($settings['zindex']); ?>"/>
                                            <p class="sfm-description"><?php esc_html_e('Leave empty for default value', 'simple-floating-menu'); ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="sfm-panel" data-panel="design" hidden>
                                    <div class="sfm-settings-row">
                                        <label><?php esc_html_e('Button', 'simple-floating-menu'); ?></label>
                                        <div class="sfm-settings-fields">
                                            <div class="sfm-settings-list-row">
                                                <div class="sfm-settings-list">
                                                    <ul class="sfm-color-fields">
                                                        <?php foreach ($sfm_menu_colors['button'] as $sfm_key => $sfm_label) { ?>
                                                            <li>
                                                                <label for="sfm-c-<?php echo esc_attr($sfm_key); ?>"><?php echo esc_html($sfm_label); ?></label>
                                                                <div class="sfm-color-input-field">
                                                                    <input type="text" id="sfm-c-<?php echo esc_attr($sfm_key); ?>" class="sfm-color"
                                                                           name="sfm[<?php echo esc_attr($sfm_key); ?>]" value="<?php echo esc_attr($settings[$sfm_key]); ?>"/>
                                                                </div>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>

                                                <div class="sfm-settings-list">
                                                    <label><?php esc_html_e('Shadow', 'simple-floating-menu'); ?></label>
                                                    <ul class="sfm-shadow-fields">
                                                        <?php foreach ($sfm_shadow_fields as $sfm_key => $sfm_label) { ?>
                                                            <li>
                                                                <input type="number" name="sfm[button_shadow][<?php echo esc_attr($sfm_key); ?>]" step="1"
                                                                       value="<?php echo esc_attr($settings['button_shadow'][$sfm_key]); ?>"/>
                                                                <label><?php echo esc_html($sfm_label); ?></label>
                                                            </li>
                                                        <?php } ?>
                                                        <li>
                                                            <div class="sfm-color-input-field">
                                                                <input type="text" class="sfm-color" name="sfm[button_shadow][color]"
                                                                       value="<?php echo esc_attr($settings['button_shadow']['color']); ?>"/>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sfm-settings-row">
                                        <label><?php esc_html_e('ToolTip', 'simple-floating-menu'); ?></label>
                                        <div class="sfm-settings-fields">
                                            <div class="sfm-settings-list-row">
                                                <div class="sfm-settings-list">
                                                    <ul class="sfm-color-fields">
                                                        <?php foreach ($sfm_menu_colors['tooltip'] as $sfm_key => $sfm_label) { ?>
                                                            <li>
                                                                <label for="sfm-c-<?php echo esc_attr($sfm_key); ?>"><?php echo esc_html($sfm_label); ?></label>
                                                                <div class="sfm-color-input-field">
                                                                    <input type="text" id="sfm-c-<?php echo esc_attr($sfm_key); ?>" class="sfm-color"
                                                                           name="sfm[<?php echo esc_attr($sfm_key); ?>]" value="<?php echo esc_attr($settings[$sfm_key]); ?>"/>
                                                                </div>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>

                                                <div class="sfm-settings-list">
                                                    <ul class="sfm-typography-fields">
                                                        <li>
                                                            <label for="sfm-tooltip-family"><?php esc_html_e('Font Family', 'simple-floating-menu'); ?></label>
                                                            <div class="sfm-typography-input-field">
                                                                <select id="sfm-tooltip-family" name="sfm[tooltip_font][family]">
                                                                    <optgroup label="<?php esc_attr_e('Standard fonts', 'simple-floating-menu'); ?>">
                                                                        <?php foreach (sfm_get_standard_font_families() as $sfm_font_name) { ?>
                                                                            <option value="<?php echo esc_attr($sfm_font_name); ?>" <?php selected($settings['tooltip_font']['family'], $sfm_font_name); ?>><?php echo esc_html($sfm_font_name); ?></option>
                                                                        <?php } ?>
                                                                    </optgroup>
                                                                    <optgroup label="<?php esc_attr_e('Google fonts', 'simple-floating-menu'); ?>">
                                                                        <?php foreach (sfm_get_google_font_families() as $sfm_font_name) { ?>
                                                                            <option value="<?php echo esc_attr($sfm_font_name); ?>" <?php selected($settings['tooltip_font']['family'], $sfm_font_name); ?>><?php echo esc_html($sfm_font_name); ?></option>
                                                                        <?php } ?>
                                                                    </optgroup>
                                                                </select>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <label for="sfm-tooltip-style"><?php esc_html_e('Font Style', 'simple-floating-menu'); ?></label>
                                                            <div class="sfm-typography-input-field">
                                                                <select id="sfm-tooltip-style" name="sfm[tooltip_font][style]">
                                                                    <?php foreach (sfm_get_font_weight_choices($settings['tooltip_font']['family']) as $sfm_value => $sfm_label) { ?>
                                                                        <option value="<?php echo esc_attr($sfm_value); ?>" <?php selected($settings['tooltip_font']['style'], $sfm_value); ?>><?php echo esc_html($sfm_label); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <label for="sfm-tooltip-transform"><?php esc_html_e('Text Transform', 'simple-floating-menu'); ?></label>
                                                            <div class="sfm-typography-input-field">
                                                                <select id="sfm-tooltip-transform" name="sfm[tooltip_font][transform]">
                                                                    <?php foreach (sfm_get_text_transform_choices() as $sfm_value => $sfm_label) { ?>
                                                                        <option value="<?php echo esc_attr($sfm_value); ?>" <?php selected($settings['tooltip_font']['transform'], $sfm_value); ?>><?php echo esc_html($sfm_label); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <label for="sfm-tooltip-decoration"><?php esc_html_e('Text Decoration', 'simple-floating-menu'); ?></label>
                                                            <div class="sfm-typography-input-field">
                                                                <select id="sfm-tooltip-decoration" name="sfm[tooltip_font][decoration]">
                                                                    <?php foreach (sfm_get_text_decoration_choices() as $sfm_value => $sfm_label) { ?>
                                                                        <option value="<?php echo esc_attr($sfm_value); ?>" <?php selected($settings['tooltip_font']['decoration'], $sfm_value); ?>><?php echo esc_html($sfm_label); ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <label for="sfm-tooltip-size"><?php esc_html_e('Font Size', 'simple-floating-menu'); ?></label>
                                                            <div class="sfm-typography-input-field sfm-range-slider-field">
                                                                <div class="sfm-range-slider"></div>
                                                                <input type="number" class="sfm-range-input-selector" id="sfm-tooltip-size"
                                                                       name="sfm[tooltip_font][size]" min="10" max="60" step="1"
                                                                       value="<?php echo esc_attr($settings['tooltip_font']['size']); ?>"/> <?php esc_html_e('px', 'simple-floating-menu'); ?>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <label for="sfm-tooltip-line-height"><?php esc_html_e('Line Height', 'simple-floating-menu'); ?></label>
                                                            <div class="sfm-settings-fields sfm-range-slider-field">
                                                                <div class="sfm-range-slider"></div>
                                                                <input type="number" class="sfm-range-input-selector" id="sfm-tooltip-line-height"
                                                                       name="sfm[tooltip_font][line_height]" min="0.5" max="5" step="0.1"
                                                                       value="<?php echo esc_attr($settings['tooltip_font']['line_height']); ?>"/>
                                                            </div>
                                                        </li>
                                                        <li>
                                                            <label for="sfm-tooltip-letter-spacing"><?php esc_html_e('Letter Spacing', 'simple-floating-menu'); ?></label>
                                                            <div class="sfm-settings-fields sfm-range-slider-field">
                                                                <div class="sfm-range-slider"></div>
                                                                <input type="number" class="sfm-range-input-selector" id="sfm-tooltip-letter-spacing"
                                                                       name="sfm[tooltip_font][letter_spacing]" min="-5" max="5" step="0.1"
                                                                       value="<?php echo esc_attr($settings['tooltip_font']['letter_spacing']); ?>"/> <?php esc_html_e('px', 'simple-floating-menu'); ?>
                                                            </div>
                                                        </li>
                                                    </ul>
                                                </div>

                                                <div class="sfm-settings-list">
                                                    <label><?php esc_html_e('Tool Tip Padding (px)', 'simple-floating-menu'); ?></label>
                                                    <ul class="sfm-shadow-fields">
                                                        <?php foreach (array('top' => __('Top', 'simple-floating-menu'), 'right' => __('Right', 'simple-floating-menu'), 'bottom' => __('Bottom', 'simple-floating-menu'), 'left' => __('Left', 'simple-floating-menu')) as $sfm_side => $sfm_label) { ?>
                                                            <li>
                                                                <input type="number" min="0" max="100" step="1"
                                                                       name="sfm[tooltip_padding][<?php echo esc_attr($sfm_side); ?>]"
                                                                       value="<?php echo esc_attr($settings['tooltip_padding'][$sfm_side]); ?>"/>
                                                                <label><?php echo esc_html($sfm_label); ?></label>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>

                                                <div class="sfm-settings-list">
                                                    <label><?php esc_html_e('Tool Tip Border Radius', 'simple-floating-menu'); ?></label>
                                                    <div class="sfm-settings-fields">
                                                        <div class="sfm-range-slider-field">
                                                            <div class="sfm-range-slider"></div>
                                                            <input type="number" class="sfm-range-input-selector" id="sfm-tip-radius"
                                                                   name="sfm[tooltip_border_radius]" min="0" max="200" step="1"
                                                                   value="<?php echo esc_attr($settings['tooltip_border_radius']); ?>"/> <?php esc_html_e('px', 'simple-floating-menu'); ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="sfm-panel" data-panel="display" hidden>
                                    <?php
                                    /* The premium plugin's own display panel, brought across field for
                                       field: the same rows, the same names, the same way of showing and
                                       hiding the lists under them. */
                                    $sfm_chosen_pages = array_map('intval', (array) $settings['specific_pages']);
                                    $sfm_chosen_types = (array) $settings['cpt_pages'];
                                    $sfm_chosen_archives = (array) $settings['specific_archive'];
                                    $sfm_chosen_list = implode(',', $sfm_chosen_pages);
                                    $sfm_selecting = in_array($settings['display_condition'], array('show_selected', 'hide_selected'), true);
                                    ?>
                                    <div class="sfm-settings-row">
                                        <label><?php esc_html_e('Show/Hide in Pages', 'simple-floating-menu') ?></label>
                                        <div class="sfm-settings-fields">
                                            <select name="sfm[display_condition]" data-condition="toggle" id="sfm-display-condition">
                                                <option value="show_all" <?php selected($settings['display_condition'], 'show_all'); ?>><?php esc_html_e('Show in All Pages', 'simple-floating-menu'); ?></option>
                                                <option value="hide_all" <?php selected($settings['display_condition'], 'hide_all'); ?>><?php esc_html_e('Hide in All Pages', 'simple-floating-menu'); ?></option>
                                                <option value="show_selected" <?php selected($settings['display_condition'], 'show_selected'); ?>><?php esc_html_e('Show in Selected Pages', 'simple-floating-menu'); ?></option>
                                                <option value="hide_selected" <?php selected($settings['display_condition'], 'hide_selected'); ?>><?php esc_html_e('Hide in Selected Pages', 'simple-floating-menu'); ?></option>
                                            </select>
                                        </div>
                                    </div>

                                    <div style="<?php echo $sfm_selecting ? '' : 'display: none'; ?>" data-condition-toggle="sfm-display-condition" data-condition-val="show_selected,hide_selected">
                                        <div class="sfm-settings-row">
                                            <label><?php esc_html_e('Default WordPress Pages', 'simple-floating-menu') ?></label>
                                            <div class="sfm-settings-fields sfm-setting-checkbox-field">
                                                <?php
                                                /* A box that came off would otherwise post nothing at all, and
                                                   the design panels are saved over what is already stored, so
                                                   each of these carries a partner that always posts. */
                                                ?>
                                                <p>
                                                    <input type="hidden" name="sfm[front_pages]" value="off"/>
                                                    <input type="checkbox" name="sfm[front_pages]" value="on" id="sfm_front_pages" <?php checked($settings['front_pages'], 'on'); ?>/>
                                                           <label for="sfm_front_pages"><?php esc_html_e('Front Page', 'simple-floating-menu'); ?></label>
                                                </p>

                                                <p>
                                                    <input type="hidden" name="sfm[blog_pages]" value="off"/>
                                                    <input type="checkbox" name="sfm[blog_pages]" value="on" id="sfm_blog_pages" <?php checked($settings['blog_pages'], 'on'); ?>/>
                                                           <label for="sfm_blog_pages"><?php esc_html_e('Home/Blog Page', 'simple-floating-menu'); ?></label>
                                                </p>

                                                <p>
                                                    <input type="hidden" name="sfm[archive_pages]" value="off"/>
                                                    <input type="checkbox" name="sfm[archive_pages]" value="on" id="sfm_archive_pages" <?php checked($settings['archive_pages'], 'on'); ?>/>
                                                           <label for="sfm_archive_pages"><?php esc_html_e('All Archive Page', 'simple-floating-menu'); ?></label>
                                                </p>

                                                <p>
                                                    <input type="hidden" name="sfm[error_pages]" value="off"/>
                                                    <input type="checkbox" name="sfm[error_pages]" value="on" id="sfm_404_pages" <?php checked($settings['error_pages'], 'on'); ?>/>
                                                           <label for="sfm_404_pages"><?php esc_html_e('404 Page', 'simple-floating-menu'); ?></label>
                                                </p>

                                                <p>
                                                    <input type="hidden" name="sfm[search_pages]" value="off"/>
                                                    <input type="checkbox" name="sfm[search_pages]" value="on" id="sfm_search_pages" <?php checked($settings['search_pages'], 'on'); ?>/>
                                                           <label for="sfm_search_pages"><?php esc_html_e('Search Page', 'simple-floating-menu'); ?></label>
                                                </p>

                                                <?php
                                                $sfm_post_types = get_post_types(array('public' => true));
                                                sort($sfm_post_types);
                                                foreach ($sfm_post_types as $sfm_post_type) {
                                                    if (!($sfm_post_type == 'attachment' || $sfm_post_type == SFM_Standalone::POST_TYPE) and get_posts(array('post_type' => $sfm_post_type))) {
                                                        ?>
                                                        <p>
                                                            <input type="checkbox" name="sfm[cpt_pages][]" class="sfm-hide-show-cpt-posts" id="sfm-hscpt-<?php echo esc_attr($sfm_post_type); ?>" data-posttype="<?php echo esc_attr($sfm_post_type); ?>" value="<?php echo esc_attr($sfm_post_type); ?>" <?php checked(in_array($sfm_post_type, $sfm_chosen_types)); ?>/>
                                                                   <label for="sfm-hscpt-<?php echo esc_attr($sfm_post_type); ?>"><?php echo esc_html('All ' . ucwords($sfm_post_type)); ?></label>
                                                        </p>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <?php
                                        $sfm_post_types = get_post_types(array('public' => true));
                                        sort($sfm_post_types);
                                        ?>
                                        <div class="sfm-settings-row sfm-hide-singular" id="sfm-show-archive" style="<?php echo $settings['archive_pages'] == 'on' ? 'display: none;' : ''; ?>">
                                            <label>
                                                <?php
                                                esc_html_e('Specific Archive Page', 'simple-floating-menu');
                                                ?>
                                            </label>

                                            <div class="sfm-settings-fields sfm-setting-checkbox-field">
                                                <?php
                                                foreach ($sfm_post_types as $sfm_post_type) {
                                                    if (!($sfm_post_type == 'attachment' || $sfm_post_type == SFM_Standalone::POST_TYPE)) {
                                                        ?>
                                                        <p>
                                                            <input type="checkbox" name="sfm[specific_archive][]" id="sfm-archive-<?php echo esc_attr($sfm_post_type); ?>" value="<?php echo esc_attr($sfm_post_type); ?>" <?php checked(in_array($sfm_post_type, $sfm_chosen_archives)); ?> />
                                                                   <label for="sfm-archive-<?php echo esc_attr($sfm_post_type); ?>"><?php echo esc_html(ucwords($sfm_post_type)); ?></label>
                                                        </p>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <?php
                                        foreach ($sfm_post_types as $sfm_post_type) {
                                            if (!($sfm_post_type == 'attachment' || $sfm_post_type == SFM_Standalone::POST_TYPE) and get_posts(array('post_type' => $sfm_post_type))) {
                                                ?>
                                                <div class="sfm-settings-row sfm-hide-singular" id="sfm-cpt-<?php echo esc_attr($sfm_post_type); ?>" style="<?php echo in_array($sfm_post_type, $sfm_chosen_types) ? 'display: none;' : ''; ?>">
                                                    <label>
                                                        <?php
                                                        esc_html_e('Specific ', 'simple-floating-menu');
                                                        echo esc_html(ucwords($sfm_post_type));
                                                        ?>
                                                    </label>
                                                    <div class="sfm-settings-fields sfm-specific-pages-ids" data-selected="<?php echo esc_attr($sfm_chosen_list); ?>" data-type="<?php echo esc_attr($sfm_post_type); ?>">
                                                        <select id="sfm-specific-<?php echo esc_attr($sfm_post_type); ?>" name="sfm[specific_pages][]" multiple></select>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                        }
                                        ?>

                                        <?php
                                        /* Emptying a list posts nothing of its own, so each one carries an
                                           entry the sanitiser drops again. The pages are the exception:
                                           their pickers fill themselves in when the panel is first opened,
                                           so until that has happened this entry stays out of the way and
                                           the stored list is left alone. */
                                        ?>
                                        <input type="hidden" name="sfm[cpt_pages][]" value=""/>
                                        <input type="hidden" name="sfm[specific_archive][]" value=""/>
                                        <input type="hidden" class="sfm-specific-pages-clear" name="sfm[specific_pages][]" value="" disabled/>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="sfm-frame-toolbar">
                        <div class="sfm-toolbar wp-clearfix">
                            <span class="spinner"></span>
                            <button type="button" class="button button-primary" id="sfm-design-save"><?php esc_html_e('Save Changes', 'simple-floating-menu'); ?></button>
                            <button type="button" class="button" id="sfm-design-done"><?php esc_html_e('Close', 'simple-floating-menu'); ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="media-modal-backdrop sfm-modal-backdrop"></div>
        </div>

        <?php
        /* Premium leaves the panels open after a save and says so with a
           notice that slides in and goes away on its own. */
        ?>
        <div class="sfm-alert">
            <span class="sfm-alert-message"></span>
            <button type="button" class="sfm-alert-close" aria-label="<?php esc_attr_e('Dismiss', 'simple-floating-menu'); ?>">
                <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
            </button>
        </div>

        <div class="sfm-footer">
            <?php $sfm_trash = get_delete_post_link($post->ID); ?>
            <?php if ($sfm_trash) { ?>
                <a class="sfm-trash" href="<?php echo esc_url(add_query_arg('_wp_http_referer', rawurlencode(SFM_Standalone_Page::list_url()), $sfm_trash)); ?>">
                    <?php esc_html_e('Move to Trash', 'simple-floating-menu'); ?>
                </a>
            <?php } else { ?>
                <span></span>
            <?php } ?>

            <button type="submit" class="button button-primary"><?php esc_html_e('Save Menu', 'simple-floating-menu'); ?></button>
        </div>
    </form>

    <?php
    /* The row and editor markup the script clones when a button is added. */
    ?>
    <script type="text/html" id="tmpl-sfm-row"><?php
        $sfm_index = '__i__';
        $sfm_button = array('id' => '', 'icon' => '', 'url' => '', 'tool_tip_text' => '');
        require __DIR__ . '/button-row.php';
    ?></script>

    <script type="text/html" id="tmpl-sfm-editor"><?php
        $sfm_index = '__i__';
        $sfm_button = array_merge(
            array('id' => '', 'icon' => '', 'url' => '', 'tool_tip_text' => '', 'open_new_tab' => false),
            array_fill_keys(array_keys($sfm_colors), '')
        );
        require __DIR__ . '/button-editor.php';
    ?></script>
</div>
