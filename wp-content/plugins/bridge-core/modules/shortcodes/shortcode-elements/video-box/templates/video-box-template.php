<div class="<?php echo esc_attr($holder_classes); ?>">
	<a itemprop="image" class="qode_video_image" href="<?php echo esc_url($video_link) ?>" data-rel="prettyPhoto">
		<?php if( !empty( $video_image) ) { ?>
			<?php echo wp_get_attachment_image( $video_image, 'full' ); ?>
			<span class="qode_video_box_button_holder">
				<span class="qode_video_box_button">
					<span class="qode_video_box_button_arrow">
					</span>
				</span>
			</span>
		<?php } ?>
	</a>
</div>