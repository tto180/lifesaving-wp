<?php
/**
 * Generic Functions for Course Interactions on both the Frontend and Backend of LearnDash Mailchimp
 *
 * @since		1.0.0
 *
 * @package LearnDash_MailChimp
 * @subpackage LearnDash_MailChimp/core/front
 */

defined( 'ABSPATH' ) || die();

final class LearnDash_MailChimp_Add_Course {
	
	/**
	 * LearnDash_MailChimp_Add_Course constructor.
	 * 
	 * @since		1.0.0
	 */
	function __construct() {
		
		add_filter( 'learndash_payment_button', array( $this, 'add_subscription_checkbox' ), 5, 2 );
		
		add_action( 'learndash_update_course_access', array( $this, 'course_join_add_email_to_segment' ), 11, 4 );
		
		add_filter( 'ld_after_course_status_template_container', array( $this, 'after_course_status' ) );
		
		add_action( 'init', array( $this, 'subscribe_form_add_email_to_segment' ) );
		
        add_action( 'learndash_update_course_access', array( $this, 'auto_subscribe' ), 10, 4 );
        
        add_action( 'ld_added_group_access', array( $this, 'auto_subscribe_group_access' ), 10, 2 );

        add_action( 'ld_added_course_group_access', array( $this, 'auto_subscribe_group_course_added_access' ), 10, 2 );
		
	}
	
    /**
     * Adds Register to Newsletter Checkbox before the Course Registration Button
     * 
     * @param		string $join_button    Join Button HTML
     * @param		array  $payment_params Payment Paramters
     *                                        
     * @access		public
     * @since		1.0.0
     * @return		string Join Button HTML
     */
    public function add_subscription_checkbox( $join_button, $payment_params ) {
		
        if ( is_user_logged_in() && 
			ld_mailchimp_get_option( 'auto_subscribe' ) !== '1' ) {
			
            $list_id = ld_mailchimp_get_option( 'mailchimp_list' );
			
            $user_id = get_current_user_id();
            $user_data = get_userdata($user_id );
            $user_email = $user_data->user_email;
			
            // get course options
            $course = $payment_params['post'];
			
            $show_subscription_form = get_post_meta( $course->ID, 'ld_mailchimp_display_subscription_form', true );
			
            $meta = get_post_meta( $course->ID, '_sfwd-courses', true );
            $course_price_type = @$meta['sfwd-courses_course_price_type'];
			
            $segment_id = get_post_meta( $course->ID, 'ld_mailchimp_course_segment_' . $list_id, true );
            $emails = ld_mailchimp_get_list_segment_emails( $segment_id );
			
            $subscribe_message = ld_mailchimp_get_option( 'subscribe_message', __( 'Subscribe to our newsletter', 'learndash-mailchimp' ) );
			
            if ( ($course_price_type == 'paynow' || 
				  $course_price_type == 'subscribe' ) ) {
				
				$join_button = preg_replace_callback(
					'/(<input.* id="btn-join*".*>)/', 
					function ( $matches ) {
						return ' ' . $matches[0];
					}, 
					$join_button
				);
				
            }
			else {

                if ( ! in_array( $user_email, $emails ) && 
					$show_subscription_form == '1' ) {

					$join_button = preg_replace_callback(
						'/(<input.* id="btn-join*".*>)/', 
						function ( $matches ) use( $subscribe_message ) {
							return '<div class="ld-checkbox"><input type="checkbox" name="ld_mailchimp_checkbox_course" value="1"  class="btn-join" id="btn-join"><label style="display: inline-block;">' . $subscribe_message . '</label></div>' . $matches[0];
						}, 
						$join_button
					);
					
                }
				
            }

        }       

		return $join_button;
		
    }
	
    /**
     * Adds User to List Tag if they've opted in while joining the Course
     * 
     * @param		integer $user_id     WP_User ID
     * @param		integer $course_id   WP_Post ID
     * @param		array   $access_list Access List Array
     * @param		boolean $remove      True if removed from Course
     *                                                   
     * @access		public
     * @since		1.0.0
     * @return		void
     */
	public function course_join_add_email_to_segment( $user_id, $course_id, $access_list, $remove ) {
		
        if ( ! $remove && 
			isset( $_POST['ld_mailchimp_checkbox_course'] ) && 
			$_POST['ld_mailchimp_checkbox_course'] ) {
			
			$list_id = ld_mailchimp_get_option( 'mailchimp_list' );
			
            // get course segment id
            $segment_id = get_post_meta( $course_id, 'ld_mailchimp_course_segment' . $list_id, true );
			
            if ( $segment_id ) {
                $result = ld_mailchimp_add_user_to_list_segment( $user_id, $segment_id, $list_id );
            }
			
        }
		
    }

    /**
     * Custom content after the Course Status section of the Course template output
     * 
     * @param		string $after_course_status Content to place after the Course Status
     *                                                                       
     * @access		public
     * @since		1.0.0
     * @return		string Content to place after the Course Status
     */
    public function after_course_status( $after_course_status = '' ) {

        if ( ! is_user_logged_in() ) return $after_course_status;

        if ( ld_mailchimp_get_option( 'auto_subscribe' ) == '1' ) return $after_course_status;
		
        $course_id = get_the_ID();
        $list_id = ld_mailchimp_get_option( 'mailchimp_list' );
		
        //get user info
        $user_id = get_current_user_id();
        $user_data = get_userdata( $user_id );
        $user_email = $user_data->user_email;
		
        $segment_id = get_post_meta( $course_id, 'ld_mailchimp_course_segment_' . $list_id, true );
		
        $emails = ld_mailchimp_get_list_segment_emails( $segment_id, $list_id );
		
        $user_can_take_course = sfwd_lms_has_access( $course_id, $user_id );
		$show_subscription_form = get_post_meta( $course_id, 'ld_mailchimp_display_subscription_form', true );

        if ( ! in_array( $user_email, $emails ) && 
			$user_can_take_course && 
			$segment_id && 
			$show_subscription_form ) {
			
            $after_course_status .= ld_mailchimp_subscribe_form( $course_id );
			
        }
		elseif ( in_array( $user_email, $emails ) &&
				$user_can_take_course && 
				$show_subscription_form ) {
			
           $after_course_status .= ld_mailchimp_get_option( 'subscription_success', apply_filters( 'learndash_mailchimp_already_subscribed_text', __( 'Subscribed!', 'learndash-mailchimp' ) ) );
            
        }

        return $after_course_status;
		
    }
	
    /**
     * Handle Tag Addition using the Subscribe Form on the Frontend
     * 
     * @access		public
     * @since		1.0.0
     * @return		void
     */
    public function subscribe_form_add_email_to_segment() {
		
		$course_id = isset( $_POST['ld_mailchimp_course_id'] ) ? $_POST['ld_mailchimp_course_id'] : false;
		
		if ( ! $course_id || 
			! isset( $_POST['ld_mailchimp_submit_subscribed'] ) || 
			! wp_verify_nonce( $_REQUEST['ld_mailchimp_subscribe_course_nonce'], "ld_mailchimp_subscribe_course_id_$course_id" ) ) return false;
		
        $list_id = ld_mailchimp_get_option( 'mailchimp_list' );
        $user_id = get_current_user_id();
        
        if ( $user_id && 
		   $list_id ) {

			$segment_id = get_post_meta( $course_id, 'ld_mailchimp_course_segment_' . $list_id, true );
			
			if ( $segment_id ) {
				$result = ld_mailchimp_add_user_to_list_segment( $user_id, $segment_id, $list_id );
			}
			
        }
		
    }
	
	/**
	 * Subscribes User to Mailchimp List Tag automatically when Enrolling in a Course
	 * 
	 * @param		integer $user_id     User ID
	 * @param		integer $course_id   Course ID
	 * @param		array   $access_list Course Access List
	 * @param		boolean $remove      True if removing the Course from user, false otherwise.
	 *                                                                           
	 * @access		public
	 * @since		1.1.0
	 * @return		void
	 */
	public function auto_subscribe( $user_id, $course_id, $access_list, $remove ) {
		
		// Not auto-subscribing, bail
		if ( ld_mailchimp_get_option( 'auto_subscribe' ) !== '1' ) {
			return false;
        }
        
        $auto_subscribe = apply_filters( 'learndash_mailchimp_auto_subscribe_user', true, $user_id, $course_id, $access_list, $remove, 'course_enroll' );

        if ( ! $auto_subscribe ) return;
		
		$list_id = ld_mailchimp_get_option( 'mailchimp_list' );
		$segment_id = get_post_meta( $course_id, 'ld_mailchimp_course_segment_' . $list_id, true );
		
		if ( $remove === false && 
		   $list_id && 
		   $segment_id ) {
			$result = ld_mailchimp_add_user_to_list_segment( $user_id, $segment_id, $list_id );
		}
		
    }
    
    /**
     * Subscribes User to MailChimp List Tag automatically when a Student is added to a Group
     * Each associated Course within a Group will give the Student the appropriate MailChimp Tag
     *
     * @param   integer  $user_id   User ID
     * @param   integer  $group_id  Group ID
     *
     * @access  public
     * @since   1.1.2
     * @return  void
     */
    public function auto_subscribe_group_access( $user_id, $group_id ) {

        // Not auto-subscribing, bail
		if ( ld_mailchimp_get_option( 'auto_subscribe' ) !== '1' ) {
			return false;
        }

        $list_id = ld_mailchimp_get_option( 'mailchimp_list' );

        $auto_subscribe = apply_filters( 'learndash_mailchimp_auto_subscribe_user', true, $user_id, $group_id, null, false, 'group_access' );

        if ( $auto_subscribe ) {

            $segment_id = get_post_meta( $group_id, 'ld_mailchimp_group_segment_' . $list_id, true );
                    
            if ( $list_id && 
                $segment_id ) {
                $result = ld_mailchimp_add_user_to_list_segment( $user_id, $segment_id, $list_id );
            }

        }

        $course_ids = learndash_group_enrolled_courses( $group_id );

        foreach ( $course_ids as $course_id ) {

            // This uses the same Filter as above for simplicity when trying to prevent Auto-Subscriptions for a User or sets of users programmatically
            $auto_subscribe = apply_filters( 'learndash_mailchimp_auto_subscribe_user', true, $user_id, $course_id, null, false, 'group_access' );

            if ( ! $auto_subscribe ) continue;

            $segment_id = get_post_meta( $course_id, 'ld_mailchimp_course_segment_' . $list_id, true );
            
            if ( $list_id && 
            $segment_id ) {
                $result = ld_mailchimp_add_user_to_list_segment( $user_id, $segment_id, $list_id );
            }

        }

    }

    /**
     * When a new Course is added to a Group, ensure that the Student gets that Tag
     *
     * @param   integer  $course_id  Course ID
     * @param   integer  $group_id   Group ID
     *
     * @access  public
     * @since   1.1.2
     * @return  void
     */
    public function auto_subscribe_group_course_added_access( $course_id, $group_id ) {

        // Not auto-subscribing, bail
		if ( ld_mailchimp_get_option( 'auto_subscribe' ) !== '1' ) {
			return false;
        }

        $list_id = ld_mailchimp_get_option( 'mailchimp_list' );

        $user_ids = learndash_get_groups_user_ids( $group_id );

        foreach ( $user_ids as $user_id ) {

            $auto_subscribe = apply_filters( 'learndash_mailchimp_auto_subscribe_user', true, $user_id, $course_id, null, false, 'group_course_added_access' );

            if ( $auto_subscribe ) {

                $segment_id = get_post_meta( $course_id, 'ld_mailchimp_course_segment_' . $list_id, true );
            
                if ( $list_id && 
                $segment_id ) {
                    $result = ld_mailchimp_add_user_to_list_segment( $user_id, $segment_id, $list_id );
                }

            }

        }

    }

}

$instance = new LearnDash_MailChimp_Add_Course();