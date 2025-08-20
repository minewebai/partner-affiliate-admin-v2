// File: assets/pp-admin-select.js

jQuery(document).ready(function($) {
    function setupAjaxSelect(selector, postType) {
        $(selector).select2({
            placeholder: "Search...",
            minimumInputLength: 2,
            ajax: {
                url: pp_admin_ajax.ajaxurl,
                method: "POST",
                delay: 250,
                dataType: "json",
                data: function(params) {
                    return {
                        action: "pp_admin_search_posts",
                        nonce: pp_admin_ajax.nonce,
                        term: params.term,
                        post_type: postType
                    };
                },
                processResults: function(data) {
                    return {
                        results: data.map(function(post) {
                            return {
                                id: post.id,
                                text: post.label,
                                lang: post.language
                            };
                        })
                    };
                }
            },
            templateResult: function(item) {
                if (!item.id) return item.text;
                return $("<span>").text(item.text + (item.lang ? " [" + item.lang + "]" : "") + " (" + item.id + ")");  
            },
            templateSelection: function(item) {
                return $("<span>").text(item.text + (item.lang ? " [" + item.lang + "]" : "") + " (" + item.id + ")");
            },
            escapeMarkup: function(markup) { return markup; }
        });
    }

    setupAjaxSelect("#allowed_tours", "st_tours");
    setupAjaxSelect("#allowed_activities", "st_activity");
});
