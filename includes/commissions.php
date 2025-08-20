<?php
// File: includes/commissions.php

if (!defined('ABSPATH')) exit;

class PBP_Commissions {

    public static function get_total_for_user($user_id = null) {
        $user_id = $user_id ?: get_current_user_id();

        $is_partner = PBP_Utils::is_partner($user_id);
        $is_affiliate = function_exists('get_field') && get_field('type', 'user_' . $user_id) === 'affiliate';

        if (!($is_partner && $is_affiliate)) return 0;

        $bookings = PBP_Utils::get_user_bookings($user_id);
        $total = 0;

        foreach ($bookings as $booking) {
            $fee = get_post_meta($booking->ID, 'fee', true);
            $total += floatval($fee);
        }

        return $total;
    }

    public static function get_details($user_id = null) {
        $user_id = $user_id ?: get_current_user_id();

        $is_partner = PBP_Utils::is_partner($user_id);
        $is_affiliate = function_exists('get_field') && get_field('type', 'user_' . $user_id) === 'affiliate';

        return ($is_partner && $is_affiliate)
            ? PBP_Utils::get_user_bookings($user_id)
            : [];
    }
}
