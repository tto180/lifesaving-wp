<?php
$list_item_classes = array(
	'q_list'
);

if ( $style != "" ) {
	$list_item_classes[] = esc_attr( $style );
}

if ( $number_type != "" ) {
	$list_item_classes[] = esc_attr( $number_type );
}

if ( $font_weight != "" ) {
	$list_item_classes[] = esc_attr( $font_weight );
}

if ( $animate != "" && $animate == "yes" ) {
	$list_item_classes[] = 'animate_list';
}
?>
<div <?php echo bridge_qode_get_class_attribute( implode(' ', $list_item_classes) ); ?>>
	<?php echo wp_kses_post( $content ); ?>
</div>