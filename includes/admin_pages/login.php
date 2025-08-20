<?php
// File: includes/admin_pages/login.php

if (!defined('ABSPATH')) exit;

function pp_login_form_render() {
    if (function_exists('is_user_logged_in') && is_user_logged_in()) {
        $user_id = get_current_user_id();
        $user = wp_get_current_user();
        $partner_type = get_field('partner_type', 'user_' . $user_id);

        if (in_array('partner', (array) $user->roles) && $partner_type === 'affiliate') {
            // 🚪 Redirect affiliate partners to their dashboard
            wp_redirect(home_url('/affiliate-dashboard'));
        } else {
            // 🚪 All others go to standard dashboard
            wp_redirect(admin_url('admin.php?page=pbp_dashboard'));
        }
        exit;
    }
    ?>
    <div class="pp-dashboard-login">
        <h2><?php esc_html_e('Partner Portal Login', 'partner-portal'); ?></h2>
        <form method="post" action="<?php echo esc_url(wp_login_url()); ?>">
            <label for="user_login"><?php esc_html_e('Username', 'partner-portal'); ?></label>
            <input type="text" name="log" id="user_login" required />
            <label for="user_pass"><?php esc_html_e('Password', 'partner-portal'); ?></label>
            <input type="password" name="pwd" id="user_pass" required />
            <button type="submit"><?php esc_html_e('Login', 'partner-portal'); ?></button>
        </form>
    </div>
    <?php
}
