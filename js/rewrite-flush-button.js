(function($) {

    "use strict";
    $(function () {

        // Listen for click on Flush Rewrite Rules button in WP-Admin > Settings > Permalinks.
        $(RFB.button_id).click(function(evt) {

            evt.preventDefault();

            // Store callback method name and nonce field value in an array.
            var data = {
                action: RFB.action_id,        // AJAX callback
                nonce: $(RFB.nonce_id).html() // Nonce field value
            };

            // AJAX call.
            $.post(ajaxurl, data, function(response) {

                if ( '1' === response ) {
                    $(RFB.desc_id).html(RFB.success_msg); // Success!
                    $(RFB.desc_id).addClass('updated');
                } else {
                    $(RFB.desc_id).html(RFB.error_msg);   // Error
                    $(RFB.desc_id).addClass('error');
                }
                $(RFB.desc_id).removeClass('description');

            });

        });

    });
}(jQuery));
