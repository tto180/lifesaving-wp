<?php
/**
 * Welcome modal content.
 *
 * @package LearnDash\Reports
 *
 * @since 3.0.0
 * @version 3.0.0
 *
 * cspell:ignore dont
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'wrld_free_get_popup_modal_content' ) ) {
	function wrld_free_get_popup_modal_content( $other_dashboard ) {
		return '<div id="wrld-welcome-modal" class="wrld-welcome-popup-modal" style="display:none;">
        <div class="wrld-modal-content-container">
            <div class="wrld-image-section">
                <img src="' . WRLD_REPORTS_SITE_URL . '/assets/images/check_green.svg" alt="">
            </div>
            <div class="wrld-content-section">
                <div class="wrld-content-head">
                    <span>' . esc_html__( 'Hurray! Dashboard Launch Successful.', 'learndash-reports-pro' ) . '</span>
                </div>
                <div class="wrld-content-body free">
                    <span>' . esc_html__( 'You can now visit the ProPanel Dashboard to track and/or analyze trends and other statistics for your LearnDash LMS.', 'learndash-reports-pro' ) . ' </span>
                </div>
                <div class="wrld-content-footer">
                    <button class="modal-button modal-button-reports secondary">' . esc_html__( 'Let\'s get started', 'learndash-reports-pro' ) . '<i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                ' . $other_dashboard . '
                </div>
            </div>
        </div>
    </div>';
	}
}

if ( ! function_exists( 'wrld_student_get_popup_modal_content' ) ) {
	function wrld_student_get_popup_modal_content( $other_dashboard ) {
		return '<div id="wrld-welcome-modal" class="wrld-welcome-popup-modal" style="display:none;">
        <div class="wrld-modal-content-container">
            <div class="wrld-image-section">
                <img src="' . WRLD_REPORTS_SITE_URL . '/assets/images/check_green.svg" alt="">
            </div>
            <div class="wrld-content-section">
                <div class="wrld-content-head">
                    <span>' . esc_html__( 'Hurray! The Student Quiz Reports Page Launch Successful.', 'learndash-reports-pro' ) . '</span>
                </div>
                <div class="wrld-content-body free">
                    <span>' . esc_html__( 'You can now visit the page and check your quiz report.', 'learndash-reports-pro' ) . ' </span>
                </div>
                <div class="wrld-content-footer">
                    <button class="modal-button modal-button-reports secondary">' . esc_html__( 'Let\'s get started', 'learndash-reports-pro' ) . '<i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                    ' . $other_dashboard . '
                </div>
            </div>
        </div>
    </div>';
	}
}

if ( ! function_exists( 'wrld_pro_get_popup_modal_content' ) ) {
	function wrld_pro_get_popup_modal_content( $other_dashboard ) {
		return '<div id="wrld-welcome-modal" class="wrld-welcome-popup-modal" style="display:none;">
        <div class="wrld-modal-content-container">
            <div class="wrld-image-section">
                <img src="' . WRLD_REPORTS_SITE_URL . '/assets/images/check_green.svg" alt="">
            </div>
            <div class="wrld-content-section">
                <div class="wrld-content-head">
                    <span>' . esc_html__( 'Hurray! You have successfully configured ProPanel.', 'learndash-reports-pro' ) . '</span>
                </div>
                <div class="wrld-content-subhead">
                    <span>' . esc_html__( 'You can now access:', 'learndash-reports-pro' ) . '</span>
                </div>
                <div class="wrld-content-body">
                    <ul>
                    <li>' . esc_html__( 'The Quiz Reports Tab to check Quiz Attempt Reports.', 'learndash-reports-pro' ) . '</li>
                    <li>' . esc_html__( 'The Learner-specific reports to check important reports for individual learners.', 'learndash-reports-pro' ) . '</li>
                    <li>' . esc_html__( 'More Insights.', 'learndash-reports-pro' ) . '</li>
                    </ul>
                </div>
                <div class="wrld-content-footer">
                    <button class="modal-button modal-button-reports secondary">' . esc_html__( 'Got it!', 'learndash-reports-pro' ) . '<i class="fa fa-chevron-right" aria-hidden="true"></i></button>
                    ' . $other_dashboard . '
                </div>
            </div>
        </div>
    </div>';
	}
}

if ( ! function_exists( 'wrld_dont_turn_off_modal_content' ) ) {
	function wrld_dont_turn_off_modal_content() {
		return '<div id="wrld-welcome-modal" class="wrld-warning-popup-modal" style="display:none;">
        <div class="wrld-modal-content-container">
            <div class="wrld-image-section">
                <img src="' . WRLD_REPORTS_SITE_URL . '/assets/images/warning.svg" alt="">
            </div>
            <div class="wrld-content-section">
                <div class="wrld-content-head">
                    <span>' . esc_html__( 'Don\'t turn OFF this setting...', 'learndash-reports-pro' ) . '</span>
                </div>
                <div class="wrld-content-body free">
                    <span>' . esc_html__( 'We do not recommend you to “switch off” this setting as it will stop tracking the “Idle Time” and affect the “Time Spent” Reports on the ProPanel Dashboard which might lead to inaccurate data.', 'learndash-reports-pro' ) . ' </span>
                </div>
                <div class="wrld-content-footer">
                    <button class="modal-button modal-button-cancel modal-button-inverse">' . esc_html__( 'Skip', 'learndash-reports-pro' ) . '</button>
                    <button class="modal-button modal-button-proceed">' . esc_html__( 'Proceed anyway', 'learndash-reports-pro' ) . '</button>
                </div>
            </div>
        </div>
    </div>';
	}
}
