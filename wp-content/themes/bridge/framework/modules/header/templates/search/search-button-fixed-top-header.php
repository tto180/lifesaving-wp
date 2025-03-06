<?php if(bridge_qode_options()->getOptionValue('enable_search') == 'yes') {
	$qodeIconCollections = bridge_qode_return_icon_collections();
	$search_type_class = 'search_covers_header';
	$icon_pack = bridge_qode_options()->getOptionValue('search_icon_pack');
	?>
    <a class="search_button <?php echo esc_attr($search_type_class); ?> <?php echo esc_attr($header_button_size); ?>" href="javascript:void(0)">
		<?php if( 'svg_path' !== $icon_pack ) {
			$qodeIconCollections->getSearchIcon( $icon_pack );
		} else {
			$search_icon_svg = bridge_qode_options()->getOptionValue('search_icon_svg_opener');
			if( ! empty( $search_icon_svg ) ) {
				echo bridge_qode_get_module_part( $search_icon_svg );
			}
		} ?>
    </a>
<?php } ?>