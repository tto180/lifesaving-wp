<?php

$headings_array = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');

//get correct heading value. If provided heading isn't valid get the default one
$title_tag = ( in_array( $title_tag, $headings_array ) ) ? $title_tag : $args['title_tag'];

$html = '';
$html .= '<div class="image_with_text">';
if ( is_numeric( $image ) ) {
    $image_src = wp_get_attachment_url( $image );
} else {
    $image_src = $image;
}

if( ! empty( $link ) && 'image_title' === $link_position ) {
	$html .= '<a itemprop="url" href="' . esc_url( $link ) . '" target="' . esc_attr( $link_target ) . '">';
}
$html .= '<img itemprop="image" src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $title ) . '" />';
if( ! empty( $link ) && 'image_title' === $link_position ) {
	$html .= '</a>';
}
$html .= '<'. esc_attr( $title_tag ) .' ';
if ($title_color != "") {
    $html .= 'style="color:' . esc_attr( $title_color ) . ';"';
}
$html .= ' class="image_with_text_title">';
if( ! empty( $link ) && 'image_title' === $link_position ) {
	$html .= '<a itemprop="url" href="' . esc_url( $link ) . '" target="' . esc_attr( $link_target ) . '">';
}
$html .= $title;
if( ! empty( $link ) && 'image_title' === $link_position ) {
	$html .= '</a>';
}
$html .= '</' . esc_attr( $title_tag ) . '>';
$html .= '<span style="margin: 6px 0px;" class="separator transparent"></span>';
$html .= do_shortcode( $content );
if( ! empty( $link ) && 'overlay' === $link_position ) {
	$html .= '<a itemprop="url" class="image-with-text-link-overlay" href="' . esc_url( $link ) . '" target="' . esc_attr( $link_target ) . '"></a>';
}
$html .= '</div>';

echo bridge_qode_get_module_part( $html );