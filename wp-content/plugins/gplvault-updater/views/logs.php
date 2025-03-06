<?php
defined( 'ABSPATH' ) || exit;
/** @var GPLVault_Settings_Manager $settings_manager */
/** @var $log_files */
$is_license_activated = $settings_manager->license_is_activated();
$settings_url         = GPLVault_Admin::admin_links( 'settings' );
?>
<div class="wrap gv-wrapper gv-wrapper-logs" id="gv_logs_wrapper">
<?php if ( $log_files ) : ?>
	<div id="gv-log-viewer-select" class="gv-log-viewer-select-block">
		<div class="alignright">
			<form action="<?php echo esc_url( admin_url( 'admin.php?page=' . GPLVault_Admin::SLUG_LOGS ) ); ?>" method="post">
				<label for="gv_log_file" class="screen-reader-text">Select Log File</label>
				<select class="gv-select2" id="gv_log_file" name="gv_log_file">
					<?php foreach ( $log_files as $log_key => $log_file ) : ?>
						<?php
						$timestamp = filemtime( GV_UPDATER_LOG_DIR . $log_file );
						$date      = sprintf(
						/* translators: 1: last access date 2: last access time 3: last access timezone abbreviation */
							__( '%1$s at %2$s %3$s', 'gplvault' ),
							wp_date( gv_date_format(), $timestamp ),
							wp_date( gv_time_format(), $timestamp ),
							wp_date( 'T', $timestamp )
						);
						?>
						<option value="<?php echo esc_attr( $log_key ); ?>" <?php selected( sanitize_title( $active_log ), $log_key ); ?>><?php echo esc_html( $log_file ); ?> (<?php echo esc_html( $date ); ?>)</option>
					<?php endforeach; ?>
				</select>
				<button type="submit" class="button" value="<?php esc_attr_e( 'View', 'gplvault' ); ?>"><?php esc_html_e( 'View', 'gplvault' ); ?></button>
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<?php if ( ! empty( $active_log ) ) : ?>
	<div class="gv-log-info">
		<div class="gv-log-filename">
			<strong><?php echo esc_html( $active_log ); ?></strong>
		</div>
		<div class="gv-log-file-action">
			<?php if ( ! empty( $active_log ) ) : ?>
				<a class="page-title-action" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'gv_log_remove' => sanitize_title( $active_log ) ), admin_url( 'admin.php?page=' . GPLVault_Admin::SLUG_LOGS ) ), 'gv_remove_log' ) ); ?>" class="button"><?php esc_html_e( 'Delete log', 'gplvault' ); ?></a>
				<a class="page-title-action" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'gv_log_download' => sanitize_title( $active_log ) ), admin_url( 'admin.php?page=' . GPLVault_Admin::SLUG_LOGS ) ), 'gv_download_log' ) ); ?>" class="button"><?php esc_html_e( 'Download log', 'gplvault' ); ?></a>
			<?php endif; ?>
		</div>
	</div>
	<?php endif; ?>
	<div id="gv-log-viewer" class="gv-log-content">
		<pre><?php echo esc_html( file_get_contents( GV_UPDATER_LOG_DIR . $active_log ) ); // @phpcs:ignore ?></pre>
	</div>
<?php else : ?>
	<div class="updated inline"><p><?php esc_html_e( 'There are currently no logs to view.', 'gplvault' ); ?></p></div>
<?php endif; ?>
</div>
