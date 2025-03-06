<?php
	
	//init variables
	$html                   = "";
	$counter_holder_classes = "";
	$counter_holder_data    = array();
	$counter_classes        = "";
	$counter_holder_styles  = "";
	$counter_styles         = "";
	$text_styles            = "";
	$separator_styles       = "";
	
	if ( $position != "" ) {
		$counter_holder_classes .= " " . esc_attr( $position );
	}
	
	if ( $box == "yes" ) {
		$counter_holder_classes .= " boxed_counter";
		if ( $box_border_color != '' ) {
			$counter_holder_styles = 'border-color:' . esc_attr( $box_border_color ) . ';';
		}
	}
	
	if ( $type != "" ) {
		$counter_classes .= " " . esc_attr( $type );
	}
	
	if ( $font_color != "" ) {
		$counter_styles .= "color: " . esc_attr( $font_color ) . ";";
	}
	
	if ( $font_size != "" ) {
		$counter_styles .= "font-size: " . esc_attr( $font_size ) . "px;";
	}
	if ( $font_weight != "" ) {
		$counter_styles .= "font-weight: " . esc_attr( $font_weight ) . ";";
	}
	if ( $text_size != "" ) {
		$text_styles .= "font-size: " . esc_attr( $text_size ) . "px;";
	}
	if ( $text_font_weight != "" ) {
		$text_styles .= "font-weight: " . esc_attr( $text_font_weight ) . ";";
	}
	if ( $text_transform != "" ) {
		$text_styles .= "text-transform: " . esc_attr( $text_transform ) . ";";
	}
	
	if ( $text_color != "" ) {
		$text_styles .= "color: " . esc_attr( $text_color ) . ";";
	}
	
	if ( $element_appearance != "" ) {
		$counter_holder_data['data-element-appearance'] = esc_attr( $element_appearance );
	}
	
	if ( $digit != "" ) {
		$counter_holder_data['data-digit'] = esc_attr( $digit );
	}
	
	if ( $separator_color != "" ) {
		if ( $separator_transparency !== '' ) {
			$rgba_color       = bridge_qode_rgba_color(
				$separator_color,
				$separator_transparency
			);
			$separator_styles .= "background-color: " . esc_attr( $rgba_color ) . ';';
		} else {
			$separator_styles .= "background-color: " . esc_attr( $separator_color ) . ";";
		}
	}
	
	$html .= '<div class="q_counter_holder ' . esc_attr( $counter_holder_classes ) . '" style="' . esc_attr( $counter_holder_styles ) . '" ' . bridge_qode_get_inline_attrs( $counter_holder_data ) . '>';
	$html .= '<span class="counter ' . esc_attr( $counter_classes ) . '" style="' . esc_attr( $counter_styles ) . '">' . esc_html( $digit ) . '</span>';
	
	if ( $separator == "yes" ) {
		$html .= '<span class="separator small" style="' . esc_attr( $separator_styles ) . '"></span>';
	}
	
	$html .= wp_kses_post( $content );
	
	if ( $text != "" ) {
		$html .= '<p class="counter_text" style="' . esc_attr( $text_styles ) . '">' . wp_kses_post( $text ) . '</p>';
	}
	
	$html .= '</div>'; //close q_counter_holder
	
	echo bridge_qode_get_module_part( $html );
