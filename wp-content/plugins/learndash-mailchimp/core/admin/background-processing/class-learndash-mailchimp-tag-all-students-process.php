<?php
/**
 * Handles the background processes for tagging all Students
 *
 * @since	1.1.0
 *
 * @package	LearnDash_MailChimp
 * @subpackage LearnDash_MailChimp/core/admin/background-processing
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

final class LearnDash_MailChimp_Tag_All_Students_Process extends WP_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'learndash_mailchimp_tag_all_students';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $user_id ) {

        $user_progress = SFWD_LMS::get_course_info( $user_id, array(
            'user_id' => $user_id,
            'return' => true,
            'type' => array( 'registered', 'course' ),
        ) );

        if ( ! isset( $user_progress['courses_registered'] ) || 
            empty( $user_progress['courses_registered'] ) ) {

            $user_progress['courses_registered'] = array();

        }

        $course_progress = array();
        if ( isset( $user_progress['course_progress'] ) || 
            ! empty( $user_progress['course_progress'] ) ) {

            foreach ( $user_progress['course_progress'] as $course_id => $progress ) {

                if ( ! empty( $progress ) ) {
                    $course_progress[] = $course_id;
                }
                
            }

        }

		$list_id = ld_mailchimp_get_option( 'mailchimp_list' );

		$course_ids = array_merge( $user_progress['courses_registered'], $course_progress );
		
		// Ensure we get any Courses from Groups for the Student
		$group_ids = learndash_get_users_group_ids( $user_id );
		foreach ( $group_ids as $group_id ) {

			// We use a Group ID here, but the concept is the same as the other uses for this Filter
			$auto_subscribe = apply_filters( 'learndash_mailchimp_auto_subscribe_user', true, $user_id, $group_id, null, false, 'tag_all_student' );

			if ( $auto_subscribe ) {
			
				$segment_id = get_post_meta( $group_id, 'ld_mailchimp_group_segment_' . $list_id, true );
				
				if ( $list_id && 
					$segment_id ) {
					$result = ld_mailchimp_add_user_to_list_segment( $user_id, $segment_id, $list_id );
				}

			}

			$group_course_ids = learndash_group_enrolled_courses( $group_id );

			if ( empty( $group_course_ids ) ) continue;

			$course_ids = array_merge( $course_ids, $group_course_ids );

		}

		$course_ids = array_unique( $course_ids );

        foreach ( $course_ids as $course_id ) {
			
            $auto_subscribe = apply_filters( 'learndash_mailchimp_auto_subscribe_user', true, $user_id, $course_id, null, false, 'tag_all_students' );

			if ( ! $auto_subscribe ) continue;
			
            $segment_id = get_post_meta( $course_id, 'ld_mailchimp_course_segment_' . $list_id, true );
            
            if ( $list_id && 
                $segment_id ) {
                $result = ld_mailchimp_add_user_to_list_segment( $user_id, $segment_id, $list_id );
            }

        }
		
		error_log( "$user_id has been processed" );

		return false;
		
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
		
        error_log( "All Users have been Tagged in MailChimp" );

        delete_transient( 'ld_mailchimp_users_tag_started' );
		set_transient( 'ld_mailchimp_users_tag_complete', true, DAY_IN_SECONDS );
		
	}

}