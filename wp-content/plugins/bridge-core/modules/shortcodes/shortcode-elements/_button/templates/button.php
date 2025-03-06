<?php
	
	$qodeIconCollections = bridge_qode_return_icon_collections();
	
	if ( $target == "" ) {
		$target = "_self";
	}
	
	//init variables
	$html           = "";
	$button_classes = "qbutton ";
	$button_styles  = "";
	$add_icon       = "";
	$data_attr      = array();
	
	if ( $size != "" ) {
		$button_classes .= ' ' . esc_attr( $size );
	}
	
	if ( $text_align != "" ) {
		$button_classes .= ' ' . esc_attr( $text_align );
	}
	
	if ( $style == "white" ) {
		$button_classes .= ' ' . esc_attr( $style );
	}
	
	$button_classes .= " " . esc_attr( $hover_type );
	
	if ( $custom_class != '' ) {
		$button_classes .= " " . esc_attr( $custom_class );
	}
	if ( $button_shadow == 'yes' ) {
		$button_classes .= "  qode-button-shadow";
	}
	
	if ( $color != "" ) {
		$button_styles .= 'color: ' . esc_attr( $color ) . '; ';
	}
	
	if ( $border_color != "" ) {
		$button_styles .= 'border-color: ' . esc_attr( $border_color ) . '; ';
	}
	
	if ( $font_style != "" ) {
		$button_styles .= 'font-style: ' . esc_attr( $font_style ) . '; ';
	}
	
	if ( $font_weight != "" ) {
		$button_styles .= 'font-weight: ' . esc_attr( $font_weight ) . '; ';
	}
	
	if ( $font_family != "" ) {
		$button_styles .= 'font-family: ' . esc_attr( $font_family ) . '; ';
	}
	
	if ( $text_transform != "" ) {
		$button_styles .= 'text-transform: ' . esc_attr( $text_transform ) . '; ';
	}
	
	if ( $font_size != "" ) {
		$button_styles .= 'font-size: ' . esc_attr( $font_size ) . 'px; ';
	}
	
	if ( $letter_spacing != "" ) {
		$button_styles .= 'letter-spacing: ' . esc_attr( $letter_spacing ) . 'px; ';
	}
	
	if ( $gradient == 'yes' ) {
		$button_classes .= " qode-type1-gradient-left-to-right";
	}
	
	$icon_pack = $icon_pack == '' ? 'font_awesome' : esc_attr( $icon_pack );
	
	if ( $qodeIconCollections->getIconCollectionParamNameByKey( $icon_pack ) && ${$qodeIconCollections->getIconCollectionParamNameByKey( $icon_pack )} != "" ) {
		$icon_style = "";
		if ( $icon_color != "" ) {
			$icon_style .= 'color: ' . esc_attr( $icon_color ) . ';';
		}
		
		if ( $qodeIconCollections->getIconCollectionParamNameByKey( $icon_pack ) ) {
			$add_icon .= $qodeIconCollections->getIconHTML(
				${$qodeIconCollections->getIconCollectionParamNameByKey( $icon_pack )},
				$icon_pack,
				array( 'icon_attributes' => array( 'style' => esc_attr( $icon_style ), 'class' => 'qode_button_icon_element' ) )
			);
		}
		//$add_icon .= '<i class="fa '.$icon.'" style="'.$icon_style.'"></i>';
	}
	
	if ( $margin != "" ) {
		$button_styles .= 'margin: ' . esc_attr( $margin ) . '; ';
	}
	
	if ( $border_radius != "" ) {
		$button_styles .= 'border-radius: ' . esc_attr( $border_radius ) . 'px;-moz-border-radius: ' . esc_attr( $border_radius ) . 'px;-webkit-border-radius: ' . esc_attr( $border_radius ) . 'px; ';
	}
	
	if ( $background_color != "" ) {
		$button_styles .= 'background-color:' . esc_attr( $background_color ) .';';
	}
	
	if ( ! empty( $hover_background_color ) ) {
		$data_attr['data-hover-background-color'] = esc_attr( $hover_background_color );
	}
	
	if ( ! empty( $hover_border_color ) ) {
		$data_attr['data-hover-border-color'] = esc_attr( $hover_border_color );
	}
	
	if ( ! empty( $hover_color ) ) {
		$data_attr['data-hover-color'] = esc_attr( $hover_color );
	}
	
	if ( $html_type !== "button" ) {
		$html .= '<a ' . bridge_qode_get_inline_attr( $button_id, 'id' ) . ' itemprop="url" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '" ' . bridge_qode_get_inline_attrs( $data_attr ) . ' class="' . esc_attr( $button_classes ) . '" style="' . esc_attr( $button_styles ) . '">' . wp_kses_post( $text ) . wp_kses_post( $add_icon ) . '</a>';
	} else {
		$html .= '<button type="submit" ' . bridge_qode_get_inline_attr( $button_id, 'id' ) . ' ' . bridge_qode_get_inline_attrs( $data_attr ) . ' class="' . esc_attr( $button_classes ) . '" style="' . esc_attr( $button_styles ) . '">' . wp_kses_post( $text ) . wp_kses_post( $add_icon ) . '</button>';
	}
	
	echo bridge_qode_get_module_part( $html );