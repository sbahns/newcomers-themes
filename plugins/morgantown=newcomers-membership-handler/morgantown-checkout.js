jQuery(document).ready(function($) {
    console.log("Morgantown checkout script loaded");
    
    if (typeof morgantown_registration_data !== 'undefined') {
        console.log("Registration data available:", morgantown_registration_data);
        
        function populateCheckoutFields() {
            var fieldMapping = {
                'first_name': ['billing_first_name', 'billing-first-name', 'billing-first_name'],
                'last_name': ['billing_last_name', 'billing-last-name', 'billing-last_name'],
                'user_email': ['billing_email', 'email']
            };

            Object.keys(fieldMapping).forEach(function(field) {
                if (morgantown_registration_data[field]) {
                    var fieldFound = false;
                    fieldMapping[field].forEach(function(selector) {
                        var input = $('input[name="' + selector + '"], input[id="' + selector + '"], input[data-id="' + selector + '"]');
                        if (input.length) {
                            input.val(morgantown_registration_data[field]).trigger('change').trigger('blur');
                            console.log("Set", selector, "to", morgantown_registration_data[field]);
                            fieldFound = true;

                            // For WooCommerce Blocks
                            if (input.closest('.wc-block-components-text-input').length) {
                                input.closest('.wc-block-components-text-input').addClass('is-active').find('label').addClass('screen-reader-text');
                            }
                        }
                    });
                    if (!fieldFound) {
                        console.log("Field not found for", field);
                    }
                }
            });

            // Force update on WooCommerce Blocks
            if (typeof wc !== 'undefined' && wc.blocksCheckout && typeof wc.blocksCheckout.refreshCheckoutFragments === 'function') {
                wc.blocksCheckout.refreshCheckoutFragments();
            }
        }

        // Initial population attempt
        populateCheckoutFields();

        // Set up a MutationObserver to watch for changes in the DOM
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList' || mutation.type === 'attributes') {
                    populateCheckoutFields();
                }
            });
        });

        // Start observing the document body for changes
        observer.observe(document.body, { childList: true, subtree: true, attributes: true });

        // Additionally, try to populate fields periodically
        var attemptCounter = 0;
        var intervalId = setInterval(function() {
            populateCheckoutFields();
            attemptCounter++;
            if (attemptCounter >= 10) { // Stop after 10 attempts (20 seconds)
                clearInterval(intervalId);
            }
        }, 2000);

        // Add event listener for when blocks are updated
        $(document).on('wc-blocks-checkout-update-checkout', populateCheckoutFields);
    } else {
        console.log("Registration data not available");
    }
});