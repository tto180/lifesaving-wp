<?php
$separator_style = array();

if ( $color != "" ) {
	$separator_style[] = "color:" . esc_attr( $color );
}

if ( $opacity != "" ) {
	$separator_style[] = "opacity:" . esc_attr( $opacity );
}

$html = '<span class="separator_with_icon" ' . bridge_qode_get_inline_style( $separator_style ) .'><i class="fa '. esc_attr( $icon ) .'"></i></span>';

echo bridge_qode_get_module_part( $html );