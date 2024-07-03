<?php

///////////////////////// WP-Members profile sync

/**
 * Map xProfile fields to WP-Members fields during registration.
 *
 * @param array $fields     WP-Members fields.
 * @param array $userdata   User data from the registration form.
 *
 * @return array            Mapped fields.
 */
// function my_map_xprofile_fields($fields, $userdata) {
//     // Get xProfile field data
//     $xprofile_data = isset($_POST['xprofile-data']) ? $_POST['xprofile-data'] : array();

//     // Get all xProfile fields
//     $xprofile_groups = bp_xprofile_get_groups();
//     foreach ($xprofile_groups as $group) {
//         $xprofile_fields = bp_xprofile_fields_by_group($group->id);
//         foreach ($xprofile_fields as $field) {
//             // Map xProfile field to WP-Members field
//             $fields[$field->name] = isset($xprofile_data[$field->id]) ? $xprofile_data[$field->id] : '';
//         }
//     }

//     return $fields;
// }
// add_filter('wpmem_register_data', 'my_map_xprofile_fields', 10, 2);

/**
 * Save xProfile data after successful registration.
 *
 * @param int    $user_id    ID of the newly registered user.
 * @param array  $userdata   User data from the registration form.
 * @param bool   $new_user   True if a new user was registered.
 */
// function my_save_xprofile_data($user_id, $userdata, $new_user) {
//     if ($new_user) {
//         // Get xProfile field data
//         $xprofile_data = isset($_POST['xprofile-data']) ? $_POST['xprofile-data'] : array();

//         // Save xProfile data for the new user
//         foreach ($xprofile_data as $field_id => $field_value) {
//             xprofile_set_field_data($field_id, $user_id, $field_value);
//         }
//     }
// }
// add_action('wpmem_register_successful', 'my_save_xprofile_data', 10, 3);

/**
 * Add xProfile fields to the WP-Members registration form.
 *
 * @param array $form_rows   WP-Members form rows.
 *
 * @return array Updated form rows with xProfile fields.
 */
// function my_add_xprofile_fields($form_rows) {
//     // Get xProfile fields markup
//     $xprofile_fields = '';
//     $xprofile_groups = bp_xprofile_get_groups();
//     foreach ($xprofile_groups as $group) {
//         $xprofile_fields .= bp_get_template_part('members/members-xprofile-fields');
//     }

//     // Inject xProfile fields into the form
//     $form_rows['my_custom_fields'] = $xprofile_fields;

//     return $form_rows;
// }
// add_filter('wpmem_register_form_rows', 'my_add_xprofile_fields');

///////////////////////////////

/////// Activate All Members (remove when not needed)
/** WP Members Function
 * A drop-in code snippet to set all users on the site as 
 * "activated" (when using the plugin's moderated registration
 * setting). All existing users on the site will be set as 
 * active without affecting passwords and no email will be sent.
 *
 * To Use:
 * 1. Save the code snippet to your theme's functions.php
 * 2. Go to Tools > Activate All Users.
 * 3. Follow prompts on screen.
 * 4. Remove the code snippet when completed.
 */
// add_action( 'init', 'activate_all_users_init' );
// function activate_all_users_init() {
//     global $wpmem;
//     $wpmem->activate_all_users = New My_Activate_All_Users_Class();
// }
// class My_Activate_All_Users_Class {
 
//     function __construct() {
//         add_action( 'admin_menu', array( $this, 'admin_menu' ) );
//     }

//     function admin_menu() {
//         $hook = add_management_page( 'Activate All Users Page', 'Activate All Users', 'edit_users', 'activate-all-users', array( $this, 'admin_page' ), '' );
//         add_action( "load-$hook", array( $this, 'admin_page_load' ) );
//     }

//     function admin_page_load() {
//         global $activate_all_complete;
//         $activate_all_complete = false;
//         if ( isset( $_GET['page'] ) && 'activate-all-users' == $_GET['page'] && isset( $_POST['activate-all-confirm'] ) && 1 == $_POST['activate-all-confirm'] ) {
//             $users = get_users( array( 'fields'=>'ID' ) );
//             foreach ( $users as $user_id ) {
//                 update_user_meta( $user_id, 'active', 1 );
//                 wpmem_set_user_status( $user_id, 0 );
//             }
//             $activate_all_complete = true;
//         }
//     }

//     function admin_page() {
//         global $activate_all_complete;
//         echo "<h2>Activate All Users</h2>";
//         if ( $activate_all_complete ) {
//             echo '<p>All users were activated.<br />';
//             echo 'You may now remove this code snippet if desired.</p>';
//         } else {
//             $form_post = ( function_exists( 'wpmem_admin_form_post_url' ) ) ? wpmem_admin_form_post_url() : '';
//             echo "<p>This process will mark all existing user accounts as activated in WP-Members.<br />It will not change any passwords or send any emails to users.";
//             echo '<form name="activate-all-users" id="activate-all-users" method="post" action="' . $form_post . '">';
//             echo '<p><input type="checkbox" name="activate-all-confirm" value="1" /><label for="activate-all-confirm">Activate all users?</label></p>';
//             echo '<p><input type="submit" name="submit" value="Submit" /></p>';
//             echo '</form>';
//         }
//     }
// }
// End of My_Activate_All_Users_Class


//////// WP-Members Fields display on WooCommerce My Account
/**
 * This code snippet will add fields from the WP-Members fields array to the
 * WooCommerce My Account > Account Details screen.  Set the meta keys to be
 * included in the array in my_add_wc_account_fields().  The snippet will
 * handle the rest.
 */
function my_add_wc_account_fields() {
    // Use this array to identify meta keys of fields you want to include
    return array( 'description', 'a_custom_field' );
}

add_action( 'woocommerce_edit_account_form', function() {
    $user    = wp_get_current_user();
    $fields  = wpmem_fields();
    $include = my_add_wc_account_fields();

    foreach ( $fields as $meta_key => $field ) {
        if ( in_array( $meta_key, $include ) && 1 == $field['register'] ) {
            woocommerce_form_field( $meta_key, array(
                'type'        => $field['type'],
                'required'    => ( 1 == $field['required'] ) ? true : false,
                'label'       => $field['label'],
            ), wpmem_get_user_meta( $user->ID, $meta_key ) );
        }
    }
});

add_action( 'woocommerce_save_account_details', function( $user_id ) {
    $fields  = wpmem_fields();
    $include = my_add_wc_account_fields();
    foreach ( $fields as $meta_key => $field ) {
        if ( in_array( $meta_key, $include ) && isset( $_POST[ $meta_key ] ) ) {
            update_user_meta( $user_id, $meta_key, sanitize_text_field( $_POST[ $meta_key ] ) );
        }
    }
});

add_filter('woocommerce_save_account_details_required_fields', function( $required_fields ) {
    $fields  = wpmem_fields();
    $include = my_add_wc_account_fields();
    foreach( $fields as $meta_key => $field ) {
        if ( in_array( $meta_key, $include ) && 1 == $field['required'] ) {
            $required_fields[ $meta_key ] = $field['label'];
        }
    }
    return $required_fields;
});

//////// End WP-Members Fields display on WooCommerce My Account