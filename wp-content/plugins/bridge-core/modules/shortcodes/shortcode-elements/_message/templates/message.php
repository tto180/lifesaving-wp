<?php
	
	$qodeIconCollections = bridge_qode_return_icon_collections();
	
	// Initialize variables
	$html                   = "";
	$icon_html              = "";
	$message_classes        = "";
	$message_styles         = "";
	$icon_styles            = "";
	$close_button_styles    = "";
	
	// Set message classes based on type
	if ( $type === "with_icon" ) {
		$message_classes .= " with_icon";
	}
	
	// Set message styles
	if ( ! empty( $background_color ) ) {
		$message_styles .= "background-color: " . esc_attr( $background_color ) . ";";
	}
	if ( $border === "yes" ) {
		$message_styles .= "border-style: solid;";
		if ( ! empty( $border_width ) ) {
			$message_styles .= "border-width: " . esc_attr( $border_width ) . "px;";
		}
		if ( ! empty( $border_color ) ) {
			$message_styles .= "border-color: " . esc_attr( $border_color ) . ";";
		}
	}
	if ( ! empty( $icon_color ) ) {
		$icon_styles .= "color: " . esc_attr( $icon_color ) . ";";
	}
	if ( ! empty( $icon_background_color ) ) {
		$icon_styles .= "background-color: " . esc_attr( $icon_background_color ) . ";";
	}
	if ( ! empty( $close_button_color ) ) {
		$close_button_styles .= "color: " . esc_attr( $close_button_color ) . ";";
	}
	
	// Construct HTML for the message
	$html .= "<div class='q_message " . esc_attr( $message_classes ) . "' style='" . esc_attr( $message_styles ) . "'>";
	$html .= "<div class='q_message_inner'>";
	
	// Icon handling
	if ($type === "with_icon") {
		$icon_html .= '<div class="q_message_icon_holder"><div class="q_message_icon"><div class="q_message_icon_inner">';
		if ( ! empty( $custom_icon ) ) {
			if ( is_numeric( $custom_icon ) ) {
				$custom_icon_src = wp_get_attachment_url( $custom_icon );
			} else {
				$custom_icon_src = esc_url( $custom_icon );
			}
			
			$icon_html .= '<img itemprop="image" src="' . esc_url( $custom_icon_src ) . '" alt="">';
		} else {
			if (empty($is_elementor)) {
				$icon_pack = 'font_awesome';
			}
			
			if ($qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)) {
				$icon_html .= $qodeIconCollections->getIconHTML(
					${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)},
					$icon_pack,
					array('icon_attributes' => array('style' => $icon_styles, 'class' => esc_attr( $icon_size )))
				);
			}
		}
		$icon_html .= '</div></div></div>';
	}
	
	$html .= $icon_html;
	
	// Close button
	$html .= "<a href='#' class='close' aria-label='Close message'>";
	$html .= "<i class='fa fa-times' style='" . esc_attr( $close_button_styles ) . "'></i>";
	$html .= "</a>"; // Close a.close
	
	// Message text
	$html .= "<div class='message_text_holder'><div class='message_text'><div class='message_text_inner'>" . do_shortcode( $content ) . "</div></div></div>";
	$html .= "</div></div>"; // Close message text div
	
	// Output the constructed HTML
	echo bridge_qode_get_module_part($html);
