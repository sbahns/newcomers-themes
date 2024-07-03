jQuery(document).ready(function($) {
    console.log("Morgantown checkout script loaded");
    
    if (typeof morgantown_registration_data !== 'undefined') {
        console.log("Registration data available:", morgantown_registration_data);
        
        // Function to populate fields
        function populateCheckoutFields() {
            var fieldMapping = {
                'first_name': ['billing-first_name', 'billing_first_name'],
                'last_name': ['billing-last_name', 'billing_last_name'],
                'user_email': ['billing-email', 'billing_email', 'email']
            };

            Object.keys(fieldMapping).forEach(function(field) {
                if (morgantown_registration_data[field]) {
                    var fieldFound = false;
                    fieldMapping[field].forEach(function(selector) {
                        var input = $('[id$="' + selector + '"], [name$="' + selector + '"]');
                        if (input.length) {
                            input.val(morgantown_registration_data[field]).trigger('change');
                            console.log("Set", selector, "to", morgantown_registration_data[field]);
                            fieldFound = true;
                        }
                    });
                    if (!fieldFound) {
                        console.log("Field not found for", field);
                    }
                } else {
                    console.log("No data for", field);
                }
            });
        }

        // Initial population attempt
        populateCheckoutFields();

        // Set up a MutationObserver to watch for changes in the DOM
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    populateCheckoutFields();
                }
            });
        });

        // Start observing the document body for changes
        observer.observe(document.body, { childList: true, subtree: true });

        // Additionally, try to populate fields after a short delay
        setTimeout(populateCheckoutFields, 1000);

        // Add event listener for when blocks are updated
        $(document).on('wc-blocks-checkout-update-checkout', populateCheckoutFields);
    } else {
        console.log("Registration data not available");
    }
});