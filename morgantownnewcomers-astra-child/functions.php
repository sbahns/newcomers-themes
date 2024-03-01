<?php
/**
 * Morgantown Newcomers Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Morgantown Newcomers
 * @since 1.0.0
 */

/**
 * Define Constants
 */
define( 'CHILD_THEME_MORGANTOWN_NEWCOMERS_VERSION', '1.0.0' );

/**
 * Enqueue styles
 */
function child_enqueue_styles() {

	wp_enqueue_style( 'morgantown-newcomers-theme-css', get_stylesheet_directory_uri() . '/style.css', array('astra-theme-css'), CHILD_THEME_MORGANTOWN_NEWCOMERS_VERSION, 'all' );

}

add_action( 'wp_enqueue_scripts', 'child_enqueue_styles', 15 );


function display_current_user_display_name() {
    $user = wp_get_current_user();
    $display_name = $user->display_name;
    return $display_name;
}
add_shortcode('current_user_display_name', 'display_current_user_display_name');


