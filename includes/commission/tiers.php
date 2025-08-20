<?php
// File: includes/commission/tiers.php

if (!defined('ABSPATH')) exit;

function pp_admin_commission_tiers_page() {
    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Commission Tiers per Partner', 'partner-portal') . '</h1>';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['partner_tier_submit'])) {
        $partner_key = sanitize_text_field($_POST['partner_key']);
        $raw_input = wp_unslash($_POST['tier_lines']);
        $lines = array_filter(array_map('trim', explode("\n", $raw_input)));
        $json = [];

        foreach ($lines as $line) {
            if (preg_match('/^(\d+|\d+\+)-?(\d+)?\s*:\s*(\d+)%$/', $line, $matches)) {
                $min = intval($matches[1]);
                $max = isset($matches[2]) && is_numeric($matches[2]) ? intval($matches[2]) : null;
                $rate = $matches[3] . '%';
                $json[] = ['min' => $min, 'max' => $max, 'rate' => $rate];
            }
        }

        if (!empty($partner_key) && count($json) > 0) {
            update_option('pp_tiers_' . $partner_key, wp_json_encode($json, JSON_PRETTY_PRINT));
            echo '<div class="notice notice-success"><p>' . esc_html__('Tier saved successfully.', 'partner-portal') . '</p></div>';
        } else {
            echo '<div class="notice notice-error"><p>' . esc_html__('Invalid format. Please check your lines.', 'partner-portal') . '</p></div>';
        }
    }

    // Show editor if editing
    $current_key = isset($_GET['edit']) ? sanitize_text_field($_GET['edit']) : '';
    $raw_json = $current_key ? get_option('pp_tiers_' . $current_key, '') : '';
    $json_data = json_decode($raw_json, true);
    $line_format = '';

    if (is_array($json_data)) {
        foreach ($json_data as $tier) {
            $min = $tier['min'];
            $max = isset($tier['max']) && is_numeric($tier['max']) ? $tier['max'] : '+';
            $rate = $tier['rate'];
            $line_format .= "{$min}-{$max}:{$rate}\n";
        }
    }

    echo '<form method="post">';
    echo '<label><strong>' . esc_html__('Partner Identifier:', 'partner-portal') . '</strong></label><br />';
    echo '<input type="text" name="partner_key" required value="' . esc_attr($current_key) . '" placeholder="e.g. amy-travel" /><br /><br />';

    echo '<label><strong>' . esc_html__('Commission Tiers (one per line):', 'partner-portal') . '</strong></label><br />';
    echo '<textarea name="tier_lines" rows="10" cols="80" required placeholder="e.g. 1-5:10%">' . esc_textarea($line_format) . '</textarea><br /><br />';

    echo '<button type="submit" name="partner_tier_submit">' . esc_html__('Save Tier', 'partner-portal') . '</button>';
    echo '</form>';

    echo '<hr />';

    // Existing tiers list
    echo '<h2>' . esc_html__('Existing Partner Tier Groups', 'partner-portal') . '</h2>';
    foreach (wp_load_alloptions() as $key => $value) {
        if (strpos($key, 'pp_tiers_') === 0) {
            $partner = str_replace('pp_tiers_', '', $key);
            echo '<p><strong>' . esc_html($partner) . '</strong> — ';
            echo '<a href="?page=pbp_commission_tiers&edit=' . urlencode($partner) . '">' . esc_html__('Edit', 'partner-portal') . '</a></p>';
        }
    }

    echo '</div>';
}
