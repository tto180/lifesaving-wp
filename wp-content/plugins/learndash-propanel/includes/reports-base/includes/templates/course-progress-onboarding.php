<?php
/**
 * Template to display the Learner activity onboarding notice.
 *
 * @package LearnDash\Reports
 *
 * @since 3.0.0
 * @version 3.0.0
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="notice notice-error is-dismissible wrld-la-main-container" style="padding:0px !important;">

	<div class="wrld-la-logo">
		<img class="wrld-la-logo-img" src='<?php echo esc_html( $wisdm_logo ); ?>'>
	</div>
	<div class="wrld-la-center">
		<div class="wrld-la-head-text">
			<?php echo esc_html( $banner_head ); ?>
		</div>
		<div class="wrld-la-sub-text">
			<?php echo esc_html( $banner_message ); ?>
		</div>
		<?php if ( isset( $banner_message_addon ) && ! empty( $banner_message_addon ) ) : ?>
		<div class="wrld-la-additional-text">
			<?php echo esc_html( $banner_message_addon ); ?>
		</div>
		<?php endif; ?>

		<div class="wrld-la-btn-container">
			<div class="left-box">
				<div class="first-row">
				<b class="add-weight"> <?php esc_html_e( ' Manually Enable', 'learndash-reports-pro' ); ?></b>
					<?php esc_html_e( ' Manually Enable the reporting blocks', 'learndash-reports-pro' ); ?>
				</div>
				<div class="second-row">
					<?php esc_html_e( 'Go to the dashboard page --> edit --> search for the Course Progress blocks --> Insert.', 'learndash-reports-pro' ); ?>
					<a href="https://go.learndash.com/ppdocs" target="_blank"><?php esc_html_e( 'Learn more', 'learndash-reports-pro' ); ?></a>
				</div>
				<a href="<?php echo esc_attr( add_query_arg( array( 'preload_progress' => 1 ), $page_link ) ); ?>"> <button href="#" class="wrld-la-manual-button">
						<div class="wrld-btn-txt">
							<?php esc_html_e( ' Manually Edit Page', 'learndash-reports-pro' ); ?></div>
						<span class="right_arrow_icon">></span>
					</button></a>
			</div>
			<div class="center-box">
				<div class="or-container">
					<span><?php esc_html_e( ' OR', 'learndash-reports-pro' ); ?></span>
				</div>
			</div>
			<div class="right-box">
				<div class="first-r-row">
				<b class="add-weight"><?php esc_html_e( ' Note:', 'learndash-reports-pro' ); ?></b>
					<?php esc_html_e( 'While Auto updating  we will delete the current blocks pattern and replace with the new one including the course progress block', 'learndash-reports-pro' ); ?>
					<b class="add-weight"><?php esc_html_e( 'If any custom changes were made to the Dashboard page, then they will be lost.', 'learndash-reports-pro' ); ?></b>
				</div>
				<a href="#"><button href="#" class="wrld-la-auto-button">
						<div class="wrld-btn-txt">
							<?php esc_html_e( ' Auto Update Page ', 'learndash-reports-pro' ); ?></div>
						<span class="right_arrow_icon">></span>
					</button></a>
			</div>
		</div>

	</div>
</div>
