<?php

namespace Uncanny_Automator_Pro;

use Uncanny_Automator\Recipe\Action;

/**
 * Class WCS_REMOVE_VARIATION_FROM_SUBSCRIPTION
 *
 * @pacakge Uncanny_Automator_Pro
 */
class WCS_REMOVE_VARIATION_FROM_SUBSCRIPTION extends Action {

	/**
	 * @return mixed|void
	 */
	protected function setup_action() {

		if ( ! class_exists( 'WC_Subscriptions' ) ) {
			return false;
		}

		$this->set_integration( 'WC' );
		$this->set_action_code( 'WCS_REMOVE_VARIATION' );
		$this->set_action_meta( 'WCS_VARIATION' );
		$this->set_is_pro( true );
		$this->set_requires_user( true );
		$this->set_sentence( sprintf( esc_attr_x( "Remove {{a variation:%1\$s}} of {{a subscription product:%2\$s}} from the user's {{subscription:%3\$s}}", 'Woo Subscription', 'uncanny-automator-pro' ), $this->get_action_meta(), 'WCS_PRODUCTS:' . $this->get_action_meta(), 'SUBSCRIPTION_ID:' . $this->get_action_meta() ) );
		$this->set_readable_sentence( esc_attr_x( "Remove {{a variation}} of {{a subscription product}} from the user's {{subscription}}", 'Woo Subscription', 'uncanny-automator-pro' ) );
	}

	/**
	 * Define the Action's options
	 *
	 * @return array
	 */
	public function options() {
		$subscription_options = Automator()->helpers->recipe->woocommerce->options->pro->all_wc_variation_subscriptions();
		$options              = array();
		foreach ( $subscription_options['options'] as $k => $option ) {
			$options[] = array(
				'text'  => $option,
				'value' => $k,
			);
		}

		return array(
			array(
				'input_type'     => 'select',
				'option_code'    => 'WCS_PRODUCTS',
				'label'          => _x( 'Variable subscription', 'WooCommerce Subscription', 'uncanny-automator-pro' ),
				'required'       => true,
				'options'        => $options,
				'is_ajax'        => true,
				'endpoint'       => 'select_variations_from_WOOSELECTVARIATION',
				'fill_values_in' => $this->get_action_meta(),
			),
			array(
				'input_type'  => 'select',
				'option_code' => $this->get_action_meta(),
				'label'       => _x( 'Variation', 'WooCommerce Subscription', 'uncanny-automator-pro' ),
				'required'    => true,
				'options'     => array(),
			),
			Automator()->helpers->recipe->field->int(
				array(
					'option_code' => 'SUBSCRIPTION_ID',
					'label'       => _x( 'Subscription ID', 'Woo Subscription', 'uncanny-automator-pro' ),
					'description' => _x( 'Leave blank to remove from all active subscriptions.', 'Woo Subscription', 'uncanny-automator-pro' ),
					'required'    => false,
				)
			),
		);
	}

	public function define_tokens() {
		return array(
			'WC_SUBSCRIPTION_ID'               => array(
				'name' => _x( 'Subscription ID(s)', 'WooCommerce Subscriptions', 'uncanny-automator-pro' ),
				'type' => 'int',
			),
			$this->get_action_meta() . '_NAME' => array(
				'name' => _x( 'Variation name', 'WooCommerce Subscriptions', 'uncanny-automator-pro' ),
				'type' => 'text',
			),
			'WCS_PRODUCTS_NAME'                => array(
				'name' => _x( 'Variable subscription name', 'WooCommerce Subscriptions', 'uncanny-automator-pro' ),
				'type' => 'text',
			),
		);
	}

	/**
	 * @param int $user_id
	 * @param array $action_data
	 * @param int $recipe_id
	 * @param array $args
	 * @param       $parsed
	 */
	protected function process_action( $user_id, $action_data, $recipe_id, $args, $parsed ) {
		$variation_id    = sanitize_text_field( $parsed[ $this->get_action_meta() ] );
		$subscription_id = sanitize_text_field( $parsed['SUBSCRIPTION_ID'] );

		$product = wc_get_product( absint( $variation_id ) );

		if ( ! $product->is_type( 'variation' ) ) {
			$this->add_log_error( esc_attr_x( "The product you've selected isn't a valid variable subscription product.", 'WooCommerce Subscription', 'uncanny-automator-pro' ) );

			return false;
		}

		// If Subscription ID is set, remove the product from the subscription
		if ( ! empty( $subscription_id ) ) {
			$subscription = wcs_get_subscription( absint( $subscription_id ) );
			if ( ! $subscription instanceof \WC_Subscription ) {
				$this->add_log_error( esc_attr_x( 'The provided subscription ID is invalid.', 'Woo Subscription', 'uncanny-automator-pro' ) );

				return false;
			}

			$this->remove_product_from_subscription( $subscription, $variation_id );

			return true;
		}

		$active_subscriptions = wcs_get_subscriptions(
			array(
				'subscriptions_per_page' => 9999,
				'orderby'                => 'start_date',
				'order'                  => 'DESC',
				'customer_id'            => $user_id,
				'variation_id'           => absint( $variation_id ),
				'subscription_status'    => array( 'active' ),
				'meta_query_relation'    => 'AND',
			)
		);

		if ( empty( $active_subscriptions ) ) {
			$this->add_log_error( esc_attr_x( 'There are no active subscriptions associated with this user.', 'WooCommerce Subscription', 'uncanny-automator-pro' ) );

			return null;
		}

		$subscription_ids = array();

		// Loop through active subscriptions
		foreach ( $active_subscriptions as $subscription ) {
			$this->remove_product_from_subscription( $subscription, $variation_id );
			$subscription_ids[] = $subscription->get_id();
		}

		$this->hydrate_tokens(
			array(
				'WC_SUBSCRIPTION_ID'               => join( ', ', $subscription_ids ),
				'SUBSCRIPTION_ID'                  => empty( $parsed['SUBSCRIPTION_ID'] ) ? join( ', ', $subscription_ids ) : $parsed['SUBSCRIPTION_ID'],
				$this->get_action_meta() . '_NAME' => $parsed[ $this->get_action_meta() . '_readable' ],
				'WCS_PRODUCTS_NAME'                => $parsed['WCS_PRODUCTS_readable'],
			)
		);

		return true;
	}

	/**
	 * @param $subscription
	 * @param $variation_id
	 *
	 * @return bool
	 */
	public function remove_product_from_subscription( $subscription, $variation_id ) {
		// Check if the subscription contains the specified product
		if ( ! $subscription->has_product( $variation_id ) ) {
			$this->add_log_error( esc_attr_x( 'The subscription does not include the specified variation.', 'Woo Subscription', 'uncanny-automator-pro' ) );

			return false;
		}
		// Check if the subscription contains the specified product
		$subscription_items = $subscription->get_items();

		if ( empty( $subscription_items ) ) {
			$this->add_log_error( esc_attr_x( 'The subscription does not include any products.', 'Woo Subscription', 'uncanny-automator-pro' ) );

			return false;
		}

		$modified = false;
		foreach ( $subscription_items as $item_id => $item ) {
			$product = $item->get_product();
			if ( $product && (int) $product->get_variation_id() === (int) $variation_id ) {
				$subscription->update_status( 'on-hold' );
				// Remove the product from the subscription
				$c = $subscription->remove_item( $item_id );
				if ( $c ) {
					$modified = true;
				}
				// Save the subscription
				$subscription->calculate_totals();
				$subscription->save();
				$subscription->update_status( 'active' );
			}
		}

		if ( ! $modified ) {
			$this->add_log_error( esc_attr_x( 'Failed to remove the product from the subscription.', 'Woo Subscription', 'uncanny-automator-pro' ) );

			return false;
		}
	}
}
