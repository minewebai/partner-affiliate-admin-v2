<?php
// File: includes/admin_pages/account.php

if (!defined('ABSPATH')) exit;

function pp_admin_account_page() {
    $user_id = get_current_user_id();
    $user = get_userdata($user_id);

    $is_partner = PBP_Utils::is_partner($user_id);
    $is_affiliate = function_exists('get_field') && get_field('type', 'user_' . $user_id) === 'affiliate';

    if (!($is_partner && $is_affiliate)) {
        echo '<div class="wrap"><h1>' . esc_html__('Access Denied', 'partner-portal') . '</h1><p>' . esc_html__('You must be a verified partner affiliate to view this page.', 'partner-portal') . '</p></div>';
        return;
    }

    $commission = PBP_Utils::get_partner_commission($user_id);
    $allowed_tours = PBP_Utils::get_partner_allowed_posts($user_id, 'st_tours');
    $allowed_activities = PBP_Utils::get_partner_allowed_posts($user_id, 'st_activity');
    ?>

    <div class="wrap">
        <h1><?php echo esc_html__('Account Settings', 'partner-portal'); ?></h1>

        <table class="form-table">
            <tr>
                <th><?php esc_html_e('Username', 'partner-portal'); ?></th>
                <td><?php echo esc_html($user->user_login); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Email', 'partner-portal'); ?></th>
                <td><?php echo esc_html($user->user_email); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Commission Type', 'partner-portal'); ?></th>
                <td><?php echo esc_html($commission['type'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Commission Rate (%)', 'partner-portal'); ?></th>
                <td><?php echo esc_html($commission['rate'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Payout Schedule', 'partner-portal'); ?></th>
                <td><?php echo esc_html($commission['schedule'] ?? '-'); ?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Allowed Tours', 'partner-portal'); ?></th>
                <td>
                    <?php
                    if ($allowed_tours) {
                        echo implode(', ', array_map(function($id) {
                            $post = get_post($id);
                            return $post ? esc_html($post->post_title) : '';
                        }, $allowed_tours));
                    } else {
                        echo '<em>No tours assigned</em>';
                    }
                    ?>
                </td>
            </tr>
            <tr>
                <th><?php esc_html_e('Allowed Activities', 'partner-portal'); ?></th>
                <td>
                    <?php
                    if ($allowed_activities) {
                        echo implode(', ', array_map(function($id) {
                            $post = get_post($id);
                            return $post ? esc_html($post->post_title) : '';
                        }, $allowed_activities));
                    } else {
                        echo '<em>No activities assigned</em>';
                    }
                    ?>
                </td>
            </tr>
        </table>
    </div>
    <?php
}
