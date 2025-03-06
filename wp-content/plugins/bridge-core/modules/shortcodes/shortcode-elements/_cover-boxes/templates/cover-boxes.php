<?php
	
	$html = "";
	$html .= "<div class='cover_boxes' data-active-element='" . esc_attr( $active_element ) . "'><ul class='clearfix'>";
	
	$html .= "<li>";
	$html .= "<div class='box'>";
	if ( $target1 != "" ) {
		$target1 = esc_attr( $target1 );
	} else {
		$target1 = "_self";
	}
	if ( is_numeric( $image1 ) ) {
		$image_src1 = esc_url( wp_get_attachment_url( $image1 ) );
	} else {
		$image_src1 = esc_url( $image1 );
	}
	if ( is_numeric( $image2 ) ) {
		$image_src2 = esc_url( wp_get_attachment_url( $image2 ) );
	} else {
		$image_src2 = esc_url( $image2 );
	}
	if ( is_numeric( $image3 ) ) {
		$image_src3 = esc_url( wp_get_attachment_url( $image3 ) );
	} else {
		$image_src3 = esc_url( $image3 );
	}
	$html .= "<a itemprop='url' class='thumb' href='" . esc_url( $link1 ) . "' target='" . esc_attr( $target1 ) . "'><img itemprop='image' alt='" . esc_attr( $title1 ) . "' src='" . esc_url( $image_src1 ) . "' /></a>";
	
	if ( $title_color1 != "" ) {
		$color1 = " style='color:" . esc_attr( $title_color1 ) . "'";
	} else {
		$color1 = "";
	}
	if ( $text_color1 != "" ) {
		$t_color1 = " style='color:" . esc_attr( $text_color1 ) . "'";
	} else {
		$t_color1 = "";
	}
	$html .= "<div class='box_content'><h3" . esc_attr( $color1 ) . ">" . esc_html( $title1 ) . "</h3>";
	$html .= "<p" . esc_attr( $t_color1 ) . ">" . esc_html( $text1 ) . "</p>";
	
	$button_class               = "";
	$button_class_wrapper_open  = "";
	$button_class_wrapper_close = "";
	if ( $read_more_button_style != "no" ) {
		$button_class = "qbutton tiny";
	} else {
		$button_class               = "cover_boxes_read_more";
		$button_class_wrapper_open  = "<h5>";
		$button_class_wrapper_close = "</h5>";
	}
	
	if ( $link_label1 != "" ) {
		$html .= $button_class_wrapper_open . "<a itemprop='url' class='" . esc_attr( $button_class ) . "' href='" . esc_url( $link1 ) . "' target='" . esc_attr( $target1 ) . "'>" . esc_html( $link_label1 ) . "</a>" . $button_class_wrapper_close;
	}
	
	$html .= "</div></div>";
	$html .= "</li>";
	
	$html .= "<li>";
	$html .= "<div class='box'>";
	if ( $target2 != "" ) {
		$target2 = esc_attr( $target2 );
	} else {
		$target2 = "_self";
	}
	$html .= "<a itemprop='url' class='thumb' href='" . esc_url( $link2 ) . "' target='" . esc_attr( $target2 ) . "'><img itemprop='image' alt='" . esc_attr( $title2 ) . "' src='" . esc_url( $image_src2 ) . "' /></a>";
	
	if ( $title_color2 != "" ) {
		$color2 = " style='color:" . esc_attr( $title_color2 ) . "'";
	} else {
		$color2 = "";
	}
	if ( $text_color2 != "" ) {
		$t_color2 = " style='color:" . esc_attr( $text_color2 ) . "'";
	} else {
		$t_color2 = "";
	}
	$html .= "<div class='box_content'><h3" . esc_attr( $color2 ) . ">" . esc_html( $title2 ) . "</h3>";
	$html .= "<p" . esc_attr( $t_color2 ) . ">" . esc_html( $text2 ) . "</p>";
	
	if ( $link_label2 != "" ) {
		$html .= $button_class_wrapper_open . "<a itemprop='url' class='" . esc_attr( $button_class ) . "' href='" . esc_url( $link2 ) . "' target='" . esc_attr( $target2 ) . "'>" . esc_html( $link_label2 ) . "</a>" . $button_class_wrapper_close;
	}
	
	$html .= "</div></div>";
	$html .= "</li>";
	
	$html .= "<li>";
	$html .= "<div class='box'>";
	if ( $target3 != "" ) {
		$target3 = esc_attr( $target3 );
	} else {
		$target3 = "_self";
	}
	$html .= "<a itemprop='url' class='thumb' href='" . esc_url( $link3 ) . "' target='" . esc_attr( $target3 ) . "'><img itemprop='image' alt='" . esc_attr( $title3 ) . "' src='" . esc_url( $image_src3 ) . "' /></a>";
	
	if ( $title_color3 != "" ) {
		$color3 = " style='color:" . esc_attr( $title_color3 ) . "'";
	} else {
		$color3 = "";
	}
	if ( $text_color3 != "" ) {
		$t_color3 = " style='color:" . esc_attr( $text_color3 ) . "'";
	} else {
		$t_color3 = "";
	}
	$html .= "<div class='box_content'><h3" . esc_attr( $color3 ) . ">" . esc_html( $title3 ) . "</h3>";
	$html .= "<p" . esc_attr( $t_color3 ) . ">" . esc_html( $text3 ) . "</p>";
	
	if ( $link_label3 != "" ) {
		$html .= $button_class_wrapper_open . "<a itemprop='url' class='" . esc_attr( $button_class ) . "' href='" . esc_url( $link3 ) . "' target='" . esc_attr( $target3 ) . "'>" . esc_html( $link_label3 ) . "</a>" . $button_class_wrapper_close;
	}
	
	$html .= "</div></div>";
	$html .= "</li>";
	
	$html .= "</ul></div>";
	
	echo bridge_qode_get_module_part( $html );
