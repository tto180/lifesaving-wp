<?php
	
	$heading_styles = '';
	
	if ( ! empty( $title_color ) ) {
		$heading_styles .= "color: " . esc_attr( $title_color ) . ";";
	}
	
	if ( ! empty( $background_color ) ) {
		$heading_styles .= " background-color: " . esc_attr( $background_color ) . ";";
	}
	
	if ( empty( $title_tag ) ) {
		$title_tag = 'h5';
	}
	
	$output = '';
	
	$output .= "\n\t\t\t\t" . '<' . esc_attr($title_tag) . ' class="clearfix title-holder" style="' . esc_attr($heading_styles) . '">';
	$output .= '<span class="accordion_mark left_mark"><span class="accordion_mark_icon"></span></span><span class="tab-title">' . esc_html($title) . '</span>';
	
	$output .= '<span class="accordion_mark right_mark"><span class="accordion_mark_icon"></span></span>';
	
	$output .= '</' . esc_attr($title_tag) . '>';
	$output .= "\n\t\t\t\t" . '<div ' . (isset($el_id) && !empty($el_id) ? "id='" . esc_attr($el_id) . "'" : "") . ' class="accordion_content no_icon">';
	$output .= "\n\t\t\t" . '<div class="accordion_content_inner">';
	$output .= (trim($content) === '') ? esc_html__('Empty section. Edit page to add content here.', 'bridge-core') : "\n\t\t\t\t" . $content;
	$output .= "\n\t\t\t" . '</div>';
	$output .= "\n\t\t\t\t" . '</div>' . "\n";
	
	echo bridge_qode_get_module_part($output);
