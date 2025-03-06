<?php

if ( ! class_exists( 'Bridge_Qode_Quick_View_For_WooCommerce' ) ) {
	class Bridge_Qode_Quick_View_For_WooCommerce {
		private static $instance;

		public function __construct() {

			if ( defined( 'QODE_QUICK_VIEW_FOR_WOOCOMMERCE_VERSION' ) ) {
				// Add button element for product list shortcode
				add_action( 'bridge_qode_action_woocommerce_info_below_image_hover', array( $this, 'render_quick_view_button_shortcode' ), 2 );

				// Set Show Quick View in loop option to no
				add_filter( 'qode_quick_view_for_woocommerce_filter_enable_quick_view_default_value', array( $this, 'change_quick_view_show_in_loop_default_option' ) );
				
				// Set Show Mobile Quick View in loop option to no
				add_filter( 'qode_quick_view_for_woocommerce_filter_enable_quick_view_on_mobile_default_value', array( $this, 'change_quick_view_show_in_loop_default_option' ) );
				
				add_filter( 'qode_quick_view_for_woocommerce_filter_is_enabled', array( $this, 'enable_quick_view_globally' ) );
				
				add_filter( 'body_class', array( $this, 'add_body_class' ) );
				
				add_filter( 'qode_quick_view_for_woocommerce_filter_is_product_image_enabled', '__return_false' );
				
				add_action( 'qode_quick_view_for_woocommerce_action_product_image', array( $this, 'replace_gallery' ), 30 );
				
				add_filter( 'qode_quick_view_for_woocommerce_filter_is_product_meta_enabled', '__return_false' );
				
				add_filter( 'bridge_qode_filter_enqueue_owl_carousel_script', '__return_true' );
			}
		}

		/**
		 * @return Bridge_Qode_Quick_View_For_WooCommerce
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		function render_quick_view_button_shortcode() {
			if ( class_exists( 'Qode_Quick_View_For_WooCommerce_Module' ) ) {
				$quick_view_object = Qode_Quick_View_For_WooCommerce_Module::get_instance();
				$quick_view_object->add_button();
			}
		}

		function change_quick_view_show_in_loop_default_option() {
			return 'no';
		}
		
		function enable_quick_view_globally() {
			return true;
		}
		
		function add_body_class( $classes ) {
			if( function_exists( 'qode_quick_view_for_woocommerce_get_option_value' ) ) {
				$option_enabled        = 'yes' === qode_quick_view_for_woocommerce_get_option_value( 'admin', 'qode_quick_view_for_woocommerce_enable_quick_view' );
				$option_enabled_mobile = 'yes' === qode_quick_view_for_woocommerce_get_option_value( 'admin', 'qode_quick_view_for_woocommerce_enable_quick_view_on_mobile' );
				
				if( ! $option_enabled && bridge_qode_is_woocommerce_page() ) {
					$classes[] = 'qode-remove-quick-view-button-on-default-shop-list';
				}
				
				if( ! $option_enabled_mobile && bridge_qode_is_woocommerce_page() ) {
					$classes[] = 'qode-remove-quick-view-button-on-default-shop-list-on-mobile';
				}
			}
			
			return $classes;
		}
		
		public function replace_gallery() {
			bridge_qode_woocommerce_show_product_images();
		}
	}
	
	Bridge_Qode_Quick_View_For_WooCommerce::get_instance();
}
