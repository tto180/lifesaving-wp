<?php
	global $bridge_qode_options;
	global $qodeIconCollections;
	
	$headings_array = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
	
	// Get correct heading value. If provided heading isn't valid get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? esc_html($title_tag) : esc_html($args['title_tag']);
	
	// Initialize variables
	$html = "";
	$title_holder_style = "";
	$title_style = "";
	$title_classes = "";
	$icon_style = "";
	$content_style = "";
	$service_table_holder_style = "";
	$service_table_style = "";
	$background_image_src = "";
	
	// Set background styles
	if ($title_background_type == "background_color_type") {
		if ($title_background_color != "") {
			$title_holder_style .= "background-color: " . esc_attr($title_background_color) . ";";
		}
	} else {
		if (is_numeric($background_image)) {
			$background_image_src = wp_get_attachment_url($background_image);
		} else {
			$background_image_src = esc_url($background_image);
		}
		
		if (!empty($bridge_qode_options['first_color'])) {
			$service_table_style = esc_attr($bridge_qode_options['first_color']);
		} else {
			$service_table_style = "#00c6ff";
		}
		
		if ($background_image != "") {
			$title_holder_style .= "background-image: url(" . esc_url($background_image_src) . ");";
		}
		
		if ($background_image_height != "") {
			$title_holder_style .= "height: " . esc_attr($background_image_height) . "px;";
		}
	}
	
	// Set border styles
	if ( $border == "yes" ) {
		$service_table_holder_style .= " style='border-style:solid;";
		if ( $border_width != "" ) {
			$service_table_holder_style .= "border-width:" . esc_attr( $border_width ) . "px;";
		}
		if ( $border_color != "" ) {
			$service_table_holder_style .= "border-color:" . esc_attr( $border_color ) . ";";
		}
		$service_table_holder_style .= "'";
	}
	
	// Set title color
	if ($title_color != "") {
		$title_style .= "color: " . esc_attr($title_color) . ";";
	}
	
	$title_classes .= esc_attr($title_background_type);
	$icon_html = '';
	$icon_classes = array();
	$icon_classes[] = esc_attr($icon_size);
	$icon_pack = $icon_pack == '' ? 'font_awesome' : esc_attr($icon_pack);
	if ($qodeIconCollections->getIconCollectionParamNameByKey($icon_pack) && ${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)} != "") {
		
		if ($custom_size != "") {
			$icon_style .= "font-size: " . esc_attr($custom_size) . "px;";
		}
		
		if ($icon_color != "") {
			$icon_style .= "color: " . esc_attr($icon_color) . " !important;"; //important because of the first color importance
		}
		
		if ($qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)) {
			$icon_html .= $qodeIconCollections->getIconHTML(
				${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)},
				$icon_pack,
				array('icon_attributes' => array('style' => esc_attr($icon_style), 'class' => implode(' ', $icon_classes)))
			);
		}
	}
	
	if ($content_background_color != "") {
		$content_style .= "background-color: " . esc_attr($content_background_color) . ";";
	}
	
	// Generate HTML output
	$html .= "<div class='service_table_holder'" . esc_attr( $service_table_holder_style ) . "><ul class='service_table_inner'>";
	
	$html .= "<li class='service_table_title_holder " . esc_attr( $title_classes ) . "' style='" . esc_attr($title_holder_style) . "'>";
	
	$html .= "<div class='service_table_title_inner'><div class='service_table_title_inner2'>";
	
	if ( $title != "" ) {
		$html .= "<" . esc_attr( $title_tag ) . " class='service_title' style='" . esc_attr($title_style) . "'>" . esc_html($title) . "</" . esc_attr($title_tag) . ">";
	}
	
	$html .= $icon_html;
	$html .= "</div></div>";
	
	$html .= "</li>";
	
	$html .= "<li class='service_table_content' style='" . esc_attr($content_style) . "'>";
	
	$html .= do_shortcode($content);
	
	$html .= "</li>";
	
	$html .= "</ul></div>";
	
	echo bridge_qode_get_module_part($html);