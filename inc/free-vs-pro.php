<?php
/**
 * The Free vs Pro page.
 *
 * @package Simple_Floating_Menu
 */

defined('ABSPATH') or die;
?>
<div class="sfm-title-bar">
    <h2>
        <i class="essentialicon-menu"></i>
        <?php esc_html_e('Free vs Pro', 'simple-floating-menu'); ?>
    </h2>
</div>

<div class="wrap sfm-fvp">
    <h1 class="screen-reader-text"><?php esc_html_e('Free vs Pro', 'simple-floating-menu'); ?></h1>

    <?php include SFM_PATH . 'inc/free-vs-pro-table.php'; ?>
</div>
