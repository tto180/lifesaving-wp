<?php
	
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
	
	// Get correct heading value. If provided heading isn't valid get the default one
	$title_tag = ( in_array( $title_tag, $headings_array ) ) ? $title_tag : esc_attr( $args['title_tag'] );
	
	if (empty($number_of_posts)) {
		$number_of_posts = -1;
	}
	
	$q = new WP_Query(
		array(
			'orderby'        => esc_attr( $order_by ),
			'order'          => esc_attr( $order ),
			'posts_per_page' => esc_attr( $number_of_posts ),
			'category_name'  => esc_attr( $category )
		)
		);
	
	$columns_number = "";
	switch ($number_of_columns) {
		case 1:
			$columns_number = 'one_column';
			break;
		case 2:
			$columns_number = 'two_columns';
			break;
		case 3:
			$columns_number = 'three_columns';
			break;
		case 4:
			$columns_number = 'four_columns';
			break;
		default:
			break;
	}
	
	$image_size = "portfolio_masonry_with_space";
	switch ($featured_image_size) {
		case 'landscape':
			$image_size = 'portfolio-landscape';
			break;
		case 'portrait':
			$image_size = 'portfolio-portrait';
			break;
		case 'full':
			$image_size = 'full';
			break;
		default:
			$image_size = 'portfolio_masonry_with_space';
			break;
	}
	
	$title_style = array();
	if ( ! empty( $title_color ) ) {
		$title_style[] = "color:" . esc_attr( $title_color );
	}
	
	$separator_style = array();
	if ( ! empty( $separator_color ) ) {
		$separator_style[] = "background-color:" . esc_attr( $separator_color );
	}
	
	$separator_class = '';
	if ($separator_gradient == "yes") {
		$separator_class = 'qode-type1-gradient-left-to-right';
	}
	
	$excerpt_style = array();
	if ( ! empty( $excerpt_color ) ) {
		$excerpt_style[] = "color:" . esc_attr($excerpt_color);
	}
	
	$post_info_style = array();
	if ( ! empty( $post_info_color ) ) {
		$post_info_style[] = "color:" . esc_attr( $post_info_color );
	}
	
	$post_info_holder_style = array();
	if ( ! empty( $post_info_separator_color ) ) {
		$post_info_holder_style[] = "border-color:" . esc_attr( $post_info_separator_color );
	}
	
	$holder_style = array();
	if ( ! empty( $background_color ) ) {
		$holder_style[] = "background-color:" . esc_attr($background_color);
	}
	
	$html = "";
	$html .= "<div class='latest_post_two_holder " . esc_attr($columns_number) . "'>";
	$html .= "<ul>";
	
	$num_of_posts = 0;
	if (intval($number_of_posts) === -1) {
		if (!empty($category)) {
			$category_slugs = explode(',', $category);
			$category_ids = array();
			
			if (is_array($category_slugs) && count($category_slugs) > 0) {
				foreach ($category_slugs as $category_slug) {
					$category = get_category_by_slug(trim($category_slug));
					if (is_object($category)) {
						$category_ids[] = $category->term_id;
					}
				}
			}
			
			$posts = get_posts(
				array(
                   'category' => $category_ids,
                   'numberposts' => -1
			       )
				);
			
			$num_of_posts = count($posts);
		} else {
			$num_of_posts = $q->post_count;
		}
	} else {
		$num_of_posts = $number_of_posts;
	}
	
	while ($q->have_posts() && $q->current_post + 1 < $num_of_posts) : $q->the_post();
		
		$html .= '<li class="clearfix">';
		if ($display_featured_images === "yes") {
			$html .= '<div class="latest_post_two_image">';
			$html .= '<a itemprop="url" href="' . get_permalink() . '">';
			
			if ($featured_image_size !== 'custom' || $image_width == '' || $image_height == '') {
				$html .= get_the_post_thumbnail(get_the_ID(), $image_size);
			} else {
				$html .= bridge_qode_generate_thumbnail(get_post_thumbnail_id(get_the_ID()), null, intval($image_width), intval($image_height));
			}
			$html .= '</a>';
			$html .= '</div>';
		}
		$html .= '<div class="latest_post_two_inner" ' . bridge_qode_get_inline_style( $holder_style ) . '>';
		
		$html .= '<div class="latest_post_two_text">';
		
		$html .= '<' . esc_html($title_tag) . ' itemprop="name" class="latest_post_two_title entry_title"><a itemprop="url" href="' . get_permalink() . '" ' . bridge_qode_get_inline_style( $title_style)  . '>' . get_the_title() . '</a></' . esc_html($title_tag) . '>';
		
		$html .= '<div class="separator small left ' . esc_attr( $separator_class ) . '" ' . bridge_qode_get_inline_style( $separator_style ) . '></div>';
		
		if ( $text_length != '0' ) {
			$excerpt = ($text_length > 0) ? mb_substr(get_the_excerpt(), 0, intval($text_length)) : get_the_excerpt();
			$html .= '<p itemprop="description" class="latest_post_two_excerpt" ' . bridge_qode_get_inline_style( $excerpt_style ) . '>' . esc_html($excerpt) . '</p>';
		}
		
		$html .= '</div>';
		
		$html .= '<div class="latest_post_two_info" ' . bridge_qode_get_inline_style( $post_info_holder_style ) . '>';
		$html .= '<div class="latest_post_two_info_inner" ' . bridge_qode_get_inline_style( $post_info_style ). '>';
		$html .= '<div class="post_info_author">';
		$html .= get_avatar(get_the_author_meta('ID'), 30, '', esc_html__('Author Image', 'bridge-core'));
		$html .= '<span class="post_info_author_name">' . esc_html(get_the_author_meta('display_name')) . '</span>';
		$html .= '</div>';
		
		$html .= '<div itemprop="dateCreated" class="post_info_date entry_date updated">' . esc_html( get_the_time( 'd F, Y' ) ) . '<meta itemprop="interactionCount" content="UserComments: ' . esc_html( get_comments_number( qode_get_page_id() ) ) . '"/></div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</li>';
	
	endwhile;
	wp_reset_postdata();
	
	$html .= "</ul></div>";
	echo bridge_qode_get_module_part($html);