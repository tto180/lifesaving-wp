<?php
	
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
	
	// Get correct heading value. If provided heading isn't valid get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : esc_html($args['title_tag']);
	
	$product_list_args = array(
		'post_type' => 'product',
		'posts_per_page' => esc_attr($per_page),
		'orderby' => esc_attr($order_by),
		'order' => esc_attr($order)
	);
	
	if ('yes' === get_option('woocommerce_hide_out_of_stock_items')) {
		$product_list_args['meta_query'] = array(
			array(
				'key' => '_stock_status',
				'value' => 'instock',
				'compare' => '=',
			)
		);
	}
	
	if (!empty($category)) {
		$product_list_args['product_cat'] = esc_attr($category);
	}
	
	$q = new WP_Query($product_list_args);
	
	$html = "<div class='qode_product_list_holder " . esc_attr($columns) . "'><ul>";
	
	while ($q->have_posts()) : $q->the_post();
		
		global $product;
		
		$holder_style = array();
		if ( $holder_padding !== '' ) {
			$holder_style[] = 'padding: ' . esc_attr( $holder_padding );
		}
		
		$image = '';
		if ( has_post_thumbnail() ) {
			$image = get_the_post_thumbnail();
		}
		
		$cat = wc_get_product_category_list(
			$product->get_id(),
			' '
		);
		
		$title = get_the_title();
		
		$separator_style = array();
		if ( ! empty( $separator_color ) ) {
			$separator_style[] = 'background-color: ' . esc_attr( $separator_color );
		}
		
		$price = $product->get_price_html();
		
		$price_style = array();
		if ( ! empty( $price_color ) ) {
			$price_style[] = 'color:' . esc_attr( $price_color );
		}
		if ( ! empty( $price_font_size ) ) {
			$price_style[] = 'font-size:' . esc_attr( $price_font_size ) . 'px';
		}
		
		$button_size_class       = esc_attr( $button_size !== '' ? $button_size : '' );
		$button_hover_type_class = esc_attr( $button_hover_type !== '' ? $button_hover_type : '' );
		
		if (version_compare(WOOCOMMERCE_VERSION, '3.0') >= 0) {
			$button = sprintf(
				'<a rel="nofollow" href="%s" data-quantity="%s" data-product_id="%s" data-product_sku="%s" class="%s %s %s %s">%s</a>',
				esc_url($product->add_to_cart_url()),
				esc_attr(isset($quantity) ? $quantity : 1),
				esc_attr($product->get_id()),
				esc_attr($product->get_sku()),
				esc_attr('qbutton ' . $button_size_class . ' ' . $button_hover_type_class),
				esc_attr($product->supports('ajax_add_to_cart') ? 'ajax_add_to_cart ' : ' '),
				esc_attr($product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : 'qode-product-out-of-stock'),
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
				esc_attr($product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : 'qode-product-out-of-stock'),
				esc_attr($product->product_type),
				esc_html($product->add_to_cart_text())
			);
		}
		
		$html .= '<li>';
		$html .= '<div class="product_list_inner" ' . bridge_qode_get_inline_style( $holder_style ) . '>';
		
		$html .= '<div class="product_category">' . wp_kses_post( $cat ) . '</div>';
		
		$html .= '<' . esc_attr( $title_tag ) . ' itemprop="name" class="product_title entry-title">' . wp_kses_post( $title ) . '</' . esc_attr( $title_tag ) . '>';
		
		$html .= '<div class="product_separator separator small center" ' . bridge_qode_get_inline_style( $separator_style ) . '></div>';
		
		if ($image !== '') {
			$html .= '<div class="product_image">' .  wp_kses_post( $image ) . '</div>';
		}
		
		$html .= '<div class="product_price" ' . bridge_qode_get_inline_style( $price_style ) . '>' . wp_kses_post( $price ) . '</div>';
		
		$html .= '<div class="product_button">' . wp_kses_post( $button ) . '</div>';
		
		$html .= '</div>';
		$html .= '<a itemprop="url" class="product_list_link" href="' . esc_url(get_the_permalink()) . '" target="_self"></a>';
		$html .= '</li>';
	
	endwhile;
	wp_reset_postdata();
	
	$html .= "</ul></div>";
	
	echo bridge_qode_get_module_part($html);
