<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Returns the main instance of Woocommerce_Gateway_Purchase_Order_Admin to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Woocommerce_Gateway_Purchase_Order_Admin
 */
function Woocommerce_Gateway_Purchase_Order_Admin() {
	return Woocommerce_Gateway_Purchase_Order_Admin::instance();
}

/**
 * Main Woocommerce_Gateway_Purchase_Order_Admin Class
 *
 * @class Woocommerce_Gateway_Purchase_Order_Admin
 * @version	1.0.0
 * @since 1.0.0
 * @package	Woocommerce_Gateway_Purchase_Order_Admin
 * @author Matty
 */
final class Woocommerce_Gateway_Purchase_Order_Admin {
	/**
	 * Woocommerce_Gateway_Purchase_Order_Admin The single instance of Woocommerce_Gateway_Purchase_Order_Admin.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Meta key for the Purchase Order number.
	 *
	 * @var string
	 */
	private const META_KEY_PO_NUMBER = '_po_number';

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct () {
		add_action( 'woocommerce_admin_order_data_after_order_details', array( $this, 'display_purchase_order_number' ) );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'display_purchase_order_number' ) );
		add_action( 'woocommerce_order_details_after_order_table', array( $this, 'display_purchase_order_number' ) );

		// Print Invoices/Packing Lists Integration.
		add_action( 'wc_pip_after_body', array( $this, 'add_po_number_to_pip' ), 10, 4 );

		// Update the Purchase Order number when the transaction ID is updated from the order edit screen.
		add_action( 'woocommerce_process_shop_order_meta', array( $this, 'update_po_number_from_transaction_id' ), 99 );
	}

	/**
	 * Display the Purchase Order number on a Print Invoices/Packing lists output.
	 *
	 * @param string   $type Type.
	 * @param string   $action Action.
	 * @param object   $document Document.
	 * @param WC_Order $order Order.
	 * @return void
	 */
	public function add_po_number_to_pip( $type, $action, $document, $order ) {
		if ( 'invoice' !== $type ) {
			return;
		}

		$payment_method = $order->get_payment_method();

		if ( 'woocommerce_gateway_purchase_order' === $payment_method ) {
			$po_number = $order->get_meta( self::META_KEY_PO_NUMBER, true );
			/* translators: %s = Purchase order number */
			echo '<div class="purchase-order-number"><strong>' . esc_html( printf( __( 'Purchase order number: %s', 'woocommerce-gateway-purchase-order' ), $po_number ) ) . '</strong></div>';
		}
	}

	/**
	 * Display the Purchase Order number.
	 *
	 * @param WC_Order $order Order object.
	 * @return void
	 */
	public function display_purchase_order_number( $order ) {
		if ( ! $order instanceof WC_Order ) {
			return;
		}

		if ( 'woocommerce_gateway_purchase_order' !== $order->get_payment_method() ) {
			return;
		}

		$po_number = $order->get_meta( self::META_KEY_PO_NUMBER, true );
		if ( empty( $po_number ) ) {
			return;
		}

		// Handle different display contexts.
		switch ( current_action() ) {
			case 'woocommerce_admin_order_data_after_order_details':
				?>
					<p class="form-field form-field-wide">
						<label for="purchase_order_number"><strong><?php echo esc_html( $this->get_field_label() ); ?>:</strong></label>
						<span class="woocommerce-order-data__meta"><?php echo esc_html( $po_number ); ?></span>
					</p>
				<?php
				break;

			case 'woocommerce_order_details_after_order_table':
				if ( wp_is_block_theme() ) {
					$this->render_block_theme_po_number( $po_number );
				} else {
					$this->render_classic_theme_po_number( $po_number );
				}
				break;

			default:
				printf(
					'<p><strong>%s</strong>: %s</p>',
					esc_html__( 'Purchase Order Number', 'woocommerce-gateway-purchase-order' ),
					esc_html( $po_number )
				);
				break;
		}
	}

	/**
	 * Get the field label from settings or use default translatable string.
	 *
	 * @return string
	 */
	private function get_field_label() {
		$settings = get_option( 'woocommerce_woocommerce_gateway_purchase_order_settings', array() );
		return ! empty( $settings['field_label'] )
			? $settings['field_label']
			: __( 'Purchase Order Number', 'woocommerce-gateway-purchase-order' );
	}

	/**
	 * Render PO number for block themes.
	 *
	 * @param string $po_number The PO number value.
	 * @return void
	 */
	private function render_block_theme_po_number( $po_number ) {
		?>
		<div class="wc-block-order-confirmation-summary alignwide">
			<ul class="wc-block-order-confirmation-summary-list">
				<li class="wc-block-order-confirmation-summary-list-item">
					<span class="wc-block-order-confirmation-summary-list-item__key">
						<?php esc_html_e( 'Purchase Order Number', 'woocommerce-gateway-purchase-order' ); ?>:
					</span>
					<span class="wc-block-order-confirmation-summary-list-item__value">
						<?php echo esc_html( $po_number ); ?>
					</span>
				</li>
			</ul>
		</div>
		<?php
	}

	/**
	 * Render PO number for classic themes.
	 *
	 * @param string $po_number The PO number value.
	 * @return void
	 */
	private function render_classic_theme_po_number( $po_number ) {
		?>
		<ul class="woocommerce-order-overview woocommerce-thankyou-order-details order_details">
			<li class="woocommerce-order-overview__purchase-order purchase-order">
				<?php echo esc_html__( 'Purchase Order Number', 'woocommerce-gateway-purchase-order' ); ?>:
				<strong><?php echo esc_html( $po_number ); ?></strong>
			</li>
		</ul>
		<?php
	}

	/**
	 * Main Woocommerce_Gateway_Purchase_Order_Admin Instance
	 *
	 * Ensures only one instance of Woocommerce_Gateway_Purchase_Order_Admin is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Woocommerce_Gateway_Purchase_Order_Admin()
	 * @return Main Woocommerce_Gateway_Purchase_Order_Admin instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Update the Purchase Order number when the transaction ID is updated from the order edit screen.
	 *
	 * @param int $order_id Order ID.
	 * @return void
	 */
	public function update_po_number_from_transaction_id( $order_id ) {
		$order = wc_get_order( $order_id );

		if ( ! $order || ! isset( $_POST['_payment_method'] ) || ! isset( $_POST['_transaction_id'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return;
		}

		$payment_method = isset( $_POST['_payment_method'] ) ? wc_clean( wp_unslash( $_POST['_payment_method'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( 'woocommerce_gateway_purchase_order' !== $payment_method ) {
			return;
		}

		$transaction_id = isset( $_POST['_transaction_id'] ) ? wc_clean( wp_unslash( $_POST['_transaction_id'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$po_number      = $order->get_meta( self::META_KEY_PO_NUMBER, true );

		// Update the PO number if it's different from the transaction ID.
		if ( $transaction_id !== $po_number ) {
			$order->update_meta_data( self::META_KEY_PO_NUMBER, esc_attr( $transaction_id ) );
			$order->save();
		}
	}
}
