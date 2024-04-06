<?php 

add_action('woocommerce_thankyou', 'custom_redirect_after_purchase', 10, 1);
function custom_redirect_after_purchase($order_id) {
    $redirect_page_id = 3135; // Replace with the desired page ID

    // Redirect to the specified page
    wp_redirect(get_permalink($redirect_page_id));
    exit;
}