<?php
// File: includes/commissions/class-commission.php

if (!defined('ABSPATH')) exit;

class PBP_Commissions {
    public static function get_details($user_id) {
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

    public static function get_total_for_user($user_id) {
        $is_partner = PBP_Utils::is_partner($user_id);
        $is_affiliate = function_exists('get_field') && get_field('type', 'user_' . $user_id) === 'affiliate';

        if (!($is_partner && $is_affiliate)) return 0;

        $bookings = self::get_details($user_id);
        $total = 0;

        foreach ($bookings as $booking) {
            $fee = get_post_meta($booking->ID, 'fee', true);
            $total += floatval($fee);
        }

        $commission = PBP_Utils::get_partner_commission($user_id);

        if ($commission['type'] === 'fixed') {
            return $total * (floatval($commission['rate']) / 100);
        }

        if ($commission['type'] === 'tiered') {
            $tiers = get_option('pp_commission_tiers', []);
            foreach ($tiers as $tier) {
                if ($total >= $tier['min'] && $total <= $tier['max']) {
                    return $total * (floatval($tier['rate']) / 100);
                }
            }
        }

        return 0;
    }
}
