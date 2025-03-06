<?php

//init variables
$html = "";
$parallax_layers_data_styles = '';
$parallax_layers_holder_styles = '';
$parallax_layers_holder_classes = '';

//is full screen height for the slider set?
if ($full_screen == 'yes') {
    $parallax_layers_holder_classes .= ' full_screen_height';
}

//is height for the slider set?
if ($height !== '' && $full_screen == 'no') {
    $parallax_layers_holder_styles .= 'height: ' . esc_attr( $height ) . 'px;';
    $parallax_layers_data_styles = 'data-height="' . esc_attr( $height ) . '"';
}

$html .= "<div class='qode_parallax_layers " . esc_attr( $parallax_layers_holder_classes ) . "' style='" . esc_attr( $parallax_layers_holder_styles ) . "' ".esc_attr($parallax_layers_data_styles)."><div class='qode_parallax_layers_holder preload_parallax_layers'>";

if ( $images != '' ) {
    $parallax_images_array = explode(',', $images);
}

if (isset($parallax_images_array) && count($parallax_images_array) != 0) {

    foreach ($parallax_images_array as $pimg_id) {
        $pimage_src = wp_get_attachment_image_src($pimg_id, 'full', true);
        $pimage_alt = get_post_meta($pimg_id, '_wp_attachment_image_alt', true);
        $pimage_src = $pimage_src[0];

        $html .= '<div class="image" style="background-image:url(' . esc_url( $pimage_src ) . ');" ></div>';
    }
}

if ( $content != "" ) {
    $html .= '<div class="paralax_layers_content_holder"><div class="paralax_layers_content"><div class="paralax_layers_content_inner"><div class="container_inner">'.do_shortcode($content).'</div></div></div></div>';
}

$html .= '</div></div>';

echo bridge_qode_get_module_part( $html );