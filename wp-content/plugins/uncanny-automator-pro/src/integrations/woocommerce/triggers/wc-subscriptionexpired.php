<?php

namespace Uncanny_Automator_Pro;

/**
 * Class WS_SUBSCRIPTION_EXPIRED
 *
 * @package Uncanny_Automator_Pro
 */
class WC_SUBSCRIPTIONEXPIRED {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WC';

	/**
	 * @var string
	 */
	private $trigger_code;
	/**
	 * @var string
	 */
	private $trigger_meta;


	/**
	 * SetAutomatorTriggers constructor.
	 */
	public function __construct() {
		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
			$this->trigger_code = 'WCSUBSCRIPTIONEXPIRED';
			$this->trigger_meta = 'WOOSUBSCRIPTIONS';
			$this->define_trigger();
		}
	}

	/**
	 *
	 */
	public function define_trigger() {

		$trigger = array(
			'author'              => Automator()->get_author_name( $this->trigger_code ),
			'support_link'        => Automator()->get_author_support_link( $this->trigger_code, 'integration/woocommerce/' ),
			'is_pro'              => true,
			'integration'         => self::$integration,
			'code'                => $this->trigger_code,
			/* translators: Logged-in trigger - WooCommerce */
			'sentence'            => sprintf( __( "A user's subscription to {{a product:%1\$s}} expires {{a number of:%2\$s}} times", 'uncanny-automator-pro' ), $this->trigger_meta, 'NUMTIMES' ),
			/* translators: Logged-in trigger - WooCommerce */
			'select_option_name'  => __( "A user's subscription to {{a product}} expires", 'uncanny-automator-pro' ),
			'action'              => 'woocommerce_subscription_status_expired',
			'priority'            => 30,
			'accepted_args'       => 2,
			'validation_function' => array( $this, 'order_expired' ),
			'options_callback'    => array( $this, 'load_options' ),
		);
		$trigger = Woocommerce_Pro_Helpers::add_loopable_tokens( $trigger );

		Automator()->register->trigger( $trigger );
	}

	/**
	 * @return array
	 */
	public function load_options() {
		$options       = Automator()->helpers->recipe->woocommerce->options->pro->all_wc_subscriptions();
		$options_array = array(
			'options' => array(
				Automator()->helpers->recipe->options->number_of_times(),
				$options,
			),
		);

		return Automator()->utilities->keep_order_of_options( $options_array );
	}

	/**
	 * Wrapper for wcs_maybe_make_user_inactive() that accepts a subscription instead of a user ID.
	 * Handy for hooks that pass a subscription object.
	 *
	 * @param WC_Subscription|WC_Order
	 *
	 * @since 2.2.9
	 */
	public function order_expired( $subscription ) {

		$user_id            = $subscription->get_user_id();
		$recipes            = Automator()->get->recipes_from_trigger_code( $this->trigger_code );
		$required_product   = Automator()->get->meta_from_recipes( $recipes, $this->trigger_meta );
		$matched_recipe_ids = array();

		//Add where option is set to Any product
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( - 1 === intval( $required_product[ $recipe_id ][ $trigger_id ] ) ) {
					$matched_recipe_ids[] = array(
						'recipe_id'  => $recipe_id,
						'trigger_id' => $trigger_id,
					);

					break;
				}
			}
		}

		$items       = $subscription->get_items();
		$product_ids = array();
		foreach ( $items as $item ) {
			$product_ids[] = $item->get_product_id();
		}

		//Add where Product ID is set for trigger
		foreach ( $recipes as $recipe_id => $recipe ) {
			foreach ( $recipe['triggers'] as $trigger ) {
				$trigger_id = $trigger['ID'];//return early for all products
				if ( in_array( $required_product[ $recipe_id ][ $trigger_id ], $product_ids, false ) ) {
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
					'user_id'          => $user_id,
					'recipe_to_match'  => $matched_recipe_id['recipe_id'],
					'trigger_to_match' => $matched_recipe_id['trigger_id'],
					'ignore_post_id'   => true,
				);

				$args = Automator()->maybe_add_trigger_entry( $pass_args, false );

				if ( $args ) {
					foreach ( $args as $result ) {
						if ( true === $result['result'] ) {

							// Add token for options
							$trigger_meta = array(
								'user_id'        => $user_id,
								'trigger_id'     => $result['args']['trigger_id'],
								'trigger_log_id' => $result['args']['trigger_log_id'],
								'run_number'     => $result['args']['run_number'],
							);

							Automator()->db->token->save( 'subscription_id', $subscription->get_id(), $trigger_meta );
							Automator()->db->token->save( 'order_id', $subscription->get_parent_id(), $trigger_meta );

							// Manually added do_action for loopable tokens.
							do_action( 'automator_loopable_token_hydrate', $result['args'], func_get_args() );

							Automator()->maybe_trigger_complete( $result['args'] );
						}
					}
				}
			}
		}
	}

}
