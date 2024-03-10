<?php
// Gravity Forms Customization for creating The Events Calendar events from the front end
// Reference: https://docs.gravityforms.com/advanced-post-creation-add-on-using-third-party-post-types/#h-populating-drop-down-with-event-categories

add_action( 'gform_advancedpostcreation_post_after_creation', 'update_event_information', 10, 4 );
function update_event_information( $post_id, $feed, $entry, $form ){
    //update the All Day setting
    $all_day = $entry['9.1'];
    if ( $all_day == 'All Day' ){
        update_post_meta( $post_id, '_EventAllDay', 'yes');
    }
  
    //update the Hide From Monthly View Setting
    $hide = $entry['9.2'];
    if ( $hide == 'Hide From Event Listings') {
        update_post_meta( $post_id, '_EventHideFromUpcoming', 'yes' );
    }
  
    //update the Sticky in Month View setting
    $sticky = $entry['9.3'];
    if ( $sticky == 'Sticky in Month View' ){
        wp_update_post(array( 'ID' => $post_id, 'menu_order' => '-1' ) );
    }
    else{
        wp_update_post(array( 'ID' => $post_id, 'menu_order' => '0' ) );
    }
  
    //update the Feature Event setting
    $feature = $entry['9.4'];
    if ( $feature == 'Feature Event'){
        update_post_meta( $post_id, '_tribe_featured', '1');
    }
    else{
        update_post_meta( $post_id, '_tribe_featured', '0');
    }
}
