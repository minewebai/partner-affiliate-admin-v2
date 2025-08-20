<?php
/*
Plugin Name: Partner Affiliate Admin
Description: Custom backend interface for partners and admins to manage bookings, commissions, and access.
Version: 2
Author: Rob
*/

if (!defined('ABSPATH')) exit;

define('PBP_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PBP_PLUGIN_URL', plugin_dir_url(__FILE__));

// 🔧 Load core plugin logic
require_once PBP_PLUGIN_PATH . 'includes/loader.php';
require_once PBP_PLUGIN_PATH . 'includes/utils.php';
require_once PBP_PLUGIN_PATH . 'includes/class-commission.php';
//require_once PBP_PLUGIN_PATH . 'includes/shortcodes.php';
require_once PBP_PLUGIN_PATH . 'includes/admin/admin.php';
require_once PBP_PLUGIN_PATH . 'includes/commission/tiers.php'; // ✅ CORRECTED path

// 🕒 Load admin pages safely after WordPress initializes
add_action('init', function() {
    require_once PBP_PLUGIN_PATH . 'includes/admin_pages/dashboard.php';
//  require_once PBP_PLUGIN_PATH . 'includes/admin_pages/book.php';
//    require_once PBP_PLUGIN_PATH . 'includes/admin_pages/bookings.php';
    require_once PBP_PLUGIN_PATH . 'includes/admin_pages/commissions.php';
//    require_once PBP_PLUGIN_PATH . 'includes/admin_pages/account.php';
    require_once PBP_PLUGIN_PATH . 'includes/admin_pages/assign.php';
    require_once PBP_PLUGIN_PATH . 'includes/admin_pages/partner-edit.php';
//   require_once PBP_PLUGIN_PATH . 'includes/admin_pages/login.php';
});

// 🔎 AJAX handler for dynamic Select2 search (partner-edit)
add_action('wp_ajax_pp_admin_search_posts', function() {
    check_ajax_referer('pp_admin_search_nonce', 'nonce');

    $term = sanitize_text_field($_POST['term'] ?? '');
    $type = sanitize_text_field($_POST['post_type'] ?? '');
    $results = [];

    if ($term && in_array($type, ['st_tours', 'st_activity'])) {
        $query = new WP_Query([
            'post_type' => $type,
            's' => $term,
            'posts_per_page' => 20
        ]);

        foreach ($query->posts as $post) {
            $lang = get_post_meta($post->ID, 'language', true);
            $results[] = [
                'id'       => $post->ID,
                'label'    => $post->post_title,
                'language' => $lang
            ];
        }
    }

    wp_send_json($results);
});