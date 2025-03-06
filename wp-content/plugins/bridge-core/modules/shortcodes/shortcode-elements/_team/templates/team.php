<?php
	$headings_array = array('h1', 'h2', 'h3', 'h4', 'h5', 'h6');
	
	//get correct heading value. If provided heading isn't valid get the default one
	$title_tag = ( in_array($title_tag, $headings_array ) ) ? $title_tag : esc_attr( $args['title_tag'] );
	$team_image_title = '';
	
	if ( is_numeric( $team_image ) ) {
		$team_image_src   = wp_get_attachment_url( $team_image );
		$team_image_title = get_the_title( $team_image );
	} else {
		$team_image_src = $team_image;
	}
	
	$q_team_style = array();
	if ( ! empty( $background_color ) ) {
		$q_team_style[] = 'background-color:' . esc_attr( $background_color );
	}
	
	$qteam_box_style = array();
	if ( $box_border == "yes" ) {
		$qteam_box_style[] = "border-style:solid";
		if ( $box_border_color != "" ) {
			$qteam_box_style[] = "border-color:" . esc_attr( $box_border_color );
		}
		if ( $box_border_width != "" ) {
			$qteam_box_style[] = "border-width:" . esc_attr( $box_border_width ) . "px";
		}
	}
	
	$qteam_info_on_hover_box_style = array();
	if ( $type == "info_on_hover" && $overlay_color != "" ) {
		$qteam_info_on_hover_box_style[] = "background-color:" . esc_attr( $overlay_color );
	}
	
	$name_style = array();
	if ( ! empty( $name_color ) ) {
		$name_style[]  = 'color: ' . esc_attr( $name_color );
	}
	
	$position_style = array();
	if ( ! empty( $position_color ) ) {
		$position_style[] = 'color: ' . esc_attr( $position_color );
	}
	
	$separator_style = array();
	if ( ! empty( $separator_color ) ) {
		$separator_style[] = 'background-color: ' . esc_attr( $separator_color );
	}
	
	
	if ( $type == "info_on_hover" ) {
		$html = '<div class="q_team info_on_hover" ' . bridge_qode_get_inline_style( $q_team_style ) . '>';
		$html .=  "<div class='q_team_inner'>";
		if($team_image != "") {
			$html .=  '<div class="q_team_image">';
			$html .= '<img itemprop="image" src="' . esc_url( $team_image_src ) . '" alt="' . esc_attr( $team_image_title ) . '" />';
			$html .=  "<div class='q_team_text' ". bridge_qode_get_inline_style( $qteam_info_on_hover_box_style ) . ">";
			$html .=  "<div class='q_team_text_holder'>";
			$html .=  "<div class='q_team_text_holder_inner'>";
			$html .=  "<div class='q_team_text_inner'>";
			$html .=  "<div class='q_team_title_holder'>";
			$html .=  '<' . esc_attr( $title_tag ) . ' class="q_team_name" ' . bridge_qode_get_inline_style( $name_style ) . '>';
			$html .= esc_html( $team_name );
			$html .=  '</'. esc_attr( $title_tag ) . '>';
			if ( $team_position != "" ) {
				$html .= "<span " . bridge_qode_get_inline_style( $position_style ) . ">" . esc_html($team_position ) . "</span>";
			}
			$html .=  "</div>";
			if($show_separator != "no"){
				$html .=  "<div class='separator small center' " . bridge_qode_get_inline_style( $separator_style ) . "></div>";
			}
			$html .=  "</div>";
			$html .=  "<div class='q_team_social_holder'>";
			if($team_social_icon_1 != "") {
				$html .=  do_shortcode('[social_icons type="normal_social" icon="'. esc_attr( $team_social_icon_1 ) .'" size="fa-2x" link="' . esc_url( $team_social_icon_1_link ) . '" target="' . esc_attr( $team_social_icon_1_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]');
			}
			if($team_social_icon_2 != "") {
				$html .=  do_shortcode('[social_icons type="normal_social" icon="'. esc_attr( $team_social_icon_2 ) .'" size="fa-2x" link="' . esc_url( $team_social_icon_2_link ) . '" target="' . esc_attr( $team_social_icon_2_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]');
			}
			if($team_social_icon_3 != "") {
				$html .=  do_shortcode('[social_icons type="normal_social" icon="'. esc_attr( $team_social_icon_3 ) .'" size="fa-2x" link="' . esc_url( $team_social_icon_3_link ) . '" target="' . esc_attr( $team_social_icon_3_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]');
			}
			if($team_social_icon_4 != "") {
				$html .=  do_shortcode('[social_icons type="normal_social" icon="'. esc_attr( $team_social_icon_4 ) .'" size="fa-2x" link="' . esc_url( $team_social_icon_4_link ) . '" target="' . esc_attr( $team_social_icon_4_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]');
			}
			if($team_social_icon_5 != "") {
				$html .=  do_shortcode('[social_icons type="normal_social" icon="'. esc_attr( $team_social_icon_5 ) .'" size="fa-2x" link="' . esc_url( $team_social_icon_5_link ) . '" target="' . esc_attr( $team_social_icon_5_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]');
			}
			
			$html .=  "</div>";
			$html .=  "</div>";
			$html .=  "</div>";
			$html .=  "</div>";
			$html .=  "</div>";
			
			$html .=  "</div>";
		}
		
		if ( $team_description != "" ) {
			$html .= "<div class='q_team_description_wrapper' " . bridge_qode_get_inline_style( $qteam_box_style ) . ">";
			$html .= "<div class='q_team_description'>";
			$html .= "<div class='q_team_description_inner'>";
			$html .= "<p>" . wp_kses_post( $team_description ) . "</p>";
			$html .= "</div>"; // close q_team_description_inner
			$html .= "</div>"; // close q_team_description
			$html .= "</div>"; // close q_team_description_wrapper
		}
		
		$html .=  "</div>";
	} else if ( $type == "info_description_below_image" ) {
		$html =  "<div class='q_team info_description_below_image";
		if ( $disable_hover == 'yes' ) {
			$html .= " qode_team_disabled_hover'";
		}
		else{
			$html .= "'";
		}
		$html .= bridge_qode_get_inline_style( $q_team_style ) .">";
		$html .=  "<div class='q_team_inner'>";
		if ( $team_image != "" ) {
			$html .=  "<div class='q_team_image'>";
			$html .=  "<div class='q_team_image_holder'>";
			$html .= '<img itemprop="image" src="' . esc_url( $team_image_src ) . '" alt="' . esc_attr( $team_image_title ) . '" />';
			$html .= "</div>";
		}
		$html .=  "<div class='q_team_text' ". bridge_qode_get_inline_style( $qteam_box_style ) .">";
		$html .=  "<div class='q_team_text_inner'>";
		$html .=  "<div class='q_team_title_holder'>";
		$html .=  '<' . esc_attr( $title_tag ) . ' class="q_team_name" ' . bridge_qode_get_inline_style( $name_style ) . '>';
		$html .= esc_html( $team_name );
		$html .=  '</'. esc_attr( $title_tag ) . '>';
		
		if ( $team_position != "" ) {
			$html .= "<span " . bridge_qode_get_inline_style( $position_style ) . ">" . $team_position . "</span>";
		}
		$html .=  "</div>";
		
		if ( $show_separator != "no" ) {
			$html .= "<div class='separator small center' " . bridge_qode_get_inline_style( $separator_style ) . "></div>";
		}
		
		if ( $team_description != "" ) {
			$html .= "<div class='q_team_description_below_image_wrapper'>";
			$html .= "<div class='q_team_description'>";
			$html .= "<div class='q_team_description_inner'>";
			$html .= "<p>" . wp_kses_post( $team_description ) . "</p>";
			$html .= "</div>"; // close q_team_description_inner
			$html .= "</div>"; // close q_team_description
			$html .= "</div>"; // close q_team_description_wrapper
		}
		
		$html .=  "</div>";
		$html .=  "</div>";
		
		$html .=  "<div class='q_team_social_holder'>";
		if($team_social_icon_1 != "") {
			$html .=  do_shortcode('[social_icons type="normal_social" icon="'. esc_attr( $team_social_icon_1 ) .'" size="fa-2x" link="' . esc_url( $team_social_icon_1_link ) . '" target="' . esc_attr( $team_social_icon_1_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]');
		}
		if($team_social_icon_2 != "") {
			$html .=  do_shortcode('[social_icons type="normal_social" icon="'. esc_attr( $team_social_icon_2 ) .'" size="fa-2x" link="' . esc_url( $team_social_icon_2_link ) . '" target="' . esc_attr( $team_social_icon_2_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]');
		}
		if($team_social_icon_3 != "") {
			$html .=  do_shortcode('[social_icons type="normal_social" icon="'. esc_attr( $team_social_icon_3 ) .'" size="fa-2x" link="' . esc_url( $team_social_icon_3_link ) . '" target="' . esc_attr( $team_social_icon_3_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]');
		}
		if($team_social_icon_4 != "") {
			$html .=  do_shortcode('[social_icons type="normal_social" icon="'. esc_attr( $team_social_icon_4 ) .'" size="fa-2x" link="' . esc_url( $team_social_icon_4_link ). '" target="' . esc_attr( $team_social_icon_4_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]');
		}
		if($team_social_icon_5 != "") {
			$html .=  do_shortcode('[social_icons type="normal_social" icon="'. esc_attr( $team_social_icon_5 ) .'" size="fa-2x" link="' . esc_url( $team_social_icon_5_link ) . '" target="' . esc_attr( $team_social_icon_5_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]');
		}
		
		$html .=  "</div>";
		$html .=  "</div>";
		$html .=  "</div>";
		$html .=  "</div>";
	}
	
	else {
		$html = '<div class="q_team" ' . bridge_qode_get_inline_style( $q_team_style ) . '>';
		$html .= '<div class="q_team_inner">';
		if ( $team_image != "" ) {
			$html .=  '<div class="q_team_image">';
			$html .= '<img itemprop="image" src="' . esc_url( $team_image_src ) . '" alt="' . esc_attr( $team_image_title ) . '" />';
			
			if ( $team_description != "" ) {
				$html .= "<div class='q_team_description_wrapper'>";
				$html .= "<div class='q_team_description'>";
				$html .= "<div class='q_team_description_inner'>";
				$html .= "<p>" . wp_kses_post( $team_description ) . "</p>";
				$html .= "</div>"; // close q_team_description_inner
				$html .= "</div>"; // close q_team_description
				$html .= "</div>"; // close q_team_description_wrapper
			}
			
			$html .=  "</div>";
		}
		$html .=  "<div class='q_team_text' ". bridge_qode_get_inline_style( $qteam_box_style ) .">";
		$html .=  '<div class="q_team_text_inner">';
		$html .=  '<div class="q_team_title_holder">';
		$html .=  '<' . esc_attr( $title_tag ) . ' class="q_team_name" '  . bridge_qode_get_inline_style( $name_style ) . ">";
		$html .= esc_html( $team_name );
		$html .=  '</' . esc_attr( $title_tag ) . '>';
		if($team_position != "") {
			$html .= "<span " . bridge_qode_get_inline_style( $position_style ) . ">" . esc_html( $team_position ) . "</span>";
		}
		$html .=  "</div>";
		if($show_separator != "no"){
			$html .=  "<div class='separator small center' " . bridge_qode_get_inline_style( $separator_style ) . "></div>";
		}
		$html .=  "</div>";
		$html .=  "<div class='q_team_social_holder'>";
		if ( $team_social_icon_1 != "" ) {
			$html .= do_shortcode( '[social_icons type="normal_social" icon="' . esc_attr( $team_social_icon_1 ) . '" size="fa-2x" link="' . esc_url( $team_social_icon_1_link ) . '" target="' . esc_attr( $team_social_icon_1_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]' );
		}
		if ( $team_social_icon_2 != "" ) {
			$html .= do_shortcode( '[social_icons type="normal_social" icon="' . esc_attr( $team_social_icon_2 ) . '" size="fa-2x" link="' . esc_url( $team_social_icon_2_link ) . '" target="' . esc_attr( $team_social_icon_2_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]' );
		}
		if ( $team_social_icon_3 != "" ) {
			$html .= do_shortcode( '[social_icons type="normal_social" icon="' . esc_attr( $team_social_icon_3 ) . '" size="fa-2x" link="' . esc_url( $team_social_icon_3_link ) . '" target="' . esc_attr( $team_social_icon_3_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]' );
		}
		if ( $team_social_icon_4 != "" ) {
			$html .= do_shortcode( '[social_icons type="normal_social" icon="' . esc_attr( $team_social_icon_4 ) . '" size="fa-2x" link="' . esc_url( $team_social_icon_4_link ) . '" target="' . esc_attr( $team_social_icon_4_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]' );
		}
		if ( $team_social_icon_5 != "" ) {
			$html .= do_shortcode( '[social_icons type="normal_social" icon="' . esc_attr( $team_social_icon_5 ) . '" size="fa-2x" link="' . esc_url( $team_social_icon_5_link ) . '" target="' . esc_attr( $team_social_icon_5_target ) . '" icon_color="' . esc_attr( $icons_color ) . '"]' );
		}
		
		$html .=  "</div>";
		$html .=  "</div>";
		$html .=  "</div>";
		$html .=  "</div>";
	}
	
	echo bridge_qode_get_module_part( $html );
