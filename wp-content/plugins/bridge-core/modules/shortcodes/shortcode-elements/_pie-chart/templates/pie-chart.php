<?php
	
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
	
	// Get the correct heading value. If the provided heading isn't valid, get the default one
	$title_tag = in_array($title_tag, $headings_array) ? esc_attr($title_tag) : esc_attr($args['title_tag']);
	$q_percentage_data = array();
	
	if ($percent != '') {
		$q_percentage_data['data-percent'] = esc_attr($percent);
	}
	if ($line_width != '') {
		$q_percentage_data['data-linewidth'] = esc_attr($line_width);
	}
	
	if ($active_color != '') {
		$q_percentage_data['data-active'] = esc_attr($active_color);
	}
	if ($noactive_color != '') {
		$q_percentage_data['data-noactive'] = esc_attr($noactive_color);
	}
	if ($element_appearance != '') {
		$q_percentage_data['data-element-appearance'] = esc_attr($element_appearance);
	}
	
	$html = '';
	$html .= '<div class="q_pie_chart_holder"><div class="q_percentage" ' . bridge_qode_get_inline_attrs($q_percentage_data);
	
	// Add styles if percentage color, font size or font weight is provided
	if ($percentage_color != "" || $percent_font_size != "" || $percent_font_weight != "") {
		$html .= ' style="';
		
		if ($percentage_color != "") {
			$html .= 'color:' . esc_attr($percentage_color) . ';';
		}
		if ($percent_font_size != "") {
			$html .= 'font-size:' . esc_attr($percent_font_size) . 'px;';
		}
		if ($percent_font_weight != "") {
			$html .= 'font-weight:' . esc_attr($percent_font_weight) . ';';
		}
		$html .= '"';
	}
	
	$html .= '><span class="tocounter">' . esc_html($percent) . '</span>%';
	$html .= '</div><div class="pie_chart_text">';
	
	// Add title if it's provided
	if ($title != "") {
		$html .= '<' . $title_tag . ' class="pie_title"';
		if ($title_color != "") {
			$html .= ' style="color: ' . esc_attr($title_color) . ';"';
		}
		$html .= '>' . esc_html($title) . '</' . $title_tag . '>';
	}
	
	$separator_styles = "";
	if ($separator_color != "") {
		$separator_styles .= " style='background-color: " . esc_attr($separator_color) . ";'";
	}
	
	// Add separator if required
	if ($separator == "yes") {
		$html .= '<span class="separator small"' . $separator_styles . '></span>';
	}
	
	// Add text if it's provided
	if ($text != "") {
		$html .= '<p';
		if ($text_color != "") {
			$html .= ' style="color: ' . esc_attr($text_color) . ';"';
		}
		$html .= '>' . esc_html($text) . '</p>';
	}
	
	$html .= "</div></div>";
	
	echo bridge_qode_get_module_part($html);
