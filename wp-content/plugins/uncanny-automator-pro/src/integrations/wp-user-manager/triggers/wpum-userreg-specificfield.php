<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WPUM_USERREG_SPECIFICFIELD
 *
 * @package Uncanny_Automator_Pro
 */
class WPUM_USERREG_SPECIFICFIELD {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WPUSERMANAGER';

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
		$this->trigger_code = 'WPUMUSERREGISTERS';
		$this->trigger_meta = 'WPUMFIELDVALUE';
		if ( class_exists( 'WPUM_Registration_Forms' ) ) {
			$this->define_trigger();
		}
	}

	/**
	 * Define and register the trigger by pushing it into the Automator object
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/wp-user-manager/' ),
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			'is_pro'              => true,
			/* translators: Logged-in trigger - WP User Manager */
			'sentence'            => sprintf( __( 'A user registers using {{a form:%1$s}} with {{a specific value:%2$s}} in {{a specific field:%3$s}}', 'uncanny-automator-pro' ), 'WPUMFORMS', $this->trigger_meta . ':WPUMFORMS', 'WPUMSPECIFIEDFIELD' . ':WPUMFORMS' ),
			/* translators: Logged-in trigger - WP User Manager */
			'select_option_name'  => __( 'A user registers using {{a form}} with {{a specific value}} in {{a specific field}}', 'uncanny-automator-pro' ),
			'action'              => 'wpum_after_registration',
			'priority'            => 99,
			'accepted_args'       => 3,
			'validation_function' => array( $this, 'wpum_register_user' ),
			'options_callback'    => array( $this, 'load_options' ),
		);

		Automator()->register->trigger( $trigger );
	}

	/**
	 * @return array[]
	 */
	public function load_options() {
		return Automator()->utilities->keep_order_of_options(
			array(
				'options'       => array(),
				'options_group' => array(
					'WPUMFORMS' => array(
						/* translators: Noun */
						Automator()->helpers->recipe->wp_user_manager->pro->get_all_forms(
							__( 'Form', 'uncanny-automator-pro' ),
							'WPUMFORMS',
							array(
								'token'        => false,
								'is_ajax'      => true,
								'target_field' => 'WPUMSPECIFIEDFIELD',
								'endpoint'     => 'select_form_fields_WPUMRF',
							)
						),
						Automator()->helpers->recipe->field->select_field(
							'WPUMSPECIFIEDFIELD',
							__( 'Field', 'uncanny-automator-pro' )
						),
						Automator()->helpers->recipe->field->text_field(
							$this->trigger_meta,
							__( 'Field value', 'uncanny-automator-pro' )
						),
					),
				),
			)
		);
	}

	/**
	 * @param $new_user_id
	 * @param $values
	 * @param $form
	 */
	public function wpum_register_user( $new_user_id, $values, $form ) {

		if ( 0 === absint( $new_user_id ) ) {
			// Its a logged in recipe and
			// user ID is 0. Skip process
			return;
		}

		$recipes            = Automator()->get->recipes_from_trigger_code( $this->trigger_code );
		$required_value     = Automator()->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$required_field     = Automator()->get->meta_from_recipes( $recipes, 'WPUMSPECIFIEDFIELD' );
		$required_form      = Automator()->get->meta_from_recipes( $recipes, 'WPUMFORMS' );
		$matched_recipe_ids = array();

		$matched_field = '';

		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];
				if ( $form->id == $required_form[ $recipe_id ][ $trigger_id ] &&
					 $required_value[ $recipe_id ][ $trigger_id ] == $values['register'][ $required_field[ $recipe_id ][ $trigger_id ] ]
				) {
					$matched_field        = $required_field[ $recipe_id ][ $trigger_id ];
					$matched_recipe_ids[] = array(
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					);
				}
			}
		}

		if ( ! empty( $matched_recipe_ids ) ) {
			foreach ( $matched_recipe_ids as $matched_recipe_id ) {
				$pass_args = array(
					'code'             => $this->trigger_code,
					'meta'             => $this->trigger_meta,
					'user_id'          => $new_user_id,
					'recipe_to_match'  => $matched_recipe_id['recipe_id'],
					'trigger_to_match' => $matched_recipe_id['trigger_id'],
					'ignore_post_id'   => true,
					'is_signed_in'     => true,
				);

				$args = Automator()->maybe_add_trigger_entry( $pass_args, false );

				if ( $args ) {
					foreach ( $args as $result ) {
						if ( true === $result['result'] ) {
							$trigger_meta = array(
								'user_id'        => $new_user_id,
								'trigger_id'     => $result['args']['trigger_id'],
								'trigger_log_id' => $result['args']['trigger_log_id'],
								'run_number'     => $result['args']['run_number'],
							);

							$trigger_meta['meta_key']   = $this->trigger_meta;
							$trigger_meta['meta_value'] = maybe_serialize( $values['register'][ $matched_field ] );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'WPUMFORMS';
							$trigger_meta['meta_value'] = maybe_serialize( $form->name );
							Automator()->insert_trigger_meta( $trigger_meta );

							$trigger_meta['meta_key']   = 'WPUMSPECIFIEDFIELD';
							$trigger_meta['meta_value'] = maybe_serialize( $matched_field );
							Automator()->insert_trigger_meta( $trigger_meta );

							foreach ( $values['register'] as $key => $value ) {
								$trigger_meta['meta_key']   = $key;
								$trigger_meta['meta_value'] = maybe_serialize( $value );
								Automator()->insert_trigger_meta( $trigger_meta );
							}

							Automator()->maybe_trigger_complete( $result['args'] );
						}
					}
				}
			}
		}
	}

}
