<?php
	
	$qodeIconCollections = bridge_qode_return_icon_collections();
	
	$headings_array = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p' );
	
	//get correct heading value. If provided heading isn't valid get the default one
	$title_tag = ( in_array( $title_tag, $headings_array ) ) ? $title_tag : esc_attr( $args['title_tag'] );
	
	//init icon styles
	$style              = '';
	$icon_stack_classes = '';
	
	//holder hover effect
	$icon_text_clasess = '';
	if ( $holder_hover_effect == 'yes' ) {
		$icon_text_clasess .= 'qode_iwt_hover_enabled ';
	}
	
	//init icon stack styles
	$icon_margin_style       = '';
	$icon_stack_square_style = '';
	$icon_stack_base_style   = '';
	$icon_stack_style        = '';
	$img_styles              = '';
	$animation_delay_style   = '';
	$icon_text_holder_styles = '';
	
	//generate inline icon styles
	if ( $use_custom_icon_size == "yes" ) {
		if ( $custom_icon_size != "" ) {
			//remove px if user has entered it
			$custom_icon_size = strstr( $custom_icon_size, 'px', true ) ? strstr( $custom_icon_size, 'px',true ) : $custom_icon_size;
			$icon_stack_style .= 'font-size: ' . esc_attr ( $custom_icon_size ) . 'px;';
		}
		
		if ( $custom_icon_margin != "" && $icon_position !== 'left_from_title' ) {
			//remove px if user has entered it
			$custom_icon_margin = strstr(
				$custom_icon_margin,
				'px',
				true
			) ? strstr(
				$custom_icon_margin,
				'px',
				true
			) : $custom_icon_margin;
			$custom_icon_margin = intval( $custom_icon_size ) + intval( $custom_icon_margin );
			
			if ( $icon_position !== 'right' ) {
				$icon_text_holder_styles .= 'padding-left:' . esc_attr ( $custom_icon_margin ) . 'px;';
			} else {
				$icon_text_holder_styles .= 'padding-right:' . esc_attr ( $custom_icon_margin ) . 'px;';
			}
			
		}
		
		if ( $custom_icon_margin != "" && $icon_position === 'left_from_title' ) {
			//remove px if user has entered it
			$custom_icon_margin = strstr( $custom_icon_margin, 'px', true ) ? strstr( $custom_icon_margin, 'px', true ) : $custom_icon_margin;
			$custom_icon_margin = intval( $custom_icon_size ) + intval( $custom_icon_margin );
			
			$icon_margin_style .= 'padding-right:' . esc_attr( $custom_icon_margin ) . 'px;';
		}
		
		if ( $custom_icon_size_inner != '' && in_array( $icon_type, array( 'circle', 'square' ) ) ) {
			$style .= 'font-size: ' . esc_attr( $custom_icon_size_inner ) . 'px;';
		}
		
	}
	
	if ( $icon_color != "" ) {
		$style .= 'color: ' . esc_attr( $icon_color ) . ';';
	}
	
	//generate icon stack styles
	if ( $icon_background_color != "" ) {
		$icon_stack_base_style   .= 'background-color: ' . esc_attr( $icon_background_color ) . ';';
		$icon_stack_square_style .= 'background-color: ' . esc_attr( $icon_background_color ) . ';';
	}
	
	if ( $icon_border_color != "" ) {
		$icon_stack_style .= 'border-color: ' . esc_attr( $icon_border_color ) . ';';
	}
	
	if ( $icon_margin != "" && ( $icon_position == "" || $icon_position == "top" ) ) {
		$icon_margin_style .= "margin: " . esc_attr( $icon_margin ) . ";";
		$img_styles        .= "margin: " . esc_attr( $icon_margin ). ";";
	}
	
	if ( $icon_animation_delay != "" ) {
		$animation_delay_style .= 'transition-delay: ' . esc_attr( $icon_animation_delay ) . 'ms; -webkit-transition-delay: ' . esc_attr( $icon_animation_delay ) . 'ms; -moz-transition-delay: ' . esc_attr( $icon_animation_delay ) . 'ms; -o-transition-delay: ' . esc_attr( $icon_animation_delay ) . 'ms;';
	}
	
	$box_size = '';
	//generate icon text holder styles and classes
	
	//map value of the field to the actual class value
	switch ( $icon_size ) {
		case 'large': //smallest icon size
			$box_size = 'tiny';
			break;
		case 'fa-2x':
			$box_size = 'small';
			break;
		case 'fa-3x':
			$box_size = 'medium';
			break;
		case 'fa-4x':
			$box_size = 'large';
			break;
		case 'fa-5x':
			$box_size = 'very_large';
			break;
		default:
			$box_size = 'tiny';
	}
	
	if ( $image != "" ) {
		$icon_type = 'image';
	}
	
	$box_icon_type = '';
	switch ( $icon_type ) {
		case 'normal':
			$box_icon_type = 'normal_icon';
			break;
		case 'square':
			$box_icon_type = 'square';
			break;
		case 'circle':
			$box_icon_type = 'circle';
			break;
		case 'image':
			if ( $box_type == 'normal' ) {
				$box_icon_type = 'custom_icon_image';
			} else {
				$box_icon_type = 'image';
			}
			break;
	}
	
	/* Generate text styles */
	$title_style = "";
	if ( $title_color != "" ) {
		$title_style .= "color: " . esc_attr( $title_color ) . ";";
	}
	if ( $title_font_weight !== "" ) {
		$title_style .= "font-weight: " . esc_attr( $title_font_weight ) . ";";
	}
	
	$text_style = "";
	if ( $text_color != "" ) {
		$text_style .= "color: " . esc_attr( $text_color );
	}
	
	$link_style = "";
	
	if ( $link_color != "" ) {
		$link_style .= "color: " . esc_attr( $link_color ) . ";";
	}
	
	$html      = "";
	$html_icon = "";
	
	if ( $link_icon == 'yes' && $link !== '' ) {
		$html_icon .= '<a itemprop="url" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '" class="q_icon_link">';
	}
	
	//have to set default because of already created shortcodes
	$icon_pack           = $icon_pack == '' ? 'font_awesome' : $icon_pack;
	$icon_gradient_class = '';
	if ( $icon_gradient == 'yes' ) {
		$icon_gradient_class .= 'qode-type1-gradient-left-to-right-text';
	}
	
	$separator_style = 'height: 2px;';
	if ( ! empty( $separator_color ) ) {
		$separator_style .= 'background-color: ' . esc_attr( $separator_color ) . ';';
	}
	if ( ! empty( $separator_width ) ) {
		$separator_style .= 'width: ' . bridge_qode_filter_px( $separator_width ) . 'px;';
	}
	if ( ! empty( $separator_top_margin ) ) {
		$separator_style .= 'margin-top: ' . bridge_qode_filter_px( $separator_top_margin ) . 'px;';
	}
	if ( ! empty( $separator_bottom_margin ) ) {
		$separator_style .= 'margin-bottom: ' . bridge_qode_filter_px( $separator_bottom_margin ) . 'px;';
	}
	
	if ( $image == "" ) {
		//genererate icon html
		switch ( $icon_type ) {
			case 'circle':
				$html_icon .= '<span ' . bridge_qode_get_inline_attr( $icon_type, 'data-icon-type' ) . ' ' . bridge_qode_get_inline_attr( $icon_hover_color, 'data-icon-hover-color' ) . ' ' . bridge_qode_get_inline_attr( $icon_hover_background_color, 'data-icon-hover-bg-color' ) . ' class="qode_iwt_icon_holder fa-stack ' . esc_attr( $icon_size ) . ' ' . esc_attr( $icon_stack_classes ) . ' ' . esc_attr( $icon_gradient_class ) . '" style="' . esc_attr( $icon_stack_style ) . esc_attr( $icon_stack_base_style ) . '">';
				
				if ( $qodeIconCollections->getIconCollectionParamNameByKey( $icon_pack ) ) {
					$html_icon .= $qodeIconCollections->getIconHTML(
						${$qodeIconCollections->getIconCollectionParamNameByKey( $icon_pack )},
						$icon_pack,
						array( 'icon_attributes' => array( 'style' => $style, 'class' => 'qode_iwt_icon_element' ) )
					);
				}
				$html_icon .= '</span>';
				break;
			case 'square':
				$html_icon .= '<span ' . bridge_qode_get_inline_attr( $icon_type, 'data-icon-type' ) . '  ' . bridge_qode_get_inline_attr(	$icon_hover_color, 'data-icon-hover-color' ) . ' ' . bridge_qode_get_inline_attr( $icon_hover_background_color, 'data-icon-hover-bg-color' ) . ' class="qode_iwt_icon_holder fa-stack ' . esc_attr( $icon_size ) . ' ' . esc_attr( $icon_stack_classes ) . ' ' . esc_attr( $icon_gradient_class ) . '" style="' . esc_attr( $icon_stack_style) . esc_attr( $icon_stack_square_style ) . '">';
				
				if ( $qodeIconCollections->getIconCollectionParamNameByKey( $icon_pack ) ) {
					$html_icon .= $qodeIconCollections->getIconHTML(
						${$qodeIconCollections->getIconCollectionParamNameByKey( $icon_pack )},
						$icon_pack,
						array( 'icon_attributes' => array( 'style' => $style, 'class' => 'qode_iwt_icon_element' ) )
					);
				}
				
				$html_icon .= '</span>';
				break;
			default:
				$html_icon .= '<span ' . bridge_qode_get_inline_attr(
						$icon_type,
						'data-icon-type'
					) . '  ' . bridge_qode_get_inline_attr(
					              $icon_hover_color,
					              'data-icon-hover-color'
				              ) . ' style="' . $icon_stack_style . '" class="qode_iwt_icon_holder q_font_awsome_icon ' . $icon_size . ' ' . $icon_stack_classes . ' ' . $icon_gradient_class . '">';
				
				if ( $qodeIconCollections->getIconCollectionParamNameByKey( $icon_pack ) ) {
					$html_icon .= $qodeIconCollections->getIconHTML(
						${$qodeIconCollections->getIconCollectionParamNameByKey( $icon_pack )},
						$icon_pack,
						array( 'icon_attributes' => array( 'style' => $style, 'class' => 'qode_iwt_icon_element' ) )
					);
				}
				
				$html_icon .= '</span>';
				break;
		}
	} else {
		if ( is_numeric( $image ) ) {
			$image_src = wp_get_attachment_url( $image );
			$image_alt = get_post_meta( $image, '_wp_attachment_image_alt', true );
		} else {
			$image_src = $image;
			$image_id  = bridge_qode_get_attachment_id_from_url( $image_src );
			if ( ! empty( $image_id ) ) {
				$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
			} else {
				$image_alt = esc_html__( 'Icon With Text Alt', 'bridge-core' );
			}
		}
		$html_icon = '<img itemprop="image" style="' . esc_attr( $img_styles ) . '" src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $image_alt ) . '">';
	}
	
	if ( $link_icon == 'yes' && $link !== '' ) {
		$html_icon .= '</a>';
	}
	
	//generate normal type of a box html
	if ( $box_type == "normal" ) {
		
		//init icon text wrapper styles
		$icon_with_text_clasess = '';
		$icon_with_text_style   = '';
		$icon_text_inner_style  = '';
		
		$icon_with_text_clasess .= esc_attr( $box_size );
		$icon_with_text_clasess .= ' ' . esc_attr( $box_icon_type );
		
		if ( $box_border_color != "" ) {
			$icon_text_inner_style .= 'border-color: ' . esc_attr( $box_border_color );
		}
		
		if ( $icon_position == "" || $icon_position == "top" ) {
			$icon_with_text_clasess .= " center";
		}
		if ( $icon_position == "left_from_title" ) {
			$icon_with_text_clasess .= " left_from_title";
		}
		
		if ( $icon_position == 'right' ) {
			$icon_with_text_clasess .= ' right';
		}
		if ( $content_alignment != '' ) {
			$icon_with_text_clasess .= ' qode-iwt-content-alignment-' . esc_attr( $content_alignment );
		}
		
		$html .= "<div class='q_icon_with_title " . esc_attr( $icon_with_text_clasess ) . " " . esc_attr( $icon_text_clasess ) . "'>";
		if ( $icon_position != "left_from_title" ) {
			//generate icon holder html part with icon
			$html .= '<div class="icon_holder ' . esc_attr( $icon_animation ) . '" style="' . esc_attr( $icon_margin_style ) . ' ' . esc_attr( $animation_delay_style ) . '">';
			$html .= wp_kses_post( $html_icon );
			$html .= '</div>'; //close icon_holder
		}
		//generate text html
		$html .= '<div class="icon_text_holder" style="' . esc_attr( $icon_text_holder_styles ) . '">';
		$html .= '<div class="icon_text_inner" style="' . esc_attr( $icon_text_inner_style ) . '">';
		if ( $icon_position == "left_from_title" ) {
			$html .= '<div class="icon_title_holder">'; //generate icon_title holder for icon from title
			//generate icon holder html part with icon
			$html .= '<div class="icon_holder ' . esc_attr( $icon_animation ) . '" style="' . esc_attr( $icon_margin_style ) . ' ' . esc_attr( $animation_delay_style ) . '">';
			$html .= wp_kses_post( $html_icon );
			$html .= '</div>'; //close icon_holder
		}
		$html .= '<' . esc_attr( $title_tag ) . ' class="icon_title" style="' . esc_attr( $title_style ) . '">' . wp_kses_post( $title ) . '</' . esc_attr( $title_tag ). '>';
		if ( $icon_position == "left_from_title" ) {
			$html .= '</div>'; //close icon_title holder for icon from title
		}
		if ( $separator == "yes" ) {
			$html .= '<div class="separator small left" style="' . esc_attr( $separator_style ) . '"></div>';
		}
		$html .= "<p style='" . esc_attr( $text_style ) . "'>" . wp_kses_post( $text ) . "</p>";
		if ( $link != "" ) {
			if ( $target == "" ) {
				$target = "_self";
			}
			
			if ( $link_text == "" ) {
				$link_text = esc_html__('Read More', 'bridge-core' );
			}
			
			$html .= "<a itemprop='url' class='icon_with_title_link' href='" . esc_url( $link ) . "' target='" . esc_attr( $target ) . "' style='" . esc_attr( $link_style ) . "'>" . esc_html( $link_text ) . "</a>";
		}
		$html .= '</div>';  //close icon_text_inner
		$html .= '</div>'; //close icon_text_holder
		
		$html .= '</div>'; //close icon_with_title
	} else {
		//init icon text wrapper styles
		$icon_with_text_clasess = '';
		$box_holder_styles      = '';
		
		if ( $box_border_color != "" ) {
			$box_holder_styles .= 'border-color: ' . esc_attr( $box_border_color ) . ';';
		}
		
		if ( $box_background_color != "" ) {
			$box_holder_styles .= 'background-color: ' . esc_attr( $box_background_color ) . ';';
		}
		
		$icon_with_text_clasess .= esc_attr( $box_size );
		$icon_with_text_clasess .= ' ' . esc_attr( $box_icon_type );
		
		$html .= '<div class="q_box_holder with_icon" style="' . esc_attr( $box_holder_styles ) . '">';
		
		$html .= '<div class="box_holder_icon">';
		$html .= '<div class="box_holder_icon_inner ' . esc_attr( $icon_with_text_clasess ) . ' ' . esc_attr( $icon_animation ) . '" style="' . esc_attr( $animation_delay_style ) . '">';
		$html .= wp_kses_post( $html_icon );
		$html .= '</div>'; //close box_holder_icon_inner
		$html .= '</div>'; //close box_holder_icon
		
		//generate text html
		$html .= '<div class="box_holder_inner ' . esc_attr( $box_size ) . ' center">';
		$html .= '<' . esc_attr( $title_tag ) . ' class="icon_title" style="' . esc_attr( $title_style ) . '">' . wp_kses_post( $title ) . '</' . esc_attr( $title_tag ) . '>';
		if ( $separator == "yes" ) {
			$html .= '<div class="separator small left" style="' . esc_attr( $separator_style ) . '"></div>';
			//$html .= do_shortcode('[vc_separator type="small" position="left" color="'.$separator_color.'" thickness="2" width="'.$separator_width.'" up="'.$separator_top_margin.'" down="'.$separator_bottom_margin.'"]');
		} else {
			$html .= '<span class="separator transparent" style="margin: 8px 0;"></span>';
		}
		$html .= '<p style="' . esc_attr( $text_style ) . '">' . wp_kses_post( $text ) . '</p>';
		
		if ( $link != "" ) {
			if ( $target == "" ) {
				$target = "_self";
			}
			
			if ( $link_text == "" ) {
				$link_text = esc_html__(
					'Read More',
					'bridge-core'
				);
			}
			
			$html .= "<a itemprop='url' class='icon_with_title_link' href='" . esc_url( $link ) . "' target='" . esc_attr( $target ) . "' style='" . esc_attr( $link_style ) . "'>" . esc_html( $link_text ) . "</a>";
		}
		
		$html .= '</div>'; //close box_holder_inner
		
		$html .= '</div>'; //close box_holder
	}
	
	echo bridge_qode_get_module_part( $html );
