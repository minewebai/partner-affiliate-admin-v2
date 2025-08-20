<?php
// File: includes/assets/partner-portal.js

jQuery(document).ready(function($) {
    // Modal open
    $('.pp-book-btn').on('click', function() {
        const postId = $(this).data('postid');
        const type = $(this).data('type');
        const modal = $('#pp-booking-modal');
        const contentArea = modal.find('.pp-booking-modal-content');

        contentArea.html('<p>Loading form...</p>');
        modal.show();

        $.ajax({
            url: pp_ajax.ajaxurl,
            type: 'POST',
            data: {
                action: 'pp_load_booking_form',
                post_id: postId,
                booking_type: type,
                nonce: pp_ajax.nonce
            },
            success: function(response) {
                contentArea.html(response);
            },
            error: function() {
                contentArea.html('<p>Error loading booking form.</p>');
            }
        });
    });

    // Modal close
    $('.pp-booking-modal-close').on('click', function() {
        $('#pp-booking-modal').hide();
    });
});
