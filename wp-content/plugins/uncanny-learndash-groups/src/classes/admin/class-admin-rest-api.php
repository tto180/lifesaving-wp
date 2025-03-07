<?php


namespace uncanny_learndash_groups;

use WP_Error;
use WP_REST_Request;

/**
 * Class Admin_Rest_API
 *
 * @package uncanny_learndash_groups
 */
class Admin_Rest_API {

	/**
	 * class constructor
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'admin_register_routes' ) );
	}

	/**
	 *
	 */
	public function admin_register_routes() {

		register_rest_route(
			ULGM_REST_API_PATH,
			'/save_general_settings/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_general_settings' ),
				'permission_callback' => function () {
					return self::permission_callback_check( true );
				},
			)
		);

		register_rest_route(
			ULGM_REST_API_PATH,
			'/save_email_templates/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'save_email_templates' ),
				'permission_callback' => function () {
					return $this->permission_callback_check( true );
				},
			)
		);

		register_rest_route(
			ULGM_REST_API_PATH,
			'/regenerate_pages/',
			array(
				'methods'             => 'POST',
				'callback'            => array( $this, 'regenerate_pages' ),
				'permission_callback' => function () {
					return $this->permission_callback_check( true );
				},
			)
		);
	}

	/**
	 * Check permission of a current logged in user for rest_api call
	 *
	 * @param bool $admin_only
	 *
	 * @since 3.7
	 * @return bool|WP_Error
	 *
	 */
	public function permission_callback_check( $admin_only = false ) {
		if ( ! is_user_logged_in() ) {
			return new WP_Error( 'ulgm_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'uncanny-learndash-groups' ), array( 'status' => rest_authorization_required_code() ) );
		}

		if ( $admin_only ) {
			return current_user_can( 'manage_options' );
		}

		$user          = wp_get_current_user();
		$allowed_roles = apply_filters(
			'ulgm_rest_api_callback_roles',
			array(
				'administrator',
				'group_leader',
				'super_admin',
			)
		);
		if ( array_intersect( $allowed_roles, $user->roles ) ) {
			return true;
		}

		return new WP_Error( 'ulgm_rest_cannot_view', __( 'Sorry, you cannot view this resource.', 'uncanny-learndash-groups' ), array( 'status' => rest_authorization_required_code() ) );
	}

	/**
	 * Save email templates for user Send enrollment key, Add and invite email
	 * and Add group leader/Create group email
	 *
	 * @since 1.0.0
	 *
	 */
	public function save_general_settings( WP_REST_Request $request ) {

		// Actions permitted by the pi call (collected from input element with name action )
		$permitted_actions = array( 'save-general-settings' );

		// Was an action received, and is the actions allowed
		if ( ! $request->has_param( 'action' ) || ! in_array( $request->get_param( 'action' ), $permitted_actions ) ) {
			$data['message'] = __( 'Select an action.', 'uncanny-learndash-groups' );
			wp_send_json_error( $data );
		}

		// Does the current user have permission
		$permission = apply_filters( 'general_settings_update_permission', 'manage_options' );

		if ( ! current_user_can( $permission ) ) {
			$data['message'] = __( 'You do not have permission to save settings.', 'uncanny-learndash-groups' );
			wp_send_json_error( $data );
		}

		if ( $request->has_param( 'ulgm_term_condition' ) ) {
			update_option( 'ulgm_term_condition', wp_slash( $request->get_param( 'ulgm_term_condition' ) ) );
		}

		if ( $request->has_param( 'ulgm_group_management_page' ) ) {
			update_option( 'ulgm_group_management_page', sanitize_text_field( $request->get_param( 'ulgm_group_management_page' ) ) );
		}

		if ( $request->has_param( 'ulgm_group_buy_courses_page' ) ) {
			update_option( 'ulgm_group_buy_courses_page', sanitize_text_field( $request->get_param( 'ulgm_group_buy_courses_page' ) ) );
		}

		if ( $request->has_param( 'ulgm_group_report_page' ) ) {
			update_option( 'ulgm_group_report_page', sanitize_text_field( $request->get_param( 'ulgm_group_report_page' ) ) );
		}

		if ( $request->has_param( 'ulgm_group_assignment_report_page' ) ) {
			update_option( 'ulgm_group_assignment_report_page', sanitize_text_field( $request->get_param( 'ulgm_group_assignment_report_page' ) ) );
		}

		if ( $request->has_param( 'ulgm_group_quiz_report_page' ) ) {
			update_option( 'ulgm_group_quiz_report_page', sanitize_text_field( $request->get_param( 'ulgm_group_quiz_report_page' ) ) );
		}

		if ( $request->has_param( 'ulgm_group_manage_progress_page' ) ) {
			update_option( 'ulgm_group_manage_progress_page', sanitize_text_field( $request->get_param( 'ulgm_group_manage_progress_page' ) ) );
		}

		if ( $request->has_param( 'ulgm_group_essay_report_page' ) ) {
			update_option( 'ulgm_group_essay_report_page', sanitize_text_field( $request->get_param( 'ulgm_group_essay_report_page' ) ) );
		}

		if ( $request->has_param( 'ulgm_group_license_product_cat' ) ) {
			update_option( 'ulgm_group_license_product_cat', sanitize_text_field( $request->get_param( 'ulgm_group_license_product_cat' ) ) );
		}

		if ( $request->has_param( 'ulgm_group_license_tax_status' ) ) {
			update_option( 'ulgm_group_license_tax_status', sanitize_text_field( $request->get_param( 'ulgm_group_license_tax_status' ) ) );
		}

		if ( $request->has_param( 'ulgm_group_license_tax_class' ) ) {
			update_option( 'ulgm_group_license_tax_class', sanitize_text_field( $request->get_param( 'ulgm_group_license_tax_class' ) ) );
		}

		if ( $request->has_param( 'ulgm_main_color' ) ) {
			update_option( 'ulgm_main_color', sanitize_text_field( $request->get_param( 'ulgm_main_color' ) ) );
		}

		if ( $request->has_param( 'ulgm_font_color' ) ) {
			update_option( 'ulgm_font_color', sanitize_text_field( $request->get_param( 'ulgm_font_color' ) ) );
		}

		if ( $request->has_param( 'ulgm_add_to_cart_message' ) ) {
			update_option( 'ulgm_add_to_cart_message', sanitize_text_field( $request->get_param( 'ulgm_add_to_cart_message' ) ) );
		}

		if ( $request->has_param( 'ulgm_per_seat_text' ) ) {
			update_option( 'ulgm_per_seat_text', sanitize_text_field( $request->get_param( 'ulgm_per_seat_text' ) ) );
		}

		if ( $request->has_param( 'ulgm_per_seat_text_plural' ) ) {
			update_option( 'ulgm_per_seat_text_plural', sanitize_text_field( $request->get_param( 'ulgm_per_seat_text_plural' ) ) );
		}

		if ( $request->has_param( 'show_license_product_on_front' ) ) {
			update_option( 'show_license_product_on_front', sanitize_text_field( $request->get_param( 'show_license_product_on_front' ) ) );
		} else {
			delete_option( 'show_license_product_on_front' );
		}

		if ( $request->has_param( 'allow_to_remove_users_anytime' ) ) {
			update_option( 'allow_to_remove_users_anytime', sanitize_text_field( $request->get_param( 'allow_to_remove_users_anytime' ) ) );
		} else {
			delete_option( 'allow_to_remove_users_anytime' );
		}

		if ( $request->has_param( 'do_not_restore_seat_if_user_is_removed' ) ) {
			update_option( 'do_not_restore_seat_if_user_is_removed', sanitize_text_field( $request->get_param( 'do_not_restore_seat_if_user_is_removed' ) ) );
		} else {
			delete_option( 'do_not_restore_seat_if_user_is_removed' );
		}

		if ( $request->has_param( 'allow_group_leaders_to_manage_progress' ) ) {
			update_option( 'allow_group_leaders_to_manage_progress', sanitize_text_field( $request->get_param( 'allow_group_leaders_to_manage_progress' ) ) );
		} else {
			delete_option( 'allow_group_leaders_to_manage_progress' );
		}

		if ( $request->has_param( 'group_leaders_dont_use_seats' ) ) {
			update_option( 'group_leaders_dont_use_seats', sanitize_text_field( $request->get_param( 'group_leaders_dont_use_seats' ) ) );
		} else {
			delete_option( 'group_leaders_dont_use_seats' );
		}

		if ( $request->has_param( 'do_not_add_group_leader_as_member' ) ) {
			update_option( 'do_not_add_group_leader_as_member', sanitize_text_field( $request->get_param( 'do_not_add_group_leader_as_member' ) ) );
		} else {
			delete_option( 'do_not_add_group_leader_as_member' );
		}

		if ( $request->has_param( 'allow_group_leader_change_username' ) ) {
			update_option( 'allow_group_leader_change_username', sanitize_text_field( $request->get_param( 'allow_group_leader_change_username' ) ) );
		} else {
			delete_option( 'allow_group_leader_change_username' );
		}

		if ( $request->has_param( 'allow_group_leader_change_email' ) ) {
			update_option( 'allow_group_leader_change_email', sanitize_text_field( $request->get_param( 'allow_group_leader_change_email' ) ) );
		} else {
			delete_option( 'allow_group_leader_change_email' );
		}

		if ( $request->has_param( 'allow_group_leader_edit_users' ) ) {
			update_option( 'allow_group_leader_edit_users', sanitize_text_field( $request->get_param( 'allow_group_leader_edit_users' ) ) );
		} else {
			delete_option( 'allow_group_leader_edit_users' );
		}

		if ( $request->has_param( 'add_courses_as_part_of_license' ) ) {
			update_option( 'add_courses_as_part_of_license', sanitize_text_field( $request->get_param( 'add_courses_as_part_of_license' ) ) );
		} else {
			delete_option( 'add_courses_as_part_of_license' );
		}

		if ( $request->has_param( 'ld_hide_courses_users_column' ) ) {
			update_option( 'ld_hide_courses_users_column', sanitize_text_field( $request->get_param( 'ld_hide_courses_users_column' ) ) );
		} else {
			delete_option( 'ld_hide_courses_users_column' );
		}

		if ( $request->has_param( 'ld_hierarchy_settings_child_groups' ) ) {
			update_option( 'ld_hierarchy_settings_child_groups', sanitize_text_field( $request->get_param( 'ld_hierarchy_settings_child_groups' ) ) );
		} else {
			delete_option( 'ld_hierarchy_settings_child_groups' );
		}

		if ( $request->has_param( 'add_groups_as_woo_products' ) ) {
			update_option( 'add_groups_as_woo_products', sanitize_text_field( $request->get_param( 'add_groups_as_woo_products' ) ) );
		} else {
			delete_option( 'add_groups_as_woo_products' );
		}

		if ( $request->has_param( 'ulgm_complete_group_license_orders' ) ) {
			update_option( 'ulgm_complete_group_license_orders', sanitize_text_field( $request->get_param( 'ulgm_complete_group_license_orders' ) ) );
		} else {
			delete_option( 'ulgm_complete_group_license_orders' );
		}

		if ( $request->has_param( 'ulgm_reduce_seats_on_order_refund' ) ) {
			update_option( 'ulgm_reduce_seats_on_order_refund', sanitize_text_field( $request->get_param( 'ulgm_reduce_seats_on_order_refund' ) ) );
		} else {
			delete_option( 'ulgm_reduce_seats_on_order_refund' );
		}

		if ( $request->has_param( 'ulgm_trash_linked_group' ) ) {
			update_option( 'ulgm_trash_linked_group', sanitize_text_field( $request->get_param( 'ulgm_trash_linked_group' ) ) );
		} else {
			delete_option( 'ulgm_trash_linked_group' );
		}

		if ( $request->has_param( 'ulgm_hide_edit_group_name_fields' ) ) {
			update_option( 'ulgm_hide_edit_group_name_fields', sanitize_text_field( $request->get_param( 'ulgm_hide_edit_group_name_fields' ) ) );
		} else {
			update_option( 'ulgm_hide_edit_group_name_fields', 'no' );
		}

		if ( $request->has_param( 'ulgm_hide_group_name_fields_on_product_page' ) ) {
			update_option( 'ulgm_hide_group_name_fields_on_product_page', sanitize_text_field( $request->get_param( 'ulgm_hide_group_name_fields_on_product_page' ) ) );
		} else {
			update_option( 'ulgm_hide_group_name_fields_on_product_page', 'no' );
		}

		if ( $request->has_param( 'ulgm_hide_group_name_fields_on_cart_page' ) ) {
			update_option( 'ulgm_hide_group_name_fields_on_cart_page', sanitize_text_field( $request->get_param( 'ulgm_hide_group_name_fields_on_cart_page' ) ) );
		} else {
			update_option( 'ulgm_hide_group_name_fields_on_cart_page', 'no' );
		}

		if ( $request->has_param( 'use_progress_report_instead_course' ) ) {
			update_option( 'use_progress_report_instead_course', sanitize_text_field( $request->get_param( 'use_progress_report_instead_course' ) ) );
		} else {
			delete_option( 'use_progress_report_instead_course' );
		}

		if ( $request->has_param( 'show_basic_groups_in_frontend' ) ) {
			update_option( 'show_basic_groups_in_frontend', sanitize_text_field( $request->get_param( 'show_basic_groups_in_frontend' ) ) );
		} else {
			delete_option( 'show_basic_groups_in_frontend' );
		}

		if ( $request->has_param( 'use_legacy_course_progress' ) ) {
			update_option( 'use_legacy_course_progress', sanitize_text_field( $request->get_param( 'use_legacy_course_progress' ) ) );
		} else {
			delete_option( 'use_legacy_course_progress' );
		}

		// Allow Add seats for subscription based groups
		if ( $request->has_param( 'woo_subscription_allow_additional_seats' ) ) {
			update_option( 'woo_subscription_allow_additional_seats', sanitize_text_field( $request->get_param( 'woo_subscription_allow_additional_seats' ) ) );
		} else {
			delete_option( 'woo_subscription_allow_additional_seats' );
		}

		// Allow Add seats for subscription based groups
		if ( $request->has_param( 'woo_subscription_allow_additional_seats_learn_more_link' ) ) {
			update_option( 'woo_subscription_allow_additional_seats_learn_more_link', sanitize_text_field( $request->get_param( 'woo_subscription_allow_additional_seats_learn_more_link' ) ) );
		} else {
			delete_option( 'woo_subscription_allow_additional_seats_learn_more_link' );
		}

		if ( $request->has_param( 'ld_pool_seats_in_hierarchy' ) ) {
			update_option( 'ld_pool_seats_in_hierarchy', sanitize_text_field( $request->get_param( 'ld_pool_seats_in_hierarchy' ) ) );
		} else {
			delete_option( 'ld_pool_seats_in_hierarchy' );
		}

		if ( $request->has_param( 'ld_pool_seats_all_groups' ) ) {
			update_option( 'ld_pool_seats_all_groups', sanitize_text_field( $request->get_param( 'ld_pool_seats_all_groups' ) ) );
		} else {
			delete_option( 'ld_pool_seats_all_groups' );
		}

		$data['message'] = __( 'Settings have been saved.', 'uncanny-learndash-groups' );
		wp_send_json_success( $data );
	}

	/**
	 * Save email templates for user Send enrollment key, Add and invite email
	 * and Add group leader/Create group email
	 *
	 * @since 1.0.0
	 *
	 */
	public function save_email_templates( WP_REST_Request $request ) {

		// Actions permitted by the pi call (collected from input element with name action )
		$permitted_actions = array( 'save-email-templates' );

		// Was an action received, and is the actions allowed
		if ( ! $request->has_param( 'action' ) || ! in_array( $request->get_param( 'action' ), $permitted_actions ) ) {
			$data['message'] = __( 'Select an action.', 'uncanny-learndash-groups' );
			wp_send_json_error( $data );
		}

		// Does the current user have permission
		$permission = apply_filters( 'email_template_update_permission', 'manage_options' );

		if ( ! current_user_can( $permission ) ) {
			$data['message'] = __( 'You do not have permission to save settings.', 'uncanny-learndash-groups' );
			wp_send_json_error( $data );
		}

		if ( $request->has_param( 'ulgm_invitation_user_email_subject' ) ) {
			update_option( 'ulgm_invitation_user_email_subject', sanitize_text_field( $request->get_param( 'ulgm_invitation_user_email_subject' ) ), false );
		}

		if ( $request->has_param( 'ulgm_invitation_user_email_body' ) ) {
			update_option( 'ulgm_invitation_user_email_body', wp_slash( $request->get_param( 'ulgm_invitation_user_email_body' ) ), false );
		}

		if ( $request->has_param( 'ulgm_user_welcome_email_subject' ) ) {
			update_option( 'ulgm_user_welcome_email_subject', sanitize_text_field( $request->get_param( 'ulgm_user_welcome_email_subject' ) ), false );
		}

		if ( $request->has_param( 'ulgm_user_welcome_email_body' ) ) {
			update_option( 'ulgm_user_welcome_email_body', wp_slash( $request->get_param( 'ulgm_user_welcome_email_body' ) ), false );
		}

		if ( $request->has_param( 'ulgm_existing_user_welcome_email_subject' ) ) {
			update_option( 'ulgm_existing_user_welcome_email_subject', sanitize_text_field( $request->get_param( 'ulgm_existing_user_welcome_email_subject' ), false ) );
		}

		if ( $request->has_param( 'ulgm_existing_user_welcome_email_body' ) ) {
			update_option( 'ulgm_existing_user_welcome_email_body', wp_slash( $request->get_param( 'ulgm_existing_user_welcome_email_body' ) ), false );
		}

		if ( $request->has_param( 'ulgm_group_leader_welcome_email_subject' ) ) {
			update_option( 'ulgm_group_leader_welcome_email_subject', sanitize_text_field( $request->get_param( 'ulgm_group_leader_welcome_email_subject' ) ), false );
		}

		if ( $request->has_param( 'ulgm_group_leader_welcome_email_body' ) ) {
			update_option( 'ulgm_group_leader_welcome_email_body', wp_slash( $request->get_param( 'ulgm_group_leader_welcome_email_body' ) ), false );
		}

		if ( $request->has_param( 'ulgm_existing_group_leader_welcome_email_subject' ) ) {
			update_option( 'ulgm_existing_group_leader_welcome_email_subject', sanitize_text_field( $request->get_param( 'ulgm_existing_group_leader_welcome_email_subject' ) ), false );
		}

		if ( $request->has_param( 'ulgm_existing_group_leader_welcome_email_body' ) ) {
			update_option( 'ulgm_existing_group_leader_welcome_email_body', wp_slash( $request->get_param( 'ulgm_existing_group_leader_welcome_email_body' ) ), false );
		}

		if ( $request->has_param( 'ulgm_email_from' ) ) {
			update_option( 'ulgm_email_from', sanitize_email( $request->get_param( 'ulgm_email_from' ) ), false );
		}

		if ( $request->has_param( 'ulgm_name_from' ) ) {
			update_option( 'ulgm_name_from', sanitize_text_field( $request->get_param( 'ulgm_name_from' ) ), false );
		}

		if ( $request->has_param( 'ulgm_reply_to' ) ) {
			update_option( 'ulgm_reply_to', sanitize_email( $request->get_param( 'ulgm_reply_to' ) ), false );
		}

		//New group purchase subject
		if ( $request->has_param( 'ulgm_new_group_purchase_email_subject' ) ) {
			update_option( 'ulgm_new_group_purchase_email_subject', sanitize_text_field( $request->get_param( 'ulgm_new_group_purchase_email_subject' ) ), false );
		}

		//New group purchase send email body
		if ( $request->has_param( 'ulgm_new_group_purchase_email_body' ) ) {
			update_option( 'ulgm_new_group_purchase_email_body', wp_slash( $request->get_param( 'ulgm_new_group_purchase_email_body' ) ), false );
		}

		//New send email value
		if ( $request->has_param( 'ulgm_send_code_redemption_email' ) ) {
			update_option( 'ulgm_send_code_redemption_email', 'yes' );
		} else {
			update_option( 'ulgm_send_code_redemption_email', 'no' );
		}

		//New send email value
		if ( $request->has_param( 'ulgm_send_user_welcome_email' ) ) {
			update_option( 'ulgm_send_user_welcome_email', 'yes' );
		} else {
			update_option( 'ulgm_send_user_welcome_email', 'no' );
		}

		//New send email value
		if ( $request->has_param( 'ulgm_send_existing_user_welcome_email' ) ) {
			update_option( 'ulgm_send_existing_user_welcome_email', 'yes' );
		} else {
			update_option( 'ulgm_send_existing_user_welcome_email', 'no' );
		}

		//New send email value
		if ( $request->has_param( 'ulgm_send_group_leader_welcome_email' ) ) {
			update_option( 'ulgm_send_group_leader_welcome_email', 'yes' );
		} else {
			update_option( 'ulgm_send_group_leader_welcome_email', 'no' );
		}

		//New send email value
		if ( $request->has_param( 'ulgm_send_existing_group_leader_welcome_email' ) ) {
			update_option( 'ulgm_send_existing_group_leader_welcome_email', 'yes' );
		} else {
			update_option( 'ulgm_send_existing_group_leader_welcome_email', 'no' );
		}

		//New send email value
		if ( $request->has_param( 'ulgm_send_new_group_purchase_email' ) ) {
			update_option( 'ulgm_send_new_group_purchase_email', 'yes' );
		} else {
			update_option( 'ulgm_send_new_group_purchase_email', 'no' );
		}

		$data['message'] = __( 'Email settings have been saved.', 'uncanny-learndash-groups' );
		wp_send_json_success( $data );
	}

	/**
	 * Regenerate Pages - API call
	 *
	 *
	 */
	public function regenerate_pages( WP_REST_Request $request ) {

		// Actions permitted by the pi call (collected from input element with name action )
		$permitted_actions = array( 'save-general-settings' );

		// Was an action received, and is the actions allowed
		if ( ! $request->has_param( 'action' ) || ! in_array( $request->get_param( 'action' ), $permitted_actions ) ) {
			$data['message'] = __( 'Select an action.', 'uncanny-learndash-groups' );
			wp_send_json_error( $data );
		}

		// Does the current user have permission?
		$permission = apply_filters( 'general_settings_update_permission', 'manage_options' );

		if ( ! current_user_can( $permission ) ) {
			$data['message'] = __( 'You do not have permission to save settings.', 'uncanny-learndash-groups' );
			wp_send_json_error( $data );
		}

		$count   = $this->regenerate_groups_pages();
		$message = esc_html__( 'Nothing to regenerate.', 'uncanny-learndash-groups' );
		if ( $count > 0 ) {
			$message = sprintf( esc_html__( '%s pages were generated successfully.', 'uncanny-learndash-groups' ), $count );
		}

		$data['message'] = $message;
		wp_send_json_success( $data );
	}

	/**
	 *
	 */
	private function regenerate_groups_pages( $force = false ) {
		$user_id = get_current_user_id();

		$regenerate_pages_count = 0;

		$page_id_exists = get_option( 'ulgm_group_management_page', '' );
		if ( empty( $page_id_exists ) ) {
			$create_group_page  = array(
				'post_type'    => 'page',
				'post_title'   => _x( 'Group Management', 'group page post_title', 'uncanny-learndash-groups' ),
				'post_content' => '[uo_groups]',
				'post_status'  => 'publish',
				'post_author'  => $user_id,
				'post_name'    => 'group-management',
			);
			$management_page_id = wp_insert_post( $create_group_page );
			update_option( 'ulgm_group_management_page', $management_page_id );
			++$regenerate_pages_count;
		}

		$course_label  = class_exists( '\LearnDash_Custom_Label' ) ? \LearnDash_Custom_Label::get_label( 'course' ) : __( 'Course', 'uncanny-learndash-groups' );
		$courses_label = class_exists( '\LearnDash_Custom_Label' ) ? \LearnDash_Custom_Label::get_label( 'courses' ) : __( 'Courses', 'uncanny-learndash-groups' );
		$quiz_label    = class_exists( '\LearnDash_Custom_Label' ) ? \LearnDash_Custom_Label::get_label( 'quiz' ) : __( 'Quiz', 'uncanny-learndash-groups' );

		//
		$page_id_exists = get_option( 'ulgm_group_report_page', '' );
		if ( empty( $page_id_exists ) ) {
			$create_report_page = array(
				'post_type'    => 'page',
				'post_title'   => sprintf( _x( 'Group %s Report', 'group course report post_title', 'uncanny-learndash-groups' ), $course_label ),
				'post_content' => '[uo_groups_course_report]',
				'post_status'  => 'publish',
				'post_author'  => $user_id,
				'post_name'    => 'group-management-report',
			);
			$report_page_id     = wp_insert_post( $create_report_page );
			update_option( 'ulgm_group_report_page', $report_page_id );
			++$regenerate_pages_count;
		}

		//
		$page_id_exists = get_option( 'ulgm_group_quiz_report_page', '' );
		if ( empty( $page_id_exists ) ) {
			$create_quiz_report_page = array(
				'post_type'    => 'page',
				'post_title'   => sprintf( _x( 'Group %s Report', 'group quiz report post_title', 'uncanny-learndash-groups' ), $quiz_label ),
				'post_content' => '[uo_groups_quiz_report]',
				'post_status'  => 'publish',
				'post_author'  => $user_id,
				'post_name'    => 'group-quiz-report',
			);
			$quiz_report_page_id     = wp_insert_post( $create_quiz_report_page );
			update_option( 'ulgm_group_quiz_report_page', $quiz_report_page_id );
			++$regenerate_pages_count;
		}

		//
		$page_id_exists = get_option( 'ulgm_group_assignment_report_page', '' );
		if ( empty( $page_id_exists ) ) {
			$create_assignment_report_page = array(
				'post_type'    => 'page',
				'post_title'   => _x( 'Group Assignment Report', 'group assigment report post_title', 'uncanny-learndash-groups' ),
				'post_content' => '[uo_groups_assignments]',
				'post_status'  => 'publish',
				'post_author'  => $user_id,
				'post_name'    => 'assignment-management-page',
			);
			$assignment_report_page_id     = wp_insert_post( $create_assignment_report_page );
			update_option( 'ulgm_group_assignment_report_page', $assignment_report_page_id );
			++$regenerate_pages_count;
		}

		//
		$page_id_exists = get_option( 'ulgm_group_essay_report_page', '' );
		if ( empty( $page_id_exists ) ) {
			$create_essay_report_page = array(
				'post_type'    => 'page',
				'post_title'   => _x( 'Group Essay Report', 'group essay report post_title', 'uncanny-learndash-groups' ),
				'post_content' => '[uo_groups_essays]',
				'post_status'  => 'publish',
				'post_author'  => $user_id,
				'post_name'    => 'essay-management-page',
			);
			$essay_report_page_id     = wp_insert_post( $create_essay_report_page );
			update_option( 'ulgm_group_essay_report_page', $essay_report_page_id );
			++$regenerate_pages_count;
		}

		//
		$page_id_exists = get_option( 'ulgm_group_manage_progress_page', '' );
		if ( empty( $page_id_exists ) ) {
			$create_progress_report_page = array(
				'post_type'    => 'page',
				'post_title'   => _x( 'Group Progress Report', 'group progress report post_title', 'uncanny-learndash-groups' ),
				'post_content' => '[uo_groups_manage_progress]',
				'post_status'  => 'publish',
				'post_author'  => $user_id,
				'post_name'    => 'group-progress-report',
			);
			$progress_report_page_id     = wp_insert_post( $create_progress_report_page );
			update_option( 'ulgm_group_manage_progress_page', $progress_report_page_id );
			++$regenerate_pages_count;
		}

		//
		$page_id_exists = get_option( 'ulgm_group_buy_courses_page', '' );
		if ( empty( $page_id_exists ) ) {
			$create_a_la_carte_license_page = array(
				'post_type'    => 'page',
				'post_title'   => sprintf( _x( 'Group Management Buy %s', 'group buy course report post_title', 'uncanny-learndash-groups' ), $courses_label ),
				'post_content' => '[uo_groups_buy_courses]',
				'post_status'  => 'publish',
				'post_author'  => $user_id,
				'post_name'    => 'group-management-buy-courses',
			);
			$buy_courses_id                 = wp_insert_post( $create_a_la_carte_license_page );
			update_option( 'ulgm_group_buy_courses_page', $buy_courses_id );
			++$regenerate_pages_count;
		}

		return $regenerate_pages_count;
	}
}
