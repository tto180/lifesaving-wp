<?php

$style       = "";
$style_hover = "";
$html        = "";

$headings_array = array('h1','h2', 'h3', 'h4', 'h5', 'h6', 'p');

//get correct heading value. If provided heading isn't valid get the default one
$title_tag = ( in_array( $title_tag, $headings_array ) ) ? $title_tag : $args['title_tag'];
	
$style = array();
if ( ! empty( $icon_color ) ) {
	$style[] = 'color:' . esc_attr( $icon_color );
}
if ( ! empty( $icon_background_color ) ) {
	$style[] = 'background-color:' . esc_attr( $icon_background_color );
}
if ( ! empty( $border_color ) ) {
	$style[] = 'border-color:' . esc_attr( $border_color );
}
if ( ! empty( $size ) ) {
	$style[] = 'font-size:' . esc_attr( $size ). 'px';
}

$style_hover = array();
if ( $icon_color_hover != "" ) {
	$style_hover[] = 'color:' . esc_attr( $icon_color_hover );
}
if ( $icon_background_color_hover != "" ) {
	$style_hover[] = 'background-color:' . esc_attr( $icon_background_color_hover );
}
if ( $border_color_hover != "" ) {
	$style_hover[] = 'border-color:' . esc_attr( $border_color_hover );
}
if ( $size != "" ) {
	$style_hover[] = 'font-size:' . esc_attr( $size ) . 'px';
}

$html .= '<div class="animated_icon_with_text_holder">';
if ( $enable_link == 'yes' ) {
    $html .= '<a href="' . esc_url( $link ) . '" target="' . esc_attr( $target ) . '">';
}
$html .= '<div class="animated_icon_with_text_inner">';
$html .= '<div class="animated_icon_holder">';
$html .= '<div class="animated_icon">';
$html .= '<div class="animated_icon_inner">';
$html .= '<span class="animated_icon_front">';
$html .= "<i class='fa " . esc_attr( $icon ) . "' " . bridge_qode_get_inline_style( $style ) . "></i>";
$html .= '</span>';
$html .= '<span class="animated_icon_back">';
$html .= "<i class='fa " . esc_attr( $icon ) . "' " .  bridge_qode_get_inline_style( $style_hover ) . "></i>";
$html .= '</span>';

$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '<div class="animated_text_holder">';
$html .= '<div class="animated_text_holder_wrap">';
$html .= '<div class="animated_text_holder_wrap_inner">';
$html .= '<div class="animated_text_holder_inner">';

$html .= '<div class="animated_title">';
$html .= '<div class="animated_title_inner">';
$html .= '<' . esc_attr( $title_tag ) . '>';
$html .= esc_html( $title );
$html .= '</' . esc_attr( $title_tag ) . '>';
$html .= '</div>';
$html .= '</div>';
$html .= '<div class="animated_text">';
$html .= '<p><span>';
$html .= esc_html( $text );
$html .= '</span></p>';
$html .= '</div>';

$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
if ( $enable_link == 'yes' ) {
	$html .= '</a>';
}
$html .= '</div>';

echo bridge_qode_get_module_part( $html );