<?php
/*
Plugin Name: Morgantown Newcomers Membership Handler
Description: Adds custom registration fields to WooCommerce checkout and handles membership approval
Version: 7.0
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

// Define custom fields
function morgantown_get_custom_fields() {
    return array(
        'morgantown_spouse_name' => array(
            'type' => 'text',
            'label' => 'Spouse/Partner Name',
            'required' => false,
            'class' => array('form-row-wide'),
            'priority' => 100,
        ),
        'morgantown_address' => array(
            'type' => 'text',
            'label' => 'Address',
            'required' => true,
            'class' => array('form-row-wide'),
            'priority' => 110,
        ),
        'morgantown_city' => array(
            'type' => 'text',
            'label' => 'City',
            'required' => true,
            'class' => array('form-row-wide'),
            'priority' => 120,
        ),
        'morgantown_state' => array(
            'type' => 'select',
            'label' => 'State',
            'required' => true,
            'class' => array('form-row-wide'),
            'priority' => 130,
            'options' => array(
                'WV' => 'West Virginia',
                'PA' => 'Pennsylvania',
                'OH' => 'Ohio',
                // Add more states as needed
            ),
        ),
        'morgantown_zip' => array(
            'type' => 'text',
            'label' => 'ZIP Code',
            'required' => true,
            'class' => array('form-row-wide'),
            'priority' => 140,
        ),
        'morgantown_phone' => array(
            'type' => 'tel',
            'label' => 'Phone Number',
            'required' => true,
            'class' => array('form-row-wide'),
            'priority' => 150,
        ),
        'morgantown_moved_from' => array(
            'type' => 'text',
            'label' => 'Moved From',
            'required' => false,
            'class' => array('form-row-wide'),
            'priority' => 160,
        ),
        'morgantown_interests' => array(
            'type' => 'multiselect',
            'label' => 'Interests',
            'required' => false,
            'class' => array('form-row-wide'),
            'priority' => 170,
            'options' => array(
                'book_club' => 'Book Club',
                'hiking' => 'Hiking',
                'dining_out' => 'Dining Out',
                'gardening' => 'Gardening',
                // Add more interests as needed
            ),
        ),
    );
}

// Add custom fields to WooCommerce checkout
function morgantown_add_custom_fields_to_checkout($fields) {
    morgantown_debug_log('Adding custom fields to checkout');
    
    $custom_fields = morgantown_get_custom_fields();
    
    foreach ($custom_fields as $key => $field) {
        $fields['billing'][$key] = $field;
    }

    morgantown_debug_log('Final checkout fields: ' . print_r($fields, true));

    return $fields;
}
add_filter('woocommerce_checkout_fields', 'morgantown_add_custom_fields_to_checkout', 99);

// Save custom fields data to order meta
function morgantown_save_custom_fields_to_order($order_id) {
    $custom_fields = morgantown_get_custom_fields();
    
    foreach ($custom_fields as $key => $field) {
        if (isset($_POST[$key])) {
            $value = sanitize_text_field($_POST[$key]);
            update_post_meta($order_id, '_' . $key, $value);
        }
    }
}
add_action('woocommerce_checkout_update_order_meta', 'morgantown_save_custom_fields_to_order');

// Create pending user after successful checkout
function morgantown_create_pending_user($order_id) {
    $order = wc_get_order($order_id);
    $user_email = $order->get_billing_email();
    
    if (!get_user_by('email', $user_email)) {
        $user_id = wp_create_user($user_email, wp_generate_password(), $user_email);
        if (!is_wp_error($user_id)) {
            wp_update_user([
                'ID' => $user_id,
                'first_name' => $order->get_billing_first_name(),
                'last_name' => $order->get_billing_last_name(),
            ]);
            
            // Set user role to a custom 'pending_approval' role
            $user = new WP_User($user_id);
            $user->set_role('pending_approval');
            
            // Save custom fields to user meta
            $custom_fields = morgantown_get_custom_fields();
            foreach ($custom_fields as $key => $field) {
                $value = get_post_meta($order_id, '_' . $key, true);
                if ($value) {
                    update_user_meta($user_id, $key, $value);
                }
            }
            
            update_post_meta($order_id, '_pending_user_id', $user_id);
            $order->add_order_note(__('Pending user account created. Awaiting admin approval.', 'morgantown-newcomers'));
        }
    }
}
add_action('woocommerce_order_status_completed', 'morgantown_create_pending_user');

// Add admin approval option to order actions
function morgantown_add_order_actions($actions) {
    global $theorder;
    
    if (!$theorder) {
        return $actions;
    }
    
    $pending_user_id = get_post_meta($theorder->get_id(), '_pending_user_id', true);
    if ($pending_user_id) {
        $actions['morgantown_approve_membership'] = __('Approve Membership', 'morgantown-newcomers');
    }
    
    return $actions;
}
add_filter('woocommerce_order_actions', 'morgantown_add_order_actions');

// Handle admin approval
function morgantown_handle_approval($order) {
    $pending_user_id = get_post_meta($order->get_id(), '_pending_user_id', true);
    if ($pending_user_id) {
        $user = new WP_User($pending_user_id);
        $user->set_role('subscriber'); // Or your desired member role
        
        // Send welcome email
        wp_new_user_notification($pending_user_id, null, 'user');
        
        $order->add_order_note(__('Membership approved and user account activated.', 'morgantown-newcomers'));
        delete_post_meta($order->get_id(), '_pending_user_id');
    }
}
add_action('woocommerce_order_action_morgantown_approve_membership', 'morgantown_handle_approval');

// Restrict access for pending users
function morgantown_restrict_pending_users($user) {
    if ($user && in_array('pending_approval', $user->roles)) {
        wp_redirect(home_url('/pending-approval'));
        exit;
    }
}
add_action('wp', function() {
    if (!is_admin() && !empty($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'pending-approval') === false) {
        morgantown_restrict_pending_users(wp_get_current_user());
    }
});

// Log that the plugin has been loaded
morgantown_debug_log('Morgantown Newcomers Membership Handler plugin loaded');