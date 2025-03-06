<?php
	$html = "";
	$testimonials_array = array();
	
	$args = array(
		'post_type'      => 'testimonials',
		'orderby'        => esc_attr( $order_by ),
		'order'          => esc_attr( $order ),
		'posts_per_page' => 8
	);
	
	if ( ! empty( $category ) ) {
		$args['testimonials_category'] = esc_attr( $category );
	}
	
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
	
	// Get correct heading value. If provided heading isn't valid, get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : esc_html($deafult_args['title_tag']);
	$main_title_tag = (in_array($main_title_tag, $headings_array)) ? $main_title_tag : esc_html($deafult_args['main_title_tag']);
	
	$title_style = array();
	if ( ! empty( $title_size ) ) {
		$valid_title_size = strstr(
			$title_size,
			'px',
			true
		) ? $title_size : $title_size . 'px';
		$title_style[] = "font-size: " . esc_attr( $valid_title_size );
	}
	
	
	$main_title_style = array();
	if ( ! empty( $main_title_size ) ) {
		$valid_title_size = strstr(
			$main_title_size,
			'px',
			true
		) ? $main_title_size : $main_title_size . 'px';
		$main_title_style[] = "font-size: " . esc_attr( $valid_title_size );
	}
	
	$testimonial_item_style = array();
	if ( ! empty( $background_color ) ) {
		$testimonial_item_style[] = "background-color: " . esc_attr( $background_color );
	}
	
	$author_style = array();
	if ( ! empty( $author_size ) ) {
		$valid_author_size = strstr($author_size, 'px', true) ? $author_size : $author_size . 'px';
		$author_style[] = "font-size: " . esc_attr( $valid_author_size );
	}
	
	// Button HTML generation
	$button_html = '';
	if ( ! empty( $button_text ) ) {
		$params = array(
			'text'   => esc_html( $button_text ),
			'link'   => ! empty( $button_link ) ? esc_url( $button_link ) : '',
			'target' => ! empty( $link_target ) ? esc_attr( $link_target ) : ''
		);
		if ( ! empty( $button_bckg_color ) ) {
			$params['color']            = '#fff';
			$params['background_color'] = esc_attr( $button_bckg_color );
			$params['border_color']     = esc_attr( $button_bckg_color );
		}
		$button_html .= bridge_qode_execute_shortcode('button', $params);
	}
	
	// Main Block Header HTML generation
	$main_block_header = '<div class="testimonial_content"><div class="testimonial_content_holder"><div class="testimonial_content_inner">';
	if ( ! empty( $main_title ) ) {
		$main_block_header .= '<' . esc_html( $main_title_tag ) . ' class="testimonials_header_title" ' . bridge_qode_get_inline_style( $main_title_style ) . '>' . esc_html( $main_title ) . '</' . esc_html( $main_title_tag ) . '>';
		$main_block_header .= '<div class="testimonials_sep"></div>';
	}
	if ( ! empty( $description ) ) {
		$main_block_header .= '<p class="testimonials_header_desc">' . esc_html( $description ) . '</p>';
	}
	$main_block_header .= $button_html;
	$main_block_header .= '</div></div></div>';
	
	// Query for testimonials
	$single = '';
	$query = new WP_Query($args);
	if ($query->have_posts()) {
		while ($query->have_posts()) {
			$query->the_post();
			$single = '';
			
			$title = get_the_title();
			$author = get_post_meta(get_the_ID(), "qode_testimonial-author", true);
			$text = get_post_meta(get_the_ID(), "qode_testimonial-text", true);
			$testimonial_author_image = wp_get_attachment_image_src(get_post_thumbnail_id(), "full");
			
			$single .= '<div id="testimonials' . esc_attr( get_the_ID() ) . '" class="testimonial_content">';
			$single .= '<div class="testimonial_content_holder">';
			$single .= '<div class="testimonial_content_inner">';
			
			if ( $author_image == "yes" && ! empty( $testimonial_author_image[0] ) ) {
				$single .= '<div class="testimonial_image_holder">';
				$single .= '<img itemprop="image" src="' . esc_url( $testimonial_author_image[0] ) . '" />';
				$single .= '</div>';
			}
			
			$single .= '<' . esc_html( $title_tag ) . ' itemprop="name" class="testimonial_title" ' . bridge_qode_get_inline_style( $title_style ) . '>' . esc_html( $title ) . '</' . esc_html( $title_tag ) . '>';
			$single .= '<div class="testimonials_sep"></div>';
			
			$single .= '<div class="testimonial_text_holder"><div class="testimonial_text_inner">';
			$single .= '<p>' . trim($text) . '</p>';
			$single .= '<h6 class="testimonial_author" ' . bridge_qode_get_inline_style( $author_style ) . '>' . esc_html( $author ) . '</h6>';
			$single .= '</div></div>';
			
			$single .= '</div></div></div>';
			
			$testimonials_array[] = $single;
		}
	} else {
		$html .= esc_html__('Sorry, no posts matched your criteria.', 'bridge-core');
	}
	wp_reset_postdata();
	
	// Display testimonials in a masonry layout if 8 or more are available
	if ( count( $testimonials_array ) >= 8 ) {
		$html .= "<div class='testimonials_masonry_holder clearfix'>";
		
		$html .= "<div class='testimonials_block tstm_block_1'>";
		$html .= "<div class='testimonials_item testimonials_header' " .  bridge_qode_get_inline_style( $testimonial_item_style ) . ">";
		$html .= $main_block_header;
		$html .= "</div>";
		$html .= "<div class='testimonials_item' " . bridge_qode_get_inline_style( $testimonial_item_style ) . ">" . $testimonials_array[0] . "</div>";
		$html .= "<div class='testimonials_item' " . bridge_qode_get_inline_style( $testimonial_item_style ) . ">" . $testimonials_array[1] . "</div>";
		$html .= "</div>"; // close tstm_block_1
		
		$html .= "<div class='testimonials_block tstm_block_2'>";
		$html .= "<div class='testimonials_item' " .  bridge_qode_get_inline_style( $testimonial_item_style ) . ">" . $testimonials_array[2] . "</div>";
		$html .= "<div class='testimonials_item' " .  bridge_qode_get_inline_style( $testimonial_item_style ) . ">" . $testimonials_array[3] . "</div>";
		$html .= "<div class='testimonials_item tstm_item_large' " .  bridge_qode_get_inline_style( $testimonial_item_style ) . ">" . $testimonials_array[4] . "</div>";
		$html .= "</div>"; // close tstm_block_2
		
		$html .= "<div class='testimonials_block tstm_block_3'>";
		$html .= "<div class='testimonials_item tstm_item_large' " .  bridge_qode_get_inline_style( $testimonial_item_style ) . ">" . $testimonials_array[5] . "</div>";
		$html .= "<div class='testimonials_item' " .  bridge_qode_get_inline_style( $testimonial_item_style ) . ">" . $testimonials_array[6] . "</div>";
		$html .= "<div class='testimonials_item' " .  bridge_qode_get_inline_style( $testimonial_item_style ) . ">" . $testimonials_array[7] . "</div>";
		$html .= "</div>"; // close tstm_block_3
		
		$html .= '</div>'; // close testimonials_masonry_holder
	}
	
	echo bridge_qode_get_module_part($html);
