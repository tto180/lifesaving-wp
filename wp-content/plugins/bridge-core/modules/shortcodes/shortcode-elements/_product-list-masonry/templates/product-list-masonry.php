<?php
	
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
	
	// Get correct heading value. If provided heading isn't valid get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? esc_attr($title_tag) : esc_attr($args['title_tag']);
	
	$category_style = '';
	if ( $category_color !== '' ) {
		$category_style .= ' style="color: ' . esc_attr( $category_color ) . ';"';
	}
	
	$separator_style = '';
	if ( $separator_color !== '' ) {
		$separator_style .= ' style="background-color: ' . esc_attr( $separator_color ) . ';"';
	}
	
	$price_style = '';
	if ( $price_color !== '' || $price_font_size !== '' ) {
		$price_style .= ' style="';
		
		if ( $price_color !== '' ) {
			$price_style .= 'color:' . esc_attr( $price_color ) . ';';
		}
		if ( $price_font_size !== '' ) {
			$price_style .= 'font-size:' . esc_attr( $price_font_size ) . 'px;';
		}
		
		$price_style .= '"';
	}
	
	$product_item_style = '';
	if ( $hover_background_color !== '' ) {
		$product_item_style .= 'style="background-color:' . esc_attr( $hover_background_color ) . ';"';
	}
	
	$product_list_args = array(
		'post_type'      => 'product',
		'posts_per_page' => esc_attr( $per_page ),
		'orderby'        => esc_attr( $order_by ),
		'order'          => esc_attr( $order )
	);
	
	if ( ! empty( $category ) ) {
		$product_list_args['product_cat'] = esc_attr( $category );
	}
	
	if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
		$product_list_args['meta_query'] = array(
			array(
				'key' => '_stock_status',
				'value' => 'instock',
				'compare' => '=',
			)
		);
	}
	
	$q = new WP_Query($product_list_args);
	
	$html = "";
	$html .= "<div class='qode_product_list_masonry_holder " . esc_attr( $columns ) . "'><div class='qode_product_list_masonry_holder_inner'>";
	$html .= '<div class="qode_product_list_sizer"></div>';
	$html .= '<div class="qode_product_list_gutter"></div>';
	while ($q->have_posts()) : $q->the_post();
		
		global $product;
		
		$price = $product->get_price_html();
		
		$image = '';
		$product_image_size = 'shop_single';
		if ( $image_size === 'original' ) {
			$product_image_size = 'full';
		} elseif ( $image_size === 'square' ) {
			$product_image_size = 'portfolio_masonry_regular';
		}
		if ( has_post_thumbnail() ) {
			$image = get_the_post_thumbnail(get_the_ID(), $product_image_size);
		}
		
		$cat = wc_get_product_category_list($product->get_id(), ', ');
		
		$title = esc_html( get_the_title() );
		
		$masonry_size_class = '';
		if (get_post_meta(get_the_ID(), 'qode_product_list_masonry_layout', true) !== '') {
			$masonry_size_class = esc_attr(get_post_meta(get_the_ID(), 'qode_product_list_masonry_layout', true));
		}
		
		$html .= '<div class="qode_product_list_item ' . esc_attr( $masonry_size_class ) . '">';
		if ( $image !== '' ) {
			$html .= '<div class="qode_product_image">' . wp_kses_post( $image ) . '</div>';
		}
		$html .= '<div class="qode_product_list_item_inner" ' . esc_attr( $product_item_style ) . '><div class="qode_product_list_item_table"><div class="qode_product_list_item_table_cell">';
		$html .= '<div class="qode_product_category" ' . esc_attr( $category_style ) . '>' . wp_kses_post( $cat ). '</div>';
		$html .= '<' . esc_attr( $title_tag ) . ' itemprop="name" class="qode_product_title entry-title">' . wp_kses_post( $title ) . '</' . esc_attr( $title_tag ) . '>';
		if ( $show_separator == 'yes' ) {
			$html .= '<div class="qode_product_separator separator center" ' . esc_attr( $separator_style ) . '></div>';
		}
		$html .= '<div class="qode_product_price" ' . esc_attr( $price_style ) . '>' . wp_kses_post( $price ) . '</div>';
		$html .= '</div>';
		
		$html .= '<a itemprop="url" class="product_list_link" href="' . esc_url( get_the_permalink() ) . '" target="_self"></a>';
		$html .= '</div></div></div>';
	
	endwhile;
	wp_reset_postdata();
	
	$html .= "</div></div>";
	
	echo bridge_qode_get_module_part($html);
