<?php
/* Dropcaps shortcode */
if ( ! function_exists( 'bridge_core_dropcaps' ) ) {
	function bridge_core_dropcaps( $atts, $content = null ) {
        $args = array(
	        "color"            => "",
	        "font_size"        => "",
	        "background_color" => "",
	        "border_color"     => "",
	        "type"             => ""
        );
        extract(shortcode_atts($args, $atts));
	    
	    $html = "<span class='q_dropcap " . esc_attr( $type ) . "' style='";
		
	    if ( $background_color != "" ) {
		    $html .= 'background-color:' . esc_attr( $background_color ) . ';';
	    }
	    if ( $color != "" ) {
		    $html .= 'color:' . esc_attr( $color ) . ';';
	    }
	    if ( $font_size != "" ) {
		    $html .= 'font-size: ' . esc_attr( $font_size ) . 'px;';
	    }
	    if ( $border_color != "" ) {
		    $html .= 'border-color:' . esc_attr( $border_color ) . ';';
	    }
        $html .= "'>" . $content  . "</span>";

        return $html;
    }
    add_shortcode('dropcaps', 'bridge_core_dropcaps');
}