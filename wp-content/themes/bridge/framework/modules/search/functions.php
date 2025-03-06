<?php

if ( ! function_exists( 'bridge_qode_override_search_block_templates' ) ) {
	/**
	 * Function that override `core/search` block template
	 *
	 * @see register_block_core_search()
	 */
	function bridge_qode_override_search_block_templates( $atts ) {
		if ( ! empty( $atts ) && isset( $atts['render_callback'] ) && 'render_block_core_search' === $atts['render_callback'] && function_exists( 'styles_for_block_core_search' ) ) {
			$atts['render_callback'] = 'bridge_qode_render_block_core_search';
		}
		
		return $atts;
	}
	
	add_filter( 'block_type_metadata_settings', 'bridge_qode_override_search_block_templates' );
}

if ( ! function_exists( 'bridge_qode_render_block_core_search' ) ) {
	/**
	 * Function that dynamically renders the `core/search` block
	 *
	 * @param array $attributes - the block attributes
	 *
	 * @return string - the search block markup
	 *
	 * @see render_block_core_search()
	 */
	function bridge_qode_render_block_core_search( $attributes ) {
		static $instance_id = 0;
		
		$attributes = wp_parse_args(
			$attributes,
			array(
				'label'      => esc_html__( 'Search Here', 'bridge' ),
				'buttonText' => esc_html__( 'Search', 'bridge' ),
			)
		);
		
		$input_id            = 'qode-search-form-' . ++ $instance_id;
		$classnames          = classnames_for_block_core_search( $attributes );
		$show_label          = ! empty( $attributes['showLabel'] );
		$use_icon_button     = ! empty( $attributes['buttonUseIcon'] );
		$show_input          = ! ( ( ! empty( $attributes['buttonPosition'] ) && 'button-only' === $attributes['buttonPosition'] ) );
		$show_button         = ! ( ( ! empty( $attributes['buttonPosition'] ) && 'no-button' === $attributes['buttonPosition'] ) );
		$query_params        = ( ! empty( $attributes['query'] ) ) ? $attributes['query'] : array();
		$input_markup        = '';
		$button_markup       = '';
		$query_params_markup = '';
		$inline_styles       = styles_for_block_core_search( $attributes );
		// function get_color_classes_for_block_core_search doesn't exist in wp 5.8 and below
		$color_classes    = function_exists( 'get_color_classes_for_block_core_search' ) ? get_color_classes_for_block_core_search( $attributes ) : '';
		$is_button_inside = ! empty( $attributes['buttonPosition'] ) && 'button-inside' === $attributes['buttonPosition'];
		// border color classes need to be applied to the elements that have a border color
		// function get_border_color_classes_for_block_core_search doesn't exist in wp 5.8 and below
		$border_color_classes = function_exists( 'get_border_color_classes_for_block_core_search' ) ? get_border_color_classes_for_block_core_search( $attributes ) : '';
		
		$label_markup = sprintf(
			'<label for="%1$s" class="qode-search-form-label screen-reader-text">%2$s</label>',
			$input_id,
			empty( $attributes['label'] ) ? esc_html__( 'Search', 'bridge' ) : esc_html( $attributes['label'] )
		);
		if ( $show_label && ! empty( $attributes['label'] ) ) {
			$label_markup = sprintf(
				'<label for="%1$s" class="qode-search-form-label">%2$s</label>',
				$input_id,
				esc_html( $attributes['label'] )
			);
		}
		
		if ( $show_input ) {
			$input_classes = ! $is_button_inside ? $border_color_classes : '';
			$input_markup  = sprintf(
				'<input type="text" id="%s" class="search-field %s" name="s" value="%s" placeholder="%s" %s required />',
				$input_id,
				esc_attr( $input_classes ),
				esc_attr( get_search_query() ),
				esc_attr( $attributes['placeholder'] ),
				// key input doesn't exist in wp 5.8 and below
				array_key_exists( 'input', $inline_styles ) ? $inline_styles['input'] : ''
			);
		}
		
		if ( is_array( $query_params ) && count( $query_params ) > 0 ) {
			foreach ( $query_params as $param => $value ) {
				$query_params_markup .= sprintf(
					'<input type="hidden" name="%s" value="%s" />',
					esc_attr( $param ),
					esc_attr( $value )
				);
			}
		}
		
		if ( $show_button ) {
			$button_internal_markup = '';
			$button_classes         = $color_classes;
			$button_classes         .= ! empty( $attributes['buttonPosition'] ) ? ' qode--' . $attributes['buttonPosition'] : '';
			
			if ( ! $is_button_inside ) {
				$button_classes .= ' ' . $border_color_classes;
			}
			if ( ! $use_icon_button ) {
				if ( ! empty( $attributes['buttonText'] ) ) {
					$button_internal_markup = esc_html( $attributes['buttonText'] );
				}
			} else {
				$button_classes         .= ' qode--has-icon';
				$button_internal_markup = '&#xf002';
			}
			
			$button_markup = sprintf(
				'<input type="submit" class="qode-search-form-button %s" %s value="%s"/>',
				esc_attr( $button_classes ),
				// key button doesn't exist in wp 5.8 and below
				array_key_exists( 'button', $inline_styles ) ? $inline_styles['button'] : '',
				$button_internal_markup
			);
		}
		
		$field_markup_classes = $is_button_inside ? $border_color_classes : '';
		$field_markup         = sprintf(
			'<div class="qode-input-holder %s"%s>%s</div>',
			$field_markup_classes,
			$inline_styles['wrapper'],
			$label_markup . $input_markup . $query_params_markup . $button_markup
		);
		$classnames           .= ' qode-searchform';
		$wrapper_attributes   = get_block_wrapper_attributes( array( 'class' => $classnames ) );
		
		return sprintf(
			'<form id="searchform" role="search" method="get" %s action="%s">%s</form>',
			$wrapper_attributes,
			esc_url( home_url( '/' ) ),
			$field_markup
		);
	}
}