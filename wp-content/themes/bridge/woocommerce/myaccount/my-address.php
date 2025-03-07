<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$page_title = apply_filters( 'woocommerce_my_account_my_address_title', esc_html__( 'My Addresses', 'bridge' ) );
	$get_addresses    = apply_filters( 'woocommerce_my_account_get_addresses', array(
		'billing' => esc_html__( 'Billing address', 'bridge' ),
		'shipping' => esc_html__( 'Shipping address', 'bridge' )
	), $customer_id );
} else {
	$page_title = apply_filters( 'woocommerce_my_account_my_address_title', esc_html__( 'My Address', 'bridge' ) );
	$get_addresses    = apply_filters( 'woocommerce_my_account_get_addresses', array(
		'billing' => esc_html__( 'Billing address', 'bridge' )
	), $customer_id );
}
$oldcol = 1;
$col    = 1;
?>

<h2><?php echo esc_html($page_title); ?></h2>

<p>
	<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'bridge' ) ); ?>
</p>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) echo '<div class="u-columns woocommerce-Addresses col2-set addresses">'; ?>

<?php foreach ( $get_addresses as $name => $address_title ) : ?>
	<?php
		$address = wc_get_account_formatted_address( $name );
		
	?>
	<div class="u-column<?php echo ( ( $col = $col * -1 ) < 0 ) ? 1 : 2; ?> col-<?php echo ( ( $oldcol = $oldcol * -1 ) < 0 ) ? 1 : 2; ?> woocommerce-Address address">
		<header class="woocommerce-Address-title title">
			<h3><?php echo esc_html( $address_title ); ?></h3>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit button">
				<?php
				printf(
					$address ? esc_html__( 'Edit %s', 'bridge' ) : esc_html__( 'Add %s', 'bridge' ),
					esc_html( $address_title )
				);
				?>
			</a>
		</header>
		<address>
			<?php
				$address = apply_filters( 'woocommerce_my_account_my_address_formatted_address', array(
					'first_name'  => get_user_meta( $customer_id, $name . '_first_name', true ),
					'last_name'   => get_user_meta( $customer_id, $name . '_last_name', true ),
					'company'     => get_user_meta( $customer_id, $name . '_company', true ),
					'address_1'   => get_user_meta( $customer_id, $name . '_address_1', true ),
					'address_2'   => get_user_meta( $customer_id, $name . '_address_2', true ),
					'city'        => get_user_meta( $customer_id, $name . '_city', true ),
					'state'       => get_user_meta( $customer_id, $name . '_state', true ),
					'postcode'    => get_user_meta( $customer_id, $name . '_postcode', true ),
					'country'     => get_user_meta( $customer_id, $name . '_country', true ),
				), $customer_id, $name );
				
				$formatted_address = WC()->countries->get_formatted_address( $address );
				
				if ( ! $formatted_address ) {
                    esc_html_e('You have not set up this type of address yet.', 'bridge');
                } else{
				    echo implode('<br/>', $address);
                }
			
			/**
			 * Used to output content after core address fields.
			 *
			 * @param string $name Address type.
			 * @since 8.7.0
			 */
			do_action( 'woocommerce_my_account_after_my_address', $name );
			?>
		</address>
	</div>

<?php endforeach; ?>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) echo '</div>'; ?>

