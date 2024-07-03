jQuery(document).ready(function($) {
    console.log("Morgantown checkout script loaded");
    
    if (typeof morgantown_registration_data !== 'undefined') {
        console.log("Registration data available:", morgantown_registration_data);
        
        // Map registration fields to WooCommerce checkout fields
        var fieldMapping = {
            'first_name': '#billing_first_name',
            'last_name': '#billing_last_name',
            'user_email': '#billing_email'
        };

        // Populate fields
        $.each(fieldMapping, function(regField, wooField) {
            if (morgantown_registration_data[regField]) {
                $(wooField).val(morgantown_registration_data[regField]).trigger('change');
                console.log("Set", wooField, "to", morgantown_registration_data[regField]);
            } else {
                console.log("No data for", regField);
            }
        });

        // Trigger update of checkout
        $(document.body).trigger('update_checkout');
    } else {
        console.log("Registration data not available");
    }
});