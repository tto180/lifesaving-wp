<?php

$qodeIconCollections = bridge_qode_return_icon_collections();

$html = "";

//generate inline icon styles
$style = '';
$style_normal = '';
$icon_stack_classes = '';
$animation_delay_style = '';

//generate icon stack styles
$icon_stack_style = '';
$icon_stack_base_style = '';
$icon_stack_circle_styles = '';
$icon_stack_square_styles = '';
$icon_stack_normal_style  = '';

if ( ! empty( $custom_size ) ) {
	$style .= 'font-size: ' . esc_attr( $custom_size );
	
	if ( ! empty( $custom_shape_size ) ) {
		$icon_stack_circle_styles .= 'font-size: ' . esc_attr( $custom_size );
		$icon_stack_square_styles .= 'font-size: ' . esc_attr( $custom_size );
	}
	
	
	if( ! strstr( $custom_size, 'px' ) ) {
		$style .= 'px;';
		
		if ( ! empty( $custom_shape_size ) ) {
			$icon_stack_circle_styles .= 'px;';
			$icon_stack_square_styles .= 'px;';
		}
	}
}

if ( ! empty( $custom_shape_size ) ) {
	$icon_stack_circle_styles .= 'font-size: ' . esc_attr( $custom_shape_size ) . 'px;';
	$icon_stack_square_styles .= 'font-size: ' . esc_attr( $custom_shape_size ) . 'px;';
}

if ( ! empty( $icon_color ) ) {
	$style .= 'color: ' . esc_attr( $icon_color ) . ';';
}

if ( ! empty( $position ) ) {
	$icon_stack_classes .= 'pull-' . esc_attr( $position );
}

if ( ! empty( $background_color ) ) {
	$icon_stack_base_style .= 'color: ' . esc_attr( $background_color ) . ';';
	$icon_stack_style      .= 'background-color: ' . esc_attr( $background_color ) . ';';
}

if ( $border == 'yes' && ! empty( $border_color ) ) {
	
	if ( $border_width !== '' ) {
		$icon_stack_style .= 'border: ' . esc_attr( $border_width ) . 'px solid ' . esc_attr( $border_color ) . ';';
	} else {
		$icon_stack_style .= 'border: 1px solid ' . esc_attr( $border_color ) . ';';
	}
} else if ( $border == 'no' ) {
	$icon_stack_style .= 'border: 0;';
}

if ( $border_radius !== '' ) {
	$icon_stack_square_styles .= 'border-radius: ' . esc_attr( $border_radius ) . 'px;';
}

if ( $icon_animation_delay != "" ) {
	$animation_delay_style .= 'transition-delay: ' . esc_attr( $icon_animation_delay ) . 'ms; -webkit-transition-delay: ' . esc_attr( $icon_animation_delay ) . 'ms; -moz-transition-delay: ' . esc_attr( $icon_animation_delay ) . 'ms; -o-transition-delay: ' . esc_attr( $icon_animation_delay ) . 'ms;';
}

if ( $margin != "" ) {
	$icon_stack_style         .= 'margin: ' . esc_attr( $margin ) . ';';
	$icon_stack_circle_styles .= 'margin: ' . esc_attr( $margin ) . ';';
	$icon_stack_normal_style  .= 'margin: ' . esc_attr( $margin ) . ';';
}

$icon_link_class = "";
if ( $anchor_icon == "yes" ) {
	$icon_link_class = "anchor";
}

//have to set default because of already created shortcodes
$icon_pack = $icon_pack == '' ? 'font_awesome' : esc_attr( $icon_pack );

switch ( $type ) {
	case 'circle':
		$html = '<span '.bridge_qode_get_inline_attr($type, 'data-type').' '.bridge_qode_get_inline_attr($hover_background_color, 'data-hover-bg-color').' '.bridge_qode_get_inline_attr($icon_hover_color, 'data-hover-icon-color').' class="qode_icon_shortcode fa-stack q_font_awsome_icon_stack '.$size.' '.$icon_stack_classes.' '.$icon_animation.'" style="'.$icon_stack_circle_styles.' '.$animation_delay_style.'">';
		if ( ! empty( $link ) ) {
			$html .= '<a '.bridge_qode_get_inline_attr($icon_link_class, 'class').' itemprop="url" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">';
		}
		$html .= '<i class="fa fa-circle fa-stack-base fa-stack-2x" style="'.$icon_stack_base_style.'"></i>';
		
		if( $qodeIconCollections->getIconCollectionParamNameByKey($icon_pack) ) {
			$html .= $qodeIconCollections->getIconHTML(
				${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)},
				$icon_pack,
				array('icon_attributes' => array('style' => $style, 'class' => 'qode_icon_element fa-stack-1x')));
		}
		
		break;
	case 'square':
		$html = '<span '.bridge_qode_get_inline_attr($type, 'data-type').' '.bridge_qode_get_inline_attr($hover_background_color, 'data-hover-bg-color').' '.bridge_qode_get_inline_attr($icon_hover_color, 'data-hover-icon-color').' class="qode_icon_shortcode fa-stack q_font_awsome_icon_square '.$size.' '.$icon_stack_classes.' '.$icon_animation.'" style="'.$icon_stack_style.$icon_stack_square_styles.' '.$animation_delay_style.'">';
		if ( ! empty( $link ) ) {
			$html .= '<a '.bridge_qode_get_inline_attr($icon_link_class, 'class').'  itemprop="url" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">';
		}
		
		if( $qodeIconCollections->getIconCollectionParamNameByKey($icon_pack) ) {
			$html .= $qodeIconCollections->getIconHTML(
				${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)},
				$icon_pack,
				array('icon_attributes' => array('style' => $style, 'class' => 'qode_icon_element')));
		}
		
		break;
	default:
		$html = '<span '.bridge_qode_get_inline_attr($type, 'data-type').' '.bridge_qode_get_inline_attr($icon_hover_color, 'data-hover-icon-color').' class="qode_icon_shortcode  q_font_awsome_icon '.$size.' '.$icon_stack_classes.' '.$icon_animation.'" style="'.$icon_stack_normal_style.' '.$animation_delay_style.'">';
		if ( ! empty( $link ) ) {
			$html .= '<a '.bridge_qode_get_inline_attr($icon_link_class, 'class').' itemprop="url" href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">';
		}
		
		if( $qodeIconCollections->getIconCollectionParamNameByKey($icon_pack) ) {
			$html .= $qodeIconCollections->getIconHTML(
				${$qodeIconCollections->getIconCollectionParamNameByKey($icon_pack)},
				$icon_pack,
				array('icon_attributes' => array('style' => $style, 'class' => 'qode_icon_element')));
		}
		break;
}

if ( ! empty( $link ) ) {
	$html .= '</a>';
}

$html.= '</span>';

echo bridge_qode_get_module_part( $html );