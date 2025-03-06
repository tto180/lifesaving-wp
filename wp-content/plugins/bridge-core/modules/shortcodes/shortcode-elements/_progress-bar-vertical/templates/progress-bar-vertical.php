<?php
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
	
	// Get correct heading value. If provided heading isn't valid, get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? esc_html($title_tag) : esc_html($args['title_tag']);
	
	// Initialize variables
	$html               = "";
	$title_styles       = "";
	$bar_styles         = "";
	$percentage_styles  = "";
	$bar_holder_styles  = "";
	
	// Generate styles
	if ($title_color != "") {
		$title_styles .= "color:" . esc_attr($title_color) . ";";
	}
	
	if ($title_size != "") {
		$title_styles .= "font-size:" . esc_attr($title_size) . "px;";
	}
	
	// Generate bar holder gradient styles
	if ($background_color != "") {
		$bar_holder_styles .= "background-color: " . esc_attr($background_color) . ";";
	}
	
	if ($border_radius != "") {
		$bar_holder_styles .= "border-radius: " . esc_attr($border_radius) . "px " . esc_attr($border_radius) . "px 0 0;";
	}
	
	// Generate bar gradient styles
	if ($bar_color != "") {
		$bar_styles .= "background-color: " . esc_attr($bar_color) . ";";
	}
	
	if ($bar_border_color != "") {
		$bar_styles .= "border: 1px solid " . esc_attr($bar_border_color) . ";";
	}
	
	if ($percentage_text_size != "") {
		$percentage_styles .= "font-size: " . esc_attr($percentage_text_size) . "px;";
	}
	
	if ($percent_color != "") {
		$percentage_styles .= "color: " . esc_attr($percent_color) . ";";
	}
	
	// Generate HTML output
	$html .= "<div class='q_progress_bars_vertical'>";
	$html .= "<div class='progress_content_outer' style='" . esc_attr($bar_holder_styles) . "'>";
	$html .= "<div data-percentage='" . esc_attr( $percent ) . "' class='progress_content' style='" . esc_attr( $bar_styles ) . "'></div>";
	$html .= "</div>"; // close progress_content_outer
	$html .= "<" . esc_html($title_tag) . " class='progress_title' style='" . esc_attr( $title_styles ) . "'>" . esc_html( $title ) . "</" . esc_html($title_tag) . ">";
	$html .= "<span class='progress_number' style='" . esc_attr($percentage_styles) . "'>";
	$html .= "<span>" . esc_html($percent) . "</span>%";
	$html .= "</span>"; // close progress_number
	$html .= "<span class='progress_text'>" . esc_html( $text ) . "</span>"; // close progress_text
	$html .= "</div>"; // close progress_bars_vertical
	
	echo bridge_qode_get_module_part($html);