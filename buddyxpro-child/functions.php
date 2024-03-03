<?php
/**
 * Theme functions and definitions.
 * This child theme was generated by Merlin WP.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

/*
 * If your child theme has more than one .css file (eg. ie.css, style.css, main.css) then
 * you will have to make sure to maintain all of the parent theme dependencies.
 *
 * Make sure you're using the correct handle for loading the parent theme's styles.
 * Failure to use the proper tag will result in a CSS file needlessly being loaded twice.
 * This will usually not affect the site appearance, but it's inefficient and extends your page's loading time.
 *
 * @link https://codex.wordpress.org/Child_Themes
 */
function buddyxpro_child_enqueue_styles() {
	wp_enqueue_style( 'buddyx-pro-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style(
		'buddyx-pro-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'buddyx-pro-style' ),
		wp_get_theme()->get( 'Version' )
	);
}

add_action( 'wp_enqueue_scripts', 'buddyxpro_child_enqueue_styles' );

if ( get_stylesheet() !== get_template() ) {
	add_filter(
		'pre_update_option_theme_mods_' . get_stylesheet(),
		function ( $value, $old_value ) {
			update_option( 'theme_mods_' . get_template(), $value );
			return $old_value; // prevent update to child theme mods.
		},
		10,
		2
	);
	add_filter(
		'pre_option_theme_mods_' . get_stylesheet(),
		function (
			$default_values
		) {
			return get_option( 'theme_mods_' . get_template(), $default_values );
		}
	);
}

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

// Functions.php or custom plugin

function bp_members_default_sort_alphabetical( $sort_options ) {

	$sort_options['default'] = 'alphabetical'; 
  
	return $sort_options;
  
  }
  add_filter( 'bp_members_directory_sort_options', 'bp_members_default_sort_alphabetical' );
  
  function bp_members_sort_alphabetical( $query_args ) {
  
	if ( isset( $query_args['type'] ) && $query_args['type'] == 'alphabetical' ) {
	  $query_args['orderby'] = 'user_lastname';
	  $query_args['order'] = 'ASC'; 
	}
  
	return $query_args;
  
  }
  add_filter( 'bp_after_has_members_parse_args', 'bp_members_sort_alphabetical' );


