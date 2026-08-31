<?php

/**
 * The settings for one button.
 *
 * @var int|string $sfm_index
 * @var array      $sfm_button
 * @var array      $sfm_colors
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

$sfm_name = 'sfm[buttons][' . $sfm_index . ']';
$sfm_id = 'sfm-b-' . $sfm_index;

$sfm_editor_title = __('Untitled button', 'simple-floating-menu');

foreach (array('label', 'tool_tip_text') as $sfm_source) {
    if (isset($sfm_button[$sfm_source]) && $sfm_button[$sfm_source] !== '') {
        $sfm_editor_title = $sfm_button[$sfm_source];
        break;
    }
}
?>
<div class="sfm-editor" data-index="<?php echo esc_attr($sfm_index); ?>" hidden>
    <div class="sfm-editor-head">
        <span class="sfm-editor-icon"><i class="<?php echo esc_attr(isset($sfm_button['icon']) ? $sfm_button['icon'] : ''); ?>" aria-hidden="true"></i></span>
        <h2 class="sfm-editor-title"><?php echo esc_html($sfm_editor_title); ?></h2>
    </div>

    <div class="sfm-section">
        <h3 class="sfm-section-title"><?php esc_html_e('Link', 'simple-floating-menu'); ?></h3>

        <div class="sfm-fields">
            <div class="sfm-field sfm-field-wide">
                <label for="<?php echo esc_attr($sfm_id); ?>-label">
                    <span class="sfm-field-label"><?php esc_html_e('Label', 'simple-floating-menu'); ?></span>
                </label>
                <input type="text" id="<?php echo esc_attr($sfm_id); ?>-label"
                       name="<?php echo esc_attr($sfm_name); ?>[label]"
                       class="sfm-input-label"
                       value="<?php echo esc_attr(isset($sfm_button['label']) ? $sfm_button['label'] : ''); ?>"/>
                <span class="sfm-field-help"><?php esc_html_e('Names the button in the list, and stands in for the tooltip when that is left empty.', 'simple-floating-menu'); ?></span>
            </div>

            <div class="sfm-field sfm-field-wide">
                <label for="<?php echo esc_attr($sfm_id); ?>-url">
                    <span class="sfm-field-label"><?php esc_html_e('URL', 'simple-floating-menu'); ?></span>
                </label>
                <input type="text" id="<?php echo esc_attr($sfm_id); ?>-url"
                       name="<?php echo esc_attr($sfm_name); ?>[url]"
                       class="sfm-input-url"
                       value="<?php echo esc_attr(isset($sfm_button['url']) ? $sfm_button['url'] : ''); ?>"
                       placeholder="https://"/>
                <span class="sfm-field-help"><?php esc_html_e('A page on your site, a full web address, mailto: or tel:, or #section for somewhere on the same page.', 'simple-floating-menu'); ?></span>
            </div>

            <div class="sfm-field">
                <label for="<?php echo esc_attr($sfm_id); ?>-classes">
                    <span class="sfm-field-label"><?php esc_html_e('CSS classes', 'simple-floating-menu'); ?></span>
                </label>
                <input type="text" id="<?php echo esc_attr($sfm_id); ?>-classes"
                       name="<?php echo esc_attr($sfm_name); ?>[classes]"
                       value="<?php echo esc_attr(isset($sfm_button['classes']) ? $sfm_button['classes'] : ''); ?>"/>
            </div>

            <div class="sfm-field">
                <label for="<?php echo esc_attr($sfm_id); ?>-attr-title">
                    <span class="sfm-field-label"><?php esc_html_e('Title attribute', 'simple-floating-menu'); ?></span>
                </label>
                <input type="text" id="<?php echo esc_attr($sfm_id); ?>-attr-title"
                       name="<?php echo esc_attr($sfm_name); ?>[attr_title]"
                       value="<?php echo esc_attr(isset($sfm_button['attr_title']) ? $sfm_button['attr_title'] : ''); ?>"/>
            </div>
        </div>
    </div>

    <div class="sfm-section">
        <h3 class="sfm-section-title"><?php esc_html_e('Icon', 'simple-floating-menu'); ?></h3>

        <div class="sfm-fields">
            <div class="sfm-field">
                <label for="<?php echo esc_attr($sfm_id); ?>-icon">
                    <span class="sfm-field-label"><?php esc_html_e('Choose Icon', 'simple-floating-menu'); ?></span>
                </label>

                <?php
                /* The picker itself is built once by the script and moved into
                   whichever field asked for it, so it is not in the markup. */
                ?>
                <div class="sfm-item-icon">
                    <span class="sfm-item-icon-preview"><i class="<?php echo esc_attr(isset($sfm_button['icon']) ? $sfm_button['icon'] : ''); ?>" aria-hidden="true"></i></span>

                    <input type="text" id="<?php echo esc_attr($sfm_id); ?>-icon"
                           name="<?php echo esc_attr($sfm_name); ?>[icon]"
                           class="sfm-item-icon-value"
                           value="<?php echo esc_attr(isset($sfm_button['icon']) ? $sfm_button['icon'] : ''); ?>"
                           autocomplete="off"/>

                    <button type="button" class="button sfm-item-icon-choose">
                        <?php esc_html_e('Choose', 'simple-floating-menu'); ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="sfm-section">
        <h3 class="sfm-section-title"><?php esc_html_e('Behaviour', 'simple-floating-menu'); ?></h3>

        <?php $sfm_action = isset($sfm_button['action']) ? $sfm_button['action'] : 'default'; ?>

        <div class="sfm-fields">
            <div class="sfm-field">
                <label for="<?php echo esc_attr($sfm_id); ?>-action">
                    <span class="sfm-field-label"><?php esc_html_e('Action', 'simple-floating-menu'); ?></span>
                </label>
                <select id="<?php echo esc_attr($sfm_id); ?>-action" class="sfm-input-action"
                        name="<?php echo esc_attr($sfm_name); ?>[action]">
                    <option value="default" <?php selected($sfm_action, 'default'); ?>><?php esc_html_e('Open the link', 'simple-floating-menu'); ?></option>
                    <option value="scroll_sectionid" <?php selected($sfm_action, 'scroll_sectionid'); ?>><?php esc_html_e('Scroll to Section ID', 'simple-floating-menu'); ?></option>
                    <option value="scroll_to_top" <?php selected($sfm_action, 'scroll_to_top'); ?>><?php esc_html_e('Back to Top', 'simple-floating-menu'); ?></option>
                    <option value="scroll_to_bottom" <?php selected($sfm_action, 'scroll_to_bottom'); ?>><?php esc_html_e('Scroll to Bottom', 'simple-floating-menu'); ?></option>
                </select>
            </div>

            <div class="sfm-field" data-when-action="scroll_sectionid" <?php echo $sfm_action === 'scroll_sectionid' ? '' : 'hidden'; ?>>
                <label for="<?php echo esc_attr($sfm_id); ?>-sectionid">
                    <span class="sfm-field-label"><?php esc_html_e('Section ID', 'simple-floating-menu'); ?></span>
                </label>
                <input type="text" id="<?php echo esc_attr($sfm_id); ?>-sectionid"
                       name="<?php echo esc_attr($sfm_name); ?>[scroll_sectionid]"
                       value="<?php echo esc_attr(isset($sfm_button['scroll_sectionid']) ? $sfm_button['scroll_sectionid'] : ''); ?>"/>
                <span class="sfm-field-help"><?php esc_html_e('Without the leading #.', 'simple-floating-menu'); ?></span>
            </div>

            <div class="sfm-field sfm-field-check" data-when-action="default" <?php echo $sfm_action === 'default' ? '' : 'hidden'; ?>>
                <?php
                /* An unticked box posts nothing, and the sanitiser would then
                   fall back to the default, which is on. The companion always
                   posts, and the box overrides it when ticked. */
                ?>
                <input type="hidden" name="<?php echo esc_attr($sfm_name); ?>[open_new_tab]" value="0"/>
                <label>
                    <input type="checkbox" name="<?php echo esc_attr($sfm_name); ?>[open_new_tab]" value="1"
                           <?php checked(!empty($sfm_button['open_new_tab'])); ?>/>
                    <span class="sfm-field-label"><?php esc_html_e('Open in a new tab', 'simple-floating-menu'); ?></span>
                </label>
            </div>
        </div>
    </div>

    <div class="sfm-section">
        <h3 class="sfm-section-title"><?php esc_html_e('Tooltip', 'simple-floating-menu'); ?></h3>

        <div class="sfm-fields">
            <div class="sfm-field sfm-field-check">
                <?php
                /* Absent on a menu saved before this field existed, and a
                   tooltip was shown then, so absent has to mean on. */
                $sfm_tooltip_on = !isset($sfm_button['tooltip_enable']) || $sfm_button['tooltip_enable'];
                ?>
                <input type="hidden" name="<?php echo esc_attr($sfm_name); ?>[tooltip_enable]" value="0"/>
                <label>
                    <input type="checkbox" name="<?php echo esc_attr($sfm_name); ?>[tooltip_enable]" value="1"
                           <?php checked($sfm_tooltip_on); ?>/>
                    <span class="sfm-field-label"><?php esc_html_e('Show Tooltip', 'simple-floating-menu'); ?></span>
                </label>
            </div>

            <div class="sfm-field">
                <label for="<?php echo esc_attr($sfm_id); ?>-tip">
                    <span class="sfm-field-label"><?php esc_html_e('Tooltip Text', 'simple-floating-menu'); ?></span>
                </label>
                <input type="text" id="<?php echo esc_attr($sfm_id); ?>-tip"
                       name="<?php echo esc_attr($sfm_name); ?>[tool_tip_text]"
                       class="sfm-input-tooltip"
                       value="<?php echo esc_attr(isset($sfm_button['tool_tip_text']) ? $sfm_button['tool_tip_text'] : ''); ?>"/>
                <span class="sfm-field-help"><?php esc_html_e('Falls back to the label when left empty.', 'simple-floating-menu'); ?></span>
            </div>
        </div>
    </div>

    <div class="sfm-section">
        <h3 class="sfm-section-title"><?php esc_html_e('Colours', 'simple-floating-menu'); ?></h3>

        <div class="sfm-fields">
            <?php foreach ($sfm_colors as $sfm_key => $sfm_label) { ?>
                <div class="sfm-field sfm-field-color">
                    <label for="<?php echo esc_attr($sfm_id . '-' . $sfm_key); ?>">
                        <span class="sfm-field-label"><?php echo esc_html($sfm_label); ?></span>
                    </label>
                    <input type="text" id="<?php echo esc_attr($sfm_id . '-' . $sfm_key); ?>"
                           name="<?php echo esc_attr($sfm_name); ?>[<?php echo esc_attr($sfm_key); ?>]"
                           class="sfm-color"
                           value="<?php echo esc_attr(isset($sfm_button[$sfm_key]) ? $sfm_button[$sfm_key] : ''); ?>"/>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
