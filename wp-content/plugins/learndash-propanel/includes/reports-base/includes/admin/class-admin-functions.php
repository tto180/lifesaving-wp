<?php
/**
 * This file contains the class admin functions.
 *
 * @package LearnDash\Reports
 *
 * cspell:ignore helpscout autoupdate
 */

namespace WisdmReportsLearndash;

defined( 'ABSPATH' ) || exit;

/**
 *
 * This class is responsible for admin-related functions and actions.
 */
class Admin_Functions {
	protected static $instance = null;

	/**
	 * This function has been used to create the static instance of the admin functions class.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * This is the constructor for the class that will instantiate all processes related to the class.
	 */
	public function __construct() {
		$this->init_hooks();
	}

	/**
	 * This method is used to add all the admin related actions and filter hooks.
	 */
	public function init_hooks() {
		add_action( 'wp_ajax_wrld_page_visit', array( $this, 'update_reporting_page_visit' ) );
		add_action( 'wp_ajax_wrld_gutenberg_block_visit', array( $this, 'wp_ajax_wrld_gutenberg_block_visit' ) );
		add_action( 'wp_ajax_wrld_notice_action', array( $this, 'wrld_notice_action' ) );
		add_action( 'init', array( $this, 'wrld_create_patterns_page' ), 99 );
		add_action( 'init', array( $this, 'wrld_create_student_patterns_page' ), 99 );
		// settings page ajax actions
		add_action( 'wp_ajax_wrld_set_configuration', array( $this, 'wrld_set_configuration' ) );
		add_action( 'wp_ajax_wrld_update_settings', array( $this, 'wrld_update_settings' ) );
		add_action( 'wp_ajax_wrld_exclude_settings_save', array( $this, 'wrld_exclude_settings_save' ) );
		add_action( 'wp_ajax_apply_time_tracking_settings', array( $this, 'apply_time_tracking_settings' ) );
		add_action( 'wp_ajax_wrld_exclude_load_users', array( $this, 'wrld_exclude_load_users' ) );
		add_action( 'wp_ajax_wrld_autoupdate_dashboard', array( $this, 'wrld_autoupdate_dashboard' ) );
		// add_action( 'admin_notices', array( $this, 'wrld_pattern_update_notice' ) );
	}

	/**
	 *
	 * Processes the wrld_gutenberg_block_visit AJAX request.
	 *
	 * @return never
	 */
	public static function wp_ajax_wrld_gutenberg_block_visit() {
		$nonce = ! empty( $_REQUEST['wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wp_nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $nonce, 'wrld-admin-settings' ) ) {
			wp_send_json_error( esc_html__( "You don't have permission to do this action.", 'learndash-reports-pro' ) );
		}

		if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
			wp_send_json_error( esc_html__( "You don't have permission to do this action.", 'learndash-reports-pro' ) );
		}

		$option_key = isset( $_REQUEST['option_key'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['option_key'] ) ) : null;

		if ( empty( $option_key ) ) {
			wp_send_json_error( esc_html__( 'Invalid option key.', 'learndash-reports-pro' ) );
		}

		update_option( $option_key, true );

		wp_send_json_success(
			array(
				'updated' => $option_key,
			)
		);
	}

	public function wrld_pattern_update_notice() {
		if ( ! isset( $_GET['updated_pattern'] ) ) {
			return;
		}
		echo "<div class='notice notice-success'>
			<p>Dashboard Page successfully updated.</p>
		</div>";
	}

	/**
	 * Processes the wrld_autoupdate_dashboard AJAX request.
	 *
	 * @return never
	 */
	public function wrld_autoupdate_dashboard() {
		$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';

		$nonce_valid = wp_verify_nonce( $nonce, 'wrld-admin-settings' );
		if ( ! $nonce_valid ) {
			wp_send_json_error(
				array(
					'success' => true,
					'message' => esc_html__(
						'Nonce verification failed',
						'learndash-reports-pro'
					),
				)
			);
		}

		if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
			wp_send_json_error( esc_html__( "You don't have permission to do this action.", 'learndash-reports-pro' ) );
		}

		global $wrld_pattern;
		$reports_page = get_option( 'ldrp_reporting_page', false );
		$data         = array(
			'ID'           => $reports_page,
			'post_content' => $wrld_pattern,
		);
		wp_update_post( $data );
		wp_send_json_success(
			array(
				'url' => add_query_arg(
					array(
						'updated_pattern' => 1,
						'dla_op'          => true,
						'dla_oq'          => true,
					),
					get_permalink( $reports_page )
				),
			)
		);
		die();
	}

	/**
	 * This action is hooked to the ajax callback action 'wp_ajax_wrld_page_visit',
	 * on called verifies if the request is from the correct source & update teh value for the
	 * option 'wrld_reporting_page_visited'.
	 */
	public static function update_reporting_page_visit() {
		$nonce = ! empty( $_REQUEST['wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wp_nonce'] ) ) : '';
		// cspell:disable-next-line .
		if ( wp_verify_nonce( $nonce, 'reports-firrst-install-modal' ) ) {
			// update_option( 'wrld_reporting_page_visited', 2 );
			wp_send_json_success();
		}

		wp_send_json_error();
	}


	/**
	 * This action is hooked to the ajax callback action 'wp_ajax_wrld_notice_action',
	 * on called verifies if the request is from the correct source & update the value for the
	 * option 'wrld_last_skipped_on'.
	 */
	public static function wrld_notice_action() {
		$nonce = ! empty( $_REQUEST['wp_nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['wp_nonce'] ) ) : '';
		if ( wp_verify_nonce( $nonce, 'wrld-review-notice' ) ) {
			$user_action = sanitize_text_field( wp_unslash( $_REQUEST['user_action'] ) );
			switch ( $user_action ) {
				case 'wisdm-reports-for-ld-remind-later':
				case 'wisdm-reports-for-ld-post-review':
				case 'close':
					update_option( 'wrld_last_skipped_on', time() );
					break;
				default:
					update_option( 'ld-reports-review-dismissed', time() );
					break;
			}

			wp_send_json_success();
		}

		wp_send_json_error();
	}

	/**
	 * Outputs the submenu page.
	 *
	 * @return void
	 */
	public function show_wisdm_reports_submenu() {
		?>
<div class="wrap">
	<h1><?php esc_html_e( 'Reports', 'learndash-reports-pro' ); ?></h1>
		<?php
			$dashboard_id = get_option( 'ldrp_reporting_page', false );
		if ( ! $dashboard_id || 'publish' !== get_post_status( $dashboard_id ) ) {
			?>
	<p><?php esc_html_e( 'The page seems to have been deleted/removed. Please click on the link below to create the page manually.', 'learndash-reports-pro' ); ?>
	</p>
	<a
		href="<?php echo esc_attr( add_query_arg( array( 'create_wisdm_reports' => true ) ) ); ?>"><?php esc_html_e( 'Create ProPanel Dashboard', 'learndash-reports-pro' ); ?></a>
			<?php
		} else {
			?>
	<p><a class="primary-button primary-btn components-button is-primary"
			href="<?php echo esc_url( get_permalink( $dashboard_id ) ); ?>" target="_blank"
			style="background:#007cba;color:#fff;text-decoration:none;text-shadow:none;outline:1px solid transparent;padding:6px 12px;border-radius:2px;"><?php esc_html_e( 'Launch ProPanel Dashboard', 'learndash-reports-pro' ); ?></a>
	</p>
			<?php
		}
		?>
</div>
		<?php
	}

	/**
	 * This function creates the page with the default WRLD pattern when a get parameter 'create_wisdm_reports' is set.
	 */
	public function wrld_create_patterns_page() {
		$new_page_creation = filter_input( INPUT_GET, 'create_wisdm_reports', FILTER_VALIDATE_BOOLEAN );
		$created_page      = get_option( 'ldrp_reporting_page', false );

		if ( $created_page && 'publish' !== get_post_status( $created_page ) ) {
			if ( ! empty( $new_page_creation ) ) {
				wrld_create_patterns_page( true );
				// add_action( 'admin_notices', '\WisdmReportsLearndash\Admin_Functions::report_page_creation_notice' );
			}
		} elseif ( false == $created_page ) {
			wrld_create_patterns_page( false );
				// add_action( 'admin_notices', '\WisdmReportsLearndash\Admin_Functions::report_page_creation_notice' );
		}
	}
	/**
	 * This function creates the page with the default WRLD pattern when a get parameter 'create_wisdm_reports' is set.
	 */
	public function wrld_create_student_patterns_page() {
		$new_page_creation = filter_input( INPUT_GET, 'create_student_reports', FILTER_VALIDATE_BOOLEAN );
		$created_page      = get_option( 'ldrp_student_page', false );

		if ( $created_page && 'publish' !== get_post_status( $created_page ) ) {
			if ( ! empty( $new_page_creation ) ) {
				wrld_create_student_patterns_page( true );
				// add_action( 'admin_notices', '\WisdmReportsLearndash\Admin_Functions::report_student_page_creation_notice' );
			}
		} elseif ( false == $created_page ) {
			wrld_create_student_patterns_page( false );
				// add_action( 'admin_notices', '\WisdmReportsLearndash\Admin_Functions::report_student_page_creation_notice' );
		}
	}

	/**
	 * The function checks if the plugin specified is installed on the website.
	 * - Currently works only for the WordPress single site.
	 *
	 * @param $plugin_slug plugin path and plugin file name, ex : learndash-reports-pro/learndash-reports-pro.php.
	 */
	public static function is_plugin_installed( $plugin_slug ) {
		if ( empty( $plugin_slug ) ) {
			return false;
		}

		// Check if needed functions exists - if not, require them
		if ( ! function_exists( 'get_plugins' ) || ! function_exists( 'is_plugin_active' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed_plugins = get_plugins();
		return array_key_exists( $plugin_slug, $installed_plugins ) || in_array( $plugin_slug, $installed_plugins, true );
	}

	/**
	 * Shows admin a notice on the admin dashboard.
	 */
	public static function report_page_creation_notice() {
		echo '<div class="notice notice-success is-dismissible"><p>';
		echo esc_html__( 'Successfully created ProPanel page', 'learndash-reports-pro' );
		echo '</p></div>';
	}

	/**
	 * Shows admin a notice on the admin dashboard.
	 */
	public static function report_student_page_creation_notice() {
		echo '<div class="notice notice-success is-dismissible"><p>';
		echo esc_html__( 'Successfully created ProPanel Student Quiz Reports page', 'learndash-reports-pro' );
		echo '</p></div>';
	}

	/****************************************
	 * Actions for admin dashboard settings *
	 ****************************************/

	/**
	 * Skip license activation.
	 *
	 * @deprecated 1.8.2 This method is no longer used.
	 */
	public static function wrld_skip_license_activation() {
		_deprecated_function( __METHOD__, '1.8.2' );
	}

	/**
	 *
	 * Processes the wrld_exclude_settings_save AJAX request.
	 *
	 * @return never
	 */
	public static function wrld_exclude_settings_save() {
		$nonce       = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
		$nonce_valid = wp_verify_nonce( $nonce, 'wrld-admin-settings' );
		if ( ! $nonce_valid ) {
			wp_send_json_error(
				array(
					'success' => true,
					'message' => esc_html__(
						'Nonce verification failed',
						'learndash-reports-pro'
					),
				)
			);
		}

		if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
			wp_send_json_error( esc_html__( "You don't have permission to do this action.", 'learndash-reports-pro' ) );
		}

		$type  = filter_input( INPUT_POST, 'type', FILTER_SANITIZE_STRING );
		$value = filter_input( INPUT_POST, 'value', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		update_option( 'exclude_' . $type, $value );

		wp_send_json_success( array( 'success' => true ) );
	}

	/**
	 * Processes the apply_time_tracking_settings AJAX request.
	 *
	 * @return never
	 */
	public static function apply_time_tracking_settings() {
		$nonce       = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
		$nonce_valid = wp_verify_nonce( $nonce, 'wrld-admin-settings' );
		if ( ! $nonce_valid ) {
			wp_send_json_error(
				array(
					'success' => true,
					'message' => esc_html__(
						'Nonce verification failed',
						'learndash-reports-pro'
					),
				)
			);
		}

		if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
			wp_send_json_error( esc_html__( "You don't have permission to do this action.", 'learndash-reports-pro' ) );
		}

		$status    = filter_input( INPUT_POST, 'status', FILTER_SANITIZE_STRING );
		$timer     = filter_input( INPUT_POST, 'timeout', FILTER_SANITIZE_NUMBER_INT );
		$message   = filter_input( INPUT_POST, 'message', FILTER_SANITIZE_STRING );
		$btn_label = filter_input( INPUT_POST, 'btnlabel', FILTER_SANITIZE_STRING ); // cspell:disable-line .

		if ( ! empty( $timer ) ) {
			update_option( 'wrld_time_tracking_timer', $timer );
		}
		if ( ! empty( $message ) ) {
			update_option( 'wrld_time_tracking_message', $message );
		}
		if ( ! empty( $btn_label ) ) {
			// cspell:disable-next-line .
			update_option( 'wrld_time_tracking_btnlabel', $btn_label );
		}
		if ( ! empty( $status ) ) {
			$all_updates  = get_option( 'wrld_time_tracking_log', false );
			$current_time = current_time( 'timestamp' );
			if ( ! empty( $all_updates ) ) {
				$all_updates[] = $current_time;
			} else {
				$all_updates = array( $current_time );
			}
			update_option( 'wrld_time_tracking_status', $status );
			update_option( 'wrld_time_tracking_last_update', $current_time );
			update_option( 'wrld_time_tracking_log', $all_updates );
			wp_send_json_success(
				array(
					'success' => true,
					'time'    => date_i18n(
						'Y-m-d H:i:s',
						$current_time
					),
				)
			);
		}
		wp_send_json_success( array( 'success' => true ) ); }

	/**
	 * Processes the wrld_exclude_load_users AJAX request.
	 *
	 * @return never
	 */
	public static function wrld_exclude_load_users() {
		$nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';

		$nonce_valid = wp_verify_nonce( $nonce, 'wrld-admin-settings' );
		if ( ! $nonce_valid ) {
			wp_send_json_error(
				array(
					'success' => true,
					'message' => esc_html__(
						'Nonce verification failed',
						'learndash-reports-pro'
					),
				)
			);
		}

		if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
			wp_send_json_error( esc_html__( "You don't have permission to do this action.", 'learndash-reports-pro' ) );
		}

		$page   = filter_input( INPUT_POST, 'page', FILTER_VALIDATE_INT );
		$search = filter_input( INPUT_POST, 'search', FILTER_SANITIZE_STRING );

		if ( empty( $page ) ) {
			$page = 1;
		}

		$args = array(
			'number'         => 10,
			'paged'          => $page,
			'search_columns' => array(
				'user_login',
				'user_email',
				'user_nicename',
				'display_name',
			),
			'fields'         => array(
				'ID',
				'display_name',
			),
		);

		if ( ! empty( $search ) ) {
			$args['search'] = '*' . $search . '*';
		}

		$users = get_users( $args );
		if ( empty( $users ) ) {
			wp_send_json_error();
		}

		wp_send_json_success(
			array(
				'success' => true,
				'data'    => $users,
			)
		);
	}

	/**
	 * A function hooked to the ajax action wrld_license_activate,
	 * responsible for initiating the activation request for the
	 * reports pro plugin license key.
	 *
	 * @since 1.2.0
	 * @deprecated 1.8.2 This method is no longer used.
	 */
	public static function wrld_license_activate() {
		_deprecated_function( __METHOD__, '1.8.2' );
	}

	/**
	 * A function hooked to the ajax action wrld_license_deactivate,
	 * responsible for initiating the deactivation request for the
	 * reports pro plugin license key.
	 *
	 * @since 1.2.0
	 * @deprecated 1.8.2 This method is no longer used.
	 */
	public static function wrld_license_deactivate() {
		_deprecated_function( __METHOD__, '1.8.2' );
	}

	/**
	 * A function hooked to the ajax action wrld_set_configuration,
	 * responsible for setting up the configuration which enables inclusion
	 * of the reports page link in the primary menu.
	 *
	 * @since 1.2.0
	 */
	public static function wrld_set_configuration() {
		$nonce       = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
		$nonce_valid = wp_verify_nonce( $nonce, 'wrld-admin-settings' );
		if ( ! $nonce_valid ) {
			wp_send_json_error(
				array(
					'success' => true,
					'message' => esc_html__(
						'Nonce verification failed',
						'learndash-reports-pro'
					),
				)
			);
		}

		if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
			wp_send_json_error( esc_html__( "You don't have permission to do this action.", 'learndash-reports-pro' ) );
		}

		$settings_data = get_option( 'wrld_settings', array() );
		if ( isset( $_REQUEST['add_in_menu'] ) ) {
			$settings_data['wrld-menu-config-setting'] = 'true' === sanitize_text_field( wp_unslash( $_REQUEST['add_in_menu'] ) );
		}
		if ( isset( $_REQUEST['add_student_in_menu'] ) ) {
			$settings_data['wrld-menu-student-setting'] = 'true' === sanitize_text_field( wp_unslash( $_REQUEST['add_student_in_menu'] ) );
		}
		update_option( 'wrld_settings', $settings_data );
		wp_send_json_success( array( 'success' => true ) );
	}

	/**
	 * A function hooked to the ajax action wrld_update_settings,
	 * responsible for setting up the configuration which enables inclusion
	 * of the reports page link in the primary menu.
	 *
	 * @since 1.2.0
	 */
	public static function wrld_update_settings() {
		$nonce       = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';
		$nonce_valid = wp_verify_nonce( $nonce, 'wrld-admin-settings' );
		if ( ! $nonce_valid ) {
			wp_send_json_error(
				array(
					'success' => false,
					'message' => esc_html__(
						'Nonce verification failed',
						'learndash-reports-pro'
					),
				)
			);
		}

		if ( ! current_user_can( LEARNDASH_ADMIN_CAPABILITY_CHECK ) ) {
			wp_send_json_error( esc_html__( "You don't have permission to do this action.", 'learndash-reports-pro' ) );
		}

		$settings_data = get_option( 'wrld_settings', array() );
		if ( isset( $_REQUEST['access_to_group_leader'] ) ) {
			$access_for_group_leader = 'true' === sanitize_text_field( wp_unslash( $_REQUEST['access_to_group_leader'] ) );
		}
		if ( isset( $_REQUEST['access_to_wdm_instructor'] ) ) {
			$access_for_wdm_instructor = 'true' === sanitize_text_field( wp_unslash( $_REQUEST['access_to_wdm_instructor'] ) );
		}
		if ( isset( $_REQUEST['add_in_menu'] ) ) {
			$settings_data['wrld-menu-config-setting'] = 'true' === sanitize_text_field( wp_unslash( $_REQUEST['add_in_menu'] ) );
		}
		if ( isset( $_REQUEST['add_student_in_menu'] ) ) {
			$settings_data['wrld-menu-student-setting'] = 'true' === sanitize_text_field( wp_unslash( $_REQUEST['add_student_in_menu'] ) );
		}

		if ( isset( $access_for_group_leader ) || isset( $access_for_wdm_instructor ) ) {
			$settings_data['dashboard-access-roles'] = array(
				'group_leader'   => $access_for_group_leader,
				'wdm_instructor' => $access_for_wdm_instructor,
			);
		}
		update_option( 'wrld_settings', $settings_data );
		wp_send_json_success( array( 'settings_data' => $settings_data ) ); }

	/**
	 * @deprecated 1.8.2 This method is no longer used.
	 */
	public function wrld_license_page_visit() {
		_deprecated_function( __METHOD__, '1.8.2' );
	}

	/**
	 * Add the Helpscout Beacon script on the Reports Settings backend pages.
	 * Callback to action hook 'ldgr_helpscout_beacon'. // cspell:disable-line .
	 *
	 * @since 4.9.10
	 * @deprecated 1.8.2 This method is no longer used.
	 */
	public function wrld_add_beacon_helpscout_script() {
		_deprecated_function( __METHOD__, '1.8.2' );
	}

	/**
	 * Calling function to the helpscout beacon.
	 * Callback to action hook 'wrld_helpscout_beacon'.
	 *
	 * @since 4.9.10
	 * @deprecated 1.8.2 This method is no longer used.
	 */
	public function wrld_load_beacon_helpscout() {
		_deprecated_function( __METHOD__, '1.8.2' );
	}
}

\WisdmReportsLearndash\Admin_Functions::instance();
