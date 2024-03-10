<?php
///// MEMBER DIRECTORY

/**
 * Sort users by last name
 *
 * Changes the querystring for the member directory to sort users by their last name
 *
 * @param BP_User_Query $bp_user_query
 */
function alphabetize_by_last_name( $bp_user_query ) {
    if ( 'alphabetical' == $bp_user_query->query_vars['type'] )
        $bp_user_query->uid_clauses['orderby'] = "ORDER BY substring_index(u.display_name, ' ', -1)";
}
add_action ( 'bp_pre_user_query', 'alphabetize_by_last_name' );



///////////////////////// Member Directory Customization (from Youzify)

/**
 * Select Alphabet on Select Box.
 * https://gist.github.com/KaineLabs/4795fa7a6725389b246c9b4020491798#file-yzc_make_alphabet_selected-php
 */
function yzc_make_alphabet_selected() {

    ?>
    <script type="text/javascript">

    ( function( $ ) {

    $( document ).ready( function() {
    
        jQuery( '#members-order-by option[value="alphabetical"], #groups-order-by option[value="alphabetical"]' ).attr( 'selected', true ).trigger( 'change');

    });

    })( jQuery );
    </script>
    <?php

}
//add_action( 'wp_footer', 'yzc_make_alphabet_selected' );

///////////////////////////////