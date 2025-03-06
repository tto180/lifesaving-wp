<?php
	
	$html = "";
	$pricing_table_classes = '';
	
	$target = empty($target) ? "_self" : esc_attr($target);
	
	if($active == "yes") {
		$pricing_table_classes .= ' active';
	}
	if(!empty($type)) {
		$pricing_table_classes .= ' qode_pricing_table_' . esc_attr($type);
	}
	if($type == 'advanced'){
		
		$new_content = preg_replace('#^<\/p>|<p>$#', '', $content);
		
		$html .= '<div class="q_price_table ' . esc_attr($pricing_table_classes) . '">';
		
		if(!empty($image)) {
			
			if ( is_numeric( $image ) ) {
				$image_src = esc_url( wp_get_attachment_url( $image ) );
				$image_alt = esc_attr(
					get_post_meta(
						$image,
						'_wp_attachment_image_alt',
						true
					)
				);
			}
			
			$html .= '<div class="qode_pt_image">';
			$html .= '<img src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $image_alt ) . '" />';
			$html .= '</div>';
		}
		
		$html .= '<div class="price_table_inner">';
		$html .= '<div class="qode_price_table_prices">';
		$html .= '<div class="qode_price_table_prices_inner">';
		$html .= '<span class="price">' . esc_html( $price ) . '</span>';
		$html .= '<sup class="value">' . esc_html( $currency ) . '</sup>';
		$html .= '<span class="mark">' . esc_html( $price_period ) . '</span>';
		$html .= '</div>'; // close div.price_table_prices_inner
		$html .= '</div>'; // close div.price_table_prices
		$html .= '<ul class="qode_pricing_table_text">';
		
		if ( ! empty( $subtitle ) ) {
			$html .= '<li class="cell qode_pt_subtitle">';
			$html .= '<span>' . esc_html( $subtitle ) . '</span>';
			$html .= '</li>';
		}
		
		$html .= "<li class='cell qode_pt_title'>";
		$html .= '<' . esc_attr( $title_tag ) . '>' . esc_html( $title ) . '</' . esc_attr( $title_tag ) . '>';
		$html .= '</li>';
		
		if ( ! empty( $short_info ) ) {
			$html .= "<li class='cell qode_pt_short_info'>";
			$html .= '<span>' . esc_html( $short_info ) . '</span>';
			$html .= '</li>';
		}
		
		$html .= '<li class="pricing_table_content">';
		$html .= do_shortcode( $new_content );
		$html .= '</li>';
		
		if($show_button == 'yes') {
			$html .= '<li class="price_button">';
			$html .= '<a itemprop="url" class="qbutton ' . esc_attr( $button_size ) . '" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">' . esc_html( $button_text ) . '</a>';
			$html .= '</li>'; // close li.price_button
		}
		
		$html .= '</ul>';
		$html .= '</div>'; // close div.price_table_inner
		
		if(!empty($additional_info)) {
			$html .= "<div class='qode_pt_additional_info'>";
			$html .= '<span class="qode_pt_icon icon-basic-lightbulb"></span>';
			$html .= esc_html($additional_info);
			$html .= '</div>';
		}
		
		$html .= '</div>'; // close div.q_price_table
		
	} else {
		
		$html .= "<div class='q_price_table " . esc_attr($pricing_table_classes) . "'>";
		$html .= "<div class='price_table_inner'>";
		
		if($active == 'yes') {
			$html .= "<div class='active_text'><span>" . esc_html($active_text) . "</span></div>";
		}
		
		$html .= "<ul>";
		
		if(empty($title_tag_standard)){
			$html .= "<li class='cell table_title'><h3 class='title_content'>" . esc_html($title) . "</h3>";
		} else {
			$html .= "<li class='cell table_title'><" . esc_attr($title_tag_standard) . " class='qode_title_content_new'>" . esc_html($title) . "</" . esc_attr($title_tag_standard) . ">";
		}
		
		$html .= "<li class='prices'>";
		$html .= "<div class='price_in_table'>";
		$html .= "<sup class='value'>" . esc_html( $currency ) . "</sup>";
		$html .= "<span class='price'>" . esc_html( $price ) . "</span>";
		$html .= "<span class='mark'>" . esc_html( $price_period ) . "</span>";
		$html .= "</div>"; // close div.price_in_table
		$html .= "</li>"; // close li.prices
		
		$html .= "<li class='pricing_table_content'>";
		$html .= do_shortcode( $content );
		$html .= "</li>";
		
		if($show_button == 'yes') {
			$html .= "<li class='price_button'>";
			$html .= "<a itemprop='url' class='qbutton white " . esc_attr($button_size) . "' href='" . esc_url($link) . "' target='" . esc_attr($target) . "'>" . esc_html($button_text) . "</a>";
			$html .= "</li>"; // close li.price_button
		}
		
		$html .= "</ul>";
		$html .= "</div>"; // close div.price_table_inner
		$html .= "</div>"; // close div.q_price_table
	}
	
	echo bridge_qode_get_module_part($html);
