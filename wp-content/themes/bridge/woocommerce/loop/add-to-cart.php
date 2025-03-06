<?php
/**
 * Loop Add to Cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/add-to-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://woocommerce.com/document/template-structure/
 * @package     WooCommerce\Templates
 * @version     9.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;
	
$aria_describedby = isset( $args['aria-describedby_text'] ) ? sprintf( 'aria-describedby="woocommerce_loop_add_to_cart_link_describedby_%s"', esc_attr( $product->get_id() ) ) : '';


$bridge_qode_options = bridge_qode_return_global_options();

$button_classes = ' qbutton add-to-cart-button';
if ( isset( $bridge_qode_options['woo_products_add_to_cart_hover_type'] ) && $bridge_qode_options['woo_products_add_to_cart_hover_type'] !== '' ) {
	$button_classes .= ' enlarge';
}

if ( ! $product->is_in_stock() ) : ?>
	<span class="onsale out-of-stock-button">
        <span class="out-of-stock-button-inner">
            <?php echo apply_filters( 'out_of_stock_add_to_cart_text', esc_html__( 'Out of stock', 'bridge' ) ); ?>
        </span>
    </span>
<?php else :
	echo apply_filters( 'woocommerce_loop_add_to_cart_link', // WPCS: XSS ok.
		sprintf( '<span class="add-to-cart-button-outer"><span class="add-to-cart-button-inner"><a href="%s" %s data-quantity="%s" class="%s" %s>%s</a></span></span>',
		         esc_url( $product->add_to_cart_url() ),
		         $aria_describedby,
			esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
			esc_attr( isset( $args['class'] ) ? $args['class'] . $button_classes : 'button' . $button_classes ),
			isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
			esc_html( $product->add_to_cart_text() )
		),
		$product, $args );

endif; ?>
<?php if ( isset( $args['aria-describedby_text'] ) ) : ?>
	<span id="woocommerce_loop_add_to_cart_link_describedby_<?php echo esc_attr( $product->get_id() ); ?>" class="screen-reader-text">
		<?php echo esc_html( $args['aria-describedby_text'] ); ?>
	</span>
<?php endif; ?>