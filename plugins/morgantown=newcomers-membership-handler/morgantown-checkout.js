jQuery(document).ready(function($) {
    console.log("Morgantown checkout script loaded");
    
    if (typeof morgantown_registration_data !== 'undefined') {
        console.log("Registration data available:", morgantown_registration_data);
        
        // Map WP-Members fields to WooCommerce checkout fields
        var fieldMapping = {
            'first_name': '#billing_first_name',
            'last_name': '#billing_last_name',
            'user_email': '#billing_email',
            'billing_phone': '#billing_phone'
        };

        // Populate fields
        $.each(fieldMapping, function(wpmemField, wooField) {
            if (morgantown_registration_data[wpmemField]) {
                $(wooField).val(morgantown_registration_data[wpmemField]);
                console.log("Set", wooField, "to", morgantown_registration_data[wpmemField]);
            } else {
                console.log("No data for", wpmemField);
            }
        });
    } else {
        console.log("Registration data not available");
    }

    // Debug output of all form fields
    $('#billing_first_name, #billing_last_name, #billing_email, #billing_phone').each(function() {
        console.log($(this).attr('id') + " value: " + $(this).val());
    });
});