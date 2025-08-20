<?php
// File: includes/utils.php

if (!defined('ABSPATH')) exit;

class PBP_Utils {
    public static function get_partners() {
        $users = get_users(['role' => 'partner']);
        $filtered = [];

        foreach ($users as $user) {
            $type = function_exists('get_field') ? get_field('type', 'user_' . $user->ID) : null;
            if ($type === 'affiliate') {
                $filtered[] = $user;
            }
        }

        return $filtered;
    }

    public static function get_partner_commission($user_id) {
        return get_user_meta($user_id, '_pp_commission', true);
    }

    public static function set_partner_commission($user_id, $data) {
        update_user_meta($user_id, '_pp_commission', $data);
    }

    public static function get_partner_allowed_posts($user_id, $post_type) {
        return get_user_meta($user_id, "_pp_allowed_{$post_type}", true) ?: [];
    }

    public static function set_partner_allowed_posts($user_id, $post_type, $post_ids) {
        update_user_meta($user_id, "_pp_allowed_{$post_type}", array_filter($post_ids));
    }

    public static function get_user_bookings($user_id) {
        $is_partner = self::is_partner($user_id);
        $is_affiliate = function_exists('get_field') && get_field('type', 'user_' . $user_id) === 'affiliate';

        if (!($is_partner && $is_affiliate)) return [];

        return get_posts([
            'post_type'   => 'pp_booking',
            'numberposts' => -1,
            'meta_query'  => [
                [
                    'key'     => 'partner_id',
                    'value'   => $user_id,
                    'compare' => '='
                ]
            ]
        ]);
    }

    public static function is_partner($user_id) {
        $user = get_userdata($user_id);
        return $user && in_array('partner', (array) $user->roles);
    }
}
