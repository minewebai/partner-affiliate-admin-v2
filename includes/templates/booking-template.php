<?php
// File: includes/templates/booking-template.php

if (!defined('ABSPATH')) exit;

function pp_render_booking_modal($post_id, $type) {
    $post = get_post($post_id);
    if (!$post) return;

    ?>
    <div class="pp-booking-form">
        <h2><?php echo esc_html__('Booking Form for', 'partner-portal') . ' ' . esc_html($post->post_title); ?></h2>

        <form method="post" action="">
            <input type="hidden" name="booking_post_id" value="<?php echo esc_attr($post_id); ?>" />
            <input type="hidden" name="booking_type" value="<?php echo esc_attr($type); ?>" />

            <label><?php esc_html_e('Customer Name', 'partner-portal'); ?></label>
            <input type="text" name="customer_name" required />

            <label><?php esc_html_e('Customer Email', 'partner-portal'); ?></label>
            <input type="email" name="customer_email" required />

            <label><?php esc_html_e('Departure Date', 'partner-portal'); ?></label>
            <input type="date" name="departure_date" required />

            <label><?php esc_html_e('Fee (€)', 'partner-portal'); ?></label>
            <input type="number" name="fee" step="0.01" required />

            <button type="submit"><?php esc_html_e('Submit Booking', 'partner-portal'); ?></button>
        </form>
    </div>
    <?php
}
