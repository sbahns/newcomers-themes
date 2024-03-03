<?php
/**
 * BuddyPress - Members Loop
 *
 * @since 3.0.0
 * @version 3.0.0
 */

bp_nouveau_before_loop(); ?>

<?php
$enable_card_view = get_theme_mod( 'buddypress_memberes_directory_view', buddyx_defaults( 'buddypress-members-directory-view' ) );
$card_class       = '';
if ( $enable_card_view == 'card' ) {
	$card_class = 'card-view';
} elseif ( $enable_card_view == 'card1' ) {
	$card_class = 'card-view card1-view';
}

$buddyx_enabled_online_status = get_theme_mod( 'buddyx_enabled_member_directory_online_status', true );
$buddyx_enabled_profile_type  = get_theme_mod( 'buddyx_enabled_member_directory_profile_type', false );
$buddyx_enabled_followers     = get_theme_mod( 'buddyx_enabled_member_directory_followers', false );
$buddyx_enabled_last_active   = get_theme_mod( 'buddyx_enabled_member_directory_last_active', true );
$buddyx_enabled_joined_date   = get_theme_mod( 'buddyx_enabled_member_directory_joined_date', true );
?>

<?php if ( bp_get_current_member_type() ) : ?>
	<p class="current-member-type"><?php bp_current_member_type_message(); ?></p>
<?php endif; ?>

<?php if (bp_has_members(bp_ajax_querystring('members') . '&populate_extras&type=alphabetical')) : ?>

	<?php bp_nouveau_pagination( 'top' ); ?>

	<ul id="members-list" class="<?php bp_nouveau_loop_classes(); ?>">

	<?php
	while ( bp_members() ) :
		bp_the_member();

		// Member joined data.
		$member_joined_date = buddyx_get_member_joined_date( bp_get_member_user_id() );

		?>
		<li <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_member_user_id(); ?>" data-bp-item-component="members">
			<div class="list-wrap <?php echo esc_attr( $card_class ); ?>">

				<?php do_action( 'buddyx_before_member_avatar_member_directory' ); ?>

				<div class="item-avatar">
					<a href="<?php bp_member_permalink(); ?>">
						<?php
						if ( $buddyx_enabled_online_status ) {
							buddyx_user_status( bp_get_member_user_id() );
						}
						bp_member_avatar( bp_nouveau_avatar_args() );
						?>
					</a>
				</div>

				<div class="item">

					<div class="item-block">

						<div class="member-info-wrapper">
							<?php
							if ( $buddyx_enabled_profile_type ) {
								echo '<p class="item-meta member-type">' . wp_kses_post( buddyx_bp_get_user_member_type( bp_get_member_user_id() ) ) . '</p>';
							}
							?>

							<h2 class="list-title member-name">
								<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
							</h2>

							<?php if ( bp_nouveau_member_has_meta() ) : ?>
								<p class="item-meta last-activity">
									<?php
									if ( $buddyx_enabled_joined_date ) :
										echo wp_kses_post( $member_joined_date );
									endif;
									if ( ( $buddyx_enabled_joined_date ) && bp_get_last_activity() && $buddyx_enabled_last_active && ( $buddyx_enabled_joined_date ) ) :
										?>
										<span class="separator">&bull;</span>
										<?php
									endif;
									if ( $buddyx_enabled_last_active ) {
										bp_nouveau_member_meta();
									}
									?>
								</p><!-- #item-meta -->
							<?php endif; ?>

							<?php
							if ( class_exists( 'BP_Follow_Component' ) ) {
								if ( $buddyx_enabled_followers && function_exists( 'buddyx_get_members_followers_count' ) ) {
									?>
									<div class="followers-wrap">
										<?php buddyx_get_members_followers_count(); ?>
									</div>
									<?php
								}
							}
							?>
							<!-- BP_Follow_Component -->

							<?php if ( bp_nouveau_member_has_extra_content() ) : ?>
								<div class="item-extra-content">
									<?php bp_nouveau_member_extra_content(); ?>
								</div><!-- .item-extra-content -->
							<?php endif; ?>

						</div><!-- .member-info-wrapper --> 

						<div class="member-action-wrapper">
							<?php
							bp_nouveau_members_loop_buttons(
								array(
									'container'      => 'ul',
									'button_element' => 'button',
								)
							);
							?>
						</div><!-- .member-action-wrapper -->

					</div>

				</div><!-- // .item -->

				<!-- call extra buttons for tooltip layout -->
				<?php if ( 'card1' === $enable_card_view ) : ?>
					<div class="member-buttons-wrap member-action-bottom-wrapper">
						<?php if ( function_exists( 'bp_add_friend_button' ) ) : ?>
							<?php bp_add_friend_button(); ?>
						<?php endif; ?>
						<?php do_action( 'buddyx_buddypress_member_send_message_button_call' ); ?>
					</div><!-- .member-buttons-wrap -->
				<?php endif; ?>

			</div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php bp_nouveau_pagination( 'bottom' ); ?>

	<?php
else :

	bp_nouveau_user_feedback( 'members-loop-none' );

endif;
?>

<?php bp_nouveau_after_loop(); ?>
