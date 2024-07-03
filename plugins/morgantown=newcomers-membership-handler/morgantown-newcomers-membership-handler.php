<?php
/*
Plugin Name: Morgantown Newcomers Membership Handler
Description: Handles membership registration and redirects to WooCommerce checkout
Version: 3.1
Author: Claude
*/

// Debug logging function
function morgantown_debug_log($message) {
    if (defined('WP_DEBUG') && WP_DEBUG === true) {
        error_log('Morgantown Newcomers Debug: ' . print_r($message, true));
    }
}

// Function to check if WooCommerce is active
function morgantown_is_woocommerce_active() {
    return class_exists('WooCommerce');
}

// Prevent WP-Members from creating a user
function morgantown_prevent_user_creation($fields) {
    morgantown_debug_log("Preventing WP-Members user creation");
    
    // Modify the fields to prevent user creation
    $fields['ID'] = 0;
    $fields['username'] = '';
    $fields['password'] = '';
    $fields['email'] = '';
    
    // Save the data we need for later
    $registration_data = array(
        'first_name' => isset($fields['first_name']) ? $fields['first_name'] : '',
        'last_name' => isset($fields['last_name']) ? $fields['last_name'] : '',
        'user_email' => isset($fields['user_email']) ? $fields['user_email'] : '',
    );
    
    if (morgantown_is_woocommerce_active() && WC()->session) {
        WC()->session->set('morgantown_pending_registration', $registration_data);
        morgantown_debug_log("Registration data saved to session in prevent_user_creation: " . print_r($registration_data, true));
    } else {
        morgantown_debug_log("WooCommerce session not available in prevent_user_creation");
    }
    
    return $fields;
}
add_filter('wpmem_register_data', 'morgantown_prevent_user_creation', 99, 1);

// Redirect after WP-Members processes the form
function morgantown_redirect_after_registration($redirect_to) {
    morgantown_debug_log("morgantown_redirect_after_registration called. Original redirect: $redirect_to");
    
    if (morgantown_is_woocommerce_active()) {
        $product_id = 18076; // Specific product ID for membership
        
        // Save registration data to WooCommerce session
        $registration_data = array(
            'first_name' => isset($_POST['first_name']) ? sanitize_text_field($_POST['first_name']) : '',
            'last_name' => isset($_POST['last_name']) ? sanitize_text_field($_POST['last_name']) : '',
            'user_email' => isset($_POST['user_email']) ? sanitize_email($_POST['user_email']) : '',
        );
        
        if (WC()->session) {
            WC()->session->set('morgantown_pending_registration', $registration_data);
            morgantown_debug_log("Registration data saved to session in redirect: " . print_r($registration_data, true));
            
            WC()->cart->empty_cart();
            WC()->cart->add_to_cart($product_id);
            
            $checkout_url = wc_get_checkout_url();
            morgantown_debug_log("Redirecting to Checkout URL: $checkout_url");
            
            wp_redirect($checkout_url);
            exit;
        } else {
            morgantown_debug_log("WooCommerce session not available in redirect");
        }
    } else {
        morgantown_debug_log('WooCommerce functions not available in registration redirect');
    }
    
    return $redirect_to;
}
add_filter('wpmem_register_redirect', 'morgantown_redirect_after_registration', 99, 1);

function morgantown_create_user_after_payment($order_id) {
    morgantown_debug_log("Payment complete for order ID: $order_id");
    $order = wc_get_order($order_id);
    if ($order && $order->is_paid()) {
        $registration_data = WC()->session->get('morgantown_pending_registration');
        morgantown_debug_log("Retrieved registration data for user creation: " . print_r($registration_data, true));
        
        if ($registration_data) {
            $random_password = wp_generate_password(12, true);
            
            $user_id = wp_create_user($registration_data['user_email'], $random_password, $registration_data['user_email']);
            if (!is_wp_error($user_id)) {
                morgantown_debug_log("User created with ID: $user_id");
                wp_update_user([
                    'ID' => $user_id,
                    'first_name' => $registration_data['first_name'],
                    'last_name' => $registration_data['last_name'],
                ]);
                
                // Activate user in WP-Members
                if (function_exists('wpmem_activate_user')) {
                    wpmem_activate_user($user_id);
                    morgantown_debug_log("User activated in WP-Members");
                }
                
                // Sync with BuddyPress Extended Profile
                if (function_exists('bp_set_profile_field_data')) {
                    bp_set_profile_field_data(array(
                        'field_id' => 1, // Assuming 1 is the field ID for First Name
                        'user_id' => $user_id,
                        'value' => $registration_data['first_name']
                    ));
                    bp_set_profile_field_data(array(
                        'field_id' => 2, // Assuming 2 is the field ID for Last Name
                        'user_id' => $user_id,
                        'value' => $registration_data['last_name']
                    ));
                    morgantown_debug_log("User data synced with BuddyPress Extended Profile");
                }
                
                // Send password to user
                wp_new_user_notification($user_id, null, 'user');
                morgantown_debug_log("Password sent to user");
                
                // Clear the session data
                WC()->session->__unset('morgantown_pending_registration');
                morgantown_debug_log("Session data cleared");
            } else {
                morgantown_debug_log("Error creating user: " . $user_id->get_error_message());
            }
        } else {
            morgantown_debug_log("No registration data found in session");
        }
    } else {
        morgantown_debug_log("Order not paid or invalid order ID");
    }
}
add_action('woocommerce_payment_complete', 'morgantown_create_user_after_payment');

// Function to output registration data as JavaScript
function morgantown_output_registration_data() {
    if (is_checkout() && morgantown_is_woocommerce_active() && WC()->session) {
        $registration_data = WC()->session->get('morgantown_pending_registration');
        if ($registration_data) {
            wp_localize_script('morgantown-checkout', 'morgantown_registration_data', $registration_data);
            morgantown_debug_log('Registration data passed to JavaScript: ' . print_r($registration_data, true));
        } else {
            morgantown_debug_log('No registration data found in session for JavaScript output');
        }
    }
}
add_action('wp_enqueue_scripts', 'morgantown_output_registration_data', 20);

// Function to enqueue JavaScript for populating checkout fields
function morgantown_enqueue_checkout_script() {
    if (is_checkout()) {
        wp_enqueue_script('morgantown-checkout', plugins_url('morgantown-checkout.js', __FILE__), array('jquery'), '1.2', true);
        morgantown_debug_log('Morgantown checkout script enqueued');
    }
}
add_action('wp_enqueue_scripts', 'morgantown_enqueue_checkout_script');

// Log that the plugin has been loaded
morgantown_debug_log('Morgantown Newcomers Membership Handler plugin loaded');