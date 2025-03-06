<?php

namespace Uncanny_Automator_Pro;

use WP_Error;

/**
 * Class BDB_GROUPACTIVITYSTREAM
 *
 * @package Uncanny_Automator_Pro
 */
class BDB_GROUPACTIVITYSTREAM {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'BDB';

	/**
	 * @var string
	 */
	private $action_code;
	/**
	 * @var string
	 */
	private $action_meta;

	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		$this->action_code = 'BDBGROUPACTIVITYSTREAM';
		$this->action_meta = 'BDBGROUPS';

		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object.
	 */
	public function define_action() {

		$action = array(
			'author'             => Automator()->get_author_name(),
			'support_link'       => Automator()->get_author_support_link( $this->action_code, 'integration/buddyboss/' ),
			'is_pro'             => true,
			'requires_user'      => false,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			/* translators: Action - BuddyBoss */
			'sentence'           => sprintf( esc_attr__( 'Add a post to the activity stream of {{a group:%1$s}}', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - BuddyBoss */
			'select_option_name' => esc_attr__( 'Add a post to the activity stream of {{a group}}', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array( $this, 'add_post_stream' ),
			'options_callback'   => array( $this, 'load_options' ),
		);

		Automator()->register->action( $action );
	}

	/**
	 * @return array[]
	 */
	public function load_options() {

		$groups_argu = array(
			'uo_include_any' => true,
			'uo_any_label'   => esc_attr__( 'All groups', 'uncanny-automator' ),
			'status'         => array( 'public', 'private', 'hidden' ),
		);

		return Automator()->utilities->keep_order_of_options(
			array(
				'options_group' => array(
					$this->action_meta => array(
						Automator()->helpers->recipe->buddyboss->all_buddyboss_groups( null, $this->action_meta, $groups_argu ),
						Automator()->helpers->recipe->buddyboss->pro->all_buddyboss_actions(
							__( 'Activity action', 'uncanny-automator-pro' ),
							'BDBACTION'
						),
						Automator()->helpers->recipe->buddyboss->all_buddyboss_users(
							__( 'Author', 'uncanny-automator-pro' ),
							'BDBAUTHOR',
							array(
								'uo_include_any' => true,
								'uo_any_label'   => esc_attr__( 'User that completes the triggers', 'uncanny-automator-pro' ),
							)
						),
						Automator()->helpers->recipe->field->text_field( 'BDBACTIONLINK', esc_attr__( 'Activity action link', 'uncanny-automator-pro' ), true, 'url', '', false, esc_attr__( 'Activity generated by posts and comments uses the link field for a permalink back to the content item.', 'uncanny-automator-pro' ) ),
						Automator()->helpers->recipe->field->text_field( 'BDBCONTENT', esc_attr__( 'Activity content', 'uncanny-automator-pro' ), true, 'textarea' ),
						Automator()->helpers->recipe->field->text(
							array(
								'option_code' => 'BDB_IMAGE_PREVIEW_ENABLE',
								'label'       => __( 'Generate a preview image from the first URL included in the message', 'uncanny-automator-pro' ),
								'input_type'  => 'checkbox',
								'is_toggle'   => true,
								'required'    => false,
							)
						),
					),
				),
			)
		);
	}


	/**
	 * Remove from BP Groups
	 *
	 * @param string $user_id
	 * @param array  $action_data
	 * @param string $recipe_id
	 *
	 * @return void
	 * @since 1.1
	 */
	public function add_post_stream( $user_id, $action_data, $recipe_id, $args ) {

		$group_id         = $action_data['meta']['BDBGROUPS'];
		$action           = Automator()->parse->text( $action_data['meta']['BDBACTION'], $recipe_id, $user_id, $args );
		$action           = do_shortcode( $action );
		$action_link      = Automator()->parse->text( $action_data['meta']['BDBACTIONLINK'], $recipe_id, $user_id, $args );
		$action_link      = do_shortcode( $action_link );
		$action_content   = $action_data['meta']['BDBCONTENT'];
		$action_content   = Automator()->parse->text( $action_content, $recipe_id, $user_id, $args );
		$action_content   = do_shortcode( $action_content );
		$action_author    = $user_id;
		$generate_preview = (string) $action_data['meta']['BDB_IMAGE_PREVIEW_ENABLE'];

		if ( isset( $action_data['meta']['BDBAUTHOR'] ) ) {
			$action_author = Automator()->parse->text( $action_data['meta']['BDBAUTHOR'], $recipe_id, $user_id, $args );

			if ( $action_author == '-1' ) {
				$action_author = $user_id;
			}
		}

		if ( empty( $group_id ) ) {
			return false;
		}
		$activity     = false;
		$activity_ids = array();
		if ( '-1' === $group_id ) {
			global $wpdb;
			$statuses   = array( 'public', 'private', 'hidden' );
			$in_str_arr = array_fill( 0, count( $statuses ), '%s' );
			$in_str     = join( ',', $in_str_arr );
			$group_qry  = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}bp_groups WHERE status IN ($in_str)", $statuses );
			$results    = $wpdb->get_results( $group_qry );
			if ( $results ) {
				foreach ( $results as $result ) {
					$hide_sitewide = false;
					if ( in_array(
						$result->status,
						array(
							'private',
							'hidden',
						),
						true
					) ) {
						$hide_sitewide = true;
					}
					$activity = bp_activity_add(
						array(
							'action'        => $action,
							'content'       => $action_content,
							'primary_link'  => $action_link,
							'component'     => 'groups',
							'item_id'       => $result->id,
							'type'          => 'activity_update',
							'user_id'       => $action_author,
							'hide_sitewide' => $hide_sitewide,
							'error_type'    => 'wp_error',
						)
					);
					if ( is_wp_error( $activity ) ) {
						break;
					}
					if ( ! $activity ) {
						break;
					}
					$activity_ids[] = $activity;
				}
			}
		} else {
			global $wpdb;
			$group_qry = "SELECT * FROM {$wpdb->prefix}bp_groups WHERE id = {$group_id}";
			$results   = $wpdb->get_results( $group_qry );
			if ( $results ) {
				foreach ( $results as $result ) {
					$hide_sitewide = false;
					if ( in_array(
						$result->status,
						array(
							'private',
							'hidden',
						),
						true
					) ) {
						$hide_sitewide = true;
					}
					$activity = bp_activity_add(
						array(
							'action'        => $action,
							'content'       => $action_content,
							'primary_link'  => $action_link,
							'component'     => 'groups',
							'item_id'       => $result->id,
							'type'          => 'activity_update',
							'user_id'       => $action_author,
							'hide_sitewide' => $hide_sitewide,
							'error_type'    => 'wp_error',
						)
					);
					if ( is_wp_error( $activity ) ) {
						break;
					}
					if ( ! $activity ) {
						break;
					}
					$activity_ids[] = $activity;
				}
			}
		}

		if ( ! $activity ) {
			$activity = new WP_Error( 'bp_activity_add', __( 'There was an error posting to the activity stream.', 'uncanny-automator-pro' ) );
		}

		if ( is_wp_error( $activity ) ) {
			$action_data['complete_with_errors'] = true;
			$action_data['do-nothing']           = true;
			Automator()->complete_action( $user_id, $action_data, $recipe_id, $activity->get_error_message() );

			return;
		}

		// Check if action was triggered.
		if ( empty( did_action( 'bp_groups_posted_update' ) ) && ! empty( $activity_ids ) ) {
			foreach ( $activity_ids as $activity_id ) {
				do_action( 'bp_groups_posted_update', $action_content, $action_author, $group_id, $activity_id );

				// Link preview data
				if ( true === bp_is_activity_link_preview_active() && 'true' === $generate_preview ) {
					Automator()->helpers->recipe->buddyboss->pro->generate_preview( $action_content, $activity_id );
				}
			}
		}

		Automator()->complete_action( $user_id, $action_data, $recipe_id );
	}

}
