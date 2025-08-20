<?php
// File: includes/booking.php

if (!defined('ABSPATH')) exit;

class PBP_Booking {

    public static function get_count_for_user($user_id = null) {
        $user_id = $user_id ?: get_current_user_id();

        $is_partner = PBP_Utils::is_partner($user_id);
        $is_affiliate = function_exists('get_field') && get_field('type', 'user_' . $user_id) === 'affiliate';

        return ($is_partner && $is_affiliate)
            ? count(PBP_Utils::get_user_bookings($user_id))
            : 0;
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
