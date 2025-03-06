<?php

if (!function_exists('bridge_qode_single_product_summary_additional_tag_before')) {
	function bridge_qode_single_product_summary_additional_tag_before() {

		print '<div class="qode-single-product-summary">';
	}
}

if (!function_exists('bridge_qode_single_product_summary_additional_tag_after')) {
	function bridge_qode_single_product_summary_additional_tag_after() {

		print '</div>';
	}
}

if ( ! function_exists( 'bridge_qode_add_product_gallery_slider_on_mobile' ) ) {
	/**
	 * Function that add additional wrapper around thumbnail images on single product
	 */
	function bridge_qode_add_product_gallery_slider_on_mobile() {
		if( 'yes' === bridge_qode_options()->getOptionValue( 'product_gallery_slider_on_mobile' ) ) {
			global $product;
			$image_ids = $product->get_gallery_image_ids();
			$post_thumbnail_id = $product->get_image_id();
			array_unshift( $image_ids, intval( $post_thumbnail_id ) );
			
			if ( ! empty( $image_ids ) ) {
				$html  = '<div class="qode-product-gallery-slider flexslider">';
				$html  .= '<ul class="slides">';
				foreach ( $image_ids as $image_id ) {
					$html .= '<li class="qode-product-image">';
					$html .= wp_get_attachment_image( $image_id, 'woocommerce_thumbnail' );
					$html .= '</li>';
				}
				$html .= '</ul>';
			} else {
				$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
				$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'teenglow' ) );
			}
			
			$html .= '</div>';
			
			echo bridge_qode_get_module_part( $html );
		}
	}
}

add_action( 'woocommerce_product_thumbnails', 'bridge_qode_add_product_gallery_slider_on_mobile', 35 );