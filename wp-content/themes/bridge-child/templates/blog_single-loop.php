<?php 
$bridge_qode_options = bridge_qode_return_global_options();
$bridge_qode_blog_hide_comments = "";
if (isset($bridge_qode_options['blog_hide_comments'])) {
	$bridge_qode_blog_hide_comments = $bridge_qode_options['blog_hide_comments'];
}
$bridge_qode_blog_share_like_layout = 'in_post_info';
if (isset($bridge_qode_options['blog_share_like_layout'])) {
    $bridge_qode_blog_share_like_layout = $bridge_qode_options['blog_share_like_layout'];
}
$bridge_qode_enable_social_share = 'no';
if(isset($bridge_qode_options['enable_social_share'])){
    $bridge_qode_enable_social_share = $bridge_qode_options['enable_social_share'];
}
$bridge_qode_blog_author_info="no";
if (isset($bridge_qode_options['blog_author_info'])) {
	$bridge_qode_blog_author_info = $bridge_qode_options['blog_author_info'];
}
$bridge_qode_like = "on";
if (isset($bridge_qode_options['qode_like'])) {
    $bridge_qode_like = $bridge_qode_options['qode_like'];
}

$bridge_qode_gallery_post_layout = bridge_qode_check_gallery_post_layout(get_the_ID());

$bridge_qode_params = array(
    'blog_share_like_layout' => $bridge_qode_blog_share_like_layout,
    'enable_social_share' => $bridge_qode_enable_social_share,
    'qode_like' => $bridge_qode_like
);

$bridge_qode_post_format = get_post_format();
?>
<?php
	switch ($bridge_qode_post_format) {
		case "video":
?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post_content_holder">
				<div class="post_image">
					<?php $bridge_qode_video_type = get_post_meta(get_the_ID(), "video_format_choose", true);?>
					<?php if($bridge_qode_video_type == "youtube") { ?>
						<iframe name="fitvid-<?php the_ID(); ?>" src="//www.youtube.com/embed/<?php echo get_post_meta(get_the_ID(), "video_format_link", true);  ?>?wmode=transparent" wmode="Opaque" width="805" height="403" allowfullscreen></iframe>
					<?php } elseif ($bridge_qode_video_type == "vimeo"){ ?>
						<iframe name="fitvid-<?php the_ID(); ?>" src="//player.vimeo.com/video/<?php echo get_post_meta(get_the_ID(), "video_format_link", true);  ?>?title=0&amp;byline=0&amp;portrait=0" width="800" height="450" allowfullscreen></iframe>
					<?php } elseif ($bridge_qode_video_type == "self"){ ?> 
						<div class="video"> 
						<div class="mobile-video-image" style="background-image: url(<?php echo get_post_meta(get_the_ID(), "video_format_image", true);  ?>);"></div> 
						<div class="video-wrap"  > 
							<video class="video" poster="<?php echo get_post_meta(get_the_ID(), "video_format_image", true);  ?>" preload="auto"> 
								<?php if(get_post_meta(get_the_ID(), "video_format_webm", true) != "") { ?> <source type="video/webm" src="<?php echo get_post_meta(get_the_ID(), "video_format_webm", true);  ?>"> <?php } ?> 
								<?php if(get_post_meta(get_the_ID(), "video_format_mp4", true) != "") { ?> <source type="video/mp4" src="<?php echo get_post_meta(get_the_ID(), "video_format_mp4", true);  ?>"> <?php } ?> 
								<?php if(get_post_meta(get_the_ID(), "video_format_ogv", true) != "") { ?> <source type="video/ogg" src="<?php echo get_post_meta(get_the_ID(), "video_format_ogv", true);  ?>"> <?php } ?> 
								<object width="320" height="240" type="application/x-shockwave-flash" data="<?php echo get_template_directory_uri(); ?>/js/flashmediaelement.swf"> 
									<param name="movie" value="<?php echo get_template_directory_uri(); ?>/js/flashmediaelement.swf" /> 
									<param name="flashvars" value="controls=true&file=<?php echo get_post_meta(get_the_ID(), "video_format_mp4", true);  ?>" /> 
									<img itemprop="image" src="<?php echo get_post_meta(get_the_ID(), "video_format_image", true);  ?>" width="1920" height="800" title="<?php echo esc_html__('No video playback capabilities', 'bridge'); ?>" alt="<?php echo esc_html__('Video thumb', 'bridge'); ?>" />
								</object> 
							</video>   
						</div></div> 
					<?php } ?>
				</div>
	
				<div class="post_text">
					<div class="post_text_inner">
<!-- MSJ CHANGE FROM h2 TO h1  --> 									 
																			  
						<h1 itemprop="name" class="entry_title"><span itemprop="dateCreated" class="date entry_date updated"><?php the_time('F j, Y'); ?><meta itemprop="interactionCount" content="UserComments: <?php echo get_comments_number(bridge_qode_get_page_id()); ?>"/></span> <?php the_title(); ?></h1>
						<div class="post_info">
							<span class="time"><?php esc_html_e('Posted at','bridge'); ?> <?php the_time('F j, Y'); ?><?php esc_html_e('h','bridge'); ?></span>
							<?php esc_html_e('in','bridge'); ?> <?php the_category(', '); ?>
							<span class="post_author">
								<?php esc_html_e('by','bridge'); ?>
								<a itemprop="author" class="post_author_link" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author_meta('display_name'); ?></a>
							</span>
							<?php if($bridge_qode_blog_hide_comments != "yes"){ ?>
								<span class="dots"><i class="fa fa-square"></i></span><a itemprop="url" class="post_comments" href="<?php comments_link(); ?>" target="_self"><?php comments_number('0 ' . esc_html__('Comments','bridge'), '1 '.esc_html__('Comment','bridge'), '% '.esc_html__('Comments','bridge') ); ?></a>
							<?php } ?>
                            <?php bridge_qode_get_template_part('templates/blog-parts/blog','share-like-post-info',$bridge_qode_params); ?>
						</div>
						<?php the_content(); ?>
					</div>
				</div>
			</div>
<?php
		break;
		case "audio":
?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post_content_holder">
				<div class="post_image">
					<?php if(bridge_qode_options()->getOptionValue('show_image_on_audio_post') == 'yes' && get_post_meta(get_the_ID(), "qode_hide-featured-image", true) != "yes") {
						if ( has_post_thumbnail() ) { ?>
								<?php the_post_thumbnail('full'); ?>
						<?php }
					} ?>
					<audio class="blog_audio" src="<?php echo get_post_meta(get_the_ID(), "audio_link", true) ?>" controls="controls">
						<?php esc_html_e("Your browser don't support audio player","bridge"); ?>
					</audio>
				</div>
				<div class="post_text">
					<div class="post_text_inner">
<!-- MSJ CHANGE FROM h2 TO h1  --> 									 
																				
						<h1 itemprop="name" class="entry_title"><span itemprop="dateCreated" class="date entry_date updated"><?php the_time('F j, Y'); ?><meta itemprop="interactionCount" content="UserComments: <?php echo get_comments_number(bridge_qode_get_page_id()); ?>"/></span> <?php the_title(); ?></h1>
						<div class="post_info">
							<span class="time"><?php esc_html_e('Posted at','bridge'); ?> <?php the_time('F j, Y'); ?><?php esc_html_e('h','bridge'); ?></span>
							<?php esc_html_e('in','bridge'); ?> <?php the_category(', '); ?>
							<span class="post_author">
								<?php esc_html_e('by','bridge'); ?>
								<a itemprop="author" class="post_author_link" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author_meta('display_name'); ?></a>
							</span>
							<?php if($bridge_qode_blog_hide_comments != "yes"){ ?>
								<span class="dots"><i class="fa fa-square"></i></span><a itemprop="url" class="post_comments" href="<?php comments_link(); ?>" target="_self"><?php comments_number('0 ' . esc_html__('Comments','bridge'), '1 '.esc_html__('Comment','bridge'), '% '.esc_html__('Comments','bridge') ); ?></a>
							<?php } ?>
                            <?php bridge_qode_get_template_part('templates/blog-parts/blog','share-like-post-info',$bridge_qode_params); ?>
						</div>
						<?php the_content(); ?>
					</div>
				</div>
			</div>
	
<?php
		break;
		case "link":
?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post_content_holder">
				<div class="post_text">
					<div class="post_text_inner">
						<div class="post_info">
							<span class="time"><?php esc_html_e('Posted at','bridge'); ?> <?php the_time('d M, H:i'); ?><?php esc_html_e('h','bridge'); ?></span>
							<?php esc_html_e('in','bridge'); ?> <?php the_category(', '); ?>
							<span class="post_author">
								<?php esc_html_e('by','bridge'); ?>
								<a itemprop="author" class="post_author_link" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author_meta('display_name'); ?></a>
							</span>
							<?php if($bridge_qode_blog_hide_comments != "yes"){ ?>
								<span class="dots"><i class="fa fa-square"></i></span><a itemprop="url" class="post_comments" href="<?php comments_link(); ?>" target="_self"><?php comments_number('0 ' . esc_html__('Comments','bridge'), '1 '.esc_html__('Comment','bridge'), '% '.esc_html__('Comments','bridge') ); ?></a>
							<?php } ?>
                            <?php bridge_qode_get_template_part('templates/blog-parts/blog','share-like-post-info',$bridge_qode_params); ?>
						</div>
						<i class="link_mark fa fa-link pull-left"></i>
						 <?php $bridge_qode_title_link = get_post_meta(get_the_ID(), "title_link", true) != '' ? get_post_meta(get_the_ID(), "title_link", true) : 'javascript: void(0)'; ?>
						<div class="post_title entry_title">
							<p><a itemprop="url" href="<?php echo esc_url( $bridge_qode_title_link ); ?>"><?php the_title(); ?></a></p>
						</div>
					</div>
				</div>
				<?php the_content(); ?>
			</div>
<?php
		break;
		case "gallery":
?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post_content_holder">
				<div class="post_image">
                    <?php
                    $bridge_qode_post_content = get_the_content();
                    preg_match('/\[gallery.*ids=.(.*).\]/', $bridge_qode_post_content, $bridge_qode_ids);
                    $bridge_qode_array_id = explode(",", $bridge_qode_ids[1]);

                    $bridge_qode_content =  str_replace($bridge_qode_ids[0], "", $bridge_qode_post_content);
                    $bridge_qode_filtered_content = apply_filters( 'the_content', $bridge_qode_content);

                    switch ($bridge_qode_gallery_post_layout) {
                    case 'slider':
                    ?>
                    <div class="flexslider">
                        <ul class="slides">
                            <?php
                            foreach ($bridge_qode_array_id as $bridge_qode_img_id) { ?>
                                <li><a itemprop="url"
                                       href="<?php the_permalink(); ?>"><?php echo wp_get_attachment_image($bridge_qode_img_id, 'full'); ?></a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                    <?php break;
                    case 'masonry':
                        echo bridge_qode_get_blog_gallery_layout($bridge_qode_array_id);
                        break;
                    } ?>

				</div>
				<div class="post_text">
					<div class="post_text_inner">
<!-- MSJ CHANGE FROM h2 TO h1  -->  									 
																				 
						<h1 itemprop="name" class="entry_title"><span itemprop="dateCreated" class="date entry_date updated"><?php the_time('F j, Y'); ?><meta itemprop="interactionCount" content="UserComments: <?php echo get_comments_number(bridge_qode_get_page_id()); ?>"/></span> <?php the_title(); ?></h1>
						<div class="post_info">
							<span class="time"><?php esc_html_e('Posted ','bridge'); ?> <?php the_time('F j, Y'); ?><?php esc_html_e('','bridge'); ?></span>
							<?php esc_html_e('in','bridge'); ?> <?php the_category(', '); ?>
							<span class="post_author">
								<?php esc_html_e('by','bridge'); ?>
								<a itemprop="author" class="post_author_link" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author_meta('display_name'); ?></a>
							</span>
							<?php if($bridge_qode_blog_hide_comments != "yes"){ ?>
								<span class="dots"><i class="fa fa-square"></i></span><a itemprop="url" class="post_comments" href="<?php comments_link(); ?>" target="_self"><?php comments_number('0 ' . esc_html__('Comments','bridge'), '1 '.esc_html__('Comment','bridge'), '% '.esc_html__('Comments','bridge') ); ?></a>
							<?php } ?>
                            <?php bridge_qode_get_template_part('templates/blog-parts/blog','share-like-post-info',$bridge_qode_params); ?>
						</div>
						<?php echo do_shortcode($bridge_qode_filtered_content); ?>	
					</div>
				</div>

			</div>
		
<?php
		break;
		case "quote":
?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<div class="post_content_holder">
					<div class="post_text">
						<div class="post_text_inner">
							<div class="post_info">
								<span class="time"><?php esc_html_e('Posted at','bridge'); ?> <?php the_time('d M, H:i'); ?><?php esc_html_e('h','bridge'); ?></span>
								<?php esc_html_e('in','bridge'); ?> <?php the_category(', '); ?>
								<span class="post_author">
									<?php esc_html_e('by','bridge'); ?>
									<a itemprop="author" class="post_author_link" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author_meta('display_name'); ?></a>
								</span>
								<?php if($bridge_qode_blog_hide_comments != "yes"){ ?>
									<span class="dots"><i class="fa fa-square"></i></span><a itemprop="url" class="post_comments" href="<?php comments_link(); ?>" target="_self"><?php comments_number('0 ' . esc_html__('Comments','bridge'), '1 '.esc_html__('Comment','bridge'), '% '.esc_html__('Comments','bridge') ); ?></a>
								<?php } ?>
                                <?php bridge_qode_get_template_part('templates/blog-parts/blog','share-like-post-info',$bridge_qode_params); ?>
							</div>	
							<i class="qoute_mark fa fa-quote-right pull-left"></i>
							<div class="post_title entry_title">
								<p><?php echo get_post_meta(get_the_ID(), "quote_format", true); ?></p>
								<span class="quote_author">&mdash; <?php the_title(); ?></span>
							</div>
						</div>
					</div>
					<?php the_content(); ?>
				</div>
<?php
		break;
		default:
?>
		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<div class="post_content_holder">
				<?php if(get_post_meta(get_the_ID(), "qode_hide-featured-image", true) != "yes") {
					if ( has_post_thumbnail() ) { ?>
						<div class="post_image">
	                        <?php the_post_thumbnail('full'); ?>
						</div>
				<?php } } ?>
				<div class="post_text">
					<div class="post_text_inner">
<!-- MSJ CHANGE FROM h2 TO h1  -->   									 
																			 
						<h1 itemprop="name" class="entry_title"><span itemprop="dateCreated" class="date entry_date updated"><?php the_time('F j, Y'); ?><meta itemprop="interactionCount" content="UserComments: <?php echo get_comments_number(bridge_qode_get_page_id()); ?>"/></span> <?php the_title(); ?></h1>
						<div class="post_info">
							<span class="time"><?php esc_html_e('Posted ','bridge'); ?> <?php the_time('F j, Y'); ?><?php esc_html_e('','bridge'); ?></span>
							<?php esc_html_e('in','bridge'); ?> <?php the_category(', '); ?>
							<span class="post_author">
								<?php esc_html_e('by','bridge'); ?>
								<a itemprop="author" class="post_author_link" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author_meta('display_name'); ?></a>
							</span>
							<?php if($bridge_qode_blog_hide_comments != "yes"){ ?>
								<span class="dots"><i class="fa fa-square"></i></span><a itemprop="url" class="post_comments" href="<?php comments_link(); ?>" target="_self"><?php comments_number('0 ' . esc_html__('Comments','bridge'), '1 '.esc_html__('Comment','bridge'), '% '.esc_html__('Comments','bridge') ); ?></a>
							<?php } ?>
                            <?php bridge_qode_get_template_part('templates/blog-parts/blog','share-like-post-info',$bridge_qode_params); ?>
							
<?php							
$u_time = get_the_time('U'); 
$u_modified_time = get_the_modified_time('U'); 
if ($u_modified_time >= $u_time + 86400) { 
echo nl2br ("\n Last modified on "); 
the_modified_time('F jS, Y'); 
echo " "; } 
?>							
							
						
							
						</div>
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		
<?php
}
do_action('bridge_qode_action_after_article_content');
?>
	<?php if( has_tag()) { ?>
		<div class="single_tags clearfix">
            <div class="tags_text">
				<h5><?php esc_html_e('Tags:','bridge'); ?></h5>
				<?php 
				if ((isset($bridge_qode_options['tags_border_style']) && $bridge_qode_options['tags_border_style'] !== '') || (isset($bridge_qode_options['tags_background_color']) && $bridge_qode_options['tags_background_color'] !== '')){
					the_tags('', ' ', '');
				}
				else{
					the_tags('', ', ', '');
				}
				?>
			</div>
		</div>
	<?php } ?>
    <?php bridge_qode_get_template_part('templates/blog-parts/blog','share-like-below-text',$bridge_qode_params); ?>
	<?php 
		$bridge_qode_args_pages = array(
			'before'           => '<p class="single_links_pages">',
			'after'            => '</p>',
			'link_before'      => '<span>',
			'link_after'       => '</span>',
			'pagelink'         => '%'
		);

		wp_link_pages($bridge_qode_args_pages);
	?>
<?php if($bridge_qode_blog_author_info == "yes") { ?>
	<div class="author_description">
		<div class="author_description_inner">
			<div class="image">
				<?php echo get_avatar(get_the_author_meta( 'ID' ), 75); ?>
			</div>
			<div class="author_text_holder">
				<h5 class="author_name vcard author">
				<span class="fu">
				<?php  
					if(get_the_author_meta('first_name') != "" || get_the_author_meta('last_name') != "") {
						echo get_the_author_meta('first_name') . " " . get_the_author_meta('last_name');
					} else {
						echo get_the_author_meta('display_name');
					}
				?>
			    </span>
				</h5>
				<span class="author_email"><?php echo get_the_author_meta('email'); ?></span>
				<?php if(get_the_author_meta('description') != "") { ?>
					<div class="author_text">
						<p><?php echo get_the_author_meta('description') ?></p>
					</div>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>
</article>