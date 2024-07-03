<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package buddyxpro
 */

namespace BuddyxPro\BuddyxPro;

global $blog_id, $wp_query, $booking, $post, $current_user;
$event = new Eab_EventModel($post);

get_header();

buddyxpro()->print_styles( 'buddyxpro-content' );
buddyxpro()->print_styles( 'buddyxpro-sidebar', 'buddyxpro-widgets' );

if ( get_post_type() == 'post' ) {
	$default_sidebar = get_theme_mod( 'single_post_sidebar_option', buddyx_defaults( 'single-post-sidebar-option' ) );
} else {
	$default_sidebar = get_theme_mod( 'sidebar_option', buddyx_defaults( 'sidebar-option' ) );
}

$single_post_content_width = '';
$classes                   = '';

if ( get_post_type() == 'post' ) {
	$single_post_content_width = get_theme_mod( 'single_post_content_width', buddyx_defaults( 'single-post-content-width' ) );

	// Sidebar Classes.
	if ( $default_sidebar == 'left' ) {
		$classes = 'has-single-post-left-sidebar';
	} elseif ( $default_sidebar == 'right' ) {
		$classes = 'has-single-post-right-sidebar';
	} elseif ( $default_sidebar == 'both' ) {
		$classes = 'has-single-post-both-sidebar';
	} else {
		$classes = 'has-single-post-no-sidebar';
	}
}

?>
<div class="single-post-main-wrapper buddyx-content--<?php echo esc_attr( $single_post_content_width ); ?> <?php echo esc_attr( $classes ); ?>">

	<?php do_action( 'buddyx_sub_header' ); ?>

	<?php
	if ( get_post_type() == 'post' ) {
		get_template_part( 'template-parts/content/entry-header', get_post_type() );
	}
	?>

	<?php do_action( 'buddyx_before_content' ); ?>

	<?php if ( $default_sidebar == 'left' || $default_sidebar == 'both' ) : ?>
		<aside id="secondary" class="left-sidebar widget-area">
			<div class="sticky-sidebar">
				<?php buddyxpro()->display_left_sidebar(); ?>
			</div>
		</aside>
	<?php endif; ?>

	<main id="primary" class="site-main">

		<?php
		while ( have_posts() ) {
			the_post();

			//get_template_part( 'template-parts/content/entry', get_post_type() );
			$start_day = date_i18n('m', strtotime(get_post_meta($post->ID, 'incsub_event_start', true)));
			?>
			<div class="wpmudevevents-header">
			<h2><?php echo $event->get_title(); ?></h2>
			<div class="eab-needtomove"><div id="event-bread-crumbs" ><?php echo Eab_Template::get_breadcrumbs($event); ?></div></div>
			<?php
			echo Eab_Template::get_rsvp_form($post);
			echo Eab_Template::get_inline_rsvps($post);
			if ($event->is_premium() && $event->user_is_coming() && !$event->user_paid()) { ?>
    		    <div id="wpmudevevents-payment">
    			<?php _e('You haven\'t paid for this event', Eab_EventsHub::TEXT_DOMAIN); ?>
                            <?php echo Eab_Template::get_payment_forms($post); ?>
    		    </div>
                        <?php } ?>

                        <?php echo Eab_Template::get_error_notice(); ?>

                        <div class="wpmudevevents-content">
    			<div id="wpmudevevents-contentheader">
                                <h3><?php _e('About this event:', Eab_EventsHub::TEXT_DOMAIN); ?></h3>

    			    <div id="wpmudevevents-user"><?php _e('Created by ', Eab_EventsHub::TEXT_DOMAIN); ?><?php the_author_link();?></div>
    			</div>

                            <hr />
    			<div class="wpmudevevents-contentmeta">
                                <?php echo Eab_Template::get_event_details($post); //event_details(); ?>
    			</div>
    			<div id="wpmudevevents-contentbody">
    			    <?php
    			    	add_filter('agm_google_maps-options', 'eab_autoshow_map_off', 99);
    			    	the_content();
    					remove_filter('agm_google_maps-options', 'eab_autoshow_map_off');
    			    ?>
    			    <?php if ($event->has_venue_map()) { ?>
    			    	<div class="wpmudevevents-map"><?php echo $event->get_venue_location(Eab_EventModel::VENUE_AS_MAP); ?></div>
    			    <?php } ?>
                            </div>
                            <?php comments_template( '', true ); ?>
                        </div>
                    </div>
            </div>
          <?php 
		}
		?>

	</main><!-- #primary -->

	<?php if ( $default_sidebar == 'right' || $default_sidebar == 'both' ) : ?>
		<aside id="secondary" class="primary-sidebar widget-area">
			<div class="sticky-sidebar">
				<?php buddyxpro()->display_right_sidebar(); ?>
			</div>
		</aside>
	<?php endif; ?>

	<?php do_action( 'buddyx_after_content' ); ?>
</div>
<?php
get_footer();
