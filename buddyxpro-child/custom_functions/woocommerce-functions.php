<?php 

add_action('woocommerce_thankyou', 'custom_membership_pending_redirect', 10, 1);
function custom_membership_pending_redirect($order_id) {
    $order = wc_get_order($order_id);
    $items = $order->get_items();

    foreach ($items as $item) {
        $product_id = $item->get_product_id();
        $product = wc_get_product($product_id);

        // Check if the purchased product is a membership product
        if ($product->is_type('membership')) {
            wp_redirect(get_permalink(3135));
            exit;
        }
    }
}

add_action('woocommerce_checkout_update_order_meta', 'custom_pay_by_check_redirect', 10, 2);
function custom_pay_by_check_redirect($order_id, $posted_data) {
    $order = wc_get_order($order_id);
    $payment_method = $order->get_payment_method();

    if ($payment_method == 'cheque') {
        wp_redirect(get_permalink(31));
        exit;
    }
}