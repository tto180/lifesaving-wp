<?php
	$headings_array = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p');
	
	//get correct heading value. If provided heading isn't valid get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : $args['title_tag'];
	
	//init variables
	$html                           = "";
	$progress_title_holder_styles   = "";
	$number_styles                  = "";
	$outer_progress_styles          = "";
	$percentage_styles              = "";
	
	//generate styles
	if ( $title_color != "" ) {
		$progress_title_holder_styles .= "color: " . esc_attr( $title_color ) . ";";
	}
	
	if ( $percent_color != "" ) {
		$number_styles .= "color: " . esc_attr( $percent_color ) . ";";
	}
	
	if ( $percent_font_size != "" ) {
		$number_styles .= "font-size: " . esc_attr( $percent_font_size ) . "px;";
	}
	if ( $percent_font_weight != "" ) {
		$number_styles .= "font-weight: " . esc_attr( $percent_font_weight ) . ";";
	}
	if ($height != "") {
		$valid_height = (strstr($height, 'px', true)) ? esc_attr($height) : esc_attr($height) . "px";
		$outer_progress_styles .= "height: " . esc_attr($valid_height) . ";";
		$percentage_styles .= "height: " . esc_attr($valid_height) . ";";
	}
	
	if ($border_radius != "") {
		$border_radius = (strstr($border_radius, 'px', true)) ? esc_attr($border_radius) : esc_attr($border_radius) . "px";
		$outer_progress_styles .= "border-radius: " . esc_attr($border_radius) . ";-moz-border-radius: " . esc_attr($border_radius) . ";-webkit-border-radius: " . esc_attr($border_radius) . ";";
	}
	
	if ($noactive_background_color != "") {
		if ($noactive_background_color_transparency !== '' && ($noactive_background_color_transparency >= 0 && $noactive_background_color_transparency <= 1)) {
			$noactive_background_color = bridge_qode_hex2rgb($noactive_background_color);
			$outer_progress_styles .= "background-color: rgba(" . esc_attr($noactive_background_color[0]) . ", " . esc_attr($noactive_background_color[1]) . ", " . esc_attr($noactive_background_color[2]) . ", " . esc_attr($noactive_background_color_transparency) . ");";
		} else {
			$outer_progress_styles .= "background-color: " . esc_attr($noactive_background_color) . ";";
		}
	}
	
	if ($active_background_color != "") {
		$percentage_styles .= "background-color: " . esc_attr($active_background_color) . ";";
	}
	
	if ($active_border_color != "") {
		$percentage_styles .= "border: 1px solid " . esc_attr($active_border_color) . ";";
	}
	
	$html .= "<div class='q_progress_bar'>";
	$html .= "<{$title_tag} class='progress_title_holder clearfix' style='" . esc_attr($progress_title_holder_styles) . "'>";
	$html .= "<span class='progress_title'>";
	$html .= "<span>" . esc_html($title) . "</span>";
	$html .= "</span>"; //close progress_title
	
	$html .= "<span class='progress_number' style='" . esc_attr($number_styles) . "'>";
	$html .= "<span>0</span>%</span>";
	$html .= "</{$title_tag}>"; //close progress_title_holder
	
	$html .= "<div class='progress_content_outer' style='" . esc_attr($outer_progress_styles) . "'>";
	$html .= "<div data-percentage='" . esc_attr($percent) . "' class='progress_content";
	if ($gradient == 'yes') {
		$html .= " qode-type1-gradient-left-to-right";
	}
	$html .= "' style='" . esc_attr($percentage_styles) . "'>";
	$html .= "</div>"; //close progress_content
	$html .= "</div>"; //close progress_content_outer
	
	$html .= "</div>"; //close progress_bar
	
	echo bridge_qode_get_module_part($html);
