<?php
/**
 * Displays a lesson.
 *
 * Available Variables:
 *
 * $course_id        : (int) ID of the course
 * $course        : (object) Post object of the course
 * $course_settings : (array) Settings specific to current course
 * $course_status    : Course Status
 * $has_access    : User has access to course or is enrolled.
 *
 * $courses_options : Options/Settings as configured on Course Options page
 * $lessons_options : Options/Settings as configured on Lessons Options page
 * $quizzes_options : Options/Settings as configured on Quiz Options page
 *
 * $user_id        : (object) Current User ID
 * $logged_in        : (true/false) User is logged in
 * $current_user    : (object) Currently logged in user object
 *
 * $quizzes        : (array) Quizzes Array
 * $post            : (object) The lesson post object
 * $topics        : (array) Array of Topics in the current lesson
 * $all_quizzes_completed : (true/false) User has completed all quizzes on the lesson Or, there are no quizzes.
 * $lesson_progression_enabled    : (true/false)
 * $show_content    : (true/false) true if lesson progression is disabled or if previous lesson is completed.
 * $previous_lesson_completed    : (true/false) true if previous lesson is completed
 * $lesson_settings : Settings specific to the current lesson.
 *
 * @since 2.1.0
 *
 * @package LearnDash\Lesson
 */
?>
<?php if ( @$lesson_progression_enabled && ! @$previous_lesson_completed ) : ?>
	<span id="learndash_complete_prev_lesson">
	<?php
	$previous_item = learndash_get_previous( $post );
	if ( ( ! empty( $previous_item ) ) && ( $previous_item instanceof WP_Post ) ) {
		if ( $previous_item->post_type == 'sfwd-quiz' ) {
			echo sprintf( esc_attr_x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%1$s">%2$s</a>.', 'placeholders: quiz URL, quiz label', 'learndash' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower( 'quiz' ) );

		} elseif ( $previous_item->post_type == 'sfwd-topic' ) {
			echo sprintf( esc_attr_x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%1$s">%2$s</a>.', 'placeholders: topic URL, topic label', 'learndash' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower( 'topic' ) );
		} else {
			echo sprintf( esc_attr_x( 'Please go back and complete the previous <a class="learndash-link-previous-incomplete" href="%1$s">%2$s</a>.', 'placeholders: lesson URL, lesson label', 'learndash' ), get_permalink( $previous_item->ID ), LearnDash_Custom_Label::label_to_lower( 'lesson' ) );
		}
	} else {
		echo sprintf( esc_attr_x( 'Please go back and complete the previous %s.', 'placeholder lesson', 'learndash' ), LearnDash_Custom_Label::label_to_lower( 'lesson' ) );
	}
	?>
	</span><br/>
	<?php add_filter( 'comments_array', 'learndash_remove_comments', 1, 2 ); ?>
<?php endif; ?>

<?php if ( $show_content ) : ?>
	<?php if ( ( isset( $materials ) ) && ( ! empty( $materials ) ) ) : ?>
		<div id="learndash_lesson_materials" class="learndash_lesson_materials">
			<h4>
			<?php
			// translators: placeholder: Lesson.
			printf( esc_html_x( '%s Materials', 'placeholder: Lesson', 'learndash' ), LearnDash_Custom_Label::get_label( 'lesson' ) );
			?>
			</h4>
			<p><?php echo $materials; ?></p>
		</div>
	<?php endif; ?>

	<div class="learndash_content"><?php echo $content; ?></div>

	<?php
	/**
	 * Lesson Topics
	 */
	?>
	<?php if ( ! empty( $topics ) ) : ?>
		<div class="uo_custom_grid clear flush" style="clear:both">
			<?php
			if ( ! has_shortcode( $post->post_content, 'uo_lessons_topics_grid' ) ) {
				$cols         = \uncanny_pro_toolkit\LessonTopicGrid::get_shortcode_default_cols_attr();
				$grid_quizzes = \uncanny_pro_toolkit\LessonTopicGrid::get_shortcode_default_show_quizzes_attr();
				echo do_shortcode( '[uo_lessons_topics_grid lesson_id="' . $post->ID . '"' . $cols . $grid_quizzes . ']' );
				$grid_quizzes = ! empty( $grid_quizzes );
			} else {
				$grid_quizzes = \uncanny_pro_toolkit\LessonTopicGrid::shortcode_has_show_quizzes_set( $post->post_content );
			}
			?>
		</div>
	<?php endif; ?>


	<?php
	/**
	 * Show Quiz List
	 */
	?>
	<?php if ( ! $grid_quizzes && ! empty( $quizzes ) ) : ?>
		<div id="learndash_quizzes" class="learndash_quizzes">
			<div id="quiz_heading">
				<span><?php echo LearnDash_Custom_Label::get_label( 'quizzes' ); ?></span><span class="right"><?php esc_attr_e( 'Status', 'learndash' ); ?></span>
			</div>
			<div id="quiz_list" class="quiz_list">

				<?php foreach ( $quizzes as $quiz ) : ?>
					<div id='post-<?php echo esc_attr( $quiz['post']->ID ); ?>' class='<?php echo esc_attr( $quiz['sample'] ); ?>'>
						<div class="list-count"><?php echo esc_attr( $quiz['sno'] ); ?></div>
						<h4>
							<a class='<?php echo esc_attr( $quiz['status'] ); ?>' href='<?php echo esc_attr( $quiz['permalink'] ); ?>'><?php echo $quiz['post']->post_title; ?></a>
						</h4>
					</div>
				<?php endforeach; ?>

			</div>
		</div>
	<?php endif; ?>


	<?php
	/**
	 * Display Lesson Assignments
	 */
	?>
	<?php $assignments_exist_fn = function_exists( 'learndash_lesson_hasassignments' ) ? 'learndash_lesson_hasassignments' : 'lesson_hasassignments'; ?>
	<?php if ( $assignments_exist_fn( $post ) ) : ?>
		<?php $assignments = learndash_get_user_assignments( $post->ID, $user_id ); ?>

		<div id="learndash_uploaded_assignments" class="learndash_uploaded_assignments">
			<h2><?php esc_attr_e( 'Files you have uploaded', 'learndash' ); ?></h2>
			<table>
				<?php if ( ! empty( $assignments ) ) : ?>
					<?php foreach ( $assignments as $assignment ) : ?>
						<tr>
							<td>
								<a href='<?php echo esc_attr( get_post_meta( $assignment->ID, 'file_link', true ) ); ?>' target="_blank"><?php echo esc_attr__( 'Download', 'learndash' ) . ' ' . get_post_meta( $assignment->ID, 'file_name', true ); ?></a>
								<br/>
								<span class="learndash_uploaded_assignment_points"><?php echo learndash_assignment_points_awarded( $assignment->ID ); ?></span>
							</td>
							<td>
								<a href='<?php echo esc_attr( get_permalink( $assignment->ID ) ); ?>'><?php esc_attr_e( 'Comments', 'learndash' ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</table>
		</div>
	<?php endif; ?>


	<?php
	/**
	 * Display Mark Complete Button
	 */
	?>
	<?php if ( $all_quizzes_completed && $logged_in ) : ?>
		<br/>
		<?php echo learndash_mark_complete( $post ); ?>
	<?php endif; ?>

<?php endif; ?>

<br/>

<p id="learndash_next_prev_link"><?php echo learndash_previous_post_link(); ?>
	<?php
	/*
	 * See details for filter 'learndash_show_next_link' https://bitbucket.org/snippets/learndash/5oAEX
	 *
	 * @since version 2.3
	 */
	?>
	<?php if ( apply_filters( 'learndash_show_next_link', learndash_is_lesson_complete( $user_id, $post->ID ), $user_id, $post->ID ) ) { ?>
		&nbsp;&nbsp;&nbsp;&nbsp;<?php echo learndash_next_post_link(); ?>
	<?php } ?>
</p>
