(function($) {

    "use strict";
    $(function () {

        // Listen for click on Flush Rewrite Rules button in WP-Admin > Settings > Permalinks.
        $(RFB.buttonid).click(function(evt) {

            evt.preventDefault();

            // Store callback method name and nonce field value in an array.
            var data = {
                action: RFB.actionid,        // AJAX callback
                nonce: RFB.nonce // Nonce field value
            };

            // AJAX call.
            $.post(ajaxurl, data, function(response) {

                if ('1' === response) {
                    $(RFB.descid).html('Success!'); // Success!
                } else {
                    $(RFB.descid).html('Error.');   // Error
                }

            });

        });

    });
}(jQuery));
