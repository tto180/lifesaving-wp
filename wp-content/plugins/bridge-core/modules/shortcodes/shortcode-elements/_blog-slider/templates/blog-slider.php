<?php
	
	$qode_options = bridge_qode_return_global_options();
	
	$headings_array = array( 'h2', 'h3', 'h4', 'h5', 'h6' );
	
	//get correct heading value. If provided heading isn't valid get the default one
	$title_tag = ( in_array(
		$title_tag,
		$headings_array
	) ) ? $title_tag : esc_attr( $args['title_tag'] );
	
	$html           = "";
	$data_attribute = array();
	
	if ( ! empty( $blogs_shown ) && $type != "simple" ) {
		$data_attribute['data-blogs_shown'] = esc_attr( $blogs_shown );
	}
	$data_attribute['data-auto_start'] = esc_attr( $auto_start );
	
	$title_color_style = array();
	if ( ! empty( $title_color ) ) {
		$title_color_style[] = 'color: ' . esc_attr( $title_color );
	}
	
	$category_style = array();
	if ( ! empty( $category_color ) ) {
		$category_style[] = 'color: ' . esc_attr( $category_color );
	}
	
	$hover_box_style = array();
	if ( ! empty( $hover_box_color ) ) {
		$hover_box_style[] = 'background-color:' . esc_attr( $hover_box_color );
	}
	
	$day_style = array();
	if ( ! empty( $day_color ) ) {
		$day_style[] = 'color: ' . esc_attr( $day_color );
	}
	if ( ! empty( $day_font_size ) ) {
		$day_style[] = 'font-size: ' . esc_attr( $day_font_size ) . 'px';
	}
	
	
	$month_style = array();
	if ( ! empty( $month_color ) ) {
		$month_style[] = 'color: ' . esc_attr( $month_color );
	}
	if ( ! empty( $month_font_size ) ) {
		$month_style[] = 'font-size: ' . esc_attr( $month_font_size ) . 'px';
	}
	
	$date_style = array();
	if ( ! empty( $date_color ) ) {
		$date_style[] = 'color: ' . esc_attr( $date_color );
	}
	
	$author_style = array();
	if ( ! empty( $author_color ) ) {
		$author_style[] = 'color: ' . esc_attr( $author_color );
	}
	
	$comments_style = array();
	if ( ! empty( $comments_color ) ) {
		$comments_style[] = 'color: ' . esc_attr( $comments_color );
	}
	
	$excerpt_style = array();
	if ( ! empty( $excerpt_color ) ) {
		$excerpt_style[] = 'color: ' . esc_attr( $excerpt_color );
	}
	
	//get proper image size
	switch ( $image_size ) {
		case 'landscape':
			$thumb_size = 'portfolio-landscape';
			break;
		case 'portrait':
			$thumb_size = 'portfolio-portrait';
			break;
		default:
			$thumb_size = 'full';
			break;
	}
	
	$type_class = " blog_slider_carousel";
	if ( $type == "simple" ) {
		$type_class = " simple_slider";
	}
	
	$html .= "<div class='blog_slider_holder clearfix " . esc_attr( $add_class ) . "'><div class='blog_slider" . esc_attr( $type_class ) . "' " . bridge_qode_get_inline_attrs( $data_attribute ) . "><ul class='blog_slides'>";
	
	if ( $category == "" ) {
		$q = array(
			'post_type'      => 'post',
			'orderby'        => esc_attr( $order_by ),
			'order'          => esc_attr( $order ),
			'posts_per_page' => esc_attr( $number )
		);
	} else {
		$q = array(
			'post_type'      => 'post',
			'category_name'  => esc_attr( $category ),
			'orderby'        => esc_attr( $order_by ),
			'order'          => esc_attr( $order ),
			'posts_per_page' => esc_attr( $number )
		);
	}
	
	$project_ids = null;
	if ( $selected_projects != "" ) {
		$project_ids   = explode(
			",",
			$selected_projects
		);
		$q['post__in'] = $project_ids;
	}
	
	$query = new WP_Query( $q );
	
	if ( $query->have_posts() ) : $postCount = 0;
		while ( $query->have_posts() ) : $query->the_post();
			
			if ( $type == "" || $type == "carousel" ) {
				
				$html .= "<li class='item'>";
				$html .= '<div class="item_holder">';
				
				$blog_info_class = "";
				if ( $info_position == "info_in_bottom_always" ) {
					$blog_info_class .= "info_bottom";
				}
				
				$html .= '<div class="blog_text_holder ' . esc_attr( $blog_info_class ) . '" ' . bridge_qode_get_inline_style( $hover_box_style ) . '>';
				$html .= '<div class="blog_text_holder_outer">';
				
				if ( $info_position == "info_in_bottom_always" ) {
					$html .= '<div class="blog_text_date_holder">';
					$html .= '<div itemprop="dateCreated" class="blog_slider_date_holder entry_date updated">';
					$html .= '<span class="blog_slider_day" ' . bridge_qode_get_inline_style( $day_style ) . ' >' . get_the_time( 'd' ) . '</span><span class="blog_slider_month" ' . bridge_qode_get_inline_style( $month_style ) . '>' . get_the_time( 'M' ) . '</span>';
					$html .= '<meta itemprop="interactionCount" content="UserComments:' . get_comments_number( bridge_qode_get_page_id() ) . '"/></div>';
					$html .= '</div>';
					
					$html .= '<div class="blog_text_holder_inner">';
					$html .= '<' . esc_attr( $title_tag ) . ' itemprop="name" class="blog_slider_title entry_title" ><a itemprop="url" href="' . get_permalink() . '" ' . bridge_qode_get_inline_style( $title_color_style ) . '>' . get_the_title() . '</a></' . esc_attr( $title_tag ) . '>';
					
					if ( $show_categories == 'yes' ) {
						$html .= '<div class="blog_slider_categories">';
						
						// get categories for specific article
						$category_html = "";
						$k             = 1;
						$cat           = get_the_category();
						
						foreach ( $cat as $cats ) {
							$category_html = "$cats->name";
							if ( count( $cat ) != $k ) {
								$category_html .= ' / ';
							}
							$html .= '<a itemprop="url" class="blog_project_category" ' . bridge_qode_get_inline_style( $category_style ) . ' href="' . get_category_link( $cats->term_id ) . '">' . esc_html( $category_html ) . ' </a> ';
							$k ++;
						}
						
						$html .= '</div>';
					}
					if ( $show_comments == "yes" ) {
						$comments_count = get_comments_number();
						switch ( $comments_count ) {
							case 0:
								$comments_count_text = esc_html__( 'No comment', 'bridge-core' );
								break;
							case 1:
								$comments_count_text = $comments_count . ' ' . esc_html__( 'Comment', 'bridge-core' );
								break;
							default:
								$comments_count_text = $comments_count . ' ' . esc_html__( 'Comments','bridge-core' );
								break;
						}
						
						$html .= '<a itemprop="url" class="blog_slider_post_comments" ' . bridge_qode_get_inline_style( $comments_style ) . ' href="' . get_comments_link() . '">';
						if ( $show_categories == 'yes' ) {
							$html .= ' / ';
						}
						$html .= esc_html( $comments_count_text );
						$html .= '</a>';//close post_comments
					}
					$html .= '</div>';
				} else {
					$html .= '<div class="blog_text_holder_inner">';
					
					if ( $show_date == 'yes' ) {
						$html .= '<span itemprop="dateCreated" class="blog_slider_date_holder entry_date updated" ' . bridge_qode_get_inline_style( $date_style ) . '>';
						$html .= get_the_time( 'F d, Y' );
						$html .= '<meta itemprop="interactionCount" content="UserComments: ' . get_comments_number( bridge_qode_get_page_id() ) . '"/></span>';
					}
					
					$html .= '<' . esc_attr( $title_tag ) . ' itemprop="name" class="blog_slider_title entry_title" ><a itemprop="url" href="' . get_permalink() . '" ' . bridge_qode_get_inline_style( $title_color_style ) . '>' . get_the_title() . '</a></' . esc_attr(  $title_tag ) . '>';
					
					if ( $show_categories == 'yes' ) {
						$html .= '<div class="blog_slider_categories">';
						
						// get categories for specific article
						$category_html = "";
						$k             = 1;
						$cat           = get_the_category();
						
						foreach ( $cat as $cats ) {
							$category_html = "$cats->name";
							if ( count( $cat ) != $k ) {
								$category_html .= ' / ';
							}
							$html .= '<a itemprop="url" class="blog_project_category" ' . bridge_qode_get_inline_style( $category_style ) . ' href="' . get_category_link( $cats->term_id ) . '">' . wp_kses_post( $category_html ) . ' </a> ';
							$k ++;
						}
						
						$html .= '</div>';
					}
					
					$html .= '</div>'; // blog_text_holder_inner
				}
				$html .= '</div>';  // blog_text_holder_outer
				$html .= '</div>'; // blog_text_holder
				
				$html .= '<div class="blog_image_holder">';
				$html .= '<span class="image">';
				if ( $image_size !== 'custom' || $image_width == '' || $image_height == '' ) {
					$html .= get_the_post_thumbnail( get_the_ID(), $thumb_size );
				} else {
					$html .= bridge_qode_generate_thumbnail( get_post_thumbnail_id( get_the_ID() ), null, $image_width, $image_height );
				}
				$html .= '</span>';
				$html .= '</div>'; // close blog_image_holder
				$html .= '</div>'; // close item_holder
				$html .= "</li>";
			} else if ( $type == "simple" ) {
				$html .= '<li class="item">';
				$html .= '<div class = "blog_post_holder">';
				$html .= '<div class = "blog_image_holder">';
				$html .= '<span class = "image">';
				if ( $image_size !== 'custom' || $image_width == '' || $image_height == '' ) {
					$html .= get_the_post_thumbnail( get_the_ID(), $thumb_size );
				} else {
					$html .= bridge_qode_generate_thumbnail(
						get_post_thumbnail_id( get_the_ID() ), null, $image_width, $image_height );
				}
				$html .= '</span>';
				$html .= '</div>';//close blog_image_holder div
				$html .= '<div class = "blog_text_wrapper">';
				$html .= '<div class = "blog_text_holder_outer">';
				$html .= '<div class = "blog_text_holder_inner">';
				$html .= '<div class = "blog_text_holder_inner2" ' . bridge_qode_get_inline_style( $hover_box_style ) . '>';
				if ( $post_info_position !== "above_title" ) {
					$html .= '<' . esc_attr( $title_tag ) . ' itemprop="name"  class= "blog_slider_simple_title entry_title" ><a itemprop="url" href="' . get_permalink() . '" ' . bridge_qode_get_inline_style( $title_color_style ) . '>' . get_the_title() . '</a></' . esc_attr( $title_tag ) . '>';
				}
				if ( $show_categories == "yes" || $show_author == "yes" || $show_date == "yes" ) {
					$html .= '<div class="blog_slider_simple_info">';
					if ( $show_categories == "yes" ) {
						$html .= '<div class = "post_info_item category" >';
						// get categories for specific article
						$cat_html = "";
						$k        = 1;
						$cat      = get_the_category();
						
						foreach ( $cat as $cats ) {
							$cat_html = "$cats->name";
							if ( count( $cat ) != $k ) {
								$cat_html .= ' / ';
							}
							$html .= '<a itemprop="url" class="blog_simple_slider_category" ' . bridge_qode_get_inline_style( $category_style ) . ' href="' . get_category_link( $cats->term_id ) . '">' . wp_kses_post( $cat_html ) . ' </a> ';
							$k ++;
						}
						$html .= '</div>'; //close post_info_item category div
					}
					if ( $show_author == "yes" ) {
						$html .= '<div class = "post_info_item author" >';
						$html .= '<a itemprop="author" href="' . get_author_posts_url( get_the_author_meta( "ID" ) ) . '" ' . bridge_qode_get_inline_style( $author_style ) . ' >' . get_the_author_meta( "display_name" ) . '</a>';
						$html .= '</div>'; //close post_info_item author div
					}
					if ( $show_date == "yes" ) {
						$html .= '<div class = "post_info_item date"><span itemprop="dateCreated" class="entry_date updated" ' . bridge_qode_get_inline_style( $date_style ) . '>' . get_the_time( get_option( 'date_format' ) ) . '<meta itemprop="interactionCount" content="UserComments:' . get_comments_number(qode_get_page_id()) . '"/></span></div>';
					}
					$html .= '</div>'; //close blog_slider_simple_info div
				}
				if ( $post_info_position == "above_title" ) {
					$html .= '<' . esc_attr( $title_tag ) . ' itemprop="name" class= "blog_slider_simple_title entry_title" ><a itemprop="url" href="' . get_permalink() . '" ' . bridge_qode_get_inline_style( $title_color_style ) . '>' . get_the_title() . '</a></' . esc_attr( $title_tag ) . '>';
				}
				if ( $show_excerpt == "yes" ) {
					$excerpt = ( $excerpt_length > 0 ) ? substr( get_the_excerpt(), 0, intval( $excerpt_length ) ) . '...' : get_the_excerpt();
					$html    .= '<p itemprop="description" class = "blog_slider_simple_excerpt" ' . bridge_qode_get_inline_style( $excerpt_style ) . '>' . wp_kses_post( $excerpt ) . '</p>';
				}
				if ( $show_read_more == "yes" ) {
					$html .= '<div class = "read_more_wrapper">';
					$html .= '<a itemprop="url" href="' . get_the_permalink() . '" target="_self" class="qbutton ' . esc_attr( $read_more_button_size ) . ' read_more_button">' . esc_html__('Read More', 'bridge-core' ) . '</a>';
					$html .= '</div>'; //close read_more_wrapper div
				}
				$html .= '</div>'; //close blog_text_holder_inner2 div
				$html .= '</div>'; //close blog_text_holder_inner div
				$html .= '</div>'; //close blog_text_holder_outer div
				$html .= '</div>'; //close blog_text_wrapper div
				$html .= '</div>'; //close blog_post_holder div
				$html .= '</li>'; //close li
			}
			$postCount ++;
		
		endwhile;
	
	else:
		$html .= esc_html__( 'Sorry, no posts matched your criteria.', 'bridge-core' );
	endif;
	
	wp_reset_postdata();
	
	$html .= "</ul>";
	if ( $enable_navigation ) {
		$html .= '<ul class="caroufredsel-direction-nav"><li><a id="caroufredsel-prev" class="caroufredsel-prev" href="#"><div><i class="fa fa-angle-left"></i></div></a></li><li><a class="caroufredsel-next" id="caroufredsel-next" href="#"><div><i class="fa fa-angle-right"></i></div></a></li></ul>';
	}
	$html .= "</div></div>";
	
	echo bridge_qode_get_module_part( $html );