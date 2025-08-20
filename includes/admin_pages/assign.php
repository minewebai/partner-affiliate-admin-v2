<?php
// File: includes/admin_pages/assign.php

if (!defined('ABSPATH')) exit;

function pp_admin_affiliate_hub_page() {
    $user_id = get_current_user_id();
    $is_admin = current_user_can('manage_options');
    $is_partner = current_user_can('partner');
    $is_affiliate = function_exists('get_field') && get_field('type', 'user_' . $user_id) === 'affiliate';

    if (!($is_partner && $is_affiliate) && !$is_admin) {
        echo '<div class="wrap"><h1>' . esc_html__('Access Denied', 'partner-portal') . '</h1><p>' . esc_html__('You do not have permission to view this page.', 'partner-portal') . '</p></div>';
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Affiliate Hub Overview', 'partner-portal'); ?></h1>
        <p><?php esc_html_e('Manage and assign commission roles, access settings, and partner visibility from here.', 'partner-portal'); ?></p>

        <ul>
            <li><a href="admin.php?page=pbp_commission_tiers"><?php esc_html_e('Commission Tiers', 'partner-portal'); ?></a></li>
            <li><a href="admin.php?page=pbp_affiliate_edit"><?php esc_html_e('Edit Partner Settings', 'partner-portal'); ?></a></li>
        </ul>
    </div>
    <?php
}
