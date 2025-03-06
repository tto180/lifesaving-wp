<?php
	
	$headings_array = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
	
	//get correct heading value. If provided heading isn't valid get the default one
	$title_tag          = ( in_array(
		$title_tag,
		$headings_array
	) ) ? esc_attr( $title_tag ) : esc_attr( $args['title_tag'] );
	$text_in_circle_tag = ( in_array(
		$text_in_circle_tag,
		$headings_array
	) ) ? esc_attr( $text_in_circle_tag ) : esc_attr( $args['text_in_circle_tag'] );
	
	$html                 = '';
	$image_src            = '';
	$image_alt            = '';
	$circle_style         = '';
	$border_class         = '';
	$text_in_circle_style = '';
	$icon_style           = '';
	$title_style          = '';
	$text_style           = '';
	
	if ( $background_color != "" ) {
		if ( $background_transparency != "" ) {
			$bg_color     = bridge_qode_hex2rgb( $background_color );
			$circle_style .= "background-color: rgba(" . esc_attr( $bg_color[0] ) . "," . esc_attr( $bg_color[1] ) . "," . esc_attr( $bg_color[2] ) . "," . esc_attr( $background_transparency ) . ");";
		} else {
			$circle_style .= "background-color: " . esc_attr( $background_color ) . ";";
		}
	}
	
	if ( $border_color != "" ) {
		$circle_style .= "border-color: " . esc_attr( $border_color ) . ";";
	}
	if ( intval( $border_width ) > 5 ) {
		$border_class = " big_border";
	}
	if ( $border_width != "" ) {
		$circle_style .= "border-width: " . esc_attr( $border_width ) . "px;";
	}
	
	if ( $text_in_circle_color != "" ) {
		$text_in_circle_style .= "color: " . esc_attr( $text_in_circle_color ) . ";";
	}
	
	if ( $text_in_circle_font_weight != '' ) {
		$text_in_circle_style .= 'font-weight: ' . esc_attr( $text_in_circle_font_weight ) . ';';
	}
	
	if ( $font_size != "" ) {
		$text_in_circle_style .= "font-size: " . esc_attr( $font_size ) . "px;";
	}
	
	if ( $icon_color != "" ) {
		$icon_style .= "color: " . esc_attr( $icon_color ) . ";";
	}
	
	if ( $title_color != "" ) {
		$title_style .= "color: " . esc_attr( $title_color ) . ";";
	}
	
	if ( $text_color != "" ) {
		$text_style .= "color: " . esc_attr( $text_color ) . ";";
	}
	
	$html .= '<li class="q_circle_outer">';
	
	if ( $link != "" ) {
		$html .= '<a itemprop="url" href="' . esc_url( $link ) . '" target="' . esc_attr( $link_target ) . '">';
	}
	
	$html .= '<span class="q_circle_inner' . esc_attr( $border_class ) . '"><span class="q_circle_inner2" style="' . esc_attr( $circle_style ) . '">';
	
	if ( $type == "image_type" ) {
		
		if ( is_numeric( $image ) ) {
			$image_src = wp_get_attachment_url( $image );
			$image_alt = get_post_meta(
				$image,
				'_wp_attachment_image_alt',
				true
			);
		}
		
		if ( $image_src != "" ) {
			$html .= '<img itemprop="image" class="q_image_in_circle" src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $image_alt ) . '" />';
		}
		
	} else if ( $type == "icon_type" ) {
		$html .= '<i class="fa ' . esc_attr( $icon ) . ' ' . esc_attr( $size ) . '" style="' . esc_attr( $icon_style ) . '"></i>';
	} else if ( $type == "text_type" ) {
		$html .= '<' . esc_attr( $text_in_circle_tag ) . ' class="q_text_in_circle" style="' . esc_attr( $text_in_circle_style ) . '">' . esc_html( $text_in_circle ) . '</' . esc_attr( $text_in_circle_tag ) . '>';
	}
	
	$html .= '</span></span>';
	
	if ( $link != "" ) {
		$html .= '</a>';
	}
	
	if ( $title != "" || $text != "" ) {
		$html .= '<div class="q_circle_text_holder">';
		
		if ( $title != "" ) {
			$html .= '<' . esc_attr( $title_tag ) . ' class="q_circle_title" style="' . esc_attr( $title_style ) . '">' . esc_html( $title ) . '</' . esc_attr( $title_tag ) . '>';
		}
		
		if ( $text != "" ) {
			$html .= '<p class="q_circle_text" style="' . esc_attr( $text_style ) . '">' . esc_html( $text ) . '</p>';
		}
		
		$html .= '</div>';
	}
	
	$html .= '</li>';
	
	echo bridge_qode_get_module_part( $html );
