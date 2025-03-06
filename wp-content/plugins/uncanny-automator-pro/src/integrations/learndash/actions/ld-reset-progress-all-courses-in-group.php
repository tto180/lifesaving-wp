<?php

namespace Uncanny_Automator_Pro;

use Uncanny_Automator\Learndash_Helpers;
use Uncanny_Automator\Recipe;

/**
 * Class LD_RESET_PROGRESS_ALL_COURSES_IN_GROUP
 *
 * @package Uncanny_Automator_Pro
 */
class LD_RESET_PROGRESS_ALL_COURSES_IN_GROUP {

	use Recipe\Actions;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		if ( ! class_exists( 'Uncanny_Automator\Learndash_Helpers' ) ) {
			return;
		}
		$this->setup_action();
		$this->set_helpers( new Learndash_Helpers() );
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	protected function setup_action() {
		$this->set_integration( 'LD' );
		$this->set_action_code( 'LD_RESET_ALL_COURSES_PROGRESS' );
		$this->set_action_meta( 'LD_GROUPS' );
		$this->set_requires_user( true );
		$this->set_is_pro( true );

		/* translators: Action - LearnDash */
		$this->set_sentence( sprintf( esc_attr_x( "Reset the user's progress for all courses associated with {{a group:%1\$s}}", 'LearnDash', 'uncanny-automator-pro' ), $this->get_action_meta(), $this->get_action_meta() . '_GROUP' ) );
		/* translators: Action - LearnDash */
		$this->set_readable_sentence( esc_attr_x( "Reset the user's progress for all courses associated with {{a group}}", 'LearnDash', 'uncanny-automator-pro' ) );
		$this->set_options_callback( array( $this, 'load_options' ) );

		$this->register_action();
	}

	/**
	 * Load_options
	 *
	 * @return array
	 */
	public function load_options() {
		return Automator()->utilities->keep_order_of_options(
			array(
				'options' => array(
					$this->get_helpers()->all_ld_groups( null, $this->get_action_meta(), false, false, false, false ),
				),
			)
		);

	}

	/**
	 * Process the action.
	 *
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 * @param $args
	 * @param $parsed
	 *
	 * @return void.
	 * @throws \Exception
	 */
	protected function process_action( $user_id, $action_data, $recipe_id, $args, $parsed ) {

		$group_id = isset( $parsed[ $this->get_action_meta() ] ) ? sanitize_text_field( $parsed[ $this->get_action_meta() ] ) : '';

		if ( empty( $group_id ) ) {
			$action_data['complete_with_errors'] = true;
			$error_message                       = esc_html__( 'Please select at least one group to perform this action.', 'uncanny-automator-pro' );
			Automator()->complete->action( $user_id, $action_data, $recipe_id, $error_message );
		}

		$all_courses = learndash_group_enrolled_courses( $group_id );

		foreach ( $all_courses as $course_id ) {
			$this->delete_user_activity( $user_id, $course_id );
			if ( $this->delete_course_progress( $user_id, $course_id ) ) {
				$this->reset_quiz_progress( $user_id, $course_id );
				$this->delete_assignments();
			}
			$this->reset_quiz_progress( $user_id, $course_id );
		}

		Automator()->complete->action( $user_id, $action_data, $recipe_id );
	}

	/**
	 *
	 * Delete course related meta keys from user meta table.
	 * Delete all activity related to a course from LD tables
	 *
	 * @param $user_id
	 * @param $course_id
	 */
	public function delete_user_activity( $user_id, $course_id ) {
		global $wpdb;
		delete_user_meta( $user_id, 'completed_' . $course_id );
		//delete_user_meta( $user_id, 'course_' . $course_id . '_access_from' );
		delete_user_meta( $user_id, 'course_completed_' . $course_id );
		delete_user_meta( $user_id, 'learndash_course_expired_' . $course_id );

		$activity_ids = $wpdb->get_results( $wpdb->prepare( "SELECT activity_id FROM {$wpdb->prefix}learndash_user_activity WHERE course_id=%d AND user_id=%d", $course_id, $user_id ) );

		if ( $activity_ids ) {
			foreach ( $activity_ids as $activity_id ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM  {$wpdb->prefix}learndash_user_activity_meta WHERE activity_id=%d", $activity_id->activity_id ) );
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}learndash_user_activity WHERE activity_id=%d", $activity_id->activity_id ) );
			}
		}
	}

	/**
	 *
	 * Delete course progress from Usermeta Table
	 *
	 * @param $user_id
	 * @param $course_id
	 *
	 * @return bool
	 */
	public function delete_course_progress( $user_id, $course_id ) {
		$usermeta = get_user_meta( $user_id, '_sfwd-course_progress', true );
		if ( ! empty( $usermeta ) && isset( $usermeta[ $course_id ] ) ) {
			unset( $usermeta[ $course_id ] );
			update_user_meta( $user_id, '_sfwd-course_progress', $usermeta );

			$last_know_step = get_user_meta( $user_id, 'learndash_last_known_page', true );
			if ( ! empty( $last_know_step ) ) {
				if ( false !== strpos( $last_know_step, ',' ) ) {
					$last_know_step = explode( ',', $last_know_step );

					if ( isset( $last_know_step[0] ) && isset( $last_know_step[1] ) ) {
						$step_id        = $last_know_step[0];
						$step_course_id = $last_know_step[1];

						if ( (int) $step_course_id === (int) $course_id ) {
							delete_user_meta( $user_id, 'learndash_last_known_page' );
						}
					}
				}
			}

			delete_user_meta( $user_id, 'learndash_last_known_course_' . $course_id );

			return true;
		}

		return false;
	}

	/**
	 *
	 * Get lesson quiz list
	 * Get Lesson assignment list
	 * Delete quiz progress, related to course, quiz etc
	 *
	 * @param $user_id
	 * @param $course_id
	 */
	public function reset_quiz_progress( $user_id, $course_id ) {
		$lessons = learndash_get_lesson_list( $course_id, array( 'num' => 0 ) );
		foreach ( $lessons as $lesson ) {
			$this->get_topics_quiz( $user_id, $lesson->ID, $course_id );
			$lesson_quiz_list = learndash_get_lesson_quiz_list( $lesson->ID, $user_id, $course_id );

			if ( $lesson_quiz_list ) {
				foreach ( $lesson_quiz_list as $ql ) {
					$this->quiz_list[ $ql['post']->ID ] = 0;
				}
			}

			//grabbing lesson related assignments
			$assignments = get_posts(
				array(
					'post_type'      => 'sfwd-assignment',
					'posts_per_page' => 999,
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'key'     => 'lesson_id',
							'value'   => $lesson->ID,
							'compare' => '=',
						),
						array(
							'key'     => 'course_id',
							'value'   => $course_id,
							'compare' => '=',
						),
						array(
							'key'     => 'user_id',
							'value'   => $user_id,
							'compare' => '=',
						),
					),
				)
			);

			if ( $assignments ) {
				foreach ( $assignments as $assignment ) {
					$this->assignment_list[] = $assignment->ID;
				}
			}
		}

		$this->delete_quiz_progress( $user_id, $course_id );
	}

	/**
	 *
	 * Get topic quiz + assignment list
	 *
	 * @param $user_id
	 * @param $lesson_id
	 * @param $course_id
	 */
	public function get_topics_quiz( $user_id, $lesson_id, $course_id ) {
		$topic_list = learndash_get_topic_list( $lesson_id, $course_id );
		if ( $topic_list ) {
			foreach ( $topic_list as $topic ) {
				$topic_quiz_list = learndash_get_lesson_quiz_list( $topic->ID, $user_id, $course_id );
				if ( $topic_quiz_list ) {
					foreach ( $topic_quiz_list as $ql ) {
						$this->quiz_list[ $ql['post']->ID ] = 0;
					}
				}

				$assignments = get_posts(
					array(
						'post_type'      => 'sfwd-assignment',
						'posts_per_page' => 999,
						'meta_query'     => array(
							'relation' => 'AND',
							array(
								'key'     => 'lesson_id',
								'value'   => $topic->ID,
								'compare' => '=',
							),
							array(
								'key'     => 'course_id',
								'value'   => $course_id,
								'compare' => '=',
							),
							array(
								'key'     => 'user_id',
								'value'   => $user_id,
								'compare' => '=',
							),
						),
					)
				);

				if ( $assignments ) {
					foreach ( $assignments as $assignment ) {
						$this->assignment_list[] = $assignment->ID;
					}
				}
			}
		}
	}

	/**
	 *
	 * Actually deleting quiz data from user meta and pro quiz activity table
	 *
	 * @param      $user_id
	 * @param null $course_id
	 */
	public function delete_quiz_progress( $user_id, $course_id = null ) {
		$quizzes = learndash_get_course_quiz_list( $course_id, $user_id );
		if ( $quizzes ) {
			foreach ( $quizzes as $quiz ) {
				$this->quiz_list[ $quiz['post']->ID ] = 0;
			}
		}
		global $wpdb;

		$quizz_progress = array();
		if ( ! empty( $this->quiz_list ) ) {
			$usermeta       = get_user_meta( $user_id, '_sfwd-quizzes', true );
			$quizz_progress = empty( $usermeta ) ? array() : $usermeta;
			foreach ( $quizz_progress as $k => $p ) {
				if ( key_exists( $p['quiz'], $this->quiz_list ) && $p['course'] == $course_id ) {
					$statistic_ref_id = $p['statistic_ref_id'];
					unset( $quizz_progress[ $k ] );
					if ( ! empty( $statistic_ref_id ) ) {

						if ( class_exists( '\LDLMS_DB' ) ) {
							$pro_quiz_stat_table     = \LDLMS_DB::get_table_name( 'quiz_statistic' );
							$pro_quiz_stat_ref_table = \LDLMS_DB::get_table_name( 'quiz_statistic_ref' );
						} else {
							$pro_quiz_stat_table     = $wpdb->prefix . 'wp_pro_quiz_statistic';
							$pro_quiz_stat_ref_table = $wpdb->prefix . 'wp_pro_quiz_statistic_ref';
						}

						$wpdb->query( $wpdb->prepare( "DELETE FROM {$pro_quiz_stat_table} WHERE statistic_ref_id = %d", $statistic_ref_id ) ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
						$wpdb->query( $wpdb->prepare( "DELETE FROM {$pro_quiz_stat_ref_table} WHERE statistic_ref_id = %d", $statistic_ref_id ) ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					}
				}
			}
		}

		update_user_meta( $user_id, '_sfwd-quizzes', $quizz_progress );
		// Get quiz progress again for attempts
		$usermeta       = get_user_meta( $user_id, '_sfwd-quizzes', true );
		$quizz_progress = empty( $usermeta ) ? array() : $usermeta;

		foreach ( $quizz_progress as $k => $p ) {
			if ( $p['course'] == $course_id ) {
				$statistic_ref_id = $p['statistic_ref_id'];
				unset( $quizz_progress[ $k ] );
				if ( ! empty( $statistic_ref_id ) ) {

					if ( class_exists( '\LDLMS_DB' ) ) {
						$pro_quiz_stat_table     = \LDLMS_DB::get_table_name( 'quiz_statistic' );
						$pro_quiz_stat_ref_table = \LDLMS_DB::get_table_name( 'quiz_statistic_ref' );
					} else {
						$pro_quiz_stat_table     = $wpdb->prefix . 'wp_pro_quiz_statistic';
						$pro_quiz_stat_ref_table = $wpdb->prefix . 'wp_pro_quiz_statistic_ref';
					}

					$wpdb->query( $wpdb->prepare( "DELETE FROM {$pro_quiz_stat_table} WHERE statistic_ref_id = %d", $statistic_ref_id ) ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
					$wpdb->query( $wpdb->prepare( "DELETE FROM {$pro_quiz_stat_ref_table} WHERE statistic_ref_id = %d", $statistic_ref_id ) ); //phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				}
			}
		}
		update_user_meta( $user_id, '_sfwd-quizzes', $quizz_progress );
	}

	/**
	 * Delete assignments of course, related to lessons / topics
	 */
	public function delete_assignments() {
		global $wpdb;
		$assignments = $this->assignment_list;
		//Utilities::log( $this->assignment_list, '$this->assignment_list', true, 'reset' );
		if ( $assignments ) {
			foreach ( $assignments as $assignment ) {
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->posts} WHERE ID = %d", $assignment ) );
				$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE post_id = %d", $assignment ) );
			}
		}
	}

}
