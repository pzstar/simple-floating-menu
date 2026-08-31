<?php

/**
 * One row in the button list.
 *
 * @var int|string $sfm_index
 * @var array      $sfm_button
 *
 * @package Simple_Floating_Menu
 * @since   1.4.0
 */

defined('ABSPATH') or die;

/* The label names the button; the tooltip stands in while it is empty, which
   is how a menu built before the label existed still reads. */
$sfm_row_label = __('Untitled button', 'simple-floating-menu');

foreach (array('label', 'tool_tip_text') as $sfm_source) {
    if (isset($sfm_button[$sfm_source]) && $sfm_button[$sfm_source] !== '') {
        $sfm_row_label = $sfm_button[$sfm_source];
        break;
    }
}

$sfm_row_url = isset($sfm_button['url']) && $sfm_button['url'] !== ''
    ? $sfm_button['url']
    : __('No link', 'simple-floating-menu');
?>
<li class="sfm-row" data-index="<?php echo esc_attr($sfm_index); ?>">
    <input type="hidden" class="sfm-row-id" name="sfm[buttons][<?php echo esc_attr($sfm_index); ?>][id]"
           value="<?php echo esc_attr(isset($sfm_button['id']) ? $sfm_button['id'] : ''); ?>"/>

    <button type="button" class="sfm-row-handle"
            aria-label="<?php esc_attr_e('Reorder this button with the up and down arrow keys', 'simple-floating-menu'); ?>"
            title="<?php esc_attr_e('Drag, or use the up and down arrow keys', 'simple-floating-menu'); ?>">
        <span class="dashicons dashicons-menu" aria-hidden="true"></span>
    </button>

    <button type="button" class="sfm-row-open">
        <span class="sfm-row-icon"><i class="<?php echo esc_attr(isset($sfm_button['icon']) ? $sfm_button['icon'] : ''); ?>" aria-hidden="true"></i></span>
        <span class="sfm-row-text">
            <span class="sfm-row-label"><?php echo esc_html($sfm_row_label); ?></span>
            <span class="sfm-row-url"><?php echo esc_html($sfm_row_url); ?></span>
        </span>
    </button>

    <span class="sfm-row-tools">
        <button type="button" class="sfm-row-remove"
                aria-label="<?php esc_attr_e('Remove this button', 'simple-floating-menu'); ?>"
                title="<?php esc_attr_e('Remove', 'simple-floating-menu'); ?>">
            <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
        </button>
    </span>
</li>
