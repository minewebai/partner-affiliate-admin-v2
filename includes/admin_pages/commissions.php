<?php
// File: includes/admin_pages/commissions.php

if (!defined('ABSPATH')) exit;

function pp_admin_commissions_page() {
    $user_id = get_current_user_id();

    // 🔐 Access restriction: redirect affiliates
    $user = wp_get_current_user();
    $partner_type = get_field('partner_type', 'user_' . $user_id);
    if (in_array('partner', (array) $user->roles) && $partner_type === 'affiliate') {
        wp_redirect(home_url('/affiliate-dashboard'));
        exit;
    }

    $bookings = PBP_Commissions::get_details($user_id);
    $total = PBP_Commissions::get_total_for_user($user_id);
    ?>

    <div class="wrap">
        <h1><?php echo esc_html__('My Commissions', 'partner-portal'); ?></h1>

        <p><strong><?php esc_html_e('Total Commission Earned:', 'partner-portal'); ?></strong>
        <?php echo esc_html(number_format(floatval($total), 2)); ?> EUR</p>

        <?php if (empty($bookings)) : ?>
            <p><?php esc_html_e('No commission entries found.', 'partner-portal'); ?></p>
        <?php else : ?>
            <table class="pp-bookings-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('Booking ID', 'partner-portal'); ?></th>
                        <th><?php esc_html_e('Title', 'partner-portal'); ?></th>
                        <th><?php esc_html_e('Departure', 'partner-portal'); ?></th>
                        <th><?php esc_html_e('Fee', 'partner-portal'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $booking) :
                        $title = get_the_title($booking->ID);
                        $departure = get_post_meta($booking->ID, 'departure_date', true);
                        $fee = get_post_meta($booking->ID, 'fee', true);
                        ?>
                        <tr>
                            <td><?php echo esc_html($booking->ID); ?></td>
                            <td><?php echo esc_html($title); ?></td>
                            <td><?php echo esc_html(date('Y-m-d', intval($departure))); ?></td>
                            <td><?php echo esc_html(number_format(floatval($fee), 2)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <?php
}
