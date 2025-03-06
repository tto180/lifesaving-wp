<?php

$content = preg_replace('#^<\/p>|<p>$#', '', $content);
$image_alt = '';
$banner_classes = array('qode-banner');
if ( $vertical_alignment != '' ) {
    $banner_classes[] = 'qode-banner-va-'. esc_attr( $vertical_alignment );
}

$html = '';
$html .= '<div '. bridge_qode_get_class_attribute( implode(' ', $banner_classes) ) .'>';

if ( $link != '' ) {
    $html .= '<a class="qode-banner-link" href="'. esc_url( $link ) . '" target="'. esc_attr( $target ) .'"></a>';
}

if ( is_numeric( $image ) ) {
    $image_src = wp_get_attachment_url($image);
    $image_alt = get_post_meta($image, '_wp_attachment_image_alt', true);
} else {
    $image_src = $image;
}
$html .= '<div class="qode-banner-image">';
$html .= '<img itemprop="image" src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $image_alt ) . '" />';
$html .= '</div>';

$html .= '<div class="qode-banner-content">';
$html .= '<div class="qode-banner-content-inner">';
$html .= '<div class="qode-banner-text-holder">';
$html .= do_shortcode($content);
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';
$html .= '</div>';

echo bridge_qode_get_module_part( $html );