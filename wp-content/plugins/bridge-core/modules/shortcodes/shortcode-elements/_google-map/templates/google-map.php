<?php
	
	$html = "";
	$unique_id = rand(0, 100000);
	$holder_id = 'map_canvas_' . esc_attr($unique_id);
	$map_pin = "";
	
	if (!empty($pin)) {
		$map_pin = wp_get_attachment_image_src($pin, 'full', true);
		$map_pin = esc_url($map_pin[0]);
	} else {
		$map_pin = esc_url(get_template_directory_uri() . "/img/pin.png");
	}
	
	$data_attribute = '';
	$addresses_array = array();
	
	if (!empty($address1)) {
		array_push($addresses_array, esc_html($address1));
	}
	if (!empty($address2)) {
		array_push($addresses_array, esc_html($address2));
	}
	if (!empty($address3)) {
		array_push($addresses_array, esc_html($address3));
	}
	if (!empty($address4)) {
		array_push($addresses_array, esc_html($address4));
	}
	if (!empty($address5)) {
		array_push($addresses_array, esc_html($address5));
	}
	
	if (!empty($addresses_array)) {
		$data_attribute .= "data-addresses='[\"" . implode('","', $addresses_array) . "\"]'";
	}
	
	$data_attribute .= " data-custom-map-style='" . esc_attr($custom_map_style) . "'";
	$data_attribute .= " data-color-overlay='" . esc_attr($color_overlay) . "'";
	$data_attribute .= " data-saturation='" . esc_attr($saturation) . "'";
	$data_attribute .= " data-lightness='" . esc_attr($lightness) . "'";
	$data_attribute .= " data-zoom='" . esc_attr($zoom) . "'";
	$data_attribute .= " data-pin='" . esc_url($map_pin) . "'";
	$data_attribute .= " data-unique-id='" . esc_attr($unique_id) . "'";
	$data_attribute .= " data-google-maps-scroll-wheel='" . esc_attr($google_maps_scroll_wheel) . "'";
	$data_attribute .= " data-snazzy-map-style='" . esc_attr($snazzy_map_style) . "'";
	
	if (!empty($map_height)) {
		$data_attribute .= " data-map-height='" . esc_attr($map_height) . "'";
	}
	
	$map_height_style = is_numeric($map_height) ? esc_attr($map_height) . "px" : esc_attr($map_height);
	
	$html .= "<div class='google_map_shortcode_holder' style='height:" . $map_height_style . ";'>";
	$html .= "<div class='qode_google_map' style='height:" . $map_height_style . ";' id='" . esc_attr($holder_id) . "' " . $data_attribute . "></div>";
	
	if ($snazzy_map_style == 'yes') {
		if (isset($is_elementor) && $is_elementor) {
			$snazzy_map_code = str_replace(array("[", "]", '"'), array("`{`", "`}`", '``'), $snazzy_map_code);
		}
		
		$html .= '<input type="hidden" class="qode-snazzy-map" value="' . str_replace('<br />', '', $snazzy_map_code) . '" />';
	}
	
	if ($google_maps_scroll_wheel == "false") {
		$html .= "<div class='google_map_shortcode_overlay'></div>";
	}
	
	$html .= "</div>";
	echo bridge_qode_get_module_part($html);
