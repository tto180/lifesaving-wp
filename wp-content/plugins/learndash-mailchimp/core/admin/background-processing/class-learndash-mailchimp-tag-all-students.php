<?php
/**
 * Handles setting up the background process for tagging all Students
 *
 * @since	1.1.0
 *
 * @package	LearnDash_MailChimp
 * @subpackage LearnDash_MailChimp/core/admin/background-processing
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'LearnDash_MailChimp_Tag_All_Students' ) ) {

	final class LearnDash_MailChimp_Tag_All_Students {

		function __construct() {
			
			require_once __DIR__ . '/class-learndash-mailchimp-tag-all-students-process.php';
			
            $this->process_all = new LearnDash_MailChimp_Tag_All_Students_Process();

            add_action( 'init', function() {
            
                if ( get_transient( 'ld_mailchimp_users_tag_started' ) ) {
                    
                    LDMAILCHIMP()->add_admin_notice( array(
                        'message' => sprintf( 
                            _x( '%s is currently adding all of your Students to the appropriate List Tags. You can navigate away from this page safely.', '%s is the Plugin Name', 'learndash-mailchimp' ),
                            LDMAILCHIMP()->plugin_data['Name'] 
                        ),
                        'type' => 'notice notice-warning'
                    ) );

                }
                elseif ( get_transient( 'ld_mailchimp_users_tag_complete' ) ) {

                    delete_transient( 'ld_mailchimp_users_tag_complete' );

                    LDMAILCHIMP()->add_admin_notice( array(
                        'message' => sprintf( 
                            _x( '%s has finished adding all of your Students to the appropriate List Tags!', '%s is the Plugin Name', 'learndash-mailchimp' ),
                            LDMAILCHIMP()->plugin_data['Name'] 
                        ),
                        'type' => 'notice updated'
                    ) );

                    add_action( 'admin_notices', array( $this, 'tagging_complete_notice' ) );

                }

            } );
			
			add_action( 'admin_init', array( $this, 'process_handler' ) );
			
		}
		
		/**
		 * Process handler
		 */
		public function process_handler() {
			
			if ( isset( $_REQUEST[ 'ld_mailchimp_tag_all_students_submit' ] ) ) {

                if ( ! ld_mailchimp_get_option( 'mailchimp_list' ) ) return false;
		
                if ( ! isset( $_REQUEST['ld_mailchimp_tag_all_students_nonce'] ) || 
                    ! wp_verify_nonce( $_REQUEST[ 'ld_mailchimp_tag_all_students_nonce' ], 'ld_mailchimp_tag_all_students' ) ) return false;

                set_transient( 'ld_mailchimp_users_tag_started', true, DAY_IN_SECONDS );
                
                $this->handle_all();
				
			}
			
		}
		
		/**
		 * Handle all
		 */
		protected function handle_all() {
			
			$users = $this->get_users();
			
			foreach ( $users as $user ) {
				$this->process_all->push_to_queue( $user->id );
			}
			
			$this->process_all->save()->dispatch();
			
		}
		
		/**
		 * Get names
		 *
		 * @return array
		 */
		protected function get_users() {
			
			$users = get_users( array(
                'number' => -1,
                'fields' => array( 'id' ),
            ) );
			
			error_log( count( $users ) . " Users Found. Starting..." );
			
			return $users;
			
        }
		
	}
	
}

$instance = new LearnDash_MailChimp_Tag_All_Students();