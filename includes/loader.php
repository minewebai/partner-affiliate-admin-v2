<?php
// File: includes/loader.php

if (!defined('ABSPATH')) exit;

// 🔌 Enqueue plugin assets
add_action('admin_enqueue_scripts', function($hook) {
    // Only load on our plugin pages
    if (!strpos($hook, 'pbp_')) return;

    wp_enqueue_style('pp_styles', PBP_PLUGIN_URL . 'includes/assets/partner-portal.css');
    wp_enqueue_script('pp_scripts', PBP_PLUGIN_URL . 'includes/assets/partner-portal.js', ['jquery'], null, true);

    // Pass AJAX data
    wp_localize_script('pp_scripts', 'pp_ajax', [
        'ajaxurl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('pp_ajax_nonce')
    ]);
});

// 🔄 Include booking AJAX handler
require_once PBP_PLUGIN_PATH . 'includes/ajax/booking-handler.php';

// 🎯 Register custom post type for partner bookings
add_action('init', function() {
    register_post_type('pp_booking', [
        'label'       => __('Partner Booking', 'partner-portal'),
        'public'      => false,
        'show_ui'     => true,
        'menu_icon'   => 'dashicons-tickets-alt',
        'supports'    => ['title', 'custom-fields'],
        'capability_type' => 'post',
        'map_meta_cap'    => true
    ]);
});
