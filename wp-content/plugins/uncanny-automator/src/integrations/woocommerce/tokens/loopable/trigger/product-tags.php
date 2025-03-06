<?php
namespace Uncanny_Automator\Integrations\Woocommerce\Tokens\Loopable;

use Exception;
use Uncanny_Automator\Services\Loopable\Loopable_Token_Collection;
use Uncanny_Automator\Services\Loopable\Trigger_Loopable_Token;

/**
 * Loopable Order Items.
 *
 * @since 5.10
 *
 * @package Uncanny_Automator\Integrations\Woocommerce\Tokens\Loopable
 */
class Product_Tags extends Trigger_Loopable_Token {

	/**
	 * Register loopable tokens.
	 *
	 * @return void
	 */
	public function register_loopable_token() {

		$child_tokens = array(
			'TAG_NAME' => array(
				'name' => _x( 'Tag name', 'Woo', 'uncanny-automator' ),
			),
			'TAG_ID'   => array(
				'name'       => _x( 'Tag ID', 'Woo', 'uncanny-automator' ),
				'token_type' => 'integer',
			),
		);

		$this->set_id( 'PRODUCT_TAGS' );
		$this->set_name( _x( 'Order products tags', 'Woo', 'uncanny-automator' ) );
		$this->set_log_identifier( '#{{TAG_ID}} {{TAG_NAME}}' );
		$this->set_child_tokens( $child_tokens );

	}

	/**
	 * Hydrate the tokens.
	 *
	 * @param mixed $trigger_args
	 *
	 * @return Loopable_Token_Collection
	 */
	public function hydrate_token_loopable( $trigger_args ) {

		$loopable = new Loopable_Token_Collection();

		try {
			$order = $this->get_order( $trigger_args );
		} catch ( Exception $e ) {
			automator_log( $e->getMessage() );
			return $loopable;
		}

		// Loop through order items
		foreach ( (array) $order->get_items() as $item_id => $item ) {

			$product = $item->get_product();

			if ( $product ) {
				$tags = wp_get_post_terms( $product->get_id(), 'product_tag' );
				foreach ( $tags as $tag ) {
					$loopable->create_item(
						array(
							'TAG_NAME' => $tag->name ?? '',
							'TAG_ID'   => $tag->term_id ?? '',
						)
					);
				}
			}
		}

		return $loopable;

	}

	/**
	 * Retrieve a specific order.
	 *
	 * @param mixed $trigger_args
	 * @return bool|WC_Order|WC_Order_Refund
	 * @throws Exception
	 */
	private function get_order( $trigger_args ) {

		if ( ! function_exists( 'wc_get_order' ) ) {
			throw new \Exception( 'Order is not found', 400 );
		}

		// The order id.
		$order_id = $trigger_args[0] ?? null;

		// Get an instance of the WC_Order object.
		$order = wc_get_order( $order_id );

		if ( ! $order ) {
			throw new \Exception( 'Order is not found', 400 );
		}

		return $order;

	}

}
