<?php
// File: includes/admin/admin.php

if (!defined('ABSPATH')) exit;

add_action('admin_menu', function() {
    if (!current_user_can('partner') && !current_user_can('manage_options')) return;

    // 🎯 Partner-side dashboard
    add_menu_page(
        __('Partner Portal', 'partner-portal'),
        __('Partner Portal', 'partner-portal'),
        'read',
        'pbp_dashboard',
        'pp_admin_dashboard_page',
        'dashicons-groups',
        30
    );

    add_submenu_page('pbp_dashboard', __('Book', 'partner-portal'), __('Book', 'partner-portal'), 'read', 'pbp_book', 'pp_admin_book_page');
    add_submenu_page('pbp_dashboard', __('Bookings', 'partner-portal'), __('Bookings', 'partner-portal'), 'read', 'pbp_bookings', 'pp_admin_bookings_page');
    add_submenu_page('pbp_dashboard', __('Commissions', 'partner-portal'), __('Commissions', 'partner-portal'), 'read', 'pbp_commissions', 'pp_admin_commissions_page');
    add_submenu_page('pbp_dashboard', __('Account', 'partner-portal'), __('Account', 'partner-portal'), 'read', 'pbp_account', 'pp_admin_account_page');

    // 🛠️ Admin-only tools
    if (current_user_can('manage_options')) {
        add_menu_page(
            __('Affiliate Hub', 'partner-portal'),
            __('Affiliate Hub', 'partner-portal'),
            'manage_options',
            'pbp_assign',
            'pp_admin_affiliate_hub_page',
            'dashicons-networking',
            31
        );
        add_submenu_page('pbp_assign', __('Edit Partner', 'partner-portal'), __('Edit Partner', 'partner-portal'), 'manage_options', 'pbp_affiliate_edit', 'pp_admin_tour_partner_edit_page');
        add_submenu_page('pbp_assign', __('Commission Tiers', 'partner-portal'), __('Commission Tiers', 'partner-portal'), 'manage_options', 'pbp_commission_tiers', 'pp_admin_commission_tiers_page');
    }
});
