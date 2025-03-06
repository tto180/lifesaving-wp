<?php
/* Accordion shortcode */

if ( ! function_exists( 'bridge_core_accordion' ) ) {
    function bridge_core_accordion($atts, $content = null) {
	    extract(
		    shortcode_atts(
				array(
					"accordion_type" => ""
				),
				$atts )
	    );
        return '<div class="q_accordion_holder ' . esc_attr( $accordion_type ) . ' clearfix">' . $content . '</div>';
    }
	add_shortcode('accordion', 'bridge_core_accordion' );
}

/* Accordion item shortcode */
if ( ! function_exists( 'bridge_core_accordion_item' ) ) {
    function bridge_core_accordion_item ($atts, $content = null ) {
        extract(
			shortcode_atts(
				array(
					"caption"          => "",
					"title_color"      => "",
					"icon"             => "",
					"icon_color"       => "",
					"background_color" => ""
				),
				$atts)
        );
		
        $html           = '';
        $heading_styles = '';
        $no_icon        = '';
	    
	    if ( $icon == "" ) {
            $no_icon = 'no_icon';
        }
	    
	    if ( $title_color != "" ) {
            $heading_styles .= "color: ". esc_attr( $title_color ) .";";
        }
	    
	    if ( $background_color != "" ) {
            $heading_styles .= " background-color: " . esc_attr( $background_color ) . ";";
        }

        $html .= "<h5 style='" . esc_attr( $heading_styles ) . "'>";
	    if ( $icon != "" ) {
            $html .= '<div class="icon-wrapper"><i class="fa '. esc_attr( $icon ) .'" style="color: ' . esc_attr( $icon_color ) .';"></i></div>';
        }
        $html .= "<div class='accordion_mark'></div><span class='tab-title'>" . esc_html( $caption ) . "</span><span class='accordion_icon_mark'></span></h5><div class='accordion_content " . esc_attr( $no_icon ) . "'><div class='accordion_content_inner'>" . wp_kses_post( $content ) . "</div></div>";

        return $html;
    }
    add_shortcode('accordion_item', 'bridge_core_accordion_item');
}
