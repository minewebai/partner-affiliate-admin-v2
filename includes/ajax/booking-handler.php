<?php
// File: includes/ajax/booking-handler.php

if (!defined('ABSPATH')) exit;

add_action('wp_ajax_pp_load_booking_form', function() {
    check_ajax_referer('pp_ajax_nonce', 'nonce');

    $post_id = intval($_POST['post_id'] ?? 0);
    $type = sanitize_text_field($_POST['booking_type'] ?? '');

    if (!$post_id || !$type) {
        wp_send_json_error('Invalid parameters.');
    }

    ob_start();
    pp_render_booking_modal($post_id, $type);
    $html = ob_get_clean();

    wp_send_json_success($html);
});
