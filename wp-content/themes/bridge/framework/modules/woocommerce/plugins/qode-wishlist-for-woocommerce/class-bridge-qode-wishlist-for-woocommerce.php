<?php

if ( ! class_exists( 'Bridge_Qode_Wishlist_For_WooCommerce' ) ) {
	class Bridge_Qode_Wishlist_For_WooCommerce {
		private static $instance;

		public function __construct() {

			if ( defined( 'QODE_WISHLIST_FOR_WOOCOMMERCE_VERSION' ) ) {
				// Add button element for product list shortcode
				add_action( 'bridge_qode_action_woocommerce_info_below_image_hover', array( $this, 'render_add_to_wishlist_shortcode' ), 2 );

				// Set Show Add To Wishlist in loop option to no
				add_filter( 'qode_wishlist_for_woocommerce_filter_show_button_in_loop_default_value', array( $this, 'change_add_to_wishlist_show_in_loop' ) );
				
				// Change Add to Wishlist behavior - default value
				add_filter( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_behavior_default_value', array( $this, 'change_add_to_wishlist_behavior' ) );

				// Change Add to Wishlist type - default value
				add_filter( 'qode_wishlist_for_woocommerce_filter_add_to_wishlist_loop_type_default_value', array( $this, 'change_add_to_wishlist_loop_type' ) );
			}
		}

		/**
		 * @return Bridge_Qode_Wishlist_For_WooCommerce
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function render_add_to_wishlist_shortcode() {
			echo do_shortcode( '[qode_wishlist_for_woocommerce_add_to_wishlist button_type="icon"]' );
		}

		function change_add_to_wishlist_show_in_loop( $default_value ) {
			return 'no';
		}
		
		function change_add_to_wishlist_behavior( $default_value ) {
			return 'view';
		}

		function change_add_to_wishlist_loop_type( $default_value ) {
			return 'icon';
		}
	}
	
	Bridge_Qode_Wishlist_For_WooCommerce::get_instance();
}
