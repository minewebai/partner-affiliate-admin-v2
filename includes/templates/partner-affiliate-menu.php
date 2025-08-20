<?php
// partner-affiliate-menu.php

if (!defined('ABSPATH')) exit;
?>

<li class="sidebar-dropdown">
    <a href="<?php echo admin_url('admin.php?page=pbp_dashboard'); ?>" style="color:#fff; font-weight:bold;">
        <img src="https://reimaginetours.com/wp-content/themes/traveler/v2/images/dashboard/ico_dashboard.svg" alt="" class="st-icon-menu">
        <span><?php _e('Dashboard', 'partner-portal'); ?></span>
    </a>
</li>

<li class="sidebar-dropdown">
    <a href="<?php echo admin_url('admin.php?page=pbp_book'); ?>" style="color:#fff; font-weight:bold;">
        <img src="https://reimaginetours.com/wp-content/themes/traveler/v2/images/dashboard/ico_tour.svg" alt="" class="st-icon-menu">
        <span><?php _e('Book', 'partner-portal'); ?></span>
    </a>
</li>

<li class="sidebar-dropdown">
    <a href="<?php echo admin_url('admin.php?page=pbp_bookings'); ?>" style="color:#fff; font-weight:bold;">
        <img src="https://reimaginetours.com/wp-content/themes/traveler/v2/images/dashboard/ico_booking_his.svg" alt="" class="st-icon-menu">
        <span><?php _e('Bookings', 'partner-portal'); ?></span>
    </a>
</li>

<li class="sidebar-dropdown">
    <a href="<?php echo admin_url('admin.php?page=pbp_commissions'); ?>" style="color:#fff; font-weight:bold;">
        <img src="https://reimaginetours.com/wp-content/themes/traveler/v2/images/dashboard/ico_wishlish.svg" alt="" class="st-icon-menu">
        <span><?php _e('Commissions', 'partner-portal'); ?></span>
    </a>
</li>

<li class="sidebar-dropdown">
    <a href="<?php echo admin_url('admin.php?page=pbp_account'); ?>" style="color:#fff; font-weight:bold;">
        <img src="https://reimaginetours.com/wp-content/themes/traveler/v2/images/dashboard/ico_seting.svg" alt="" class="st-icon-menu">
        <span><?php _e('Account', 'partner-portal'); ?></span>
    </a>
</li>
