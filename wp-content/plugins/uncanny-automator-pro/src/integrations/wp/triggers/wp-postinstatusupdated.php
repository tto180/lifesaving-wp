<?php

namespace Uncanny_Automator_Pro;

use Uncanny_Automator\Wp_Helpers;

/**
 * Class WP_POSTINSTATUSUPDATED
 *
 * @package Uncanny_Automator_Pro
 */
class WP_POSTINSTATUSUPDATED {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WP';

	/**
	 * @var string
	 */
	private $trigger_code;
	/**
	 * @var string
	 */
	private $trigger_meta;

	/**
	 * Set up Automator trigger constructor.
	 */
	public function __construct() {
		$this->trigger_code = 'WPPOSTINSTATUS';
		$this->trigger_meta = 'POSTSTATUSUPDATED';
		if ( Automator()->helpers->recipe->is_edit_page() ) {
			add_action(
				'wp_loaded',
				function () {
					$this->define_trigger();
				},
				99
			);

			return;
		}
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {
		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'meta'                => $this->trigger_meta,
			/* translators: Logged-in trigger - WordPress Core */
			'sentence'            => sprintf( __( 'A user updates a post in {{a specific:%1$s}} status', 'uncanny-automator-pro' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - WordPress Core */
			'select_option_name'  => __( 'A user updates a post in {{a specific}} status', 'uncanny-automator-pro' ),
			'action'              => 'post_updated',
			'priority'            => 10,
			'accepted_args'       => 3,
			'validation_function' => array( $this, 'wp_post_updated' ),
			'options_callback'    => array( $this, 'load_options' ),
		);

		if ( method_exists( '\Uncanny_Automator\Wp_Helpers', 'common_trigger_loopable_tokens' ) ) {
			$trigger['loopable_tokens'] = Wp_Helpers::common_trigger_loopable_tokens();
		}

		Automator()->register->trigger( $trigger );
	}

	/**
	 * @return \array[][]
	 */
	public function load_options() {
		$relevant_tokens = array(
			'POSTSTATUSUPDATED' => __( 'Status', 'uncanny-automator-pro' ),
		);

		return Automator()->utilities->keep_order_of_options(
			array(
				'options_group' => array(
					$this->trigger_meta => array(
						Automator()->helpers->recipe->wp->options->pro->all_wp_post_types(
							__( 'Post type', 'uncanny-automator-pro' ),
							'WPPOSTTYPES',
							array(
								'token'           => true,
								'is_ajax'         => false,
								'relevant_tokens' => array(),
							)
						),
						Automator()->helpers->recipe->wp->options->pro->wp_post_statuses(
							__( 'Status', 'uncanny-automator-pro' ),
							$this->trigger_meta,
							array(
								'is_any'          => true,
								'any_label'       => __( 'Any', 'uncanny-automator-pro' ),
								'relevant_tokens' => $relevant_tokens,
							)
						),
					),
				),
			)
		);
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $post_ID
	 * @param $post_after
	 * @param $post_before
	 */

	public function wp_post_updated( $post_ID, $post_after, $post_before ) {
		$recipes              = Automator()->get->recipes_from_trigger_code( $this->trigger_code );
		$required_post_type   = Automator()->get->meta_from_recipes( $recipes, 'WPPOSTTYPES' );
		$required_post_status = Automator()->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$post                 = $post_before;
		$matched_recipe_ids   = array();

		$user_obj = get_user_by( 'ID', (int) $post_before->post_author );

		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id              = $trigger['ID'];
				$required_post_type_data = isset( $required_post_type[ $recipe_id ] ) ? $required_post_type[ $recipe_id ] : array();
				$required_post_type_data = isset( $required_post_type_data[ $trigger_id ] ) ? $required_post_type_data[ $trigger_id ] : false;

				// is post type
				if (
					'-1' === $required_post_type_data // any post type
					|| $post->post_type === $required_post_type_data // specific post type
					|| empty( $required_post_type_data ) // Backwards compatibility -- the trigger didnt have a post type selection pre 2.10
				) {

					$required_post_status_data = isset( $required_post_status[ $recipe_id ] ) ? $required_post_status[ $recipe_id ] : array();
					$required_post_status_data = isset( $required_post_status_data[ $trigger_id ] ) ? $required_post_status_data[ $trigger_id ] : false;

					if ( ! empty( $required_post_status_data ) ) {
						// is post status
						if ( intval( '-1' ) === intval( $required_post_status_data ) || $required_post_status_data === $post_before->post_status ) {
							$matched_recipe_ids[ $recipe_id ] = array(
								'recipe_id'  => $recipe_id,
								'trigger_id' => $trigger_id,
							);
						}
					}
				}
			}
		}

		if ( empty( $matched_recipe_ids ) ) {
			return;
		}
		foreach ( $matched_recipe_ids as $matched_recipe_id ) {
			$pass_args = array(
				'code'             => $this->trigger_code,
				'meta'             => $this->trigger_meta,
				'user_id'          => $user_obj->ID,
				'recipe_to_match'  => $matched_recipe_id['recipe_id'],
				'trigger_to_match' => $matched_recipe_id['trigger_id'],
				'ignore_post_id'   => true,
			);

			$args = Automator()->maybe_add_trigger_entry( $pass_args, false );

			if ( $args ) {
				foreach ( $args as $result ) {
					if ( true === $result['result'] ) {

						$trigger_meta = array(
							'user_id'        => $user_obj->ID,
							'trigger_id'     => $result['args']['trigger_id'],
							'trigger_log_id' => $result['args']['trigger_log_id'],
							'run_number'     => $result['args']['run_number'],
						);

						do_action( 'automator_loopable_token_hydrate', $result['args'], func_get_args() );

						// post_id Token
						Automator()->db->token->save( 'post_id', $post_after->ID, $trigger_meta );

						Automator()->maybe_trigger_complete( $result['args'] );
					}
				}
			}
		}
	}
}
