<?php 

/////////////////////////////// Changes to Full Name requirement
/**
 * Change Name field With Username.
 */
function my_member_username() {
    global $members_template;
    return $members_template->member->user_login;
}

add_filter( 'bp_member_name' , 'my_member_username' );


/**
 * Change FullName field With Username.
 */
function my_bp_displayed_user_fullname( $fullname ) {
    
    if ( ! bp_is_user() ) {
        return $fullname;
    }
    global $bp;
    return isset( $bp->displayed_user->userdata->user_login ) ? $bp->displayed_user->userdata->user_login : $fullname;
}

add_filter( 'bp_displayed_user_fullname' , 'my_bp_displayed_user_fullname' );

/**
 * Header Script
 */
function yzc_sync_username_and_name_fields() {
    
    ?>

    <script>
        var url = document.location.href;
        jQuery(document).ready( function() {
            //copy profile name to account name during registration
            if ( url.indexOf( "register/" ) >= 0 ) {
                jQuery( 'label[for=field_1],#field_1' ).css( 'display', 'none' );
                jQuery( '#signup_username' ).blur( function() {
                    jQuery( '#field_1' ).val( jQuery( "#signup_username" ).val());
                });
            }
        });
    </script>

    <style type="text/css">
        #profile-details-section .logy-section-title,
        .editfield.field_1 {
            display: none;
        }

    </style>

    <?php
}
add_action( 'wp_head', 'yzc_sync_username_and_name_fields' );
///////////////////////////