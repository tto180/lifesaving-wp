<?php
	
	$id                = mt_rand(
		1000,
		9999
	);
	$month_label_value = esc_html__('Months', 'bridge-core' );
	if ( $month_label != "" ) {
		$month_label_value = esc_html( $month_label );
	}
	$day_label_value = esc_html__('Days', 'bridge-core' );
	if ( $day_label != "" ) {
		$day_label_value = $day_label;
	}
	$hour_label_value = esc_html__('Hours', 'bridge-core' );
	if ( $hour_label != "" ) {
		$hour_label_value = esc_html( $hour_label );
	}
	$minute_label_value = esc_html__('Minutes', 'bridge-core' );
	if ( $minute_label != "" ) {
		$minute_label_value = esc_html( $minute_label );
	}
	$second_label_value = esc_html__('Seconds', 'bridge-core' );
	if ( $second_label != "" ) {
		$second_label_value = esc_html( $second_label );
	}
	
	$month_singular_label_value = esc_html__('Month', 'bridge-core' );
	if ( $month_singular_label != "" ) {
		$month_singular_label_value = esc_html( $month_singular_label );
	}
	$day_singular_label_value = esc_html__('Day', 'bridge-core' );
	if ( $day_singular_label != "" ) {
		$day_singular_label_value = esc_html( $day_singular_label );
	}
	$hour_singular_label_value = esc_html__('Hour', 'bridge-core' );
	if ( $hour_singular_label != "" ) {
		$hour_singular_label_value = esc_html( $hour_singular_label );
	}
	$minute_singular_label_value = esc_html__('Minute', 'bridge-core' );
	if ( $minute_singular_label != "" ) {
		$minute_singular_label_value = esc_html( $minute_singular_label );
	}
	$second_singular_label_value = esc_html__('Second', 'bridge-core' );
	if ( $second_singular_label != "" ) {
		$second_singular_label_value = esc_html( $second_singular_label );
	}
	
	$counter_style = array();
	if ( $color != "" ) {
		$counter_style[] = "color:" . esc_attr( $color );
	}
	if ( $font_weight != "" ) {
		$counter_style[] = "font-weight:" . esc_attr( $font_weight );
	}
	
	$data_attr = "";
	if ( $year !== '' ) {
		$data_attr .= 'data-year = ' . esc_attr( $year );
	}
	if ( $month !== '' ) {
		$data_attr .= ' data-month = ' . esc_attr( $month );
	}
	if ( $day !== '' ) {
		$data_attr .= ' data-day = ' . esc_attr( $day );
	}
	if ( $hour !== '' ) {
		$data_attr .= ' data-hour = ' . esc_attr( $hour );
	}
	if ( $minute !== '' ) {
		$data_attr .= ' data-minute = ' . esc_attr( $minute );
	}
	$data_attr .= ' data-monthsLabel = ' . esc_attr( $month_label_value );
	$data_attr .= ' data-daysLabel = ' . esc_attr( $day_label_value );
	$data_attr .= ' data-hoursLabel = ' . esc_attr( $hour_label_value );
	$data_attr .= ' data-minutesLabel = ' . esc_attr( $minute_label_value );
	$data_attr .= ' data-secondsLabel = ' . esc_attr( $second_label_value );
	$data_attr .= ' data-monthLabel = ' . esc_attr( $month_singular_label_value );
	$data_attr .= ' data-dayLabel = ' . esc_attr( $day_singular_label_value );
	$data_attr .= ' data-hourLabel = ' . esc_attr( $hour_singular_label_value );
	$data_attr .= ' data-minuteLabel = ' . esc_attr( $minute_singular_label_value );
	$data_attr .= ' data-secondLabel = ' . esc_attr( $second_singular_label_value );
	$data_attr .= ' data-tickf = setCountdownStyle' . esc_attr( $id );
	$data_attr .= ' data-timezone = ' . get_option( 'gmt_offset' );
	
	if ( $digit_font_size !== '' ) {
		$data_attr .= ' data-digitfs = ' . esc_attr( $digit_font_size );
	}
	if ( $label_font_size !== '' ) {
		$data_attr .= ' data-labelfs = ' .  esc_attr( $label_font_size );
	}
	if ( $color !== '' ) {
		$data_attr .= ' data-color = ' .  esc_attr( $color );
	}
	
	$html = "<div class='countdown " .  esc_attr( $show_separator ) . "' id='countdown" .  esc_attr( $id ) . "' " .  bridge_qode_get_inline_style( $counter_style ) . " " .  esc_attr( $data_attr ). "></div>";
	
	echo bridge_qode_get_module_part( $html );