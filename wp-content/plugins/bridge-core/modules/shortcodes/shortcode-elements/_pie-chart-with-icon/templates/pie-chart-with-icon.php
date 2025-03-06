<?php
	
	$qodeIconCollections = bridge_qode_return_icon_collections();
	
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
	
	// Get correct heading value. If provided heading isn't valid, get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : esc_attr($args['title_tag']);
	
	$html = '';
	$html .= '<div class="q_pie_chart_with_icon_holder"><div class="q_percentage_with_icon" data-percent="' . esc_attr($percent) . '" data-linewidth="' . esc_attr($line_width) . '" data-active="' . esc_attr($active_color) . '" data-noactive="' . esc_attr($noactive_color) . '">';
	
	if (!empty($icon_pack)) {
		if ($qodeIconCollections->getIconCollectionParamNameByKey($icon_pack) && !empty(${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)})) {
			$icon_style = !empty($icon_color) ? 'color: ' . esc_attr($icon_color) . ';' : '';
			$icon_class = "qode_pie_chart_icon_element " . esc_attr($icon_size);
			
			$html .= $qodeIconCollections->getIconHTML(
				${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)},
				$icon_pack,
				array('icon_attributes' => array('style' => $icon_style, 'class' => $icon_class))
			);
		}
	} else {
		// Default to Font Awesome icon if no valid icon pack is specified
		$html .= '<i class="fa ' . esc_attr($icon) . ' ' . esc_attr($icon_size) . '"';
		if (!empty($icon_color)) {
			$html .= ' style="color: ' . esc_attr($icon_color) . ';"';
		}
		$html .= '></i>';
	}
	
	$html .= '</div><div class="pie_chart_text">';
	if (!empty($title)) {
		$html .= '<' . esc_attr($title_tag) . ' class="pie_title"';
		if (!empty($title_color)) {
			$html .= ' style="color: ' . esc_attr($title_color) . ';"';
		}
		$html .= '>' . esc_html($title) . '</' . esc_attr($title_tag) . '>';
	}
	
	if (!empty($text)) {
		$html .= '<p ';
		if (!empty($text_color)) {
			$html .= ' style="color: ' . esc_attr($text_color) . ';"';
		}
		$html .= '>' . esc_html($text) . '</p>';
	}
	
	$html .= "</div></div>";
	echo bridge_qode_get_module_part($html);
