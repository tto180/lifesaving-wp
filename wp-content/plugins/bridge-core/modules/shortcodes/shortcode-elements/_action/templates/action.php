<?php
	
	$html                 = '';
	$action_classes       = '';
	$action_styles        = '';
	$text_wrapper_classes = '';
	$button_styles        = '';
	$icon_styles          = '';
	$data_attr            = '';
	$content_styles       = '';
	
	if ( $show_button == 'yes' ) {
		$text_wrapper_classes .= 'column1';
	}
	
	$action_classes .= esc_attr( $type );
	
	if ( $background_color != '' ) {
		$action_styles .= 'background-color: ' . esc_attr( $background_color ) . ';';
	}
	
	if ( $padding_top != '' ) {
		$action_styles .= 'padding-top: ' . esc_attr( $padding_top ) . 'px;';
	}
	
	if ( $padding_bottom != '' ) {
		$action_styles .= 'padding-bottom: ' . esc_attr( $padding_bottom ) . 'px;';
	}
	
	if ( $border_color != '' ) {
		$action_styles .= 'border-top: 1px solid ' . esc_attr( $border_color ) . ';';
	}
	
	if ( $background_image !== '' ) {
		$background_image_src = is_numeric( $background_image ) ? wp_get_attachment_url( $background_image ) : $background_image;
		
		$action_classes = ' with_background_image';
		$action_styles  .= 'background-image: url(' . esc_url( $background_image_src ) . ');';
		
		if ( $use_background_as_pattern == 'yes' ) {
			$action_styles .= 'background-repeat: repeat;';
		}
	}
	
	if ( $button_text_color != '' ) {
		$button_styles .= 'color: ' . esc_attr( $button_text_color ) . ';';
	}
	if ( $icon_color != "" ) {
		$icon_styles = " style='color: " . esc_attr( $icon_color ) . ";'";
	}
	if ( $button_border_color != '' ) {
		$button_styles .= 'border-color: ' . esc_attr( $button_border_color ) . ';';
	}
	
	if ( $button_background_color != '' ) {
		$button_styles .= 'background-color:' . esc_attr( $button_background_color ) . ';';
		
	}
	
	if ( $button_hover_background_color != "" ) {
		$data_attr .= "data-hover-background-color=" . esc_attr( $button_hover_background_color ) . " ";
	}
	
	if ( $button_hover_border_color != "" ) {
		$data_attr .= "data-hover-border-color=" . esc_attr( $button_hover_border_color ) . " ";
	}
	
	if ( $button_hover_text_color != "" ) {
		$data_attr .= "data-hover-color=" . esc_attr( $button_hover_text_color );
	}
	
	if ( $full_width == "no" ) {
		$html .= '<div class="container_inner">';
	}
	
	$html .= '<div class="call_to_action ' . esc_attr( $action_classes ) . '" style="' . esc_attr( $action_styles ) . '">';
	
	if ( $content_in_grid == 'yes' && $full_width == 'yes' ) {
		$html .= '<div class="container_inner">';
	}
	
	if ( $show_button == 'yes' && $type !== 'simple' ) {
		$html .= '<div class="two_columns_75_25 clearfix">';
	}
	
	$content_additional_class = '';
	if ( $text_size != '' ) {
		$content_styles           .= 'font-size:' . esc_attr( $text_size ) . 'px;';
		$content_additional_class .= ' font_size_inherit';
	}
	
	if ( $text_font_weight !== '' ) {
		$content_styles           .= 'font-weight: ' . esc_attr( $text_font_weight ) . ';';
		$content_additional_class .= ' font_weight_inherit';
	}
	
	if ( $text_letter_spacing != '' ) {
		$content_styles           .= 'letter-spacing: ' . esc_attr( $text_letter_spacing ) . 'px;';
		$content_additional_class .= ' letter_spacing_inherit';
	}
	
	if ( $text_color != '' ) {
		$content_styles .= 'color: ' . esc_attr( $text_color );
	}
	
	$html .= '<div class="text_wrapper ' . esc_attr( $text_wrapper_classes ) . '">';
	
	if ( $type == "with_icon" ) {
		$html .= '<div class="call_to_action_icon_holder">';
		$html .= '<div class="call_to_action_icon">';
		$html .= '<div class="call_to_action_icon_inner">';
		if ( $custom_icon != "" ) {
			if ( is_numeric( $custom_icon ) ) {
				$custom_icon_src = wp_get_attachment_url( $custom_icon );
			} else {
				$custom_icon_src = $custom_icon;
			}
			
			$html .= '<img itemprop="image" src="' . esc_url( $custom_icon_src ) . '" alt="">';
		} else {
			$html .= "<i class='fa " . esc_attr( $icon ) . " pull-left . " . esc_attr( $icon_size ) . "'" . esc_attr( $icon_styles ) . "></i>";
		}
		
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
	}
	$content = preg_replace(
		'#^<\/p>|<p>$#',
		'',
		$content
	);
	$html    .= '<div class="call_to_action_text ' . esc_attr( $content_additional_class ) . '" style="' . esc_attr( $content_styles ) . '">' . wp_kses_post( $content ) . '</div>';
	
	if ( $show_button == 'yes' && $button_text !== '' && $type === "simple" ) {
		$button_link = ( $button_link != '' ) ? esc_url( $button_link ) : 'javascript: void(0)';
		
		$html .= '<a itemprop="url" href="' . $button_link . '" class="qbutton white ' . esc_attr( $button_size ) . '" target="' . esc_attr( $button_target ) . '" style="' . esc_attr( $button_styles ) . '" ' . esc_attr( $data_attr ) . '>' . esc_html( $button_text ) . '</a>';
	}
	$html .= '</div>'; //close text_wrapper
	
	if ( $show_button == 'yes' && $button_text !== '' && $type !== "simple" ) {
		$button_link = ( $button_link != '' ) ? esc_url( $button_link ) : 'javascript: void(0)';
		
		$html .= '<div class="button_wrapper column2">';
		$html .= '<a itemprop="url" href="' . $button_link . '" class="qbutton white ' . esc_attr( $button_size ) . '" target="' . esc_attr( $button_target ) . '" style="' . esc_attr( $button_styles ) . '" ' . esc_attr( $data_attr ) . '>' . esc_html( $button_text ) . '</a>';
		$html .= '</div>';//close button_wrapper
	}
	
	if ( $show_button == 'yes' && $type !== 'simple' ) {
		$html .= '</div>'; //close two_columns_75_25 if opened
	}
	
	if ( $content_in_grid == 'yes' && $full_width == 'yes' ) {
		$html .= '</div>'; // close .container_inner if oppened
	}
	
	$html .= '</div>';//close .call_to_action
	
	if ( $full_width == 'no' ) {
		$html .= '</div>'; // close .container_inner if oppened
	}
	
	echo bridge_qode_get_module_part( $html );