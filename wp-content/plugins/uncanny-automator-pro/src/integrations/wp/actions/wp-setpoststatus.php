<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WP_SETPOSTSTATUS
 *
 * @package Uncanny_Automator_Pro
 */
class WP_SETPOSTSTATUS {

	use Recipe\Action_Tokens;

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WP';

	/**
	 * @var string
	 */
	private $action_code;

	/**
	 * @var string
	 */
	private $action_meta;

	/**
	 * @var string
	 */
	private $status_meta = 'SETSPECIFICSTATUS';

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {
		$this->action_code = 'SETPOSTSTATUS';
		$this->action_meta = 'WPSETPOSTSTATUS';
		if ( Automator()->helpers->recipe->is_edit_page() ) {
			add_action(
				'wp_loaded',
				function () {
					$this->define_action();
				},
				99
			);

			return;
		}
		$this->define_action();
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		$action = array(
			'author'             => Automator()->get_author_name( $this->action_code ),
			'support_link'       => Automator()->get_author_support_link( $this->action_code, 'integration/wordpress-core/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			'requires_user'      => false,
			/* translators: Action - WordPress Core */
			'sentence'           => sprintf( __( 'Set {{a post:%1$s}} to {{a status:%2$s}}', 'uncanny-automator-pro' ), $this->action_meta, $this->status_meta ),
			/* translators: Action - WordPress Core */
			'select_option_name' => __( 'Set {{a post}} to {{a status}}', 'uncanny-automator-pro' ),
			'priority'           => 11,
			'accepted_args'      => 3,
			'execution_function' => array( $this, 'set_post_status' ),
			'options_callback'   => array( $this, 'load_options' ),
		);

		Automator()->register->action( $action );

		$this->set_action_tokens( $this->get_action_token_config(), $this->action_code );
	}

	/**
	 * load_options
	 *
	 * @return array
	 */
	public function load_options() {
		$options = Automator()->utilities->keep_order_of_options(
			array(
				'options_group' => array(
					$this->action_meta => array(
						Automator()->helpers->recipe->wp->options->pro->all_wp_post_types(
							__( 'Post type', 'uncanny-automator-pro' ),
							'WPSPOSTTYPES',
							array(
								'token'                   => false,
								'is_ajax'                 => true,
								'target_field'            => $this->action_meta,
								'is_any'                  => false,
								'endpoint'                => 'select_all_post_of_selected_post_type',
								'include_relevant_tokens' => false,
							)
						),
						Automator()->helpers->recipe->field->select_field( $this->action_meta, __( 'Post', 'uncanny-automator-pro' ) ),
					),
					$this->status_meta => array(
						Automator()->helpers->recipe->wp->options->pro->wp_post_statuses( null, $this->status_meta, array( 'include_relevant_tokens' => false ) ),
					),
				),
			)
		);

		return $options;
	}


	/**
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 * @param $args
	 */
	public function set_post_status( $user_id, $action_data, $recipe_id, $args ) {

		$post_type   = Automator()->parse->text( $action_data['meta']['WPSPOSTTYPES'], $recipe_id, $user_id, $args );
		$post_id     = Automator()->parse->text( $action_data['meta'][ $this->action_meta ], $recipe_id, $user_id, $args );
		$post_status = Automator()->parse->text( $action_data['meta'][ $this->status_meta ], $recipe_id, $user_id, $args );
		if ( intval( '-1' ) === intval( $post_id ) ) {
			global $wpdb;
			$post_ids = $wpdb->get_row( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_status = %s AND post_type = %s", $post_status, $post_type ) );
			if ( $post_ids ) {
				foreach ( $post_ids as $post_id ) {
					wp_update_post(
						array(
							'ID'          => $post_id,
							'post_status' => $post_status,
						)
					);
					if ( 'publish' === $post_status ) {
						wp_publish_post( $post_id );
					}
					clean_post_cache( $post_id );
				}
			}
		} else {
			wp_update_post(
				array(
					'ID'          => $post_id,
					'post_status' => $post_status,
				)
			);
			if ( 'publish' === $post_status ) {
				wp_publish_post( $post_id );
			}
			clean_post_cache( $post_id );
		}

		// Hydrate action tokens.
		$tokens                       = Automator()->helpers->recipe->wp->options->pro->hydrate_post_relevant_tokens( $this->action_meta, $post_id );
		$tokens[ $this->status_meta ] = $post_status;
		$this->hydrate_tokens( $tokens );

		Automator()->complete_action( $user_id, $action_data, $recipe_id );
	}

	/**
	 * Set up tokens for this action.
	 *
	 * @return array
	 */
	private function get_action_token_config() {
		$tokens                       = Automator()->helpers->recipe->wp->options->pro->get_post_relevant_tokens( 'action', $this->action_meta );
		$tokens[ $this->status_meta ] = array(
			'name' => esc_attr_x( 'Status', 'WordPress Token', 'uncanny-automator-pro' ),
			'type' => 'text',
		);
		return $tokens;
	}

}
