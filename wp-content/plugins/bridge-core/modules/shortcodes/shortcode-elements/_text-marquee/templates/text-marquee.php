<?php
$html  = "";
$title_styles  = "";

//generate styles
if ( $title_color != "" ) {
	$title_styles .= "color: " . esc_attr( $title_color ) . ";";
}

$html .= '<div class="qode-text-marquee">';
$html .= '<div class="qode-text-marquee-wrapper">';
$html .= '<span class="qode-text-marquee-title" style="' . esc_attr( $title_styles ) . '">' . wp_kses_post( $title ) . '</span>';
$html .= '</div>';
$html .= '</div>';

echo bridge_qode_get_module_part( $html );