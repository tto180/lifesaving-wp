<?php
/**
 * Student Table
 *
 * @package LearnDash\Reports
 */

namespace WisdmReportsLearndashBlockRegistry;

use WP_Post;

defined( 'ABSPATH' ) || exit;

require_once 'class-wrld-register-block.php';
if ( ! class_exists( '\WisdmReportsLearndashBlockRegistry\WRLD_Student_Table' ) ) {
	/**
	 * This class contains the Functionality required to register the Student Dashboard Block
	 */
	class WRLD_Student_Table extends WRLD_Register_Block {
		/**
		 * Constructor.
		 *
		 * @param string $block_name           Set block name during construct.
		 * @param string $block_title          To be displayed in the WP-Admin.
		 * @param string $description          Description of the block.
		 * @param string $server_side_callback Function name, the child class must implement the method specified as this argument.
		 * @param int    $api_version           Block API Version , default 2.
		 */
		public function __construct( $block_name = 'student-table', $block_title = 'Student Table', $description = 'Student Table To Show Filtered Results', $server_side_callback = false, $api_version = 2 ) {
			$this->block_name  = $block_name ? $block_name : $this->block_name;
			$this->api_version = $api_version;
			$this->description = $description;
			$this->block_title = $block_title;
			add_filter( 'wisdm_learndash_reports_front_end_script_student_table', array( $this, 'localize_additional_student_data' ), 11, 1 );
			$this->wrld_register_block_assets();
			$this->wrld_register_block_type();
			$this->server_side_callback = 'server_side_render_function';
		}



		/**
		 * The function can be used to render the block content on the server side.
		 */
		public function server_side_render_function() {
			return 'Html if required';
		}

		/**
		 * This function can be used to localizes the additional data required for the parent block.
		 *
		 * @param array $data Default data to be localized in the script.
		 */
		public function localize_additional_student_data( $data ) {
			$data['ld_api_settings']  = get_option( 'learndash_settings_rest_api', array() );
			$data['current_user']     = wp_get_current_user();
			$data['courses_enrolled'] = learndash_user_get_enrolled_courses( get_current_user_id() );
			$data['exclude_courses']  = get_option( 'exclude_courses', '' );

			$current_user = wp_get_current_user();
			if ( empty( $current_user ) || 0 == $current_user->ID ) {
				return $data;
			}
			$data['avatar_url'] = get_avatar_url( get_current_user_id() );
			$quiz_query_args    = array(
				'post_type'   => 'sfwd-quiz',
				'post_status' => 'publish',
				'nopaging'    => true,
				'orderby'     => 'title',
				'order'       => 'ASC',
			);

			$query = new \WP_Query( $quiz_query_args );
			if ( ! empty( $query->posts ) ) {
				foreach ( $query->posts as $quiz ) {
					if ( ! $quiz instanceof WP_Post ) {
						continue;
					}

					$course_id        = get_post_meta( $quiz->ID, 'course_id', true );
					$data['quizes'][] = array( // cspell:disable-line .
						'value'     => $quiz->ID,
						'label'     => learndash_propanel_get_the_title( $quiz->ID ),
						'course_id' => $course_id,
					);
				}
			}

			return $data;
		}
	}
}
