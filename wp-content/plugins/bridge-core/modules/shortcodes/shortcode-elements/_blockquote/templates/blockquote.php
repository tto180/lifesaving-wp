<?php

//init variables
$html               = "";
$blockquote_styles  = "";
$blockquote_classes = "";
$heading_styles     = "";
$quote_icon_styles  = "";

if ( $show_quote_icon == 'yes' ) {
    $blockquote_classes .= ' with_quote_icon';
}

if( $width != "" ) {
    $blockquote_styles .= "width: " . esc_attr( $width ) . "%;";
}

if( $border_color != "" ) {
    $blockquote_styles .= "border-left-color: " . esc_attr( $border_color ) . ";";
}

if ( $background_color != "" ) {
    $blockquote_styles .= "background-color: " . esc_attr( $background_color ) . ";";
}

if ( $text_color != "" ) {
    $heading_styles .= "color: " . esc_attr( $text_color ) . ";";
}

if( $line_height != "" ) {
    $heading_styles .= " line-height: " . esc_attr( $line_height) . "px;";
}

if( $quote_icon_color != "" ) {
    $quote_icon_styles .= "color: " . esc_attr( $quote_icon_color ) . ";";
}

$html .= "<blockquote class='" . esc_attr( $blockquote_classes ) . "' style='" . esc_attr( $blockquote_styles ) . "'>"; //open blockquote
if ( $show_quote_icon == 'yes' ) {
    $html .= "<i class='fa fa-quote-right' style='" . esc_attr( $quote_icon_styles ) ."'></i>";
}

$html .= "<h5 class='blockquote-text' style='" . esc_attr( $heading_styles ) . "'>" . wp_kses_post( $text ) . "</h5>";
$html .= "</blockquote>"; //close blockquote

echo bridge_qode_get_module_part( $html );