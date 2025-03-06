<?php
	
	$qodeIconCollections = bridge_qode_return_icon_collections();
	
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
	
	// Get correct heading value. If provided heading isn't valid get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : esc_attr($args['title_tag']);
	
	// Initialize variables
	$html                = "";
	$title_styles        = "";
	$subtitle_styles     = "";
	$line_styles         = "";
	$no_icon             = "";
	$icon_styles         = "";
	$shader_style        = "";
	$shader_hover_style   = array();
	$holder_classes      = array('q_image_with_text_over');
	
	if ( $layout_width ) {
		$holder_classes[] = esc_attr( $layout_width );
	}
	
	// Generate styles
	if ( $title_color != "" ) {
		$title_styles .= "color: " . esc_attr( $title_color ) . ";";
	}
	
	if ($title_size != "") {
		$valid_title_size = (strstr($title_size, 'px', true)) ? esc_attr($title_size) : esc_attr($title_size) . 'px';
		$title_styles .= "font-size: " . $valid_title_size . ";";
	}
	
	$icon_html = '';
	$icon_classes = array('icon_holder');
	$icon_classes[] = esc_attr($icon_size);
	$icon_pack = $icon_pack == '' ? 'font_awesome' : esc_attr($icon_pack);
	if ($qodeIconCollections->getIconCollectionParamNameByKey($icon_pack) && ${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)} != "") {
		
		if ($icon_color != "") {
			$bcolor = bridge_qode_hex2rgb($icon_color);
			$icon_styles .= "color: " . esc_attr($icon_color) . ";";
			$icon_styles .= "border-color: rgba(" . esc_attr($bcolor[0]) . "," . esc_attr($bcolor[1]) . "," . esc_attr($bcolor[2]) . ",0.6);";
		}
		
		if ($qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)) {
			$icon_html .= $qodeIconCollections->getIconHTML(
				${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)},
				$icon_pack,
				array('icon_attributes' => array('style' => esc_attr($icon_styles), 'class' => implode(' ', $icon_classes)))
			);
		}
	}
	
	if ( is_numeric( $image ) ) {
		$image_src = wp_get_attachment_url($image);
		$image_alt = get_post_meta($image, '_wp_attachment_image_alt', true);
	} else {
		$image_src = esc_url( $image );
		$image_id  = bridge_qode_get_attachment_id_from_url( $image_src );
		if ( ! empty( $image_id ) ) {
			$image_alt = get_post_meta($image_id, '_wp_attachment_image_alt', true);
		} else {
			$image_alt = esc_html__('Image With Text Over Alt', 'bridge');
		}
	}
	
	if ($image_shader_color !== '') {
		$shader_style = 'style="background-color: ' . esc_attr($image_shader_color) . '"';
	}
	
	if ($image_shader_hover_color !== '') {
		$shader_hover_style[] = 'background-color: ' . esc_attr($image_shader_hover_color);
		$holder_classes[] = 'q_iwto_hover';
	}
	
	if ($icon_html == "") {
		$no_icon = "no_icon";
	}
	
	// Generate output
	$html .= '<div ' . bridge_qode_get_class_attribute(implode(' ', $holder_classes)) . '>';
	$html .= '<div class="shader" ' . $shader_style . '></div>';
	
	if ($image_shader_hover_color != '') {
		$html .= '<div class="shader_hover" ' . bridge_qode_get_inline_style($shader_hover_style) . '></div>';
	}
	
	$html .= '<img itemprop="image" src="' . esc_url($image_src) . '" alt="' . esc_attr($image_alt) . '" />';
	$html .= '<div class="text">';
	
	// Title and subtitle table HTML
	$html .= '<table>';
	$html .= '<tr>';
	$html .= '<td>';
	if ($icon_html != "") {
		$html .= $icon_html;
	}
	$html .= '<' . esc_attr($title_tag) . ' class="caption ' . esc_attr($no_icon) . '" style="' . esc_attr($title_styles) . '">' . esc_html($title) . '</' . esc_attr($title_tag) . '>';
	$html .= '</td>';
	$html .= '</tr>';
	$html .= '</table>';
	
	// Image description table HTML which appears on mouse hover
	$html .= '<table>';
	$html .= '<tr>';
	$html .= '<td>';
	$html .= '<div class="desc">' . do_shortcode($content) . '</div>';
	$html .= '</td>';
	$html .= '</tr>';
	$html .= '</table>';
	
	$html .= '</div>'; // Close text div
	$html .= '</div>'; // Close image_with_text_over
	
	echo bridge_qode_get_module_part($html);