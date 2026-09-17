<?php
/**
 * The Free vs Pro comparison: the intro card, the table and the notes under it.
 *
 * Shared by the Free vs Pro page and the Free vs Pro tab on the Simple
 * Floating Menu screen, so the two can never drift apart.
 *
 * Every row is something this plugin or the premium one actually does, so
 * the table stays honest as either changes: a row is true for included, false
 * for not included, or a short string where a number says more than a tick.
 *
 * @package Simple_Floating_Menu
 */

defined('ABSPATH') or die;

$sfm_pro_url = 'https://1.envato.market/LPXYao';
$sfm_demo_url = 'https://demo.hashthemes.com/super-floating-and-flying-menu/';

$sfm_comparison = array(
    array(
        'title' => __('Menus and designs', 'simple-floating-menu'),
        'rows' => array(
            array(__('Kinds of menu', 'simple-floating-menu'), __('Floating button bar', 'simple-floating-menu'), __('Floating bars, circular fans, one page navigation, side panels, skew panels, full screen and icon menus', 'simple-floating-menu')),
            array(__('Floating bar styles', 'simple-floating-menu'), '3', __('10, plus quarter, half and full circular', 'simple-floating-menu')),
            array(__('Ready made designs', 'simple-floating-menu'), '3', __('100+, imported in one click', 'simple-floating-menu')),
            array(__('Unlimited menus and buttons', 'simple-floating-menu'), true, true),
            array(__('Menu builder screen', 'simple-floating-menu'), true, true),
            array(__('Build a menu from a WordPress nav menu', 'simple-floating-menu'), false, true),
            array(__('Submenus', 'simple-floating-menu'), false, __('Multi level, with 7 open animations', 'simple-floating-menu')),
        ),
    ),
    array(
        'title' => __('Design', 'simple-floating-menu'),
        'rows' => array(
            array(__('8 screen positions with offsets', 'simple-floating-menu'), true, true),
            array(__('Button shapes', 'simple-floating-menu'), '9', '11'),
            array(__('Font icons', 'simple-floating-menu'), __('5 packs, 12,000+', 'simple-floating-menu'), __('7 packs, 13,000+', 'simple-floating-menu')),
            array(__('Your own image icons', 'simple-floating-menu'), false, true),
            array(__('Animated hamburger trigger icons', 'simple-floating-menu'), false, '16'),
            array(__('Colours per button, and 1,400+ Google fonts', 'simple-floating-menu'), true, true),
            array(__('Separate sizes and spacing for tablet and mobile', 'simple-floating-menu'), false, true),
        ),
    ),
    array(
        'title' => __('Animation', 'simple-floating-menu'),
        'rows' => array(
            array(__('Ways for a floating bar to appear', 'simple-floating-menu'), false, '12'),
            array(__('Hover and idle animations for the trigger button', 'simple-floating-menu'), false, __('29 hover, 12 idle', 'simple-floating-menu')),
            array(__('Panel entrance and exit animations', 'simple-floating-menu'), false, '37 + 37'),
            array(__('Full screen wave transitions', 'simple-floating-menu'), false, '6'),
        ),
    ),
    array(
        'title' => __('Where and when a menu shows', 'simple-floating-menu'),
        'rows' => array(
            array(__('Chosen pages, posts, archives, search and 404', 'simple-floating-menu'), true, true),
            array(__('Logged in visitors and chosen user roles', 'simple-floating-menu'), false, true),
            array(__('On a schedule of dates and weekdays', 'simple-floating-menu'), false, true),
            array(__('Hidden on desktop, tablet or mobile', 'simple-floating-menu'), false, true),
            array(__('Only after scrolling, and hidden while scrolling down', 'simple-floating-menu'), false, true),
            array(__('A different menu per language', 'simple-floating-menu'), false, true),
        ),
    ),
    array(
        'title' => __('Content', 'simple-floating-menu'),
        'rows' => array(
            array(__('Tooltips, and buttons that scroll to a section, the top or the bottom', 'simple-floating-menu'), true, true),
            array(__('Header logo, search form, social icons and footer text', 'simple-floating-menu'), false, true),
            array(__('Item descriptions and badges', 'simple-floating-menu'), false, true),
            array(__('Panel content built in Elementor', 'simple-floating-menu'), false, true),
        ),
    ),
    array(
        'title' => __('Workflow and support', 'simple-floating-menu'),
        'rows' => array(
            array(__('Export, import and duplicate a menu', 'simple-floating-menu'), true, true),
            array(__('Duplicate a menu as the other menu type', 'simple-floating-menu'), false, true),
            array(__('Serve Google fonts from your own server', 'simple-floating-menu'), true, true),
            array(__('Support', 'simple-floating-menu'), __('Community forum', 'simple-floating-menu'), __('Premium support', 'simple-floating-menu')),
        ),
    ),
);

if (!function_exists('sfm_comparison_cell')) {

    /**
     * One Free or Pro cell: a tick, a dash, or the text given.
     *
     * @param bool|string $value
     */
    function sfm_comparison_cell($value) {
        if ($value === true) {
            echo '<span class="dashicons dashicons-yes sfm-fvp-yes" aria-hidden="true"></span>';
            echo '<span class="screen-reader-text">' . esc_html__('Included', 'simple-floating-menu') . '</span>';
        } elseif ($value === false) {
            echo '<span class="dashicons dashicons-minus sfm-fvp-no" aria-hidden="true"></span>';
            echo '<span class="screen-reader-text">' . esc_html__('Not included', 'simple-floating-menu') . '</span>';
        } else {
            echo '<span class="sfm-fvp-text">' . esc_html($value) . '</span>';
        }
    }
}
?>
<div class="sfm-fvp-hero">
    <div class="sfm-fvp-hero-text">
        <p class="sfm-fvp-eyebrow"><?php esc_html_e('Super Floating & Flying Menu', 'simple-floating-menu'); ?></p>
        <h2 class="sfm-fvp-heading"><?php esc_html_e('Everything in Simple Floating Menu, and a lot more', 'simple-floating-menu'); ?></h2>
        <p class="sfm-fvp-lead"><?php esc_html_e('The premium version keeps every floating bar you have built and adds side panel, full screen and one page menus, over 100 ready made designs, animations, and full control over who sees each menu and when.', 'simple-floating-menu'); ?></p>
    </div>
    <div class="sfm-fvp-actions">
        <a class="button button-primary button-hero" href="<?php echo esc_url($sfm_pro_url); ?>" target="_blank" rel="noopener">
            <?php esc_html_e('Upgrade to Pro', 'simple-floating-menu'); ?>
        </a>
        <a class="button button-hero" href="<?php echo esc_url($sfm_demo_url); ?>" target="_blank" rel="noopener">
            <?php esc_html_e('See the Pro demos', 'simple-floating-menu'); ?>
        </a>
    </div>
</div>

<div class="sfm-fvp-table-wrap">
    <table class="sfm-fvp-table">
        <caption class="screen-reader-text"><?php esc_html_e('Features in Simple Floating Menu compared with Super Floating & Flying Menu', 'simple-floating-menu'); ?></caption>
        <thead>
            <tr>
                <th scope="col" class="sfm-fvp-feature"><?php esc_html_e('Feature', 'simple-floating-menu'); ?></th>
                <th scope="col" class="sfm-fvp-free"><?php esc_html_e('Free', 'simple-floating-menu'); ?></th>
                <th scope="col" class="sfm-fvp-pro">
                    <?php esc_html_e('Pro', 'simple-floating-menu'); ?>
                </th>
            </tr>
        </thead>
        <?php foreach ($sfm_comparison as $sfm_group) { ?>
            <tbody>
                <tr class="sfm-fvp-group">
                    <th scope="rowgroup" class="sfm-fvp-feature"><?php echo esc_html($sfm_group['title']); ?></th>
                    <td class="sfm-fvp-free"></td>
                    <td class="sfm-fvp-pro"></td>
                </tr>
                <?php foreach ($sfm_group['rows'] as $sfm_row) { ?>
                    <tr>
                        <th scope="row" class="sfm-fvp-feature"><?php echo esc_html($sfm_row[0]); ?></th>
                        <td class="sfm-fvp-free"><?php sfm_comparison_cell($sfm_row[1]); ?></td>
                        <td class="sfm-fvp-pro"><?php sfm_comparison_cell($sfm_row[2]); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        <?php } ?>
        <tfoot>
            <tr>
                <td class="sfm-fvp-feature"></td>
                <td class="sfm-fvp-free">
                    <span class="sfm-fvp-current"><?php esc_html_e('Your current plugin', 'simple-floating-menu'); ?></span>
                </td>
                <td class="sfm-fvp-pro">
                    <a class="button button-primary" href="<?php echo esc_url($sfm_pro_url); ?>" target="_blank" rel="noopener">
                        <?php esc_html_e('Upgrade to Pro', 'simple-floating-menu'); ?>
                    </a>
                </td>
            </tr>
        </tfoot>
    </table>
</div>

<p class="sfm-fvp-note">
    <?php esc_html_e('Menus built on the Floating Menus screen carry over as they are, with no import step, and this plugin stands down while the premium one is active. A bar built on the Simple Floating Menu screen moves across with the premium plugin\'s Import From Free.', 'simple-floating-menu'); ?>
</p>

<p class="sfm-fvp-note">
    <?php
    printf(
        /* translators: %s: support email address, as a link. */
        esc_html__('Questions before you buy? Email us at %s.', 'simple-floating-menu'),
        '<a href="mailto:support@hashthemes.com">support@hashthemes.com</a>'
    );
    ?>
</p>
