<?php
if ( ! class_exists( 'WC_Abstract_Privacy' ) ) {
	return;
}

class Woocommerce_Gateway_Purchase_Order_Privacy extends WC_Abstract_Privacy {
	/**
	 * Constructor
	 *
	 */
	public function __construct() {
		parent::__construct( __( 'Purchase Order', 'woocommerce-gateway-purchase-order' ) );

		$this->add_exporter( 'woocommerce-gateway-purchase-order-order-data', __( 'WooCommerce PO Order Data', 'woocommerce-gateway-purchase-order' ), array( $this, 'order_data_exporter' ) );
		$this->add_eraser( 'woocommerce-gateway-purchase-order-order-data', __( 'WooCommerce PO Data', 'woocommerce-gateway-purchase-order' ), array( $this, 'order_data_eraser' ) );
	}

	/**
	 * Returns a list of orders that are using PO payment method.
	 *
	 * @param string  $email_address
	 * @param int     $page
	 *
	 * @return array WP_Post
	 */
	protected function get_po_orders( $email_address, $page ) {
		$user = get_user_by( 'email', $email_address ); // Check if user has an ID in the DB to load stored personal data.

		$order_query    = array(
			'payment_method' => array( 'woocommerce_gateway_purchase_order' ),
			'limit'          => 10,
			'page'           => $page,
		);

		if ( $user instanceof WP_User ) {
			$order_query['customer_id'] = (int) $user->ID;
		} else {
			$order_query['billing_email'] = $email_address;
		}

		return wc_get_orders( $order_query );
	}

	/**
	 * Gets the message of the privacy to display.
	 *
	 */
	public function get_privacy_message() {
		return wpautop( sprintf( __( 'By using this extension, you may be storing personal data or sharing data with an external service. <a href="%s" target="_blank">Learn more about how this works, including what you may want to include in your privacy policy.</a>', 'woocommerce-gateway-purchase-order' ), 'https://docs.woocommerce.com/document/privacy-payments/#woocommerce-gateway-purchase-order' ) );
	}

	/**
	 * Handle exporting data for Orders.
	 *
	 * @param string $email_address E-mail address to export.
	 * @param int    $page          Pagination of data.
	 *
	 * @return array
	 */
	public function order_data_exporter( $email_address, $page = 1 ) {
		$done           = false;
		$data_to_export = array();

		$orders = $this->get_po_orders( $email_address, (int) $page );

		$done = true;

		if ( 0 < count( $orders ) ) {
			foreach ( $orders as $order ) {
				$data_to_export[] = array(
					'group_id'    => 'woocommerce_orders',
					'group_label' => __( 'Orders', 'woocommerce-gateway-purchase-order' ),
					'item_id'     => 'order-' . $order->get_id(),
					'data'        => array(
						array(
							'name'  => __( 'PO Number', 'woocommerce-gateway-purchase-order' ),
							'value' => $order->get_meta( '_po_number', true ),
						),
					),
				);
			}

			$done = 10 > count( $orders );
		}

		return array(
			'data' => $data_to_export,
			'done' => $done,
		);
	}

	/**
	 * Finds and erases order data by email address.
	 *
	 * @since 3.4.0
	 * @param string $email_address The user email address.
	 * @param int    $page  Page.
	 * @return array An array of personal data in name value pairs
	 */
	public function order_data_eraser( $email_address, $page ) {
		$orders = $this->get_po_orders( $email_address, (int) $page );

		$items_removed  = false;
		$items_retained = false;
		$messages       = array();

		foreach ( (array) $orders as $order ) {
			$order = wc_get_order( $order->get_id() );

			list( $removed, $retained, $msgs ) = $this->maybe_handle_order( $order );
			$items_removed  += $removed;
			$items_retained += $retained;
			$messages        = array_merge( $messages, $msgs );
		}

		// Tell core if we have more orders to work on still
		$done = count( $orders ) < 10;

		return array(
			'items_removed'  => $items_removed,
			'items_retained' => $items_retained,
			'messages'       => $messages,
			'done'           => $done,
		);
	}

	/**
	 * Handle eraser of data tied to Orders
	 *
	 * @param WC_Order $order
	 * @return array
	 */
	protected function maybe_handle_order( $order ) {
		$po_number = $order->get_meta( '_po_number', true );

		if ( empty( $po_number ) ) {
			return array( false, false, array() );
		}

		$order->delete_meta_data( '_po_number' );
		$order->save_meta_data();

		return array( true, false, array( __( 'Purchase Order Data Erased.', 'woocommerce-gateway-purchase-order' ) ) );
	}
}

new Woocommerce_Gateway_Purchase_Order_Privacy();
