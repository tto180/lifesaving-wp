<?php
/* Highlights shortcode */
if ( ! function_exists( 'bridge_core_highlight' ) ) {
    function bridge_core_highlight( $atts, $content = null ) {
        extract(shortcode_atts( array( "color" => "", "background_color" => "" ), $atts ) );
        $html =  "<span class='highlight'";
	    if ( $color != "" || $background_color != "" ) {
            $html .= " style='color: " . esc_attr( $color ) ."; background-color:" . esc_attr( $background_color ) . ";'";
        }
        $html .= ">" . wp_kses_post( $content ) . "</span>";
        return $html;
    }
    add_shortcode('highlight', 'bridge_core_highlight');
}