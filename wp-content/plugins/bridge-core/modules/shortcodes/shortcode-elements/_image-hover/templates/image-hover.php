<?php
	
	//init variables
	$html            = "";
	$image_classes   = "";
	$image_src       = $image;
	$hover_image_src = $hover_image;
	$images_styles   = "";
	$hover_image_alt = esc_html__( 'Hover Image', 'bridge-core' );
	
	if ( is_numeric( $image ) ) {
		$image_src      = wp_get_attachment_url( $image );
		$image_alt_meta = get_post_meta(
			$image,
			'_wp_attachment_image_alt',
			true
		);
		$image_alt      = ! empty( $image_alt_meta ) ? $image_alt_meta : esc_html__( 'Main Image', 'bridge-core' );
	}
	
	if ( is_numeric( $hover_image ) ) {
		$hover_image_src      = wp_get_attachment_url( $hover_image );
		$hover_image_alt_meta = get_post_meta(
			$hover_image,
			'_wp_attachment_image_alt',
			true
		);
		$hover_image_alt      = ! empty( $hover_image_alt_meta ) ? $hover_image_alt_meta : $hover_image_alt;
	}
	
	if ( $hover_image_src != "" ) {
		$image_classes .= "active_image ";
	}
	
	$css_transition_delay = ( $transition_delay != "" && $transition_delay > 0 ) ? $transition_delay / 1000 . "s" : "";
	
	$animate_class = ( $animation == "yes" ) ? "hovered" : "";
	
	//generate output
	$html .= '<div class="image_hover ' . esc_attr( $animate_class ) . '" data-transition-delay="' . esc_attr( $transition_delay ) . '">';
	$html .= '<div class="images_holder">';
	
	if ( $link != "" ) {
		$html .= '<a itemprop="url" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">';
	}

	if ( ! empty( $image_src ) ) {
		$html .= '<img itemprop="image" class="' . esc_attr( $image_classes ) . '" src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $image_alt ) . '" style="' . esc_attr( $images_styles ) . '" />';
	}
	if ( ! empty( $hover_image_src ) ) {
		$html .= '<img itemprop="image" class="hover_image" src="' . esc_url( $hover_image_src ) . '" alt="' . esc_attr( $hover_image_alt ) . '" style="' . esc_attr( $images_styles ) . '" />';
	}
	if ( $link != "" ) {
		$html .= '</a>';
	}
	
	$html .= '</div>'; //close image_hover
	$html .= '</div>'; //close images_holder
	
	echo bridge_qode_get_module_part( $html );