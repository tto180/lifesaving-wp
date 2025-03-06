<?php
	
	$html = '';
	$holder_classes = array(
		'custom_font_holder'
	);
	$holder_classes[] = ! empty( $custom_class ) ? esc_attr( $custom_class ) : '';
	
	$html .= '<div class="' . implode( ' ', $holder_classes ) . '" style="';
	
	if ( ! empty( $font_family ) ) {
		$html .= 'font-family: ' . esc_attr( $font_family ) . ';';
	}
	
	if ( ! empty( $font_style ) ) {
		$html .= ' font-style: ' . esc_attr( $font_style ) . ';';
	}
	
	if ( ! empty( $font_weight ) ) {
		$html .= ' font-weight: ' . esc_attr( $font_weight ) . ';';
	}
	
	if ( ! empty( $color ) ) {
		$html .= ' color: ' . esc_attr( $color ) . ';';
	}
	
	if ( ! empty( $text_decoration ) ) {
		$html .= ' text-decoration: ' . esc_attr( $text_decoration ) . ';';
	}
	
	if ( $text_shadow === "yes" ) {
		$html .= ' text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.4);';
	}
	
	if ( ! empty( $letter_spacing ) ) {
		$html .= ' letter-spacing: ' . esc_attr( $letter_spacing ) . 'px;';
	}
	
	if ( ! empty( $background_color ) ) {
		$html .= ' background-color: ' . esc_attr( $background_color ) . ';';
	}
	
	if ( ! empty( $padding ) ) {
		$html .= ' padding: ' . esc_attr( $padding ) . ';';
	}
	
	if ( ! empty( $margin ) ) {
		$html .= ' margin: ' . esc_attr( $margin ) . ';';
	}
	
	$border = '';
	if ( ! empty( $border_color ) ) {
		$border .= 'border: 1px solid ' . esc_attr( $border_color ) . ';';
		
		if ( ! empty( $border_width ) ) {
			$border .= 'border-width: ' . esc_attr( $border_width ) . 'px;';
		}
	} elseif ( ! empty( $border_width ) ) {
		$border .= 'border: ' . esc_attr( $border_width ) . 'px solid;';
	}
	
	$html .= $border;
	$html .= ' text-align: ' . esc_attr( $text_align ) . ';';
	
	if ( ! empty( $font_size ) ) {
		$html .= ' --qode-cf-font-size: ' . esc_attr( $font_size ) . 'px;';
	}
	
	if ( ! empty( $line_height ) ) {
		$html .= ' --qode-cf-line-height: ' . esc_attr( $line_height ) . 'px;';
	}
	
	$screen_sizes = array( '1440', '1366', '1280', '1024', '768', '680', '480' );
	
	for ( $i = 0; $i < count( $screen_sizes ); $i++ ) {
		
		if ( $i === 0 && empty( $params[ 'font_size_' . $screen_sizes[ $i ] ] ) ) {
			$params[ 'font_size_' . $screen_sizes[ $i ] ] = $font_size;
		} elseif ( empty( $params[ 'font_size_' . $screen_sizes[ $i ] ] ) ) {
			$params[ 'font_size_' . $screen_sizes[ $i ] ] = $params[ 'font_size_' . $screen_sizes[ $i - 1 ] ];
		}
		
		$responsive_font_size = $params[ 'font_size_' . $screen_sizes[ $i ] ];
		
		if ( ! empty( $responsive_font_size ) ) {
			$html .= ' --qode-cf-font-size-' . esc_attr( $screen_sizes[ $i ] ) . ': ' . intval( $responsive_font_size ) . 'px;';
		}
		
		if ( $i === 0 && empty( $params[ 'line_height_' . $screen_sizes[ $i ] ] ) ) {
			$params[ 'line_height_' . $screen_sizes[ $i ] ] = $line_height;
		} elseif ( empty( $params[ 'line_height_' . $screen_sizes[ $i ] ] ) ) {
			$params[ 'line_height_' . $screen_sizes[ $i ] ] = $params[ 'line_height_' . $screen_sizes[ $i - 1 ] ];
		}
		
		$responsive_line_height = $params[ 'line_height_' . $screen_sizes[ $i ] ];
		
		if ( ! empty( $responsive_line_height ) ) {
			$html .= ' --qode-cf-line-height-' . esc_attr( $screen_sizes[ $i ] ) . ': ' . intval( $responsive_line_height ) . 'px;';
		}
	}
	
	$html .= '">' . bridge_core_get_custom_font_modified_title( $params, $content ) . '</div>';
	echo bridge_qode_get_module_part( $html );
