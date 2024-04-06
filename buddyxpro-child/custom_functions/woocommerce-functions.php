<?php 
// Simple redirect - (works)
// add_action('woocommerce_thankyou', 'custom_redirect_after_purchase', 10, 1);
// function custom_redirect_after_purchase($order_id) {
//     $redirect_page_id = 3135; // Replace with the desired page ID

//     // Redirect to the specified page
//     wp_redirect(get_permalink($redirect_page_id));
//     exit;
// }

add_filter('woocommerce_get_settings_pages', 'custom_woocommerce_settings_tab');
function custom_woocommerce_settings_tab($settings) {
    $settings[] = include('custom-settings.php');
    return $settings;
}

add_action('woocommerce_thankyou', 'custom_redirect_after_purchase', 10, 1);
function custom_redirect_after_purchase($order_id) {
    $redirect_page_id = get_option('woocommerce_redirect_page_id');

    // Redirect to the specified page if a page is selected
    if (!empty($redirect_page_id)) {
        wp_redirect(get_permalink($redirect_page_id));
        exit;
    }
}