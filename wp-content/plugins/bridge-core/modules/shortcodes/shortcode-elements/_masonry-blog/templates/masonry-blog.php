<?php
	
	$blog_hide_comments = "";
	if ( isset( $bridge_qode_options['blog_hide_comments'] ) ) {
		$blog_hide_comments = esc_attr( $bridge_qode_options['blog_hide_comments'] );
	}
	
	$qode_like = "on";
	if ( isset( $bridge_qode_options['qode_like'] ) ) {
		$qode_like = esc_attr( $bridge_qode_options['qode_like'] );
	}
	
	$headings_array = array('h2', 'h3', 'h4', 'h5', 'h6');
	
	//get correct heading value. If provided heading isn't valid get the default one
	$title_tag = (in_array($title_tag, $headings_array)) ? $title_tag : esc_attr( $args['title_tag'] );
	
	$q = new WP_Query(
		array(
			'orderby'        => esc_attr( $order_by ),
			'order'          => esc_attr( $order ),
			'posts_per_page' => esc_attr( $number_of_posts ),
			'category_name'  => esc_attr( $category )
		)
	);
	
	
	
	$html = "";
	$html .= "<div class='q_masonry_blog'>";
	$html .= '<div class="q_masonry_blog_grid_sizer"></div>';
	$html .= '<div class="q_masonry_blog_grid_gutter"></div>';
	while ($q->have_posts()) : $q->the_post();
		$_post_format = get_post_format();
		$_post_classes =  get_post_class();
		$html .= '<article class="' .  implode(" ", $_post_classes) .  '">';
		$featured_image_array = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');
		$featured_image_alt = get_post_meta(get_post_thumbnail_id(), '_wp_attachment_image_alt', true);
		if ( $_post_format != 'quote' && $_post_format != 'link' ) {
			$html .= '<div class="q_masonry_blog_post_image">';
			switch ($_post_format) {
				case "video":
					$_video_type = get_post_meta(get_the_ID(), "video_format_choose", true);
					if($_video_type == "youtube") {
						$html .= '<iframe src="//www.youtube.com/embed/' . get_post_meta(get_the_ID(), "video_format_link", true) . '?wmode=transparent" wmode="Opaque" frameborder="0" allowfullscreen></iframe>';
					} elseif ($_video_type == "vimeo"){
						$html .= '<iframe src="//player.vimeo.com/video/' . get_post_meta(get_the_ID(), "video_format_link", true) . '?title=0&amp;byline=0&amp;portrait=0" frameborder="0" width="800" height="450" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
					} elseif ($_video_type == "self"){
						$html .= '<div class="video"> ';
						$html .= '<div class="mobile-video-image" style="background-image: url(' . get_post_meta(get_the_ID(), "video_format_image", true) . ');"></div> ';
						$html .= '<div class="video-wrap">';
						$html .= '<video class="video" poster="' . get_post_meta(get_the_ID(), "video_format_image", true) . '" preload="auto">';
						if(get_post_meta(get_the_ID(), "video_format_webm", true) != "") {
							$html .= '<source type="video/webm" src="' . get_post_meta(get_the_ID(), "video_format_webm", true). '">';
						}
						if(get_post_meta(get_the_ID(), "video_format_mp4", true) != "") {
							$html .= '<source type="video/mp4" src="' . get_post_meta(get_the_ID(), "video_format_mp4", true) . '">';
						}
						if(get_post_meta(get_the_ID(), "video_format_ogv", true) != "") {
							$html .= '<source type="video/ogg" src="'. get_post_meta(get_the_ID(), "video_format_ogv", true).'">';
						}
						$html .= '<object width="320" height="240" type="application/x-shockwave-flash" data="' . get_template_directory_uri() . '/js/flashmediaelement.swf">';
						$html .= '<param name="movie" value="' . get_template_directory_uri() . '/js/flashmediaelement.swf" />';
						$html .= '<param name="flashvars" value="controls=true&file=' . get_post_meta(get_the_ID(), "video_format_mp4", true) . '" />';
						$html .= '<img itemprop="image" src="' . get_post_meta(get_the_ID(), "video_format_image", true)  . '" width="1920" height="800" title="' . esc_html__('No video playback capabilities', 'bridge-core') . '" alt="' . esc_html__( 'Video thumb', 'bridge-core') . '" /> ';
						$html .= '</object>';
						$html .= '</video>';
						$html .= '</div></div>';
					}
					break;
				case "audio":
					$html .= '<audio class="blog_audio" src="' . get_post_meta(get_the_ID(), "audio_link", true). '" controls="controls">';
					$html .=  esc_html__('Your browser don\'t support audio player', 'bridge-core');
					$html .= '</audio>';
					break;
				case "gallery":
					$html .= '<div class="flexslider">';
					$html .= '<ul class="slides">';
					$post_content = get_the_content();
					preg_match('/\[gallery.*ids=.(.*).\]/', $post_content, $ids);
					$array_id = explode(",", $ids[1]);
					foreach($array_id as $img_id){
						$html .= '<li><a itemprop="url" target="_self" href="' . get_permalink() . '">' . wp_get_attachment_image( $img_id, 'full' ) . '</a></li>';
					}
					$html .= '</ul>';
					$html .= '</div>';
					break;
				default:
					if ( ! empty( $featured_image_array ) ) {
						$html .= '<a itemprop="url" href="' . get_permalink() . '" target="_self">';
						$html .= '<img itemprop="image" src="' . esc_url( $featured_image_array[0] ) . '" alt="' . esc_attr( $featured_image_alt ) . '" />';
						$html .= '</a>';
					}
					break;
			}
			$html .= '</div>';
			
			$html .= '<div class="q_masonry_blog_post_text">';
			$html .= '<'. esc_attr( $title_tag ) .' itemprop="name" class="q_masonry_blog_title entry_title"><a itemprop="url" href="' . get_permalink() . '">' . get_the_title() . '</a></'. esc_attr( $title_tag ) .'>';
			$excerpt = ($text_length > 0) ? substr(get_the_excerpt(), 0, intval($text_length)) : get_the_excerpt();
			$html .= '<p itemprop="description" class="q_masonry_blog_excerpt">'. wp_kses_post( $excerpt ).'...</p>';
			$html .= '<div class="q_masonry_blog_post_info">';
			if ( $display_time != "0" ) {
				$html .= '<span itemprop="dateCreated" class="time entry_date updated">' . get_the_time( 'd F, Y' ) . '<meta itemprop="interactionCount" content="UserComments:' . get_comments_number( qode_get_page_id() ) . '"/></span>';
			}
			if ($blog_hide_comments != "yes" && $display_comments == "1") {
				$comments_count = get_comments_number();
				
				switch ($comments_count) {
					case 0:
						$comments_count_text = esc_html__('No comment', 'bridge-core');
						break;
					case 1:
						$comments_count_text = $comments_count . ' ' . esc_html__('Comment', 'bridge-core');
						break;
					default:
						$comments_count_text = $comments_count . ' ' . esc_html__('Comments', 'bridge-core');
						break;
				}
				$html .= ' / <a itemprop="url" class="post_comments" href="' . get_comments_link() . '">';
				$html .= esc_html( $comments_count_text );
				$html .= '</a>';//close post_comments
			}
			$html .= '</div>';
			$html .= '</div>';
		} else {
			$html .= '<div class="q_masonry_blog_post_text">';
			$html .= '<div class="q_masonry_blog_post_info">';
			if ($display_time != "0") {
				$html .= '<span itemprop="dateCreated" class="time entry_date updated">'. get_the_time('d F, Y') .'<meta itemprop="interactionCount" content="UserComments: <?php echo get_comments_number(qode_get_page_id()); ?>"/></span>';
			}
			if ($blog_hide_comments != "yes" && $display_comments == "1") {
				$comments_count = get_comments_number();
				
				switch ($comments_count) {
					case 0:
						$comments_count_text = esc_html__('No comment', 'bridge-core');
						break;
					case 1:
						$comments_count_text = $comments_count . ' ' . esc_html__('Comment', 'bridge-core');
						break;
					default:
						$comments_count_text = $comments_count . ' ' . esc_html__('Comments', 'bridge-core');
						break;
				}
				$html .= ' / <a itemprop="url" class="post_comments" href="' . get_comments_link() . '">';
				$html .= esc_html( $comments_count_text );
				$html .= '</a>';//close post_comments
			}
			$html .= '</div>';
			if($_post_format == "quote") {
				$html .= '<i class="qoute_mark fa fa-quote-right pull-left"></i>';
			}else{
				$html .= '<i class="link_mark fa fa-link pull-left"></i>';
			}
			$html .= '<div class="q_masonry_blog_post_title entry_title">';
			if($_post_format == "quote") {
				$html .= '<p><a itemprop="url" href="' . get_permalink(). '">' . get_post_meta(get_the_ID(), "quote_format", true) . '</a></p>';
				$html .= '<span class="quote_author">&mdash;' . get_the_title() . '</span>';
			} else {
				$html .= '<p><a itemprop="url" href="' . get_permalink(). '">' . get_the_title()  . '</a></p>';
			}
			$html .= '</div></div>';
			
		}
		$html .= '</article>';
	endwhile;
	wp_reset_postdata();
	
	$html .= "</div>";
	echo bridge_qode_get_module_part( $html );