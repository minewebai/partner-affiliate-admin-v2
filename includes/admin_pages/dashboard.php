<?php
// File: includes/admin_pages/dashboard.php

if (!defined('ABSPATH')) exit;

function pp_admin_dashboard_page() {
    $user = wp_get_current_user();
    $partner_type = function_exists('get_field') ? get_field('partner_type', 'user_' . $user->ID) : '';

    // Check if the current user is a 'partner' with type 'affiliate'
    if (!in_array('partner', (array) $user->roles) || $partner_type !== 'affiliate') {
        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Access Denied', 'partner-portal') . '</h1>';
        echo '<p>' . esc_html__('You do not have permission to view this dashboard.', 'partner-portal') . '</p>';
        echo '</div>';
        return;
    }

    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Partner Dashboard', 'partner-portal') . '</h1>';
    echo '<p>' . esc_html__('Welcome to your partner dashboard. Use the sidebar to manage bookings, commissions, and account settings.', 'partner-portal') . '</p>';

    // Optional: Add custom dashboard widgets or booking stats here
    // echo '<div>Booking summary stats coming soon…</div>';

    echo '</div>';
}
