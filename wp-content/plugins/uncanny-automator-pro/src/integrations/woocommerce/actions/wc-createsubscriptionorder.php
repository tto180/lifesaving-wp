<?php

namespace Uncanny_Automator_Pro;

use WC_Subscriptions_Product;

/**
 * Class WC_CREATESUBSCRIPTIONORDER
 *
 * @package Uncanny_Automator_Pro
 */
class WC_CREATESUBSCRIPTIONORDER {

	use \Uncanny_Automator\Recipe\Action_Tokens;
	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WC';

	/**
	 * Action code var.
	 *
	 * @var string
	 */
	private $action_code;
	/**
	 * Action meta var.
	 *
	 * @var string
	 */
	private $action_meta;

	/**
	 * Set up Automator action constructor.
	 */
	public function __construct() {

		include_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' ) ) {
			$this->action_code = 'WCCREATEORDERFORSUBSCRIPTION';
			$this->action_meta = 'CREATEORDERFORSUBSCRIPTION';
			$this->define_action();
		}
	}

	/**
	 * Define and register the action by pushing it into the Automator object
	 */
	public function define_action() {

		$action = array(
			'author'             => Automator()->get_author_name( $this->action_code ),
			'support_link'       => Automator()->get_author_support_link( $this->action_code, 'integration/woocommerce/' ),
			'is_pro'             => true,
			'integration'        => self::$integration,
			'code'               => $this->action_code,
			/* translators: Action - WooCommerce */
			'sentence'           => sprintf( __( 'Create a subscription order with {{a product:%1$s}}', 'uncanny-automator-pro' ), $this->action_meta ),
			/* translators: Action - WooCommerce */
			'select_option_name' => __( 'Create a subscription order with {{a product}}', 'uncanny-automator-pro' ),
			'priority'           => 10,
			'accepted_args'      => 1,
			'execution_function' => array(
				$this,
				'create_woocommerce_subscription_order',
			),
			'options_callback'   => array( $this, 'load_options' ),
		);

		// @see Wc_Pro_Tokens::get_order_action_tokens.
		// @see Wc_Pro_Tokens::get_order_subscription_tokens.
		$this->set_action_tokens(
			array_merge(
				Wc_Pro_Tokens::get_order_action_tokens(),
				Wc_Pro_Tokens::get_order_subscription_tokens()
			),
			$this->action_code
		);

		Automator()->register->action( $action );
	}

	/**
	 * Load options.
	 *
	 * @return array
	 */
	public function load_options() {

		$options_array = array(
			'options_group' => array(
				$this->action_meta => Automator()->helpers->recipe->woocommerce->pro->load_options_input( false, 'subscription' ),
			),
		);

		return Automator()->utilities->keep_order_of_options( $options_array );
	}

	/**
	 * Validation function when the trigger action is hit
	 *
	 * @param $user_id
	 * @param $action_data
	 * @param $recipe_id
	 * @param $args
	 *
	 * @throws \Exception
	 */
	public function create_woocommerce_subscription_order( $user_id, $action_data, $recipe_id, $args ) {

		// First make sure all required functions and classes exist
		if ( ! function_exists( 'wc_create_order' ) || ! function_exists( 'wcs_create_subscription' ) || ! class_exists( 'WC_Subscriptions' ) ) {
			$error_message                       = __( '`wc_create_order` or `wcs_create_subscription` function is missing.', 'uncanny-automator-pro' );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			Automator()->complete_action( $user_id, $action_data, $recipe_id, $error_message );

			return;
		}

		$order = Automator()->helpers->recipe->woocommerce->pro->wc_create_order( $user_id, $action_data, $recipe_id, $args, false, 'subscription' );

		if ( false === $order ) {
			$error_message                       = __( 'Action execution failed.', 'uncanny-automator-pro' );
			$action_data['do-nothing']           = true;
			$action_data['complete_with_errors'] = true;
			Automator()->complete_action( $user_id, $action_data, $recipe_id, $error_message );

			return;
		}

		// @see Wc_Pro_Tokens::hydrate_order_tokens.
		// @see Wc_Pro_Tokens::hydrate_order_subscription_tokens.
		$this->hydrate_tokens(
			array_merge(
				Wc_Pro_Tokens::hydrate_order_tokens( $order ),
				Wc_Pro_Tokens::hydrate_order_subscription_tokens( $order )
			)
		);

		Automator()->complete_action( $user_id, $action_data, $recipe_id );

	}
}
