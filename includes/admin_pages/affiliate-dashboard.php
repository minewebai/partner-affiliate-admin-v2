<?php
// File: includes/admin_pages/affiliate-dashboard.php

echo '<div style="position: fixed; bottom: 10px; right: 10px; background: green; color: white; padding: 6px 10px; z-index: 9999; font-weight: bold;">AFFILIATE DASHBOARD ACTIVE</div>';


if (!defined('ABSPATH')) exit;

function pp_affiliate_dashboard_render() {
    if (!is_user_logged_in()) {
        echo '<main class="page-content">';
        echo '<div class="st_content">';
        echo '<p class="st-alert" style="color:#fff;">' . esc_html__('Please log in to access your affiliate dashboard.', 'partner-portal') . '</p>';
        echo '</div>';
        echo '</main>';
        return;
    }

    $user = wp_get_current_user();
    $partner_type = function_exists('get_field') ? get_field('partner_type', 'user_' . $user->ID) : '';

    if (!in_array('partner', (array) $user->roles) || $partner_type !== 'affiliate') {
        echo '<main class="page-content">';
        echo '<div class="st_content">';
        echo '<p class="st-alert" style="color:#fff;">' . esc_html__('Access denied. You must be a partner affiliate or admin.', 'partner-portal') . '</p>';
        echo '</div>';
        echo '</main>';
        return;
    }

    echo '<main class="page-content">';
    echo '<div class="st_content affiliate-dashboard">';
    echo '<h2 style="color:#fff; font-weight:bold;">' . esc_html__('Affiliate Dashboard', 'partner-portal') . '</h2>';
    echo '<p style="color:#fff;">' . esc_html__('Welcome affiliate partner. Manage your bookings and commissions below.', 'partner-portal') . '</p>';

    // 👉 Optional: Add dashboard widgets/shortcodes here
    // echo do_shortcode('[pbp_affiliate_widgets]');

    // ✅ Debug label
    echo '<div style="position:absolute; bottom:20px; right:20px; background-color:#27ae60; color:#fff; font-weight:bold; padding:8px 12px; border-radius:4px; font-size:14px; box-shadow:0 0 6px rgba(0,0,0,0.2); z-index:999;">Affiliate Dashboard</div>';

    echo '</div>';
    echo '</main>';
}

add_shortcode('affiliate_dashboard', 'pp_affiliate_dashboard_render');
