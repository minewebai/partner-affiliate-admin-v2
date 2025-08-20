<?php
// File: includes/admin_pages/book.php

echo '<div style="position: fixed; bottom: 10px; right: 10px; background: green; color: white; padding: 6px 10px; z-index: 9999; font-weight: bold;">BOOK ACTIVE</div>';


if (!defined('ABSPATH')) exit;

// ✅ Register AJAX for TicketingHub autofill
add_action('wp_ajax_pp_ajax_create_manual_booking_autofill', function () {
    // (existing code unchanged here)
});

// ✅ Booking Page Output Starts
if (!function_exists('pp_admin_book_page')) {
    function pp_admin_book_page() {
        $user_id = get_current_user_id();
        $is_partner = current_user_can('partner');
        $is_affiliate = function_exists('get_field') && get_field('type', 'user_' . $user_id) === 'affiliate';

        if (!($is_partner && $is_affiliate)) {
            echo '<div class="wrap"><h1>' . esc_html__('Access Denied', 'partner-portal') . '</h1><p>' . esc_html__('You must be logged in as a partner affiliate to access the booking page.', 'partner-portal') . '</p></div>';
            return;
        }

        $tours      = PBP_Utils::get_partner_allowed_posts($user_id, 'st_tours');
        $activities = PBP_Utils::get_partner_allowed_posts($user_id, 'st_activity');

        // ✅ Booking interface output (placeholder)
echo '<div style="background: orange; color: white; padding: 10px;">🧪 Calling pp_admin_book_page()</div>';

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__('Create Booking', 'partner-portal') . '</h1>';
        echo '<p>' . esc_html__('Select a tour or activity below to create a booking.', 'partner-portal') . '</p>';
        // (Insert booking form, dropdowns, autofill components here)
        echo '</div>';
    }
}


// ✅ Register AJAX for TicketingHub autofill
add_action('wp_ajax_pp_ajax_create_manual_booking_autofill', function () {
    $post_id = intval($_POST['tour_id']);
    $post = get_post($post_id);

    if (!$post || get_post_type($post_id) === '') {
        wp_send_json_error(['message' => 'Invalid tour ID or post type']);
    }

    $widget_id = get_post_meta($post_id, 'ticketinghub_widget_id', true);

    if (empty($widget_id)) {
        wp_send_json_success([
            'message' => 'No TicketingHub widget found.',
            'field_name' => 'ticketinghub_widget_id',
            'field_value' => '(empty)',
            'post_id' => $post_id,
            'post_title' => $post->post_title
        ]);
        return;
    }

    $type        = get_post_type($post_id);
    $duration    = get_post_meta($post_id, 'duration', true);
    $price_type  = get_post_meta($post_id, 'price_type', true);
    $adult_price = ($price_type === 'fixed')
                 ? floatval(get_post_meta($post_id, 'fixed_price', true))
                 : floatval(get_post_meta($post_id, 'adult_price', true));

    if ($adult_price <= 0) {
        wp_send_json_error(['message' => 'Missing price data']);
    }

    $partner      = wp_get_current_user();
    $partner_id   = $partner->ID;
    $partner_email= $partner->user_email;
    $fee          = 35;
    $adult_number = 1;
    $total_price  = $adult_price * $adult_number;
    $check_in     = date('d/m/Y', strtotime('+1 day'));
    $start_time   = '13:00';
    $order_title  = 'Order – ' . current_time('F j, Y @ g:i a');
    $guid_link    = get_permalink($post_id);

    $order_id = wp_insert_post([
        'post_title'        => $order_title,
        'post_type'         => 'st_order',
        'post_status'       => 'publish',
        'comment_status'    => 'closed',
        'ping_status'       => 'closed',
        'post_name'         => sanitize_title($order_title),
        'post_modified'     => current_time('mysql'),
        'post_modified_gmt' => current_time('mysql', 1),
        'guid'              => $guid_link
    ]);

    if (!$order_id || is_wp_error($order_id)) {
        wp_send_json_error(['message' => 'Failed to create booking record']);
    }

    $raw_data = [
        "adult_price" => $adult_price,
        "adult_number" => $adult_number,
        "starttime" => $start_time,
        "price_type" => $price_type,
        "check_in" => str_replace('/', '\/', $check_in),
        "check_out" => str_replace('/', '\/', $check_in),
        "ori_price" => $total_price,
        "sale_price" => $total_price,
        "commission" => 10,
        "duration" => $duration,
        "st_booking_post_type" => $type,
        "st_booking_id" => $post_id,
        "title_cart" => $post->post_title,
        "user_id" => ""
        // (All other fields trimmed for brevity — add as needed)
    ];

    $data = [
        'order_item_id'        => $order_id,
        'type'                 => 'normal_booking',
        'check_in'             => $check_in,
        'check_out'            => $check_in,
        'starttime'            => $start_time,
        'duration'             => $duration,
        'adult_number'         => $adult_number,
        'st_booking_id'        => $post_id,
        'st_booking_post_type' => $type,
        'partner_id'           => $partner_id,
        'commission'           => '10',
        'total_order'          => $total_price,
        'status'               => 'complete',
        'raw_data'             => json_encode($raw_data),
        'wc_order_id'          => $post_id,
        'origin_id'            => $post_id,
        'cancel_refund_status' => 'complete'
    ];

    do_action('st_save_order_item_meta', $data, $order_id, 'normal_booking');

    wp_send_json_success([
        'message'      => 'Internal booking record created.',
        'order_id'     => $order_id,
        'post_id'      => $post_id,
        'post_title'   => $post->post_title,
        'widget_id'    => $widget_id,
        'status'       => 'complete'
    ]);
});




// ✅ Booking Page Output Starts
function pp_admin_book_page() {
    $user_id    = get_current_user_id();
    $tours      = PBP_Utils::get_partner_allowed_posts($user_id, 'st_tours');
    $activities = PBP_Utils::get_partner_allowed_posts($user_id, 'st_activity');
?>
<div class="wrap">
    <h1><?php echo esc_html__('Book Tour or Activity', 'partner-portal'); ?></h1>

    <ul class="pp-tabs">
        <li class="active" data-tab="tours-tab"><?php _e('Tours', 'partner-portal'); ?></li>
        <li data-tab="activities-tab"><?php _e('Activities', 'partner-portal'); ?></li>
    </ul>

    <div id="tours-tab" class="pp-tab-content active">
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php _e('Tour ID', 'partner-portal'); ?></th>
                    <th><?php _e('Title', 'partner-portal'); ?></th>
                    <th><?php _e('Description', 'partner-portal'); ?></th>
                    <th><?php _e('Duration', 'partner-portal'); ?></th>
                    <th><?php _e('Price', 'partner-portal'); ?></th>
                    <th><?php _e('Actions', 'partner-portal'); ?></th>
                </tr>
            </thead>
            <tbody>
    <?php foreach ($tours as $tour_id):
        $post       = get_post($tour_id);
        if (!$post) continue;
        $desc       = wp_strip_all_tags(wp_trim_words($post->post_content, 70, '...'));
        $duration = get_post_meta($tour_id, 'duration_day', true);
$type_price = get_post_meta($tour_id, 'type_price', true);
$price      = get_post_meta($tour_id, 'min_price', true); // Always used
        $widget     = get_post_meta($tour_id, 'ticketinghub_widget', true);
    ?>
// echo  all 

    <tr>
        <td><?php echo esc_html($tour_id); ?></td>
        <td><?php echo esc_html($post->post_title); ?></td>
        <td><?php echo esc_html($desc); ?></td>
        <td><?php echo esc_html($duration); ?></td>


  
    <td>
        <?php
    if ($type_price === 'fixed_price') {
            echo esc_html($price) . ' € ';
        } else {
            echo esc_html($price) . ' € ';
        }
        ?>
    </td>

        <td>
            <button class="pp-launch-modal"
                    data-id="<?php echo esc_attr($tour_id); ?>"
                    data-title="<?php echo esc_attr($post->post_title); ?>"
                    data-widget="<?php echo esc_attr($widget); ?>">
                <?php _e('Book', 'partner-portal'); ?>
            </button>
            <button class="pp-create-booking"
                    data-id="<?php echo esc_attr($tour_id); ?>">
                <?php _e('Create', 'partner-portal'); ?>
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>

        </table>
    </div>

    <div id="activities-tab" class="pp-tab-content">
        <table class="wp-list-table widefat striped">
            <thead>
                <tr>
                    <th><?php _e('Activity ID', 'partner-portal'); ?></th>
                    <th><?php _e('Title', 'partner-portal'); ?></th>
                    <th><?php _e('Description', 'partner-portal'); ?></th>
                    <th><?php _e('Duration', 'partner-portal'); ?></th>
                    <th><?php _e('Price', 'partner-portal'); ?></th>
                    <th><?php _e('Actions', 'partner-portal'); ?></th>
                </tr>
            </thead>
<tbody>
    <?php foreach ($activities as $act_id):
        $post       = get_post($act_id);
        if (!$post) continue;
        $desc       = wp_strip_all_tags(wp_trim_words($post->post_content, 70, '...'));
        $duration   = get_post_meta($act_id, 'duration', true);
        $price_type = get_post_meta($act_id, 'price_type', true);
        $price      = ($price_type === 'price_by_fixed')
                    ? get_post_meta($act_id, 'fixed_price', true)
                    : get_post_meta($act_id, 'adult_price', true);
        $widget     = get_post_meta($act_id, 'ticketinghub_widget', true);
    ?>
   <tr>
    <td><?php echo esc_html($act_id); ?></td>
    <td><?php echo esc_html($post->post_title); ?></td>
    <td><?php echo esc_html($desc); ?></td>
    <td><?php echo esc_html($duration); ?></td>



    <td>
        <?php
        if ($price_type === 'price_by_fixed') {
            echo esc_html($price) . ' € total';
        } else {
            echo esc_html($price) . ' € / person';
        }
        ?>
    </td>
</tr>

        <td>
            <button class="pp-launch-modal"
                    data-id="<?php echo esc_attr($act_id); ?>"
                    data-title="<?php echo esc_attr($post->post_title); ?>"
                    data-widget="<?php echo esc_attr($widget); ?>">
                <?php _e('Book', 'partner-portal'); ?>
            </button>
            <button class="pp-create-booking"
                    data-id="<?php echo esc_attr($act_id); ?>">
                <?php _e('Create', 'partner-portal'); ?>
            </button>
        </td>
    </tr>
    <?php endforeach; ?>
</tbody>

        </table>
    </div>
</div>

<!-- Booking Modal -->
<div id="pp-booking-modal" style="display:none;">
    <button id="pp-booking-close-icon">×</button>
    <div class="pp-booking-modal-content"></div>
    <div id="pp-booking-controls">
        <button id="pp-booking-cancel">Cancel</button>
        <button id="pp-booking-finish">Finish Booking</button>
    </div>
</div>

<!-- Confirmation Modal -->
<div id="pp-confirmation-modal" style="display:none;">
    <h2>Booking Confirmation</h2>
    <p id="pp-confirmation-details"></p>
    <button id="pp-confirmation-ok">OK</button>
</div>
 


<script>
jQuery(function($){
    let currentPostId = '', currentPostTitle = '', currentWidgetId = '';

    // Tab switching logic
    $('.pp-tabs li').on('click', function(){
        const tab = $(this).data('tab');
        $('.pp-tabs li').removeClass('active');
        $(this).addClass('active');
        $('.pp-tab-content').removeClass('active');
        $('#' + tab).addClass('active');
    });

    // Modal booking (Book button flow)
    $(document).on('click', '.pp-launch-modal', function(){
        currentPostId = $(this).data('id');
        currentPostTitle = $(this).data('title');
        currentWidgetId = $(this).data('widget') || '';

        const iframeURL = '<?php echo get_site_url(); ?>/?p=' + currentPostId + '&pp_preview=1';
        $('.pp-booking-modal-content').html('<iframe src="' + iframeURL + '" style="width:100%; height:720px; border:none;"></iframe>');
        $('#pp-booking-modal').fadeIn();
    });

    // Modal close buttons
    $(document).on('click', '#pp-booking-cancel, #pp-booking-close-icon', function(){
        $('#pp-booking-modal').fadeOut();
        $('.pp-booking-modal-content').html('');
    });

    // Finish Booking (modal)
    $(document).on('click', '#pp-booking-finish', function(){
        $('#pp-booking-modal').fadeOut();
        $('.pp-booking-modal-content').html('');

        if (currentWidgetId.trim() !== '') {
            // ✅ Widget exists — force record creation
            $.post(ajaxurl, {
                action: 'pp_ajax_create_manual_booking_autofill',
                tour_id: currentPostId
            }, function(){
                $('#pp-confirmation-details').html('<strong>✅ Complete</strong>');
                $('#pp-confirmation-modal').fadeIn();
            });
        } else {
            // ❌ No widget — fallback message
            $('#pp-confirmation-details').html(
                '<strong>Booking not created.</strong><br>' +
                'Please try again.'
            );
            $('#pp-confirmation-modal').fadeIn();
        }
    });

    // Create button — direct autofill check
    $(document).on('click', '.pp-create-booking', function(){
        const id = $(this).data('id');

        $.post(ajaxurl, {
            action: 'pp_ajax_create_manual_booking_autofill',
            tour_id: id
        }, function(response){
            let html = '';

            if (response.success && response.data) {
                const data = response.data;
                html = '<strong>' + (data.message || 'Success') + '</strong>';

                if (data.field_name) {
                    html += '<br>Custom Field: <code>' + data.field_name + '</code>';
                }
                if (data.field_value) {
                    html += '<br>Value: <code>' + data.field_value + '</code>';
                }
                if (data.widget_id) {
                    html += '<br>Widget ID: <code>' + data.widget_id + '</code>';
                }
                if (data.order_id) {
                    html += '<br>Order ID: <code>' + data.order_id + '</code>';
                }
                if (data.post_title) {
                    html += '<br>Post: <strong>' + data.post_title + '</strong>';
                }
            } else {
                html = '<strong>⚠️ Failed to create booking.</strong><br>' +
                       (response.data?.message || 'Unknown error.');
            }

            $('#pp-confirmation-details').html(html);
            $('#pp-confirmation-modal').fadeIn();
        });
    });

    // OK button — hide modal
    $(document).on('click', '#pp-confirmation-ok', function(){
        $('#pp-confirmation-modal').fadeOut();
        $('#pp-confirmation-details').html('');
    });
});
</script>




<style>
.pp-tabs {
    list-style: none;
    padding: 0;
    margin: 0 0 15px 0;
    display: flex;
    gap: 20px;
}
.pp-tabs li {
    padding: 10px 20px;
    background: #f1f1f1;
    cursor: pointer;
    border-radius: 4px;
}
.pp-tabs li.active {
    background: #0073aa;
    color: #fff;
}
.pp-tab-content {
    display: none;
}
.pp-tab-content.active {
    display: block;
}
.pp-booking-table {
    width: 100%;
    border-collapse: collapse;
}
.pp-booking-table th,
.pp-booking-table td {
    padding: 10px;
    font-size: 14px;
    text-align: left;
    border-bottom: 1px solid #ddd;
}
.pp-launch-modal,
.pp-create-booking {
    padding: 8px 14px;
    margin-right: 6px;
    background: #28a745;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}
.pp-create-booking {
    background: #17a2b8;
}
.pp-launch-modal:hover {
    background: #218838;
}
.pp-create-booking:hover {
    background: #148ba0;
}
#pp-booking-modal,
#pp-confirmation-modal {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    border: 1px solid #ccc;
    padding: 20px;
    z-index: 9999;
    max-width: 1400px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 0 30px rgba(0, 0, 0, 0.2);
    display: flex;
    flex-direction: column;
}
#pp-booking-close-icon {
    position: absolute;
    top: 10px;
    right: 12px;
    font-size: 26px;
    font-weight: bold;
    color: #000;
    background: #ff4d4d;
    border: none;
    border-radius: 4px;
    width: 36px;
    height: 36px;
    text-align: center;
    line-height: 30px;
    cursor: pointer;
}
#pp-booking-controls {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
#pp-booking-cancel,
#pp-booking-finish,
#pp-confirmation-ok {
    padding: 10px 18px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    background: #0073aa;
    color: #fff;
    font-size: 14px;
}
#pp-booking-cancel:hover,
#pp-booking-finish:hover,
#pp-confirmation-ok:hover {
    background: #005f8c;
}
#pp-confirmation-modal h2 {
    margin-top: 0;
    font-size: 22px;
    color: #333;
}
#pp-confirmation-modal p {
    margin-bottom: 20px;
    font-size: 16px;
    color: #666;
}
#pp-ticketinghub-reminder {
    background: #ffe;
    border: 1px solid #ffc107;
    padding: 15px;
    margin-top: 20px;
    max-width: 700px;
    box-shadow: 0 0 10px rgba(255, 193, 7, 0.2);
}
</style>


<?php } ?>
