<?php
/* setting up the title */
$title = get_the_title();

if ( is_tag() || is_tax() ) {
	$title = single_term_title( '', false );
} elseif ( is_post_type_archive() ) {
	$post_type = get_query_var( 'post_type' );
	if ( is_array( $post_type ) ) {
		$post_type = reset( $post_type );
	}
	$post_type_obj = get_post_type_object( $post_type );
	if ( isset( $post_type_obj->labels->name ) ) {
		$title = $post_type_obj->labels->name;
	}
} elseif ( is_category() ) {
	$title = single_cat_title( '', false );
} elseif ( is_author() ) {
	$author_id = get_query_var( 'author' );
	if ( $author_id ) {
		$author = get_user_by( 'id', $author_id );
		if ( ! empty( get_user_meta( $author_id, 'first_name', true ) ) ) {
			$author_name = get_user_meta( $author_id, 'first_name', true ) . ' ' . get_user_meta( $author_id, 'last_name', true );
		} else {
			$author_info = get_userdata( $author_id );
			$author_name = $author_info->data->user_login;
		}
		$title = $author_name;
	}
}

if ( is_front_page() ) {
	return;
}

if ( get_post_type() === 'post' && is_archive() ) {
	$title = get_the_archive_title();
}

if ( ! $title && is_single() ) {
	$title = get_the_title( get_queried_object_id() );
}

if ( ! is_front_page() && is_home() ) {
	$title = __( 'Blog', 'buddyxpro' );
}

if ( bp_is_members_directory() ) {
	$title = __('Members', 'buddyx');
}

if ( bp_is_groups_directory() ) {
	$title = __('Groups', 'buddyx');
}

if ( is_search() ) {
	$title = __( 'Search results for', 'buddyxpro' ) . get_search_query();
}

if ( class_exists( 'WooCommerce' ) && is_shop() ) {
	$title = __( 'Shop', 'buddyxpro' );
}

$title = apply_filters( 'buddyx_page_header_section_title', $title );

?>

<div class="site-sub-header">
	<div class="container">
		<?php
		if ( $title ) {
			echo '<div class="entry-header-title"><h1 class="entry-title page-title">' . $title . '</h1></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		$breadcrumbs = get_theme_mod( 'site_breadcrumbs', buddyx_defaults( 'site-breadcrumbs' ) );
		if ( ! empty( $breadcrumbs ) ) {
			buddyx_the_breadcrumb();
		}
		?>
	</div>
</div>
