<?php
	
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6', 'p');
	
	//get correct heading value. If provided heading isn't valid get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : esc_attr($args['title_tag']);
	
	$category_style = array();
	if ( ! empty( $category_color ) ) {
		$category_style[] = 'color: ' . esc_attr( $category_color );
	}
	
	$separator_style = array();
	if ( ! empty( $separator_color ) ) {
		$separator_style[] = 'background-color: ' . esc_attr( $separator_color );
	}
	
	$price_style = array();
	if ( ! empty( $price_color ) ) {
		$price_style[] = 'color:' . esc_attr( $price_color );
	}
	if ( ! empty( $price_font_size ) ) {
		$price_style[] = 'font-size:' . esc_attr( $price_font_size ) . 'px';
	}
	
	$product_item_style = array();
	if ( ! empty( $hover_background_color ) ) {
		$product_item_style[] = 'background-color:' . esc_attr( $hover_background_color );
	}
	
	$product_list_args = array(
		'post_type'      => 'product',
		'posts_per_page' => esc_attr( $per_page ),
		'orderby'        => esc_attr( $order_by ),
		'order'          => esc_attr( $order )
	);
	
	if (!empty($category)) {
		$product_list_args['product_cat'] = esc_attr($category);
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
	$html .= '<div class="qode_product_list_pinterest_holder ' . esc_attr($columns) . '"><div class="qode_product_list_pinterest_holder_inner">';
	$html .= '<div class="qode_product_list_sizer"></div>';
	$html .= '<div class="qode_product_list_gutter"></div>';
	while ($q->have_posts()) : $q->the_post();
		
		global $product;
		
		$price = $product->get_price_html();
		
		// WooCommerce plugin changed hooks in 3.0 version and because of that we have this condition
		if (version_compare(WOOCOMMERCE_VERSION, '3.0') >= 0) {
			$button = sprintf(
				'<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s %s %s %s">%s</a>',
				esc_url($product->add_to_cart_url()),
				esc_attr(isset($quantity) ? $quantity : 1),
				esc_attr($product->get_id()),
				esc_attr($product->get_sku()),
				esc_attr('qbutton '),
				esc_attr($product->supports('ajax_add_to_cart') ? 'ajax_add_to_cart ' : ' '),
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : 'qode-product-out-of-stock',
				esc_attr($product->get_type()),
				esc_html($product->add_to_cart_text())
			);
		} else {
			$button = sprintf(
				'<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s %s %s %s">%s</a>',
				esc_url($product->add_to_cart_url()),
				esc_attr(isset($quantity) ? $quantity : 1),
				esc_attr($product->get_id()),
				esc_attr($product->get_sku()),
				esc_attr('qbutton '),
				esc_attr($product->supports('ajax_add_to_cart') ? 'ajax_add_to_cart ' : ' '),
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : 'qode-product-out-of-stock',
				esc_attr($product->product_type),
				esc_html($product->add_to_cart_text())
			);
		}
		
		$image = '';
		$product_image_size = 'full';
		if ( has_post_thumbnail() ) {
			$image = get_the_post_thumbnail( get_the_ID(), $product_image_size );
		}
		
		$cat = wc_get_product_category_list($product->get_id(), ' ');
		
		$title = esc_html(get_the_title());
		
		$html .= '<div class="qode_product_list_item">';
		if ( $image !== '' ) {
			$html .= '<div class="qode_product_image">' . wp_kses_post( $image ) . '</div>';
		}
		$html .= '<div class="qode_product_list_item_text">';
		if ( ! empty( $category_color ) ) {
			$html .= '<div class="qode_product_category qode_product_category_inherit_color" ' . bridge_qode_get_inline_style( $category_style ) . '>' . wp_kses_post( $cat ) . '</div>';
		} else {
			$html .= '<div class="qode_product_category" ' . bridge_qode_get_inline_style( $category_style ) . '>' . wp_kses_post( $cat ) . '</div>';
		}
		$html .= '<' . esc_attr( $title_tag ) . ' itemprop="name" class="qode_product_title entry-title">' . wp_kses_post($title ) . '</' . esc_attr( $title_tag ) . '>';
		$html .= '<div class="qode_product_separator separator center small" ' . bridge_qode_get_inline_style( $separator_style ) . '></div>';
		$html .= '<div class="qode_product_price" ' . bridge_qode_get_inline_style( $price_style ) . '>' . wp_kses_post( $price ) . '</div>';
		$html .= '</div>';
		
		$html .= '<div class="qode_product_list_item_hover_holder">';
		$html .= '<div class="qode_product_list_item_hover">';
		$html .= '<div class="qode_product_list_item_hover_inner">';
		if ( ! empty( $category_color ) ) {
			$html .= '<div class="qode_product_category qode_product_category_inherit_color" ' . bridge_qode_get_inline_style( $category_style ) . '>' . wp_kses_post( $cat ) . '</div>';
		} else {
			$html .= '<div class="qode_product_category" ' . bridge_qode_get_inline_style( $category_style ) . '>' . wp_kses_post( $cat ) . '</div>';
		}
		$html .= '<' . esc_attr( $title_tag ) . ' itemprop="name" class="qode_product_title entry-title"><a itemprop="url" class="product_list_link" href="' . get_the_permalink() . '" target="_self">' . wp_kses_post( $title ) . '</a></' . esc_attr( $title_tag ) . '>';
		$html .= '<div class="qode_product_separator separator center small" ' . bridge_qode_get_inline_style( $separator_style ) . '></div>';
		$html .= '<div class="qode_product_price" ' . bridge_qode_get_inline_style( $price_style ) . '>' . wp_kses_post($price ). '</div>';
		$html .= '<div class="qode_product_button">' . wp_kses_post( $button ) . '</div>';
		$html .= '</div>';
		$html .= '</div>';
		$html .= '</div>';
		
		$html .= '<a itemprop="url" class="product_list_link" href="' . esc_url(get_the_permalink()) . '" target="_self"></a>';
		$html .= '</div>';
	
	endwhile;
	wp_reset_postdata();
	
	$html .= "</div></div>";
	
	echo bridge_qode_get_module_part($html);
