<?php
// File: includes/admin_pages/bookings.php

if (!defined('ABSPATH')) exit;

function pp_admin_bookings_page() {
    $user_id = get_current_user_id();

    // 🔐 Access restriction for affiliate-type partners
    $user = wp_get_current_user();
    $partner_type = get_field('partner_type', 'user_' . $user_id);

    if (in_array('partner', (array) $user->roles) && $partner_type === 'affiliate') {
        wp_redirect(home_url('/affiliate-dashboard'));
        exit;
    }

    $bookings = PBP_Utils::get_user_bookings($user_id);
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('My Bookings', 'partner-portal'); ?></h1>

        <?php if (empty($bookings)) : ?>
            <p><?php esc_html_e('No bookings found.', 'partner-portal'); ?></p>
        <?php else : ?>
            <table class="pp-bookings-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e('ID', 'partner-portal'); ?></th>
                        <th><?php esc_html_e('Title', 'partner-portal'); ?></th>
                        <th><?php esc_html_e('Departure Date', 'partner-portal'); ?></th>
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
