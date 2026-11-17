<?php
/**
 * One-time rename of saved Essential Icons classes.
 *
 * @package Simple_Floating_Menu
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('sfm_esi_names')) {

    // Essential names, from the shipped stylesheet.
    function sfm_esi_names() {
        static $names = null;

        if (null === $names) {
            $names = array();
            $css = @file_get_contents(SFM_PATH . 'assets/css/essentialicon.css'); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
            if ($css && preg_match_all('/\.esi-([a-z0-9-]+):before/', $css, $m)) {
                $names = array_flip($m[1]);
            }
        }

        return $names;
    }

}

if (!function_exists('sfm_esi_rename')) {

    // essentialicon-X / essential-icon-X -> esi-X.
    function sfm_esi_rename($value) {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = sfm_esi_rename($item);
            }
            return $value;
        }

        if (is_object($value)) {
            foreach (get_object_vars($value) as $key => $item) {
                $value->$key = sfm_esi_rename($item);
            }
            return $value;
        }

        if (!is_string($value) || (false === strpos($value, 'essentialicon-') && false === strpos($value, 'essential-icon-'))) {
            return $value;
        }

        $names = sfm_esi_names();

        return preg_replace_callback('/(?<![\w-])(?:essentialicon|essential-icon)-([a-z0-9-]+)(?![\w-])/', function ($match) use ($names) {
            return isset($names[$match[1]]) ? 'esi-' . $match[1] : $match[0];
        }, $value);
    }

}

if (!function_exists('sfm_migrate_esi_names')) {

    function sfm_migrate_esi_names() {
        if (get_option('sfm_esi_names_migrated') || !sfm_esi_names()) {
            return;
        }

        global $wpdb;

        // Menu settings.
        $settings = get_option('sfm_settings', null);
        if (null !== $settings) {
            $new = sfm_esi_rename($settings);
            if ($new !== $settings) {
                update_option('sfm_settings', $new);
            }
        }

        // Standalone menus, written by meta id so a revision never writes into its parent post.
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching -- One-time migration.
        $rows = $wpdb->get_results("SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key IN ('_sffm_items', '_sffm_config') AND (meta_value LIKE '%essentialicon-%' OR meta_value LIKE '%essential-icon-%')");
        foreach ((array) $rows as $row) {
            $old = maybe_unserialize($row->meta_value);
            $new = sfm_esi_rename($old);
            if ($new !== $old) {
                update_metadata_by_mid('post', $row->meta_id, $new);
            }
        }

        update_option('sfm_esi_names_migrated', 1, false);
    }

}
add_action('init', 'sfm_migrate_esi_names', 20);
