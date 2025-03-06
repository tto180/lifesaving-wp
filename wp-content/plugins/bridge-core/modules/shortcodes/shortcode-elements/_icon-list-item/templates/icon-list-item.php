<?php
	
	$qodeIconCollections = bridge_qode_return_icon_collections();
	
	$html           = '';
	$icon_style     = "";
	$icon_classes   = array('qode-ili-icon-holder');
	$title_style    = "";
	$add_icon       = '';
	
	// Append icon type to classes
	$icon_classes[] = esc_attr( $icon_type );
	
	// Set title styles based on provided parameters
	if ( ! empty( $title_color ) ) {
		$title_style .= "color:" . esc_attr( $title_color ) . ";";
	}
	if ( ! empty( $title_size ) ) {
		$title_style .= "font-size: " . esc_attr( $title_size ) . "px;";
	}
	if ( ! empty( $title_font_weight ) ) {
		$title_style .= "font-weight: " . esc_attr( $title_font_weight ) . ";";
	}
	if ( ! empty( $margin_bottom ) ) {
		$title_style .= "margin-bottom: " . esc_attr( $margin_bottom ) . "px;";
	}
	
	// Set the icon pack
	$icon_pack = ! empty( $icon_pack ) ? $icon_pack : 'font_awesome';
	
	// Generate icon HTML if the icon exists
	if ($qodeIconCollections->getIconCollectionParamNameByKey($icon_pack) && !empty(${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)})) {
		$icon_style = "";
		
		// Set icon styles
		if ( ! empty( $icon_size ) ) {
			$icon_style .= 'font-size: ' . esc_attr( $icon_size ) . 'px;';
		}
		if ( ! empty( $icon_color ) ) {
			$icon_style .= 'color: ' . esc_attr( $icon_color ) . ';';
		}
		if ( ! empty( $icon_background_color ) ) {
			$icon_style .= "background-color: " . esc_attr( $icon_background_color ) . ";";
		}
		if ( ! empty( $icon_border_color ) ) {
			$icon_style .= "border-color:" . esc_attr( $icon_border_color ) . ";";
			$icon_style .= "border-style: solid;";
			$icon_style .= "border-width: 1px;";
		}
		
		// Get the icon HTML
		$add_icon .= $qodeIconCollections->getIconHTML(
			${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)},
			$icon_pack,
			array('icon_attributes' => array('style' => $icon_style, 'class' => implode(' ', $icon_classes)))
		);
	}
	
	// Construct the final HTML output
	$html .= '<div class="q_icon_list">';
	$html .= $add_icon;
	$html .= '<p style="' . esc_attr( $title_style ) . '">' . esc_html( $title ) . '</p>';
	$html .= '</div>';
	
	// Output the constructed HTML
	echo bridge_qode_get_module_part($html);
