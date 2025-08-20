<?php
// File: includes/shortcodes.php

if (!defined('ABSPATH')) exit;

// 🔐 Login Form Shortcode
add_shortcode('pp_login_form', function () {
    ob_start();

    if (function_exists('pp_login_form_render')) {
        pp_login_form_render();
    } else {
        echo '<p>Login temporarily unavailable.</p>';
    }

    return ob_get_clean();
});

// 📊 Partner Dashboard Shortcode
add_shortcode('pp_dashboard', function () {
    $user_id = get_current_user_id();
    $user = wp_get_current_user();

    if (!$user_id || !$user instanceof WP_User) {
        return '<p>User authentication failed.</p>';
    }

    // ACF partner type field
    $partner_type = function_exists('get_field') ? get_field('partner_type', 'user_' . $user_id) : '';

    // Role detection
    $roles = (array) $user->roles;
    $is_partner   = in_array('partner', $roles, true);
    $is_admin     = in_array('administrator', $roles, true);
    $is_affiliate = $partner_type === 'affiliate';

    // Access control
    $has_access = ($is_partner && $is_affiliate) || $is_admin;

    if (!$has_access) {
        return sprintf(
            '<p>Access denied. You must be a partner affiliate or admin.<br>Role(s): %s<br>Partner Type: %s</p>',
            esc_html(implode(', ', $roles)),
            esc_html($partner_type)
        );
    }

    // Output dashboard
    ob_start();

    if (function_exists('pp_admin_dashboard_page')) {
        pp_admin_dashboard_page(); // should echo the dashboard HTML
    } else {
        echo '<p>Dashboard temporarily unavailable.</p>';
    }

    return ob_get_clean();
});
