<?php
/**  
 * Plugin Name: LearnDash - Mailchimp
 * Plugin URI: https://realbigplugins.com/plugins/learndash-mailchimp
 * Description: Mailchimp Integration for LearnDash
 * Version: 1.5.0
 * Text Domain: learndash-mailchimp
 * Author: Real Big Plugins
 * Author URI: https://realbigplugins.com
 * Contributors: d4mation, brashrebel
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'LearnDash_MailChimp' ) ) {

	final class LearnDash_MailChimp {
		
		/**
		 * @var			array $plugin_data Holds Plugin Header Info
		 * @since		1.0.0
		 */
		public $plugin_data;
		
		/**
		 * @var			array $admin_notices Stores all our Admin Notices to fire at once
		 * @since		1.0.0
		 */
		private $admin_notices = array();
		
		/**
		 * @var			RBP_Support $support RBP Support module
		 * @since		1.0.0
		 */
		public $support;
		
		/**
		 * @var			RBM_FieldHelpers $field_helpers RBM Field Helpers module
		 * @since		1.0.4
		 */
		public $field_helpers;
		
		/**
		 * @var			DrewM\MailChimp\MailChimp $mailchimp_api Mailchimp API class
		 * @since		1.0.0
		 */
		public $mailchimp_api = false;

        /**
		 * Get active instance
		 *
		 * @access		public
		 * @since		1.0.0
		 * @return		object self::$instance The one true LearnDash_mailchimp
		 */
		public static function instance() {
			
			static $instance = null;
			
			if ( null === $instance ) {
				$instance = new static();
			}
			
			return $instance;

		}
		
		protected function __construct() {
			
			$this->setup_constants();
			$this->load_textdomain();
			
			// That's a descriptive class name! /s
			if ( ! class_exists( 'Semper_Fi_Module' ) ) {
				
				$this->admin_notices[] = array(
					'message' => sprintf( _x( '%s requires %s to be installed!', 'Missing Plugin Dependency Error', 'learndash-mailchimp' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '<a href="//www.learndash.com/" target="_blank"><strong>LearnDash</strong></a>' ),
					'type' => 'error',
				);
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_notices' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}
				
				return;
				
			}
			
			if ( defined( 'LEARNDASH_VERSION' ) 
				&& ( version_compare( LEARNDASH_VERSION, '2.2.1.2' ) < 0 ) ) {
				
				$this->admin_notices[] = array(
					'message' => sprintf( _x( '%s requires v%s of %s or higher to be installed!', 'Outdated Dependency Error', 'learndash-mailchimp' ), '<strong>' . $this->plugin_data['Name'] . '</strong>', '2.2.1.2', '<a href="//www.learndash.com/" target="_blank"><strong>LearnDash</strong></a>' ),
					'type' => 'error',
				);
				
				if ( ! has_action( 'admin_notices', array( $this, 'admin_notices' ) ) ) {
					add_action( 'admin_notices', array( $this, 'admin_notices' ) );
				}
				
				return;
				
			}
			
			$this->require_necessities();

			if ( ! has_action( 'admin_notices', array( $this, 'admin_notices' ) ) ) {
				add_action( 'admin_notices', array( $this, 'admin_notices' ) );
			}
			
			// Register our CSS/JS for the whole plugin
			add_action( 'init', array( $this, 'register_scripts' ) );
			
		}
		
		/**
		 * Setup plugin constants
		 *
		 * @access		private
		 * @since		1.0.0
		 * @return		void
		 */
		private function setup_constants() {
			
			// WP Loads things so weird. I really want this function.
			if ( ! function_exists( 'get_plugin_data' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}
			
			// Only call this once, accessible always
			$this->plugin_data = get_plugin_data( __FILE__ );

			if ( ! defined( 'LD_MAILCHIMP_VER' ) ) {
				// Plugin version
				define( 'LD_MAILCHIMP_VER', $this->plugin_data['Version'] );
			}

			if ( ! defined( 'LD_MAILCHIMP_DIR' ) ) {
				// Plugin path
				define( 'LD_MAILCHIMP_DIR', plugin_dir_path( __FILE__ ) );
			}

			if ( ! defined( 'LD_MAILCHIMP_URL' ) ) {
				// Plugin URL
				define( 'LD_MAILCHIMP_URL', plugin_dir_url( __FILE__ ) );
			}
			
			if ( ! defined( 'LD_MAILCHIMP_FILE' ) ) {
				// Plugin File
				define( 'LD_MAILCHIMP_FILE', __FILE__ );
			}

		}
		
		/**
         * Internationalization
         *
         * @access      private
         * @since       1.0.0
         * @return      void
         */
        private function load_textdomain() {

            // Set filter for language directory
            $lang_dir = LD_MAILCHIMP_DIR . '/languages/';
            $lang_dir = apply_filters( 'learndash_mailchimp_languages_directory', $lang_dir );

            // Traditional WordPress plugin locale filter
            $locale = apply_filters( 'plugin_locale', get_locale(), 'learndash-mailchimp' );
            $mofile = sprintf( '%1$s-%2$s.mo', 'learndash-mailchimp', $locale );

            // Setup paths to current locale file
            $mofile_local = $lang_dir . $mofile;
            $mofile_global = WP_LANG_DIR . '/learndash-mailchimp/' . $mofile;

            if ( file_exists( $mofile_global ) ) {
                // Look in global /wp-content/languages/learndash-mailchimp/ folder
                // This way translations can be overridden via the Theme/Child Theme
                load_textdomain( 'learndash-mailchimp', $mofile_global );
            }
            else if ( file_exists( $mofile_local ) ) {
                // Look in local /wp-content/plugins/learndash-mailchimp/languages/ folder
                load_textdomain( 'learndash-mailchimp', $mofile_local );
            }
            else {
                // Load the default language files
                load_plugin_textdomain( 'learndash-mailchimp', false, $lang_dir );
            }

        }
		
		/**
		 * Include different aspects of the Plugin
		 * 
		 * @access		private
		 * @since		1.0.0
		 * @return		void
		 */
		private function require_necessities() {
			
			if ( ! class_exists( 'DrewM\MailChimp\MailChimp' ) ) {
				require_once LD_MAILCHIMP_DIR . '/includes/mailchimp-api/src/MailChimp.php';
			}
			
			if ( $api_key = $this->get_api_key() ) {
				
				$this->mailchimp_api = new \Drewm\MailChimp\MailChimp( $api_key );
				
				add_action( 'init', array( $this, 'check_api_key_validity' ) );
				
			}
			else {
				
				delete_transient( 'ld_mailchimp_api_key_validity' );
				
			}
			
			require_once __DIR__ . '/includes/rbm-field-helpers/rbm-field-helpers.php';
		
			$this->field_helpers = new RBM_FieldHelpers( array(
				'ID'   => 'ld_mailchimp', // Your Theme/Plugin uses this to differentiate its instance of RBM FH from others when saving/grabbing data
				'l10n' => array(
					'field_table'    => array(
						'delete_row'    => __( 'Delete Row', 'learndash-mailchimp' ),
						'delete_column' => __( 'Delete Column', 'learndash-mailchimp' ),
					),
					'field_select'   => array(
						'no_options'       => __( 'No select options.', 'learndash-mailchimp' ),
						'error_loading'    => __( 'The results could not be loaded', 'learndash-mailchimp' ),
						/* translators: %d is number of characters over input limit */
						'input_too_long'   => __( 'Please delete %d character(s)', 'learndash-mailchimp' ),
						/* translators: %d is number of characters under input limit */
						'input_too_short'  => __( 'Please enter %d or more characters', 'learndash-mailchimp' ),
						'loading_more'     => __( 'Loading more results...', 'learndash-mailchimp' ),
						/* translators: %d is maximum number items selectable */
						'maximum_selected' => __( 'You can only select %d item(s)', 'learndash-mailchimp' ),
						'no_results'       => __( 'No results found', 'learndash-mailchimp' ),
						'searching'        => __( 'Searching...', 'learndash-mailchimp' ),
					),
					'field_repeater' => array(
						'collapsable_title' => __( 'New Row', 'learndash-mailchimp' ),
						'confirm_delete'    => __( 'Are you sure you want to delete this element?', 'learndash-mailchimp' ),
						'delete_item'       => __( 'Delete', 'learndash-mailchimp' ),
						'add_item'          => __( 'Add', 'learndash-mailchimp' ),
					),
					'field_media'    => array(
						'button_text'        => __( 'Upload / Choose Media', 'learndash-mailchimp' ),
						'button_remove_text' => __( 'Remove Media', 'learndash-mailchimp' ),
						'window_title'       => __( 'Choose Media', 'learndash-mailchimp' ),
					),
					'field_checkbox' => array(
						'no_options_text' => __( 'No options available.', 'learndash-mailchimp' ),
					),
				),
			) );

			require_once LD_MAILCHIMP_DIR . '/includes/vendor/autoload.php';

			require_once LD_MAILCHIMP_DIR . '/core/admin/background-processing/class-learndash-mailchimp-tag-all-students.php';
			
			if ( is_admin() ) {
				
				require_once LD_MAILCHIMP_DIR . '/core/admin/class-learndash-mailchimp-settings.php';
				
				require_once LD_MAILCHIMP_DIR . '/core/admin/class-learndash-mailchimp-post-edit-course.php';
				
			}
			
			// Change Prefix for RBP Support Object
			add_filter( 'rbp_support_prefix', array( $this, 'change_rbp_support_prefix' ) );
			
			// Allow RBP Support Form to be a proper <form>
			add_filter( 'ld_mailchimp_support_form_tag', array( $this, 'change_rbp_support_form_tag' ) );

			add_filter( 'ld_mailchimp_download_id', array( $this, 'set_download_id' ) );
			
			// Support Module
			require_once LD_MAILCHIMP_DIR . '/includes/rbp-support/rbp-support.php';
			$this->support = new RBP_Support( LD_MAILCHIMP_FILE, admin_url( 'options-general.php?page=learndash_mailchimp&section=licensing' ), array(
				'support_form' => array(
					'enabled' => array(
						'title' => _x( 'Need some help with %s?', '%s is the Plugin Name', 'learndash-mailchimp' ),
						'subject_label' => __( 'Subject', 'learndash-mailchimp' ),
						'message_label' => __( 'Message', 'learndash-mailchimp' ),
						'send_button' => __( 'Send', 'learndash-mailchimp' ),
						'subscribe_text' => _x( 'We make other cool plugins and share updates and special offers to anyone who %ssubscribes here%s.', 'Both %s are used to place HTML for the <a> in the message', 'learndash-mailchimp' ),
						'validationError' => _x( 'This field is required', 'Only used by legacy browsers for JavaScript Form Validation', 'learndash-mailchimp' ),
						'success' => __( 'Support message succesfully sent!', 'learndash-mailchimp' ),
						'error' => __( 'Could not send support message.', 'learndash-mailchimp' ),
					),
					'disabled' => array(
						'title' => _x( 'Need some help with %s?', '%s is the Plugin Name', 'learndash-mailchimp' ),
						'disabled_message' => __( 'Premium support is disabled. Please register your product and activate your license for this website to enable.', 'learndash-mailchimp' )
					),
				),
				'licensing_fields' => array(
					'title' => _x( '%s License', '%s is the Plugin Name', 'learndash-mailchimp' ),
					'deactivate_button' => __( 'Deactivate', 'learndash-mailchimp' ),
					'activate_button' => __( 'Activate', 'learndash-mailchimp' ),
					'delete_deactivate_button' => __( 'Delete and Deactivate', 'learndash-mailchimp' ),
					'delete_button' => __( 'Delete', 'learndash-mailchimp' ),
					'license_active_label' => __( 'License Active', 'learndash-mailchimp' ),
					'license_inactive_label' => __( 'License Inactive', 'learndash-mailchimp' ),
					'save_activate_button' => __( 'Save and Activate', 'learndash-mailchimp' ),
				),
				'license_nag' => array(
					'register_message' => _x( 'Register your copy of %s now to receive automatic updates and support.', '%s is the Plugin Name', 'learndash-mailchimp' ),
					'purchase_message' => _x( 'If you do not have a license key, you can %1$spurchase one%2$s.', 'Both %s are used to place HTML for the <a> in the message', 'learndash-mailchimp' ),
				),
				'license_activation' => _x( '%s license successfully activated.', '%s is the Plugin Name', 'learndash-mailchimp' ),
				'license_deletion' => _x( '%s license successfully deleted.', '%s is the Plugin Name', 'learndash-mailchimp' ),
				'license_deactivation' => array(
					'error' => _x( 'Error: could not deactivate the license for %s', '%s is the Plugin Name', 'learndash-mailchimp' ),
					'success' => _x( '%s license successfully deactivated.', '%s is the Plugin Name', 'learndash-mailchimp' ),
				),
				'license_error_messages' => array(
					'expired' => _x( 'Your %s license key expired on %s.', 'The first %s is the Plugin name and the second %s is a localized timestamp', 'learndash-mailchimp' ),
					'revoked' => __( 'Your license key has been disabled.', 'learndash-mailchimp' ),
					'missing' => __( 'Invalid license.', 'learndash-mailchimp' ),
					'site_inactive' => __( 'Your license is not active for this URL.', 'learndash-mailchimp' ),
					'item_name_mismatch' => _x( 'This appears to be an invalid license key for %s.', '%s is the Plugin Name', 'learndash-mailchimp' ),
					'no_activations_left' => __( 'Your license key has reached its activation limit.', 'learndash-mailchimp' ),
					'manage_license_link_text' => __( 'You can manage your license key here.', 'learndash-mailchimp' ),
					'no_connection' => _x( '%s cannot communicate with %s for License Key Validation. Please check your server configuration settings.', '%s is the Plugin Name followed by the Store URL', 'learndash-mailchimp' ),
					'default' => __( 'An error occurred, please try again.', 'learndash-mailchimp' ),
				),
				'beta_checkbox' => array(
					'label' => __( 'Enable Beta Releases', 'learndash-mailchimp' ),
					'disclaimer' => __( 'Beta Releases should not be considered as Stable. Enabling this on your Production Site is done at your own risk.', 'learndash-mailchimp' ),
					'enabled_message' => _x( 'Beta Releases for %s enabled.', '%s is the Plugin Name', 'learndash-mailchimp' ),
					'disabled_message' => _x( 'Beta Releases for %s disabled.', '%s is the Plugin Name', 'learndash-mailchimp' ),
				),
			) );
			
			// Revert this change so that it won't harm any future potential instances of the object
			remove_filter( 'rbp_support_prefix', array( $this, 'change_rbp_support_prefix' ) );
			
			require_once LD_MAILCHIMP_DIR . '/core/front/class-learndash-mailchimp-add-course.php';
			
			require_once LD_MAILCHIMP_DIR . '/core/shortcodes/class-learndash-mailchimp-course-shortcode.php';

			add_action( 'plugins_loaded', array( $this, 'after_plugin_load' ), 11 );
			
		}

		/**
		 * Run after the Plugin singleton has been made
		 *
		 * This is important in cases where we could accidentally end up creating RBM FH twice, for instance
		 * 
		 * @access	public
		 * @since	1.1.4
		 * @return  void
		 */
		public function after_plugin_load() {

			add_filter( 'LDCC_exclude_post_meta_keys', array( $this, 'LDCC_exclude_post_meta_keys' ), 10, 3 );

			add_filter( 'learndash_cloning_excluded_meta_keys', array( $this, 'learndash_cloning_excluded_meta_keys' ), 10, 2 );

		}
		
		/**
		 * Helper function that grabs API Key from Database or from $_POST as appropriate
		 * 
		 * @access		private
		 * @since		1.0.0
		 * @return		boolean|string API Key. Returns False if malformed or non-existant.
		 */
		private function get_api_key() {
			
			$api_key = ld_mailchimp_get_option( 'api_key' );
			$api_key = ( strpos( $api_key, '-' ) ) ? $api_key : false;

			// If we're saving data, use that instead
			if ( isset( $_REQUEST['ld_mailchimp_check_api_key_nonce'] ) && 
				wp_verify_nonce( $_REQUEST['ld_mailchimp_check_api_key_nonce'], 'ld_mailchimp_check_api_key' ) && 
				isset( $_POST['learndash_mailchimp'] ) && 
				isset( $_POST['learndash_mailchimp']['api_key'] ) && 
			   strpos( $_POST['learndash_mailchimp']['api_key'], '-' ) ) {

				$api_key = $_POST['learndash_mailchimp']['api_key'];

			}
			
			return $api_key;
			
		}
		
		/**
		 * Determine validity of stored Mailchimp API Key
		 * 
		 * @access		public
		 * @since		1.0.0
		 * @return		string Validty of API Key
		 */
		public function check_api_key_validity() {
			
			$force_check = false;
			
			// If we're saving new data, force-check
			if ( isset( $_REQUEST['ld_mailchimp_check_api_key_nonce'] ) && 
				wp_verify_nonce( $_REQUEST['ld_mailchimp_check_api_key_nonce'], 'ld_mailchimp_check_api_key' ) && 
				isset( $_POST['learndash_mailchimp'] ) && 
				isset( $_POST['learndash_mailchimp']['api_key'] ) && 
			   strpos( $_POST['learndash_mailchimp']['api_key'], '-' ) ) {
				$force_check = true;
			}

			if ( ! $force_check && 
				$validity = get_transient( 'ld_mailchimp_api_key_validity' ) ) {
				return $validity;
			}
			
			if ( ! $this->mailchimp_api ) return 'invalid';
			
			$api_test = $this->mailchimp_api->get( '/ping' );

			// Health Status is only reported on Success
			// It is straight up false in the event a 100% bogus API Key is provided
			if ( ! isset( $api_test['health_status'] ) ) {

				// Only show Settings Error on our Settings Page
				if ( is_admin() && 
					isset( $_POST['option_page'] ) && 
					$_POST['option_page'] == 'learndash_mailchimp' ) {

					$this->admin_notices[] = array(
						'learndash_mailchimp',
						'',
						isset( $api_test['title'] ) ? $api_test['title'] : __( 'API Key Invalid', 'learndash-mailchimp' ),
						'error ld-mailchimp-notice'
					);
					
					// Hooking into admin_notices like usual does not work here
					add_action( 'admin_init', array( $this, 'admin_notices' ) );

				}

				set_transient( 'ld_mailchimp_api_key_validity', 'invalid', DAY_IN_SECONDS );

				return 'invalid';

			}
			else {

				set_transient( 'ld_mailchimp_api_key_validity', 'valid', DAY_IN_SECONDS );

				return 'valid';

			}

		}
		
		/**
		 * We are going to alter the Prefix to match what it was before the Support Module was included
		 * 
		 * @param		string $prefix RBP_Support Prefix
		 *                                    
		 * @access		public
		 * @since		1.0.0
		 * @return		string RBP_Support Prefix
		 */
		public function change_rbp_support_prefix( $prefix ) {
			
			return 'ld_mailchimp';
			
		}
		
		/**
		 * Change RBP Support Form to use a proper <form> and not utilize the special JavaScript Validation normally used
		 * 
		 * @param		string $tag RBP_Support Form Tag
		 *                                      
		 * @access		public
		 * @since		1.0.0
		 * @return		string RBP_Support Form Tag
		 */
		public function change_rbp_support_form_tag( $tag ) {
			
			return 'form';
			
		}

		/**
		 * Sets the Download ID on RBP. This will allow the Plugin/Download name to be changed without causing issues.
		 *
		 * @param	integer  $id  Download ID
		 *
		 * @access	public
		 * @since	1.2.0
		 * @return	integer       Download ID
		 */
		public function set_download_id( $id ) {

			return 3157;

		}

		/**
		 * Prevent LearnDash Content Cloner copying over our saved Tags
		 *
		 * @param   array    $excluded_keys   Array of Post Meta Keys to not copy over
		 * @param   integer  $source_post_id  Source Post ID
		 * @param   integer  $new_post_id     Destination Post ID
		 *
		 * @access	public
		 * @since	1.1.4
		 * @return  array                     Array of Post Meta Keys to not copy over
		 */
		public function LDCC_exclude_post_meta_keys( $excluded_keys, $source_post_id, $new_post_id ) {

			return $this->get_clone_excluded_meta_keys( $excluded_keys );

		}

		/**
		 * Prevent LearnDash's clone methods from copying over our saved Tags
		 *
		 * @param   array    $excluded_keys   Array of Post Meta Keys to not copy over
		 * @param   WP_Post  $source_post_id  Source Post
		 *
		 * @access	public
		 * @since	1.5.0
		 * @return  array                     Array of Post Meta Keys to not copy over
		 */
		public function learndash_cloning_excluded_meta_keys( $excluded_keys, $post ) {

			return $this->get_clone_excluded_meta_keys( $excluded_keys );

		}

		/**
		 * LearnDash Content Cloner and LearnDash's native cloner both have a way to exclude Meta Keys as an Array
		 * Since there could be other plugins in the future that could work similarly, it's worth abstracting this out
		 *
		 * @param   array  $excluded_keys  Excluded Meta Keys
		 *
		 * @return  array                  Excluded Meta Keys
		 */
		private function get_clone_excluded_meta_keys( $excluded_keys = array() ) {

			$list_id = ld_mailchimp_get_option( 'mailchimp_list' );

			if ( ! $list_id ) return $excluded_keys;

			$excluded_keys[] = "ld_mailchimp_course_segment_$list_id";
			$excluded_keys[] = "ld_mailchimp_group_segment_$list_id";
			$excluded_keys[] = 'ld_mailchimp_display_subscription_form';

			return $excluded_keys;

		}
		
		/**
		 * Register our CSS/JS to use later
		 * 
		 * @access		public
		 * @since		1.0.0
		 * @return		void
		 */
		public function register_scripts() {
			
			wp_register_style(
				'learndash-mailchimp-admin',
				LD_MAILCHIMP_URL . '/assets/css/admin.css',
				null,
				defined( 'WP_DEBUG' ) && WP_DEBUG ? time() : LD_MAILCHIMP_VER
			);
			
		}

		/**
		 * Adds an easy way to add one of our global Admin Notices from elsewhere in the plugin
		 *
		 * @param   array  $admin_notice  Admin Notice settings. See add_settings_error()
		 *
		 * @access	public
		 * @since	1.1.0
		 * @return  void
		 */
		public function add_admin_notice( $admin_notice ) {

			$this->admin_notices[] = $admin_notice;

		}
		
		/**
		 * Outputs Admin Notices
		 * This is useful if you're too early in execution to use the add_settings_error() function as you can save them for later
		 * 
		 * @access		public
		 * @since		1.0.0
		 * @return		void
		 */
		public function admin_notices() {

			foreach ( $this->admin_notices as $admin_notice ) {
				
				if ( is_array( $admin_notice ) && ! isset( $admin_notice['type'] ) ) :

					call_user_func_array( 'add_settings_error', $admin_notice );
				
				else : ?>

					<div class="<?php echo $admin_notice['type']; ?> ld-mailchimp-notice">
						
						<p>
							<?php echo $admin_notice['message']; ?>
						</p>
						
					</div>
					
				<?php endif;

			}

			$this->admin_notices = array();

		}

    }
	
}

/**
 * The main function responsible for returning the one true LearnDash_MailChimp
 * instance to functions everywhere
 *
 * @since		1.0.0
 * @return		\LearnDash_MailChimp The one true LearnDash_MailChimp
 */
add_action( 'plugins_loaded', 'learndash_mailchimp_load', 10 );
function learndash_mailchimp_load() {

	require_once __DIR__ . '/core/learndash-mailchimp-functions.php';
	LDMAILCHIMP();

}