<?php
	$html                           = "";
	$testimonial_text_inner_styles  = "";
	$testimonial_p_style			= array();
	$testimonial_name_styles        = "";
	
	if ( ! empty( $text_font_size ) ) {
		$testimonial_p_style[] = "font-size:" . esc_attr( $text_font_size ) . "px";
	}
	if ( ! empty( $text_color ) ) {
		$testimonial_p_style[] = "color:" . esc_attr( $text_color );
	}
	
	if ( $text_color != "" ) {
		$testimonial_text_inner_styles .= "color: " . esc_attr( $text_color ) . ";";
		$testimonial_name_styles       .= "color: " . esc_attr( $text_color ) . ";";
	}
	
	if ( $author_text_font_weight != '' ) {
		$testimonial_name_styles .= 'font-weight: ' . esc_attr( $author_text_font_weight ) . ';';
	}
	
	if ( $author_text_color != "" ) {
		$testimonial_name_styles .= "color: " . esc_attr( $author_text_color ) . ";";
	}
	
	if ( $author_text_font_size != "" ) {
		$testimonial_name_styles .= "font-size: " . esc_attr( $author_text_font_size ) . "px;";
	}
	
	$args = array(
		'post_type'      => 'testimonials',
		'orderby'        => esc_attr( $order_by ),
		'order'          => esc_attr( $order ),
		'posts_per_page' => (int) $number
	);
	
	if ($category != "") {
		$args['testimonials_category'] = esc_attr( $category );
	}
	
	$html .= "<div class='testimonials_c_holder clearfix " . esc_attr( $navigation_style ) . "'>";
	$html .= '<div class="testimonials_c testimonials_c_carousel" data-show-navigation="' . esc_attr( $show_navigation ) . '" data-animation-speed="' . esc_attr( $animation_speed ) . '" data-auto-rotate-slides="' . esc_attr( $auto_rotate_slides ) . '" data-number-per-slide="' . esc_attr( $number_per_slide ) . '">';
	$html .= '<ul class="slides">';
	
	$query = new WP_Query( $args );
	if ($query->have_posts()) :
		while ($query->have_posts()) : $query->the_post();
			$title = get_the_title();
			$author = get_post_meta(get_the_ID(), "qode_testimonial-author", true);
			$website = get_post_meta(get_the_ID(), "qode_testimonial_website", true);
			$rating = get_post_meta(get_the_ID(), "qode_testimonial_rating", true);
			$text = get_post_meta(get_the_ID(), "qode_testimonial-text", true);
			$testimonial_author_image = wp_get_attachment_image_src(get_post_thumbnail_id(), "thumbnail");
			
			$html .= '<li id="testimonials-c-' . esc_attr( get_the_ID() ) . '" class="testimonial_content">';
			$html .= '<div class="testimonial_content_inner">';
			
			if($author_image == "yes"){
				$html .= '<div class="testimonial_image_holder">';
				$html .= '<img src="' . esc_url( $testimonial_author_image[0] ) . '" />';
				$html .= '</div>';
			}
			if($show_title == "yes" && $title !== ''){
				$html .= '<div class="testimonial_title_holder">';
				$html .= '<' . esc_attr( $title_tag ) . '>' . esc_html( $title ) . '</' . esc_attr( $title_tag ) . '>';
				$html .= '</div>';
			}
			if($show_rating == "yes" && $rating !== ''){
				$html .= '<div class="testimonial_rating_holder">';
				for ($i=1; $i<=5; $i++) {
					$html .= '<span class="testimonial_star_holder"><span class="icon_star' . ($i <= (int)$rating ? '' : '_alt') . '"></span></span>';
				}
				$html .= '</div>';
			}
			$html .= '<div class="testimonial_text_holder">';
			$html .= '<div class="testimonial_text_inner" style="' . esc_attr( $testimonial_text_inner_styles ) . '">';
			$html .= '<p ' . bridge_qode_get_inline_style( $testimonial_p_style ) . '>' . wp_kses_post( trim( $text ) ) . '</p>';
			
			$html .= '<p class="testimonial_author" style="' . esc_attr( $testimonial_name_styles ) . '">' . esc_html( $author );
			
			if ( $website != "" ) {
				$html .= '<span class="author_company_divider"> - </span><span class="author_company">' . wp_kses_post( $website ) . '</span>';
			}
			
			$html .= '</p>';
			$html .= '</div>'; //close testimonial_text_inner
			$html .= '</div>'; //close testimonial_text_holder
			
			$html .= '</div>'; //close testimonial_content_inner
			$html .= '</li>'; //close testimonial
		endwhile;
	else:
		$html .= esc_html__('Sorry, no posts matched your criteria.', 'bridge-core');
	endif;
	
	wp_reset_postdata();
	$html .= '</ul>'; //close slides
	$html .= '</div>';
	$html .= '</div>';
	
	echo bridge_qode_get_module_part( $html );
