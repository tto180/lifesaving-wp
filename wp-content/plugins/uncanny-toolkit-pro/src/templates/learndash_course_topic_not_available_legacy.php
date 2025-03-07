<?php
/**
 * Displays the Course Topic Not Available    message
 *
 * Available Variables:
 * user_id : (integer) The user_id whose points to show
 * course_id : (integer) The ID of the couse shown
 * topic_id: (integer) The Of of the topic not available
 * ld_topic_access_from_int : (integer) timestamp when topic will become available
 * ld_topic_access_from_date : (string) Formatted human readable date/time of ld_topic_access_from_int
 * context : (string) The context will be set based on where this message is shown. course, topic, loop, etc.
 *
 * @since 2.4
 *
 * @package LearnDash\Course
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

//extract( $args );

// First generate the message
$message    = sprintf( wp_kses_post( esc_attr__( '<span class="ld-display-label">Available on:</span> <span class="ld-display-date">%s</span>', 'learndash' ) ), $topic_access_from_date );
$wrap_start = '<small class="notavailable_message">';
$wrap_end   = '</small>';

// The figure out how to display it
if ( $context == 'topic' ) {
	// On the topic single we display additional information.
	$message .= '<br><br><a href="' . get_permalink( $course_id ) . '">' . sprintf( esc_html_x( 'Return to %s Overview', 'Return to Course Overview Label', 'learndash' ), LearnDash_Custom_Label::get_label( 'course' ) ) . '</a>';

	$wrap_start = '<div class="notavailable_message">';
	$wrap_end   = '</div>';

} else if ( $context == 'course' ) {
	// No changes for course
} else {
	// Default no changes
}
echo $wrap_start . apply_filters( 'learndash_topic_available_from_text', $message, get_post( $topic_id ), $topic_access_from_int ) . $wrap_end;
