<?php
/**
 * The template for displaying archive pages
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package buddyxpro
 */

namespace BuddyxPro\BuddyxPro;

global $booking, $wpdb, $wp_query;

get_header();

buddyxpro()->print_styles( 'buddyxpro-content' );
buddyxpro()->print_styles( 'buddyxpro-sidebar', 'buddyxpro-widgets' );

$default_sidebar = get_theme_mod( 'sidebar_option', buddyx_defaults( 'sidebar-option' ) );

$post_layout = get_theme_mod( 'blog_layout_option', buddyx_defaults( 'blog-layout-option' ) );

?>

	<?php do_action( 'buddyx_sub_header' ); ?>
	
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
		if ( have_posts() ) {

			$classes = get_body_class();
			if ( in_array( 'blog', $classes, true ) || in_array( 'archive', $classes, true ) || in_array( 'search', $classes, true ) ) {

				$args = array(
					'post_layout' => $post_layout,
					'body_class'  => $classes,
				);

				get_template_part( 'template-parts/layout/entry', $post_layout, $args );

			} else {
				while ( have_posts() ) {
					the_post();

					//get_template_part( 'template-parts/content/entry', get_post_type() );
?>
					<div class="event <?php echo Eab_Template::get_status_class($post); ?>">
                            <div class="wpmudevevents-header">
                                <h3><?php echo Eab_Template::get_event_link($post); ?></h3>
                                <a href="<?php the_permalink(); ?>" class="wpmudevevents-viewevent"><?php _e('View event', Eab_EventsHub::TEXT_DOMAIN); ?></a>
                            </div>
                            <?php
                                echo Eab_Template::get_event_details($post);
                            ?>
                            <?php
                                echo Eab_Template::get_rsvp_form($post);
                            ?>
                            <hr />
                        </div>
<?php				}
			}

			if ( ! is_singular() ) {
				get_template_part( 'template-parts/content/pagination' );
			}
		} else {
			get_template_part( 'template-parts/content/error' );
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

<?php
get_footer();
