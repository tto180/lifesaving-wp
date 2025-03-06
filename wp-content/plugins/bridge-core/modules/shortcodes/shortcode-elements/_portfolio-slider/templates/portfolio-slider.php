<?php
	
	global $portfolio_project_id;
	global $bridge_qode_options;
	$portfolio_qode_like = "on";
	if (isset($bridge_qode_options['portfolio_qode_like'])) {
		$portfolio_qode_like = $bridge_qode_options['portfolio_qode_like'];
	}
	
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
	
	// Get correct heading value. If provided heading isn't valid get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? esc_attr($title_tag) : esc_attr($args['title_tag']);
	
	$html = "";
	$data_attr = "";
	
	if ($number_of_items !== '') {
		$data_attr .= " data-number_of_items='" . esc_attr($number_of_items) . "' ";
	}
	
	if (!empty($enable_autoplay) && 'yes' === $enable_autoplay) {
		$data_attr .= " data-autoplay='yes' ";
	}
	
	$html .= "<div class='portfolio_slider_holder clearfix'><div class='portfolio_slider' " . $data_attr . " ><ul class='portfolio_slides'>";
	
	if ( $category == "" ) {
		$q = array(
			'post_type'      => 'portfolio_page',
			'orderby'        => esc_attr( $order_by ),
			'order'          => esc_attr( $order ),
			'posts_per_page' => esc_attr( $number )
		);
	} else {
		$q = array(
			'post_type'          => 'portfolio_page',
			'portfolio_category' => esc_attr( $category ),
			'orderby'            => esc_attr( $order_by ),
			'order'              => esc_attr( $order ),
			'posts_per_page'     => esc_attr( $number )
		);
	}
	
	$project_ids = null;
	if ($selected_projects != "") {
		$project_ids = explode(",", esc_attr($selected_projects));
		$q['post__in'] = $project_ids;
	}
	
	$query = new WP_Query($q);
	
	if ($query->have_posts()) : $postCount = 0; while ($query->have_posts()) : $query->the_post();
		
		$title = esc_html(get_the_title());
		$terms = wp_get_post_terms(get_the_ID(), 'portfolio_category');
		
		// Get proper image size
		switch ($image_size) {
			case 'landscape':
				$thumb_size = 'portfolio-landscape';
				break;
			case 'portfolio_slider':
				$thumb_size = 'portfolio_slider';
				break;
			case 'portrait':
				$thumb_size = 'portfolio-portrait';
				break;
			case 'square':
				$thumb_size = 'portfolio-square';
				break;
			default:
				$thumb_size = 'full';
				break;
		}
		
		$featured_image_array = wp_get_attachment_image_src(get_post_thumbnail_id(), $thumb_size);
		
		$large_image = get_post_meta(get_the_ID(), 'qode_portfolio-lightbox-link', true);
		if (empty($large_image)) {
			$large_image = $featured_image_array[0];
		}
		
		$custom_portfolio_link = get_post_meta(get_the_ID(), 'qode_portfolio-external-link', true);
		$portfolio_link = $custom_portfolio_link !== "" ? esc_url($custom_portfolio_link) : get_permalink();
		
		$custom_portfolio_link_target = get_post_meta(get_the_ID(), 'qode_portfolio-external-link-target', true);
		$target = !empty($custom_portfolio_link) ? esc_attr($custom_portfolio_link_target) : '_self';
		
		$html .= "<li class='item'>";
		
		$html .= "<div class='image_holder'>";
		$html .= "<span class='image'>";
		$html .= "<span class='image_pixel_hover'></span>";
		$html .= "<a itemprop='url' href='" .  esc_url( $portfolio_link ) . "' target='" . esc_attr( $target ) . "'>";
		$html .= get_the_post_thumbnail(get_the_ID(), $thumb_size);
		$html .= "</a>";
		$html .= "</span>"; /* close span.image */
		
		$html .= "<div class='hover_feature_holder'>";
		$html .= '<div class="hover_feature_holder_outer">';
		$html .= '<div class="hover_feature_holder_inner">';
		$html .= '<' . esc_attr( $title_tag ) . ' itemprop="name" class="portfolio_title entry_title"><a itemprop="url" href="' . esc_url( $portfolio_link ) . '" target="' . esc_attr( $target ) . '">' . wp_kses_post( $title ) . '</a></' . esc_attr( $title_tag ) . '>';
		$separator_class = ( $separator == "no" ) ? " transparent" : "";
		
		$html .= '<span class="separator small' . esc_attr($separator_class) . '"></span>';
		$html .= '<div class="project_category">';
		
		$k = 1;
		foreach ($terms as $term) {
			$html .= esc_html($term->name);
			if (count($terms) != $k) {
				$html .= ', ';
			}
			$k++;
		}
		$html .= '</div>'; /* close div.project_category */
		
		if ($lightbox == "yes") {
			$html .= "<a itemprop='image' class='lightbox qbutton white small' title='" . esc_attr($title) . "' href='" . esc_url($large_image) . "' data-rel='prettyPhoto[portfolio_slider]'>" . esc_html__('zoom', 'bridge-core') . "</a>";
		}
		if ($hide_button !== 'yes') {
			$html .= '<a itemprop="url" href="' . esc_url($portfolio_link) . '" target="' . esc_attr($target) . '" class="qbutton white small">' . esc_html__('view', 'bridge-core') . '</a>';
		}
		$html .= '</div>'; /* close div.hover_feature_holder_inner */
		$html .= '</div>'; /* close div.hover_feature_holder_outer */
		$html .= "</div>"; /* close div.hover_feature_holder */
		$html .= "</div>"; /* close div.image_holder */
		
		$html .= "</li>";
		
		$postCount++;
	
	endwhile;
	
	else:
		$html .= esc_html__('Sorry, no posts matched your criteria.', 'bridge-core');
	endif;
	
	wp_reset_postdata();
	
	$html .= "</ul>";
	if ( $enable_navigation ) {
		$html .= '<ul class="caroufredsel-direction-nav"><li><a id="caroufredsel-prev" class="caroufredsel-prev" href="#"><div><i class="fa fa-angle-left"></i></div></a></li><li><a class="caroufredsel-next" id="caroufredsel-next" href="#"><div><i class="fa fa-angle-right"></i></div></a></li></ul>';
	}
	$html .= "</div></div>";
	
	echo bridge_qode_get_module_part( $html );
