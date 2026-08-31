<?php

/**
 * The menu's dynamic CSS.
 *
 * Called with nothing it describes the menu configured on the settings screen,
 * exactly as it always has. A standalone menu passes its own settings and a
 * class of its own, so several menus can sit on one page without the last one
 * rewriting the sizing and position of the others.
 *
 * @param  array|null  $settings  Null for the settings screen's own menu.
 * @param  string      $scope     A class the rules are narrowed to.
 * @return string
 */
function sfm_dymanic_styles($settings = null, $scope = '') {
    $custom_css = "";
    $settings = is_array($settings) ? $settings : Simple_Floating_Menu::get_settings();

    $button_height = $settings['button_height'];
    $button_width = $settings['button_width'];
    $icon_size = $settings['icon_size'];
    $icon_position = $settings['icon_position'];
    $button_spacing = ($settings['button_spacing']) / 2;
    $top_offset = $settings['top_offset'];
    $bottom_offset = $settings['bottom_offset'];
    $left_offset = $settings['left_offset'];
    $right_offset = $settings['right_offset'];
    $buttons = $settings['buttons'];
    $zindex = $settings['zindex'];

    /* Published as variables too, so a template can size against them. */
    $custom_css .= ".sfm-floating-menu{--sfm-button-height:{$button_height}px; --sfm-button-width:{$button_width}px;}";
    $custom_css .= ".sfm-floating-menu a.sfm-shape-button{height:{$button_height}px; width:{$button_width}px;}";
    $custom_css .= ".sfm-floating-menu a.sfm-shape-button{font-size:{$icon_size}px;}";

    /* The menu's own colours. Each button may override any of them, and does so
       through a longer selector, so those rules win without !important. */
    if (!empty($settings['button_bg_color'])) {
        $custom_css .= ".sfm-floating-menu a.sfm-shape-button{background:{$settings['button_bg_color']}}";
    }

    if (!empty($settings['button_bg_color_hover'])) {
        $custom_css .= ".sfm-floating-menu .sfm-button:hover a.sfm-shape-button{background:{$settings['button_bg_color_hover']}}";
    }

    if (!empty($settings['button_icon_color'])) {
        $custom_css .= ".sfm-floating-menu a.sfm-shape-button{color:{$settings['button_icon_color']}}";
    }

    if (!empty($settings['button_icon_color_hover'])) {
        $custom_css .= ".sfm-floating-menu .sfm-button:hover a.sfm-shape-button{color:{$settings['button_icon_color_hover']}}";
    }

    if (!empty($settings['tooltip_bg_color'])) {
        $tip_bg = $settings['tooltip_bg_color'];
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip{background:{$tip_bg}}";
        $custom_css .= ".sfm-floating-menu.top-left.horizontal .sfm-tool-tip:after,
                        .sfm-floating-menu.top-middle.horizontal .sfm-tool-tip:after,
                        .sfm-floating-menu.top-right.horizontal .sfm-tool-tip:after{border-color: transparent transparent {$tip_bg} transparent;}";
        $custom_css .= ".sfm-floating-menu.top-left.vertical .sfm-tool-tip:after,
                        .sfm-floating-menu.top-middle.vertical .sfm-tool-tip:after,
                        .sfm-floating-menu.bottom-left.vertical .sfm-tool-tip:after,
                        .sfm-floating-menu.bottom-middle.vertical .sfm-tool-tip:after,
                        .sfm-floating-menu.middle-left.vertical .sfm-tool-tip:after{border-color: transparent {$tip_bg} transparent transparent;}";
        $custom_css .= ".sfm-floating-menu.top-right.vertical .sfm-tool-tip:after,
                        .sfm-floating-menu.middle-right.vertical .sfm-tool-tip:after,
                        .sfm-floating-menu.bottom-right.vertical .sfm-tool-tip:after{border-color: transparent transparent transparent {$tip_bg};}";
        $custom_css .= ".sfm-floating-menu.bottom-left.horizontal .sfm-tool-tip:after,
                        .sfm-floating-menu.bottom-middle.horizontal .sfm-tool-tip:after,
                        .sfm-floating-menu.bottom-right.horizontal .sfm-tool-tip:after,
                        .sfm-floating-menu.middle-left.horizontal .sfm-tool-tip:after,
                        .sfm-floating-menu.middle-right.horizontal .sfm-tool-tip:after{border-color: {$tip_bg} transparent transparent transparent;}";
    }

    if (!empty($settings['tooltip_bg_color_hover'])) {
        $custom_css .= ".sfm-floating-menu .sfm-button:hover .sfm-tool-tip{background:{$settings['tooltip_bg_color_hover']}}";
    }

    if (!empty($settings['tooltip_text_color'])) {
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip a{color:{$settings['tooltip_text_color']}}";
    }

    if (!empty($settings['tooltip_text_color_hover'])) {
        $custom_css .= ".sfm-floating-menu .sfm-button:hover .sfm-tool-tip a{color:{$settings['tooltip_text_color_hover']}}";
    }

    if (isset($settings['tooltip_border_radius']) && $settings['tooltip_border_radius'] !== '') {
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip{border-radius:{$settings['tooltip_border_radius']}px}";
    }

    if (isset($settings['tooltip_padding']) && is_array($settings['tooltip_padding'])) {
        /* A side left empty keeps whatever the stylesheet gives it, so each is
           written on its own rather than as one shorthand. */
        foreach (array('top', 'right', 'bottom', 'left') as $side) {
            $value = isset($settings['tooltip_padding'][$side]) ? $settings['tooltip_padding'][$side] : '';

            if ($value !== '') {
                $custom_css .= ".sfm-floating-menu .sfm-tool-tip a{padding-{$side}:{$value}px}";
            }
        }
    }
    $custom_css .= ".sfm-floating-menu i{top:{$icon_position}px}";
    $custom_css .= ".sfm-floating-menu.horizontal{margin:0 -{$button_spacing}px}";
    $custom_css .= ".sfm-floating-menu.vertical{margin:-{$button_spacing}px 0}";
    $custom_css .= ".sfm-floating-menu.horizontal .sfm-button{margin:0 {$button_spacing}px}";
    $custom_css .= ".sfm-floating-menu.vertical .sfm-button{margin:{$button_spacing}px 0}";
    $custom_css .= ".sfm-floating-menu.top-left, .sfm-floating-menu.top-right, .sfm-floating-menu.top-middle{top:{$top_offset}px}";
    $custom_css .= ".sfm-floating-menu.bottom-left, .sfm-floating-menu.bottom-right, .sfm-floating-menu.bottom-middle{bottom:{$bottom_offset}px}";
    $custom_css .= ".sfm-floating-menu.top-left, .sfm-floating-menu.bottom-left, .sfm-floating-menu.middle-left {left:{$left_offset}px}";
    $custom_css .= ".sfm-floating-menu.top-right, .sfm-floating-menu.bottom-right, .sfm-floating-menu.middle-right {right:{$right_offset}px}";
    $custom_css .= ".sfm-floating-menu{z-index:{$zindex};}";

    $buttons = $settings['buttons'];
    if ($buttons) {
        foreach ($buttons as $button) {
            $class = $button['id'];

            if (!empty($button['button_bg_color'])) {
                $button_bg_color = $button['button_bg_color'];
                $custom_css .= ".sfm-floating-menu .{$class} a.sfm-shape-button{background:{$button_bg_color}}";
            }

            if (!empty($button['button_icon_color'])) {
                $button_icon_color = $button['button_icon_color'];
                $custom_css .= ".sfm-floating-menu .{$class} a.sfm-shape-button{color:{$button_icon_color}}";
            }

            if (!empty($button['button_bg_color_hover'])) {
                $button_bg_color_hover = $button['button_bg_color_hover'];
                $custom_css .= ".sfm-floating-menu .{$class}:hover a.sfm-shape-button{background:{$button_bg_color_hover}}";
            }

            if (!empty($button['button_icon_color_hover'])) {
                $button_icon_color_hover = $button['button_icon_color_hover'];
                $custom_css .= ".sfm-floating-menu .{$class}:hover a.sfm-shape-button{color:{$button_icon_color_hover}}";
            }

            if (!empty($button['tooltip_bg_color'])) {
                $tooltip_bg_color = $button['tooltip_bg_color'];
                $custom_css .= ".sfm-floating-menu .{$class} .sfm-tool-tip{background:{$tooltip_bg_color}}";
                $custom_css .= ".sfm-floating-menu.top-left.horizontal .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.top-middle.horizontal .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.top-right.horizontal .{$class} .sfm-tool-tip:after{border-color: transparent transparent {$tooltip_bg_color} transparent;}";
                $custom_css .= ".sfm-floating-menu.top-left.vertical .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.top-middle.vertical .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.bottom-left.vertical .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.bottom-middle.vertical .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.middle-left.vertical .{$class} .sfm-tool-tip:after{border-color: transparent {$tooltip_bg_color} transparent transparent;}";
                $custom_css .= ".sfm-floating-menu.top-right.vertical .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.middle-right.vertical .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.bottom-right.vertical .{$class} .sfm-tool-tip:after{border-color: transparent transparent transparent {$tooltip_bg_color};}";
                $custom_css .= ".sfm-floating-menu.bottom-left.horizontal .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.bottom-middle.horizontal .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.bottom-right.horizontal .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.middle-left.horizontal .{$class} .sfm-tool-tip:after,
                                .sfm-floating-menu.middle-right.horizontal .{$class} .sfm-tool-tip:after{border-color: {$tooltip_bg_color} transparent transparent transparent;}";
            }

            if (!empty($button['tooltip_bg_color_hover'])) {
                $tooltip_bg_color_hover = $button['tooltip_bg_color_hover'];
                $custom_css .= ".sfm-floating-menu .{$class}:hover .sfm-tool-tip{background:{$tooltip_bg_color_hover}}";
                $custom_css .= ".sfm-floating-menu.top-left.horizontal .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.top-middle.horizontal .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.top-right.horizontal .{$class}:hover .sfm-tool-tip:after{border-color: transparent transparent {$tooltip_bg_color_hover} transparent;}";
                $custom_css .= ".sfm-floating-menu.top-left.vertical .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.top-middle.vertical .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.bottom-left.vertical .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.bottom-middle.vertical .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.middle-left.vertical .{$class}:hover .sfm-tool-tip:after{border-color: transparent {$tooltip_bg_color_hover} transparent transparent;}";
                $custom_css .= ".sfm-floating-menu.top-right.vertical .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.middle-right.vertical .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.bottom-right.vertical .{$class}:hover .sfm-tool-tip:after{border-color: transparent transparent transparent {$tooltip_bg_color_hover};}";
                $custom_css .= ".sfm-floating-menu.bottom-left.horizontal .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.bottom-middle.horizontal .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.bottom-right.horizontal .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.middle-left.horizontal .{$class}:hover .sfm-tool-tip:after,
                                .sfm-floating-menu.middle-right.horizontal .{$class}:hover .sfm-tool-tip:after{border-color: {$tooltip_bg_color_hover} transparent transparent transparent;}";
            }

            if (!empty($button['tooltip_text_color_hover'])) {
                $tooltip_text_color_hover = $button['tooltip_text_color_hover'];
                $custom_css .= ".sfm-floating-menu .{$class}:hover .sfm-tool-tip a{color:{$tooltip_text_color_hover}}";
            }

            if (!empty($button['tooltip_text_color'])) {
                $tooltip_text_color = $button['tooltip_text_color'];
                $custom_css .= ".sfm-floating-menu .{$class} .sfm-tool-tip a{color:{$tooltip_text_color}}";
            }
        }
    }

    if (isset($settings['tooltip_font']['family'])) {
        $tooltip_font_family = $settings['tooltip_font']['family'];
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip a{font-family:{$tooltip_font_family}}";
    }

    if (isset($settings['tooltip_font']['style'])) {
        $tooltip_font_style = $settings['tooltip_font']['style'];
        $font_italic = 'normal';
        if (strpos($tooltip_font_style, 'italic')) {
            $font_italic = 'italic';
        }

        $tooltip_font_weight = absint($tooltip_font_style);
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip a{font-weight:{$tooltip_font_weight}; font-style:{$font_italic}}";
    }

    if (isset($settings['tooltip_font']['transform'])) {
        $tooltip_font_transform = $settings['tooltip_font']['transform'];
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip a{text-transform:{$tooltip_font_transform}}";
    }

    if (isset($settings['tooltip_font']['decoration'])) {
        $tooltip_font_decoration = $settings['tooltip_font']['decoration'];
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip a{text-decoration:{$tooltip_font_decoration}}";
    }

    if (isset($settings['tooltip_font']['size'])) {
        $tooltip_font_size = $settings['tooltip_font']['size'];
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip a{font-size:{$tooltip_font_size}px}";
    }

    if (isset($settings['tooltip_font']['line_height'])) {
        $tooltip_font_line_height = $settings['tooltip_font']['line_height'];
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip a{line-height:{$tooltip_font_line_height}}";
    }

    if (isset($settings['tooltip_font']['letter_spacing'])) {
        $tooltip_font_letter_spacing = $settings['tooltip_font']['letter_spacing'];
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip a{letter-spacing:{$tooltip_font_letter_spacing}px}";
    }

    if (isset($settings['tooltip_font']['color'])) {
        $tooltip_font_color = $settings['tooltip_font']['color'];
        $custom_css .= ".sfm-floating-menu .sfm-tool-tip a{color:{$tooltip_font_color}}";
    }

    if (is_numeric($settings['button_shadow']['x'])) {
        $custom_css .= ".sfm-floating-menu .sfm-button{--sfm-button-shadow-x:{$settings['button_shadow']['x']}px;}";
    }

    if (is_numeric($settings['button_shadow']['y'])) {
        $custom_css .= ".sfm-floating-menu .sfm-button{--sfm-button-shadow-y:{$settings['button_shadow']['y']}px;}";
    }

    if (is_numeric($settings['button_shadow']['blur'])) {
        $custom_css .= ".sfm-floating-menu .sfm-button{--sfm-button-shadow-blur:{$settings['button_shadow']['blur']}px;}";
    }

    if ($settings['button_shadow']['color']) {
        $custom_css .= ".sfm-floating-menu .sfm-button{--sfm-button-shadow-color:{$settings['button_shadow']['color']};}";
    }

    /* Narrowed last, so every rule above is written once and in one style. */
    if ($scope !== '') {
        $custom_css = str_replace('.sfm-floating-menu', '.sfm-floating-menu.' . $scope, $custom_css);
    }

    return sfm_css_strip_whitespace($custom_css);
}

function sfm_css_strip_whitespace($css) {
    $replace = array(
        "#/\*.*?\*/#s" => "", // Strip C style comments.
        "#\s\s+#" => " ", // Strip excess whitespace.
    );
    $search = array_keys($replace);
    $css = preg_replace($search, $replace, $css);

    $replace = array(
        ": " => ":",
        "; " => ";",
        " {" => "{",
        " }" => "}",
        ", " => ",",
        "{ " => "{",
        ";}" => "}", // Strip optional semicolons.
        ",\n" => ",", // Don't wrap multiple selectors.
        "\n}" => "}", // Don't wrap closing braces.
        "} " => "}\n", // Put each rule on it's own line.
    );
    $search = array_keys($replace);
    $css = str_replace($search, $replace, $css);

    return trim($css);
}
