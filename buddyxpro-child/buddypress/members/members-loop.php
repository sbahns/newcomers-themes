<?php
/**
 * BuddyPress - Members Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 */

/**
 * Fires before the display of the members loop.
 *
 * @since 1.2.0
 */

do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' )) ) : ?>

	<?php

	/**
	 * Fires before the display of the members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_members_list' ); ?>

	<ul id="members-list" class="<?php echo youzify_members_list_class() ?>" aria-live="assertive" aria-relevant="all">

	<?php while ( bp_members() ) : 
		bp_the_member(); 
		
		// Member joined data.
		$member_joined_date = buddyx_get_member_joined_date( bp_get_member_user_id() );
		
		?>
		

		<li <?php bp_member_class( array( 'item-entry' ) ); ?> data-bp-item-id="<?php bp_member_user_id(); ?>" data-bp-item-component="members">

			<div class="list-wrap <?php echo esc_attr( $card_class ); ?> youzify-user-data">
			<?php do_action( 'buddyx_before_member_avatar_member_directory' ); ?>

				<?php //if ( bp_is_directory() ) : ?>

					<?php //youzify_get_user_tools( bp_get_member_user_id() ); ?>

					<?php //youzify_members_directory_user_cover( bp_get_member_user_id() ); ?>

				<?php //endif; ?>

			<div class="item-avatar youzify-item-avatar">
				<a href="<?php bp_member_permalink(); ?>">
					<?php
						if ( $buddyx_enabled_online_status ) {
							buddyx_user_status( bp_get_member_user_id() );
						}
						bp_member_avatar( bp_nouveau_avatar_args() );
					?>
					<?php //bp_member_avatar( 'type=full&width=100&height=100' ); ?>
				</a>
			</div>

			<div class="item">
				<div class="item-block">
					<div class="member-info-wrapper">
						<!-- Youzify item title <div class="item-title">
							<a class="youzify-fullname" href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
						</div> -->
						<h2 class="list-title member-name">
							<a href="<?php bp_member_permalink(); ?>"><?php bp_member_name(); ?></a>
						</h2>

						<!-- Youzify item-meta <div class="item-meta">
							<?php //if ( bp_current_action( 'my-friends' ) ) { ?>
								<span class="activity" data-livestamp="<?php //bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>"><?php //bp_member_last_active(); ?></span>

							<?php //} else { ?>
								<?php //echo youzify_get_md_user_meta( bp_get_member_user_id() ); ?>
							<?php //} ?>

							<?php

								/**
								 * Fires inside the display of a directory member item.
								 *
								 * @since 1.1.0
								 */
								//do_action( 'bp_directory_members_item_meta' ); ?>
						</div> -->
						
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

						/**
						 * Fire	s inside the display of a directory member item.
						 *
						 * @since 1.1.0
						 */
						do_action( 'bp_directory_members_item' ); ?>

						<?php
							/***
							 * If you want to show specific profile fields here you can,
							* but it'll add an extra query for each member in the loop
							* (only one regardless of the number of fields you show):
							*
							* bp_member_profile_data( 'field=the field name' );
							*/
						?>
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
				</div>	<!-- // .item-block -->
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


			<?php
				// if ( bp_is_directory() ) {
				// 	youzify_get_member_statistics_data( bp_get_member_user_id() );
				// }
			?>

			<?php //if ( 'on' == youzify_option( 'youzify_enable_md_cards_actions_buttons', 'on' ) && is_user_logged_in() ) : ?>

				<div class="youzify-user-actions">

					<?php
					/**
					 * Fires inside the members action HTML markup to display actions.
					 *
					 * @since 1.1.0
					 */
					//do_action( 'bp_directory_members_actions' ); ?>

				</div>

			<?php //endif; ?>

			<?php //do_action( 'youzify_after_directory_members_actions' ); ?>

			

			</div>

		</li>

	<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the display of the members list.
	 *
	 * @since 1.1.0
	 */
	//do_action( 'bp_after_directory_members_list' ); ?>

	<?php //bp_member_hidden_fields(); ?>

	<!-- <div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-dir-count-bottom">
			<?php //bp_members_pagination_count(); ?>
		</div>

		<?php //if ( bp_get_members_pagination_links() ) : ?>
		<div class="pagination-links" id="member-dir-pag-bottom">
			<?php //bp_members_pagination_links(); ?>
		</div>
		<?php //endif; ?>

	</div> -->

<!-- <?php //else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'youzify' ); ?></p>
	</div>

<?php //endif; ?> -->

<?php bp_nouveau_pagination( 'bottom' ); ?>

	<?php
else :

	bp_nouveau_user_feedback( 'members-loop-none' );

endif;
?>


<?php

/**
 * Fires after the display of the members loop.
 *
 * @since 1.2.0
 */
//do_action( 'bp_after_members_loop' );

 bp_nouveau_after_loop(); ?>