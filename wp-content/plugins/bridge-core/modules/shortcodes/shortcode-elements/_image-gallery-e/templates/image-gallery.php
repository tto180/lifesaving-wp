<?php

$gal_images        = '';
$link_start        = '';
$link_end          = '';
$el_start          = '';
$el_end            = '';
$slides_wrap_start = '';
$slides_wrap_end   = '';
$data_attr         = array();
	
	if ( 'flexslider' === $type || 'flexslider_fade' === $type || 'flexslider_slide' === $type || 'fading' === $type ) {
		$el_start = '<li>';
		$el_end = '</li>';
		$slides_wrap_start = '<ul class="slides">';
	} else if ( 'image_grid' === $type ) {
		$li_classes = '';
		if ($grayscale == 'yes') {
			$li_classes .= 'grayscale';
		} else {
			$li_classes .= 'no_grayscale';
		}
		
		$el_start = '<li class="' . esc_attr( $li_classes ) . '">';
		$el_end = '</li>';
		$slides_wrap_start = '<div class="gallery_holder"><ul class="gallery_inner ' . esc_attr( $images_space ) . '  v' . esc_attr( $column_number ) . '">';
		$slides_wrap_end = '</ul></div>';
		
	}
	
	$frame_class = '';
	if ( 'flexslider' === $type || 'flexslider_fade' === $type || 'fading' === $type ) {
		$type = ' flexslider_fade flexslider';
		$data_attr['data-flex_fx'] = 'fade';
	} else if ( 'flexslider_slide' === $type ) {
		$type = ' flexslider_slide flexslider';
		$data_attr['data-flex_fx'] = 'slide';
		if ($frame == "use_frame") {
			$frame_class = " have_frame";
			
		}
	} else if ( 'image_grid' === $type ) {
		$type = ' wpb_image_grid';
	}

	if ( ! empty( $control_nav ) ) {
		$data_attr['data-control'] = $control_nav == 'yes' ? 'true' : 'false';
	}
	
	if ( ! empty( $direction_nav ) ) {
		$data_attr['data-direction'] = $direction_nav == 'yes' ? 'true' : 'false';
	}
	
	if ( ! empty( $pause_on_hover ) ) {
		$data_attr['data-pause-on-hover'] = $pause_on_hover == 'yes' ? 'true' : 'false';
	}
	
	if ( ! empty( $enable_drag ) ) {
		$data_attr['data-drag'] = $enable_drag == 'yes' ? 'true' : 'false';
	}
	
	$additional_classes = '';
	if($control_nav == 'yes'){
		$additional_classes .= ' has_control_nav';
	}
	if($enable_drag == 'yes'){
		$additional_classes .= ' drag_enabled';
	}
	
	
	if ( '' === $images ) {
		$images = '-1,-2,-3';
	}
	
	$pretty_rel_random = ' data-rel="prettyPhoto[rel-' . get_the_ID() . '-' . wp_rand() . ']"';
	
	if ( 'custom_link' === $onclick ) {
		$custom_links = explode( ',', $custom_links );
	}
	
	$i = 0;
	
	$image_size = trim( $img_size );
	//Find digits
	preg_match_all( '/\d+/', $image_size, $matches );
	if ( in_array( $image_size, array( 'thumbnail', 'thumb', 'medium', 'large', 'full' ) ) ) {
		$img_size = $image_size;
	} elseif ( ! empty( $matches[0] ) ) {
		$img_size = array(
			$matches[0][0],
			$matches[0][1]
		);
	} else {
		$img_size = 'thumbnail';
	}
	
	foreach ( $images as $image ) {
		if ( is_array( $image ) ) {
			if( is_array( $img_size ) ){
				$thumbnail = bridge_qode_generate_thumbnail($image['id'], null, $img_size[0], $img_size[1]);
			} else{
				$thumbnail = '<img itemprop="image" src="' . wp_get_attachment_image_src($image['id'], $img_size)[0] . '" />';
			}
			
			$large_img_src = wp_get_attachment_image_src($image['id'], $img_size)[0];
		} else {
			$large_img_src = $default_src;
			$thumbnail = '<img itemprop="image" src="' . esc_url( $default_src ) . '" />';
		}
		$hover_image = '';
		if ($type == ' wpb_image_grid' && $grayscale == 'no') {
			$hover_image = '<span class="gallery_hover"><i class="fa fa-search"></i></span>';
		}
		
		$description_html = '';
	
		if( $show_image_description == 'yes' && ! empty( get_post_field('post_content', $image['id'] ) ) ) {
			$description_html = '<div class="qode-image-slider-description"><p>' . get_post_field('post_content', $image['id'] ) . '</p></div>';
		}
		
		$link_start = $link_end = '';
		
		switch ( $onclick ) {
			case 'img_link_large':
				$link_start = '<a itemprop="url" href="' . esc_url( $large_img_src ) . '" target="' . esc_attr( $custom_links_target ) . '">' . wp_kses_post( $hover_image );
				$link_end = '</a>';
				break;
			
			case 'link_image':
				$link_start = '<a itemprop="image" class="qode-prettyphoto" href="' . esc_url( $large_img_src ) . '"' . esc_attr( $pretty_rel_random ) . '>' . wp_kses_post( $hover_image );
				$link_end = '</a>';
				break;
			
			case 'custom_link':
				if ( ! empty( $custom_links[ $i ] ) ) {
					$link_start = '<a itemprop="url" href="' . esc_url( $custom_links[ $i ] ) . '"' . ( ! empty( $custom_links_target ) ? ' target="' . esc_attr( $custom_links_target ) . '"' : '' ) . '>'. wp_kses_post( $hover_image );
					$link_end = '</a>';
				}
				break;
		}
		
		$gal_images .= $el_start . $link_start . $thumbnail . $link_end . $description_html . $el_end;
		
		$i++;
	}
	
	$css_class = 'qode-image-gallery';
	
	if ( $frame == 'use_frame' ) {
		
		$css_class .= " frame_holder";
		
		switch ( $choose_frame ) {
			case 'frame2':
				$css_class .= " frame_holder2";
				break;
			case 'frame3':
				$css_class .= " frame_holder3";
				break;
			case 'frame4':
				$css_class .= " frame_holder4";
				break;
			default:
				break;
		}
	}
	
	
	$output = '';
	$output .= '<div class="' . esc_attr( $css_class ) . '">';
	$output .= '<div class="qode-image-gallery-inner">';
	$output .= '<div class="qode-image-gallery-slides' . esc_attr ( $additional_classes ) . esc_attr ( $type ) . esc_attr ( $frame_class ) . '" data-interval="' . esc_attr ( $interval ) . '"' . bridge_qode_get_inline_attrs( $data_attr ) . '>' . wp_kses_post( $slides_wrap_start ) . wp_kses_post( $gal_images ) . wp_kses_post($slides_wrap_end ) . '</div>';
	if ($frame == 'use_frame') {
		$output .= "<div class='gallery_frame'>";
		switch ($choose_frame) {
			case 'frame2':
				$output .= "<img itemprop='image' src='" . get_template_directory_uri() . "/img/slider_frame-2.png'/>";
				break;
			
			case "frame3":
				$output .= "<img itemprop='image' src='" . get_template_directory_uri() . "/img/slider_frame-3.png'/>";
				break;
			
			case "frame4":
				$output .= "<img itemprop='image' src='" . get_template_directory_uri() . "/img/slider_frame-4.png'/>";
				break;
			
			default:
				$output .= "<img itemprop='image' src='" . get_template_directory_uri() . "/img/slider_frame.png'/>";
				break;
		}
		$output .= "</div>";
	}
	
	$output .= '</div>';
	$output .= '</div>';
	
	echo bridge_qode_get_module_part( $output );