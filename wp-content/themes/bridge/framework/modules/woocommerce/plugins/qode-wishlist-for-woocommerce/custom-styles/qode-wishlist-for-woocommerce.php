<?php

if( ! function_exists('bridge_qode_wishlist_for_woocommerce_custom_styles')) {
	
	function bridge_qode_wishlist_for_woocommerce_custom_styles() {
		$first_color_selector = array(
			'.qode-single-product-summary .qwfw-add-to-wishlist.qwfw-shortcode',
			'.qode-single-product-summary .qwfw-add-to-wishlist.qwfw-shortcode .qwfw-m-text',
			'.qode-single-product-summary .qwfw-add-to-wishlist.qwfw-shortcode.qwfw--added',
			'.qode-single-product-summary .qwfw-add-to-wishlist.qwfw-shortcode.qwfw--added .qwfw-m-text',
		);
		
		$first_color = bridge_qode_options()->getOptionValue('first_color');
		
		if( ! empty( $first_color ) ) {
			echo bridge_qode_dynamic_css( $first_color_selector, array( 'color' => $first_color ) );
		}
		
	}
	
	add_action( 'bridge_qode_action_style_dynamic', 'bridge_qode_wishlist_for_woocommerce_custom_styles' );
}
