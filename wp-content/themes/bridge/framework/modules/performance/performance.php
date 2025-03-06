<?php

if ( ! class_exists( 'BridgeQodePerformance' ) ) {
	class BridgeQodePerformance {
		private static $instance;
		
		private $page_id;
		
		private $page_template = '';
		
		private $page_content = '';
		
		public static function get_instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {
				self::$instance = new self();
			}
			
			return self::$instance;
		}
		
		function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'setup_scripts_loading' ) );
		}
		
		public function setup_scripts_loading() {
			$this->setup_initial_info();
			$this->enqueue_one_page_scroll_script();
			$this->enqueue_bigtext_script();
			$this->enqueue_infinite_scroll_script();
			$this->enqueue_flexslider_script();
			$this->enqueue_fluidvids_script();
			$this->enqueue_fitvids_script();
			$this->enqueue_qode_slider_scripts();
			$this->enqueue_nice_scroll_scripts();
			$this->enqueue_owl_carousel_script();
			$this->enqueue_google_maps_api();
			$this->enqueue_portfolio_single_additional_assets();
			$this->enqueue_wpb_shortcodes_scripts();
		}
		
		private function setup_initial_info() {
			$page_id = get_queried_object_id();
			
			if( $page_id ) {
				$this->page_id = $page_id;
				$this->page_template = get_page_template_slug( $page_id );
				
				$page_object = get_post( $page_id );
				if( $page_object ) {
					$this->page_content = $page_object->post_content;
				}
			}
		}
		
		private function enqueue_one_page_scroll_script() {
			$should_enqueue = false;
			
			if( 'full_screen.php' === $this->page_template ) {
				$should_enqueue = true;
			}
			
			$should_enqueue = apply_filters( 'bridge_qode_filter_enqueue_one_page_scroll_script', $should_enqueue );
			
			if( $should_enqueue ) {
				wp_enqueue_script( 'one_page_scroll' );
			}
		}
		
		private function enqueue_bigtext_script() {
			$should_enqueue = false;
			
			if( 'blog-headlines.php' === $this->page_template ) {
				$should_enqueue = true;
			}
			
			$should_enqueue = apply_filters( 'bridge_qode_filter_enqueue_bigtext_script', $should_enqueue );
			
			if( $should_enqueue ) {
				wp_enqueue_script( 'bigtext' );
			}
		}
		
		private function enqueue_infinite_scroll_script() {
			$should_enqueue = false;
			$pagination_type = bridge_qode_options()->getOptionValue( 'pagination_masonry' );
			
			if( 'infinite_scroll' === $pagination_type && false !== strpos( $this->page_template, 'blog' ) ) {
				$should_enqueue = true;
			}
			
			$should_enqueue = apply_filters( 'bridge_qode_filter_enqueue_infinite_scroll_script', $should_enqueue );
			
			if( $should_enqueue ) {
				wp_enqueue_script( 'infiniteScroll' );
			}
		}
		
		private function enqueue_flexslider_script() {
			$should_enqueue = false;
			
			if( is_home() || false !== strpos( $this->page_template, 'blog' ) || is_singular( 'post' ) || is_singular( 'portfolio_page' ) ) {
				$should_enqueue = true;
			}
			
			$content_to_check_array = array();
			
			if( ! empty( $this->page_content ) ) {
				$content_to_check_array[] = $this->page_content;
			}
				
			$page_rev_slider_meta_field = get_post_meta( $this->page_id, 'qode_revolution-slider', true );
			if( ! empty( $page_rev_slider_meta_field ) ) {
				$content_to_check_array[] = $page_rev_slider_meta_field;
			}
			
			$shortcodes_to_check = array(
				'qode_elliptical_slider',
				'masonry_blog',
				'testimonials_carousel',
				'testimonials',
				'qode_content_slider',
				'qode_in_device_slider',
				'qode_preview_slider',
				'vc_gallery'
			);
			
			if( count( $content_to_check_array ) > 0 ) {
				foreach ( $content_to_check_array as $content_to_check ) {
					foreach( $shortcodes_to_check as $shortcode_to_check ) {
						if( has_shortcode( $content_to_check, $shortcode_to_check ) ) {
							$should_enqueue = true;
							break 2;
						}
					}
				}
			}
			
			$should_enqueue = apply_filters( 'bridge_qode_filter_enqueue_flexslider_script', $should_enqueue );
			
			if( $should_enqueue ) {
				wp_enqueue_script( 'flexslider' );
				wp_enqueue_script( 'touchSwipe' );
				wp_enqueue_script( 'fitvids' );
			}
		}
		
		private function enqueue_fluidvids_script() {
			$should_enqueue = apply_filters( 'bridge_qode_filter_enqueue_fluidvids_script', false );
			
			if( $should_enqueue ) {
				wp_enqueue_script( 'fluidvids' );
			}
		}
		
		private function enqueue_fitvids_script() {
			$should_enqueue = false;
			
			if( is_singular( 'portfolio_page' ) ) {
				$should_enqueue = true;
			}
			
			if( is_single() && 'video' === get_post_format() ) {
				$should_enqueue = true;
			}
			
			if( ! empty( $this->page_content ) ) {
				$shortcodes_to_check = apply_filters( 'bridge_qode_filter_shortcodes_to_check_for_fitvids_script', array( 'masonry_blog' ) );
				
				foreach( $shortcodes_to_check as $shortcode_to_check )
					if( has_shortcode( $this->page_content, $shortcode_to_check ) ) {
						$should_enqueue = true;
						break;
					}
			}
			
			$should_enqueue = apply_filters( 'bridge_qode_filter_enqueue_fitvids_script', $should_enqueue );
			
			if( $should_enqueue ) {
				wp_enqueue_script( 'fitvids' );
			}
		}
		
		private function enqueue_qode_slider_scripts() {
			$should_enqueue = false;
			
			$page_slider_meta_field = get_post_meta( $this->page_id, 'qode_revolution-slider', true );
			if( ! empty( $page_slider_meta_field ) ) {
				if( false !== strpos( $page_slider_meta_field, 'qode_slider' ) ) {
					$should_enqueue = true;
				}
			}
			
			$should_enqueue = apply_filters( 'bridge_qode_filter_enqueue_qode_slider_scripts', $should_enqueue );
			
			if( $should_enqueue ) {
				wp_enqueue_script( 'bootstrapCarousel' );
				wp_enqueue_script( 'qode-slider' );
				wp_enqueue_script( 'touchSwipe' );
			}
		}
		
		private function enqueue_nice_scroll_scripts() {
			$should_enqueue = false;
			
			$side_area_enabled = bridge_qode_options()->getOptionValue( 'enable_side_area' );
			if( ! empty( $side_area_enabled ) && 'yes' === $side_area_enabled ) {
				$should_enqueue = true;
			}
			
			$left_menu_enabled = bridge_qode_options()->getOptionValue( 'vertical_area' );
			if( ! empty( $left_menu_enabled ) && 'yes' === $left_menu_enabled ) {
				$should_enqueue = true;
			}
			
			$popup_menu_enabled = bridge_qode_options()->getOptionValue( 'enable_popup_menu' );
			if( ! empty( $popup_menu_enabled ) && 'yes' === $popup_menu_enabled ) {
				$should_enqueue = true;
			}
			
			$should_enqueue = apply_filters( 'bridge_qode_filter_enqueue_nice_scroll_scripts', $should_enqueue );
			
			if( $should_enqueue ) {
				wp_enqueue_script( 'niceScroll' );
				wp_enqueue_script( 'qode-nice-scroll' );
			}
		}
		
		private function enqueue_owl_carousel_script() {
			$should_enqueue = false;
			
			$content_to_check_array = array();
			if( ! empty( $this->page_content ) ) {
				$content_to_check_array[] = $this->page_content;
			}
			
			$page_rev_slider_meta_field = get_post_meta( $this->page_id, 'qode_revolution-slider', true );
			if( ! empty( $page_rev_slider_meta_field ) ) {
				$content_to_check_array[] = $page_rev_slider_meta_field;
			}
			
			$shortcodes_to_check = apply_filters( 'bridge_qode_filter_owl_carousel_shortcodes', array( 'qode_advanced_image_gallery' ) );
			
			if( count( $content_to_check_array ) > 0 ) {
				foreach( $content_to_check_array as $content_to_check ) {
					foreach( $shortcodes_to_check as $shortcode_to_check ) {
						if( has_shortcode( $content_to_check, $shortcode_to_check ) ) {
							$should_enqueue = true;
							break 2;
						}
					}
				}
			}
			
			$should_enqueue = apply_filters( 'bridge_qode_filter_enqueue_owl_carousel_script', $should_enqueue );
			
			if( $should_enqueue ) {
				wp_enqueue_script( 'owlCarousel' );
				wp_enqueue_script( 'qode-owl-slider' );
			}
		}
		
		private function enqueue_google_maps_api() {
			$should_enqueue = false;
			$google_maps_api_key = bridge_qode_options()->getOptionValue( 'google_maps_api_key' );
			
			if( ! empty( $google_maps_api_key ) ) {
				if( ! empty( $this->page_content ) ) {
					if( has_shortcode( $this->page_content, 'qode_google_map' ) ) {
						$should_enqueue = true;
					}
				}
				
				if( 'contact-page.php' === $this->page_template ) {
					$should_enqueue = true;
				}
			}
			
			$should_enqueue = apply_filters( 'bridge_qode_filter_enqueue_google_maps_api', $should_enqueue );
			
			if( $should_enqueue ) {
				wp_enqueue_script( 'google-map-api' );
				wp_enqueue_script( 'qode-google-map' );
			}
		}
		
		private function enqueue_portfolio_single_additional_assets() {
			//enqueue portfolio list assets for related posts
			$related_posts_option = bridge_qode_options()->getOptionValue( 'enable_portfolio_related' );
			if( ! empty( $related_posts_option ) && 'yes' === $related_posts_option && is_singular( 'portfolio_page' ) ) {
				wp_enqueue_script( 'mixItUp' );
				wp_enqueue_script( 'qode-portfolio-list' );
			}
		}
		
		private function enqueue_wpb_shortcodes_scripts() {
			$scripts_array = $this->get_shortcode_3rd_party_scripts_array();
			
			if( ! empty( $this->page_content ) ) {
				if( is_array( $scripts_array ) && count( $scripts_array ) > 0 ) {
					foreach( $scripts_array as $script => $shortcodes_to_check ) {
						foreach( $shortcodes_to_check as $shortcode_to_check => $shortcode_script ) {
							if( has_shortcode( $this->page_content, $shortcode_to_check ) ) {
								wp_enqueue_script( $script );
								if ( $shortcode_script ) {
									wp_enqueue_script( $shortcode_script );
								}
							}
						}
					}
				}
			}
		}
		
		public function get_shortcode_3rd_party_scripts_array() {
			return apply_filters(
				'bridge_qode_filter_wpb_shortcode_scripts_array',
				array(
					'abstractBaseClass' => array(
						'countdown' => 'qode-countdown',
					),
					'countdown' => array(
						'countdown' => 'qode-countdown',
					),
					'twentytwenty' => array(
						'qode_comparison_slider' => 'qode-comparison-slider'
					),
					'eventMove' => array(
						'qode_comparison_slider' => 'qode-comparison-slider'
					),
					'typed' => array(
						'custom_font' => 'qode-custom-font'
					),
					'counter' => array(
						'counter' => 'qode-counter'
					),
					'countTo' => array(
						'counter' => 'qode-counter',
						'progress_bar' => 'qode-progress-bar',
						'progress_bar_icon' => 'qode-progress-bar-icon',
						'progress_bar_vertical' => 'qode-progress-bar-vertical',
						'pie_chart' => 'qode-pie-chart'
					),
					'easyPieChart' => array(
						'pie_chart' => 'qode-pie-chart',
						'pie_chart_with_icon' => 'qode-pie-chart-with-icon'
					),
					'chart' => array(
						'pie_chart2' => 'qode-pie-chart-full',
						'pie_chart3' => 'qode-pie-chart-doughnut',
						'line_graph' => 'qode-line-graph'
					),
					'lemmonSlider' => array(
						'image_slider_no_space' => 'qode-image-slider-no-space'
					),
					'swiper' => array(
						'qode_inverted_portfolio_slider' => 'qode-inverted-portfolio-slider',
						'qode_numbered_carousel' => 'qode-numbered-carousel',
						'qode_portfolio_carousel' => 'qode-portfolio-carousel',
						'qode_portfolio_project_slider' => 'qode-portfolio-project-slider',
						'qode_vertical_portfolio_slider' => 'qode-vertical-portfolio-slider'
					),
					'mixItUp' => array(
						'portfolio_list' => 'qode-portfolio-list'
					),
					'justifiedGallery' => array(
						'portfolio_list' => 'qode-portfolio-list'
					),
					'carouFredSel' => array(
						'blog_slider' => 'qode-blog-slider',
						'qode_carousel' => 'qode-carousel',
						'portfolio_slider' => 'qode-portfolio-slider',
						'qode_blog_carousel_titled' => 'qode-blog-carousel-titled'
					),
					'rangeSlider' => array(
						'qode_interest_rate_calculator' => 'qode-interest-rate-calculator'
					),
					'multiscroll' => array(
						'qode_vertical_split_slider' => 'qode-vertical-split-slider'
					),
					'stretch' => array(
						'text_marquee' => 'qode-text-marquee'
					),
					'imagesLoaded' => array(
						'qode_multi_device_showcase' => 'qode-multi-device-showcase'
					),
					'touchSwipe' => array(
						'qode_cards_gallery' => false,
						'qode_cards_slider' => false,
						'blog_slider' => 'qode-blog-slider',
						'qode_carousel' => 'qode-carousel',
						'portfolio_slider' => 'qode-portfolio-slider',
						'qode_blog_carousel_titled' => 'qode-blog-carousel-titled'
					),
					'packery' => array(
						'qode_advanced_image_gallery' => false,
						'qode_product_list' => false
					),
				)
			);
		}
	}
	
	BridgeQodePerformance::get_instance();
}
