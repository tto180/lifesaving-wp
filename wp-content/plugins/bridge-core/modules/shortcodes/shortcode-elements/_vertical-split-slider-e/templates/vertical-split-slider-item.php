<?php

$qode_splitted_item_style = array();
$qode_splitted_item_data = "";

if ( ! empty( $background_color ) ) {
	$qode_splitted_item_style[] = "background-color:" . esc_attr( $background_color );
}

if ( ! empty( $background_image ) ) {
	$background_image_src = wp_get_attachment_url( $background_image );
	$qode_splitted_item_style[] = "background-image:url(" . esc_url( $background_image_src ) . ")";
}

if ( ! empty( $aligment ) ) {
	$qode_splitted_item_style[] = "text-align:" . esc_attr( $aligment );
}

if ( $item_padding != "" ) {
	$qode_splitted_item_style[] = "padding:0px " . esc_attr( $item_padding );
}

$qode_splitted_item_data = "data-header_style='" . esc_attr( $header_style ) . "'"; //render empty value also, in order to remove header style if needed

?>

<div class="ms-section" <?php echo bridge_qode_get_inline_style( $qode_splitted_item_style ); ?> <?php echo bridge_qode_get_module_part( $qode_splitted_item_data );?>>
    <?php echo bridge_qode_get_module_part( $content ); ?>
</div>