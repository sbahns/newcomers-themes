<?php
/*
Plugin Name: Morgantown Newcomers Membership Handler
Description: Handles membership registration and redirects to WooCommerce checkout
Version: 2.0
Author: Claude
*/

// Debug logging function
function morgantown_debug_log($message) {
    if (defined('WP_DEBUG') && WP_DEBUG === true) {
        error_log('Morgantown Newcomers Debug: ' . print_r($message, true));
    }
}

// Updated Registration Redirect Function
function morgantown_wpmem_registration_redirect($redirect_to, $user_id, $wpmem_regchk = 'success') {
    morgantown_debug_log("Registration redirect function called. User ID: $user_id, RegChk: $wpmem_regchk");
    
    if ($wpmem_regchk == "success" && isset($_POST['redirect_to'])) {
        $product_id = 18076; // Specific product ID for membership
        
        if (function_exists('WC')) {
            // Save essential user registration data
            $registration_data = array(
                'first_name' => isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '',
                'last_name' => isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '',
                'user_email' => isset($_POST['user_email']) ? sanitize_email($_POST['user_email']) : '',
                'billing_phone' => isset($_POST['billing_phone']) ? sanitize_text_field($_POST['billing_phone']) : '',
            );
            morgantown_debug_log("Registration data: " . print_r($registration_data, true));
            update_user_meta($user_id, 'morgantown_registration_data', $registration_data);
            
            WC()->cart->empty_cart();
            WC()->cart->add_to_cart($product_id);
            WC()->session->set('morgantown_pending_user_id', $user_id);
            
            $checkout_url = wc_get_checkout_url();
            morgantown_debug_log("Redirecting to Checkout URL: $checkout_url");
            
            wp_redirect($checkout_url);
            exit;
        } else {
            morgantown_debug_log('WooCommerce functions not available in registration redirect');
        }
    }
    
    morgantown_debug_log("Redirect not triggered. Returning original redirect: $redirect_to");
    return $redirect_to;
}
add_filter('wpmem_register_redirect', 'morgantown_wpmem_registration_redirect', 10, 3);

// Pre-fill checkout fields function
function morgantown_prefill_checkout_fields($fields) {
    morgantown_debug_log("Prefill checkout fields function called");
    
    if (function_exists('WC') && WC()->session) {
        $user_id = WC()->session->get('morgantown_pending_user_id');
        morgantown_debug_log("User ID from session: $user_id");
        
        if ($user_id) {
            $registration_data = get_user_meta($user_id, 'morgantown_registration_data', true);
            morgantown_debug_log("Retrieved registration data: " . print_r($registration_data, true));
            
            if ($registration_data) {
                $field_mapping = [
                    'first_name' => 'billing_first_name',
                    'last_name' => 'billing_last_name',
                    'user_email' => 'billing_email',
                    'billing_phone' => 'billing_phone'
                ];
                
                foreach ($field_mapping as $wpmem_field => $wc_field) {
                    if (isset($registration_data[$wpmem_field])) {
                        $fields[$wc_field]['default'] = $registration_data[$wpmem_field];
                        morgantown_debug_log("Set $wc_field to: " . $registration_data[$wpmem_field]);
                    }
                }
            }
        }
    }
    
    morgantown_debug_log("Final fields array: " . print_r($fields, true));
    return $fields;
}
add_filter('woocommerce_checkout_fields', 'morgantown_prefill_checkout_fields');

// Function to output registration data as JavaScript
function morgantown_output_registration_data() {
    if (is_checkout() && function_exists('WC') && WC()->session) {
        $user_id = WC()->session->get('morgantown_pending_user_id');
        echo "<!-- Debug: User ID from session: $user_id -->\n";
        if ($user_id) {
            $registration_data = get_user_meta($user_id, 'morgantown_registration_data', true);
            echo "<!-- Debug: Registration data: " . esc_html(print_r($registration_data, true)) . " -->\n";
            if ($registration_data) {
                echo "<script type='text/javascript'>\n";
                echo "var morgantown_registration_data = " . json_encode($registration_data) . ";\n";
                echo "console.log('Morgantown registration data:', morgantown_registration_data);\n";
                echo "</script>\n";
            }
        }
    }
}
add_action('wp_footer', 'morgantown_output_registration_data');

// Function to enqueue JavaScript for populating checkout fields
function morgantown_enqueue_checkout_script() {
    if (is_checkout()) {
        wp_enqueue_script('morgantown-checkout', plugins_url('morgantown-checkout.js', __FILE__), array('jquery'), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'morgantown_enqueue_checkout_script');

// Log that the plugin has been loaded
morgantown_debug_log('Morgantown Newcomers Membership Handler plugin loaded');