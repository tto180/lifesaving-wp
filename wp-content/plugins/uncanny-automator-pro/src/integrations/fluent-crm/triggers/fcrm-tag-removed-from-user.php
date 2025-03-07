<?php

namespace Uncanny_Automator_Pro;

/**
 * Class FCRM_TAG_REMOVED_FROM_USER
 *
 * @package Uncanny_Automator_Pro
 */
class FCRM_TAG_REMOVED_FROM_USER {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'FCRM';

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
		$this->trigger_code = 'FCRMREMOVEUSERTAG';
		$this->trigger_meta = 'FCRMTAG';
		$this->define_trigger();
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {
		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/fluentcrm/' ),
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'is_pro'              => true,
			/* translators: Logged-in trigger - Fluent CRM */
			'sentence'            => sprintf( esc_attr_x( '{{A tag:%1$s}} is removed from a user', 'Fluent CRM', 'uncanny-automator' ), $this->trigger_meta ),
			/* translators: Logged-in trigger - Fluent CRM */
			'select_option_name'  => esc_attr_x( '{{A tag}} is removed from a user', 'Fluent CRM', 'uncanny-automator' ),
			'action'              => 'fluentcrm_contact_removed_from_tags',
			'priority'            => 20,
			'accepted_args'       => 2,
			'validation_function' => array( $this, 'contact_removed_from_tags' ),
			'options_callback'    => array( $this, 'load_options' ),
		);

		Automator()->register->trigger( $trigger );

	}

	/**
	 * Method load options.
	 *
	 * @return array The collection of fields.
	 */
	public function load_options() {

		return Automator()->utilities->keep_order_of_options(
			array(
				'options' => array(
					Automator()->helpers->recipe->fluent_crm->options->fluent_crm_tags(),
				),
			)
		);

	}

	/**
	 * @param $detached_tag_ids
	 * @param $subscriber
	 *
	 * @return void
	 */
	public function contact_removed_from_tags( $detached_tag_ids, $subscriber ) {
		$user_id = $subscriber->user_id;

		if ( 0 === $user_id ) {
			// There is no wp user associated with the subscriber
			return;
		}

		$tag_ids = Automator()
			->helpers
			->recipe
			->fluent_crm
			->get_attached_tag_ids( $detached_tag_ids );

		if ( empty( $tag_ids ) ) {
			// sanity check
			return;
		}

		$matched_recipes = Automator()
			->helpers
			->recipe
			->fluent_crm
			->match_single_condition( $tag_ids, 'int', $this->trigger_meta, $this->trigger_code );

		if ( ! empty( $matched_recipes ) ) {
			foreach ( $matched_recipes as $matched_recipe ) {
				if ( ! Automator()->is_recipe_completed( $matched_recipe->recipe_id, $user_id ) ) {

					$args = array(
						'code'             => $this->trigger_code,
						'meta'             => $this->trigger_meta,
						'recipe_to_match'  => $matched_recipe->recipe_id,
						'trigger_to_match' => $matched_recipe->trigger_id,
						'ignore_post_id'   => true,
						'user_id'          => $user_id,
					);

					$result = Automator()->maybe_add_trigger_entry( $args, false );

					if ( $result ) {
						foreach ( $result as $r ) {
							if ( true === $r['result'] ) {
								if ( isset( $r['args'] ) && isset( $r['args']['trigger_log_id'] ) ) {

									$insert = array(
										'user_id'        => $user_id,
										'trigger_id'     => (int) $r['args']['trigger_id'],
										'trigger_log_id' => $r['args']['trigger_log_id'],
										'meta_key'       => $this->trigger_meta,
										'meta_value'     => maybe_serialize( $matched_recipe->matched_value ),
										'run_number'     => $r['args']['run_number'],
									);

									Automator()->insert_trigger_meta( $insert );

									$insert = array(
										'user_id'        => $user_id,
										'trigger_id'     => (int) $r['args']['trigger_id'],
										'trigger_log_id' => $r['args']['trigger_log_id'],
										'meta_key'       => 'subscriber_id',
										'meta_value'     => $subscriber->id,
										'run_number'     => $r['args']['run_number'],
									);

									Automator()->insert_trigger_meta( $insert );
								}

								Automator()->maybe_trigger_complete( $r['args'] );
							}
						}
					}
				}
			}
		}
	}
}
