<?php
/**
 * Dashboard Class
 *
 * @package LearnDash\Reports
 *
 * @since 3.0.0
 */

namespace WRLDAdmin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

// cspell:ignore whatsnew whatsnewpage gutenbergblocks .

if ( ! class_exists( 'Dashboard' ) ) {
	/**
	 * Class for showing tabs of WRLD.
	 */
	class Dashboard {
		/**
		 * Constructor that Adds the Menu Page action
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'page_init' ), 99 );
			wp_enqueue_script( 'wrld_admin_dashboard_settings_select', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/js/multi-select.js', array(), LDRP_PLUGIN_VERSION, true );
			wp_enqueue_script( 'wrld_admin_dashboard_settings_script', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/js/admin-settings.js', array( 'jquery', 'wrld_admin_dashboard_settings_select' ), LDRP_PLUGIN_VERSION, true );
			$admin_local_data = array(
				'settings_data'      => get_option( 'wrld_settings', array() ),
				'wp_ajax_url'        => admin_url( 'admin-ajax.php' ),
				'nonce'              => wp_create_nonce( 'wrld-admin-settings' ),
				'wait_text'          => __( 'Please Wait...', 'learndash-reports-pro' ),
				'success_text'       => __( 'ProPanel Dashboard updated successfully.', 'learndash-reports-pro' ),
				'user_placeholder'   => __( 'Select Users...', 'learndash-reports-pro' ),
				'ur_placeholder'     => __( 'Select User Role...', 'learndash-reports-pro' ),
				'course_placeholder' => __( 'Select Courses...', 'learndash-reports-pro' ),
				'loading_text'       => __( 'Loading...', 'learndash-reports-pro' ),
				'activated_18n'      => __( 'Activated', 'learndash-reports-pro' ),
				'deactivated_18n'    => __( 'Deactivated', 'learndash-reports-pro' ),
			);
			wp_localize_script( 'wrld_admin_dashboard_settings_script', 'wrld_admin_settings_data', $admin_local_data );        }

		/**
		 * Initializes the setup of admin menu page & submenu pages for the plugin
		 *
		 * @return [void]
		 */
		public function page_init() {
			$capability     = apply_filters( 'wisdm_wrld_menu_page_capability', 'manage_options' );
			$page_title     = esc_html__( 'ProPanel', 'learndash-reports-pro' );
			$menu_icon      = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+CjxzdmcKICAgaGVpZ2h0PSIxMDAwIgogICB3aWR0aD0iODUzIgogICB2ZXJzaW9uPSIxLjEiCiAgIGlkPSJzdmcxMSIKICAgc29kaXBvZGk6ZG9jbmFtZT0iZHJhd2luZy10ZXN0LnN2ZyIKICAgaW5rc2NhcGU6dmVyc2lvbj0iMS4xLjEgKGMzMDg0ZWYsIDIwMjEtMDktMjIpIgogICB4bWxuczppbmtzY2FwZT0iaHR0cDovL3d3dy5pbmtzY2FwZS5vcmcvbmFtZXNwYWNlcy9pbmtzY2FwZSIKICAgeG1sbnM6c29kaXBvZGk9Imh0dHA6Ly9zb2RpcG9kaS5zb3VyY2Vmb3JnZS5uZXQvRFREL3NvZGlwb2RpLTAuZHRkIgogICB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciCiAgIHhtbG5zOnN2Zz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciPgogIDxkZWZzCiAgICAgaWQ9ImRlZnMxNSIgLz4KICA8c29kaXBvZGk6bmFtZWR2aWV3CiAgICAgaWQ9Im5hbWVkdmlldzEzIgogICAgIHBhZ2Vjb2xvcj0iI2ZmZmZmZiIKICAgICBib3JkZXJjb2xvcj0iIzY2NjY2NiIKICAgICBib3JkZXJvcGFjaXR5PSIxLjAiCiAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiIKICAgICBpbmtzY2FwZTpwYWdlb3BhY2l0eT0iMC4wIgogICAgIGlua3NjYXBlOnBhZ2VjaGVja2VyYm9hcmQ9IjAiCiAgICAgc2hvd2dyaWQ9ImZhbHNlIgogICAgIGlua3NjYXBlOnpvb209IjAuNTQiCiAgICAgaW5rc2NhcGU6Y3g9IjQyNi44NTE4NSIKICAgICBpbmtzY2FwZTpjeT0iNTAwIgogICAgIGlua3NjYXBlOndpbmRvdy13aWR0aD0iMTMxMiIKICAgICBpbmtzY2FwZTp3aW5kb3ctaGVpZ2h0PSI3NjIiCiAgICAgaW5rc2NhcGU6d2luZG93LXg9IjAiCiAgICAgaW5rc2NhcGU6d2luZG93LXk9IjI1IgogICAgIGlua3NjYXBlOndpbmRvdy1tYXhpbWl6ZWQ9IjAiCiAgICAgaW5rc2NhcGU6Y3VycmVudC1sYXllcj0ic3ZnMTEiIC8+CiAgPHBhdGgKICAgICBzdHlsZT0iZmlsbDojMDAwMDAwO3N0cm9rZS13aWR0aDoyLjIyNzE1IgogICAgIGQ9Ik0gMCw0ODMuMTkyOTIgViA1Ni42OTI5MTMgSCAyMy4zODUxMiA0Ni43NzAyMzUgViA0NTguNjk0MjMgODYwLjY5NTU1IEggNDQ5Ljg4NTEyIDg1MyB2IDI0LjQ5ODY4IDI0LjQ5ODY5IEggNDI2LjUgMCBaIgogICAgIGlkPSJwYXRoNzA0IiAvPgogIDxwYXRoCiAgICAgc3R5bGU9ImZpbGw6IzAwMDAwMDtzdHJva2Utd2lkdGg6Mi4yMjcxNSIKICAgICBkPSJNIDk3Ljk5NDc3Niw2NDAuMjA3MjkgViA0NjYuNDg5MjUgaCA3MC4xNTUzNDQgNzAuMTU1MzggdiAxNzMuNzE4MDQgMTczLjcxOCBIIDE2OC4xNTAxMiA5Ny45OTQ3NzYgWiIKICAgICBpZD0icGF0aDc0MyIgLz4KICA8cGF0aAogICAgIHN0eWxlPSJmaWxsOiMwMDAwMDA7c3Ryb2tlLXdpZHRoOjIuMjI3MTUiCiAgICAgZD0iTSAzMzYuMzAwMjgsNTkyLjMyMzQ0IFYgMzcwLjcyMTYyIGggNzAuMTU1MzMgNzAuMTU1MzQgdiAyMjEuNjAxODIgMjIxLjYwMTg1IGggLTcwLjE1NTM0IC03MC4xNTUzMyB6IgogICAgIGlkPSJwYXRoNzgyIiAvPgogIDxwYXRoCiAgICAgc3R5bGU9ImZpbGw6IzAwMDAwMDtzdHJva2Utd2lkdGg6Mi4yMjcxNSIKICAgICBkPSJNIDU3NC42MDU3Myw1NDQuNDM5NjYgViAyNzQuOTU0MDIgaCA2OS4wNDE4IDY5LjA0MTc2IHYgMjY5LjQ4NTY0IDI2OS40ODU2MyBoIC02OS4wNDE3NiAtNjkuMDQxOCB6IgogICAgIGlkPSJwYXRoODIxIiAvPgo8L3N2Zz4K';
			$submenu_titles = array();
			$dashboard_page = add_menu_page(
				$page_title,
				'ProPanel',
				$capability,
				'wrld-dashboard-page',
				array( $this, 'dashboard_page_content' ),
				$menu_icon,
				60
			);

			// Submenu Pages.

			$submenu_titles['whatsnew']           = __( 'What\'s new', 'learndash-reports-pro' );
			$submenu_titles['dashboard']          = __( 'Dashboard', 'learndash-reports-pro' );
			$submenu_titles['license_activation'] = __( 'License Activation', 'learndash-reports-pro' );
			$submenu_titles['settings']           = __( 'Settings', 'learndash-reports-pro' );
			$submenu_titles['gutenbergblocks']    = __( 'Gutenberg Blocks', 'learndash-reports-pro' );
			$submenu_titles['help']               = __( 'Help', 'learndash-reports-pro' );

			add_submenu_page( 'wrld-dashboard-page', $submenu_titles['dashboard'], $submenu_titles['dashboard'], $capability, 'wrld-dashboard-page', array( $this, 'dashboard_page_content' ) );
			add_submenu_page( 'wrld-dashboard-page', $submenu_titles['settings'], $submenu_titles['settings'], $capability, 'wrld-settings', array( $this, 'settings_page_content' ) );
			add_submenu_page( 'wrld-dashboard-page', $submenu_titles['help'], $submenu_titles['help'], $capability, 'wrld-help', array( $this, 'help_page_content' ) );

			add_submenu_page( 'wrld-dashboard-page', $submenu_titles['gutenbergblocks'], $submenu_titles['gutenbergblocks'], $capability, 'wrld-gutenbergblocks', array( $this, 'gutenbergblocks_page_content' ) );     }

		public function dashboard_page_content() {
			$current_tab = self::get_current_tab();
			if ( 'wrld-dashboard-page' !== $current_tab ) {
				self::call_to_function_for( $current_tab );
				return;
			}
			self::show_admin_dashboard_header();
			self::show_admin_dashboard_tabs();
			// cspell:disable-next-line .
			include_once 'class-dashboardpage.php';
			$dashboard_page = new \WRLDAdmin\DashboardPage();
			$dashboard_page::render();
		}

		/**
		 * Renders legacy license page.
		 *
		 * @deprecated 1.8.2 This method is no longer used.
		 */
		public function licensing_page_content() {
			_deprecated_function( __METHOD__, '1.8.2' );
		}

		/**
		 * Renders the content for the "Other Plugins" page.
		 *
		 * @deprecated 1.8.2 This method is no longer used.
		 *
		 * @return void
		 */
		public function other_plugins_page_content() {
			_deprecated_function( __METHOD__, '1.8.2' );
		}

		public function settings_page_content() {
			$current_tab = self::get_current_tab();
			if ( 'wrld-settings' !== $current_tab ) {
				self::call_to_function_for( $current_tab );
				return;
			}
			self::show_admin_dashboard_header();
			self::show_admin_dashboard_tabs();
			// cspell:disable-next-line .
			include_once 'class-settingspage.php';
			$settings_page = new \WRLDAdmin\SettingsPage();
			$settings_page::render();
		}

		public function help_page_content() {
			$current_tab = self::get_current_tab();
			if ( 'wrld-help' !== $current_tab ) {
				self::call_to_function_for( $current_tab );
				return;
			}
			self::show_admin_dashboard_header();
			self::show_admin_dashboard_tabs();
			// cspell:disable-next-line .
			include_once 'class-helppage.php';
			$help_page = new \WRLDAdmin\HelpPage();
			$help_page::render();
		}

		/**
		 * Renders the content for the "What's New" page.
		 *
		 * @deprecated 3.0.0 This method is no longer used.
		 *
		 * @return void
		 */
		public function whatsnew_page_content() {
			_deprecated_function( __METHOD__, '3.0.0' );
		}

		public function gutenbergblocks_page_content() {
			$settings_data                            = get_option( 'wrld_settings', array() );
			$settings_data['skip-license-activation'] = true;
			update_option( 'wrld_settings', $settings_data );
			$current_tab = self::get_current_tab();
			if ( 'wrld-gutenbergblocks' !== $current_tab ) {
				self::call_to_function_for( $current_tab );
				return;
			}
			self::show_admin_dashboard_header();
			self::show_admin_dashboard_tabs();
			include_once 'class-gutenbergblocks.php';
			$help_page = new \WRLDAdmin\Gutenbergblocks();
			$help_page::render();
		}

		public static function show_admin_dashboard_header() {
			if ( is_rtl() ) {
				wp_enqueue_style( 'wrld_admin_dashboard_header_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/header.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				wp_enqueue_style( 'wrld_admin_dashboard_header_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/header.css', array(), LDRP_PLUGIN_VERSION );
			}
			include_once 'templates/dashboard-header.php';
		}

		public static function show_admin_dashboard_tabs() {
			if ( is_rtl() ) {
				wp_enqueue_style( 'wrld_admin_dashboard_tabs_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/tabs.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				wp_enqueue_style( 'wrld_admin_dashboard_tabs_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/tabs.css', array(), LDRP_PLUGIN_VERSION );
			}
			$current_tab    = self::get_current_tab();
			$settings_data  = get_option( 'wrld_settings', array() );
			$license_key    = trim( get_option( 'edd_learndash-reports-pro_license_key' ) );
			$license_status = get_option( 'edd_learndash-reports-pro_license_status', false );

			$tabs = array(
				'wrld-dashboard-page'  => __( 'Dashboard', 'learndash-reports-pro' ),
				'wrld-settings'        => __( 'Settings', 'learndash-reports-pro' ),
				'wrld-gutenbergblocks' => __( 'Gutenberg Blocks', 'learndash-reports-pro' ),
				'wrld-help'            => __( 'Help', 'learndash-reports-pro' ),
			);

			?>
<div class='wrld-tab-wrapper nav-tab-wrapper'>
			<?php
			foreach ( $tabs as $tab => $title ) {
				$class  = ( $tab === $current_tab ) ? ' nav-tab-active' : '';
				$tab_og = $tab;
				if (
					'wrld-gutenbergblocks' === $tab
					&& ! (
						get_option( 'wrld_gutenberg_block_course_report', false )
						&& get_option( 'wrld_gutenberg_block_quiz_report', false )
						&& get_option( 'wrld_gutenberg_block_learner_report', false )
						&& get_option( 'wrld_gutenberg_block_activity_report', false )
						&& get_option( 'wrld_gutenberg_block_quick_stats', false )
					)
				) {
					echo '<a id="' . esc_attr( $tab_og ) . '" class="nav-tab' . esc_attr( $class ) . '" href="admin.php?page=' . esc_attr( $tab ) . '">' . esc_html( $title ) . '<span class="whatsnew-beacon-icon wrld-blink"> </span></a>';
				} else {
					echo '<a id="' . esc_attr( $tab_og ) . '" class="nav-tab' . esc_attr( $class ) . '" href="admin.php?page=' . esc_attr( $tab ) . '">' . esc_html( $title ) . '</a>';
				}
			}
			?>
</div>
			<?php
		}

		/**
		 * Returns the slug of the current dashboard tab.
		 *
		 * @return string $current_tab current tab.
		 */
		public static function get_current_tab() {
			global $pagenow;

			if ( defined( 'LDRP_PLUGIN_VERSION' ) ) {
				update_option( 'wrld_settings_page_visited', 'pro' );
			} elseif ( 'pro' !== get_option( 'wrld_settings_page_visited', false ) ) {
					update_option( 'wrld_settings_page_visited', 'free' );
			}

			static $valid_wrld_tabs = array(
				'wrld-dashboard-page',
				'wrld-gutenbergblocks',
				'wrld-settings',
				'wrld-help',
			);

			$page_requested = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
			if ( 'admin.php' === $pagenow && in_array( $page_requested, $valid_wrld_tabs, true ) ) {
				return $page_requested;
			}

			return 'wrld-dashboard-page';
		}


		/**
		 * Calls the function for the tab.
		 *
		 * @param string $tab_slug The slug of the tab.
		 *
		 * @return void
		 */
		public function call_to_function_for( $tab_slug ) {
			switch ( $tab_slug ) {
				case 'wrld-dashboard-page':
					self::dashboard_page_content();
					break;
				case 'wrld-settings':
					self::settings_page_content();
					break;
				case 'wrld-help':
					self::help_page_content();
					break;
				case 'wrld-gutenbergblocks':
					self::gutenbergblocks_page_content();
					break;

				default:
					break;
			}
		}

		/**
		 * Checking for latest upcoming versions.
		 *
		 * @deprecated 1.8.2 This method is no longer used.
		 */
		public function wrld_check_latest_version() {
			_deprecated_function( __METHOD__, '1.8.2' );
		}
	}
}
