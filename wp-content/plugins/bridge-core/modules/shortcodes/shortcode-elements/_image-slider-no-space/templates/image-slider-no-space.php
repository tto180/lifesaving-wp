<?php
	
	// Initialize variables
	$html = "";
	$image_gallery_holder_styles = '';
	$image_gallery_holder_classes = '';
	$image_gallery_item_styles = '';
	$custom_links_array = [];
	$using_custom_links = false;
	$highlight_inactive_color_style = "";
	$highlight_inactive_opacity_style = "";
	
	// Set height for the slider if provided
	if (!empty($height)) {
		$image_gallery_holder_styles .= 'height: ' . intval($height) . 'px;';
		$image_gallery_item_styles .= 'height: ' . intval($height) . 'px;';
	}
	
	// Handle custom links if applicable
	if ($on_click === 'use_custom_links' && !empty($custom_links)) {
		$custom_links_array = array_map('trim', explode(',', strip_tags($custom_links)));
	}
	
	// Set navigation style classes if provided
	if (!empty($navigation_style)) {
		$image_gallery_holder_classes = esc_attr($navigation_style);
	}
	
	// Handle highlighting for active images
	if ($highlight_active_image === 'yes') {
		$image_gallery_holder_classes .= ' highlight_active';
		if (!empty($highlight_inactive_color)) {
			$highlight_inactive_color_style = 'style="background-color: ' . esc_attr($highlight_inactive_color) . '"';
		}
		if (!empty($highlight_inactive_opacity)) {
			$highlight_inactive_opacity_style = 'style="opacity: ' . esc_attr($highlight_inactive_opacity) . '"';
		}
	}
	
	$html .= "<div class='qode_image_gallery_no_space " . esc_attr($image_gallery_holder_classes) . "'>
            <div class='qode_image_gallery_holder' style='" . esc_attr($image_gallery_holder_styles) . "'>
                <ul " . $highlight_inactive_color_style . ">";
	
	// Check if images are set
	if (!empty($images)) {
		$images_gallery_array = explode(',', $images);
	}
	
	// Handle PrettyPhoto if applicable
	if ($on_click === 'prettyphoto') {
		$pretty_photo_rel = 'prettyPhoto[rel-' . rand() . ']';
	}
	
	// Handle custom links target
	if ($on_click === 'use_custom_links' && in_array($custom_links_target, ['_self', '_blank'])) {
		$custom_links_target = 'target="' . esc_attr($custom_links_target) . '"';
	}
	
	if (isset($images_gallery_array) && count($images_gallery_array) > 0) {
		foreach ($images_gallery_array as $i => $gimg_id) {
			$gimage_src = wp_get_attachment_image_src($gimg_id, 'full', true);
			$gimage_alt = get_post_meta($gimg_id, '_wp_attachment_image_alt', true);
			
			// Get image properties
			$image_src = $gimage_src[0];
			$image_width = $gimage_src[1];
			$image_height = $gimage_src[2];
			
			// Adjust width based on slider height if set
			if (!empty($height)) {
				$proportion = intval($height) / $image_height;
				$image_width = ceil($image_width * $proportion);
			}
			
			$html .= '<li ' . $highlight_inactive_opacity_style . '>
                    <div style="' . esc_attr($image_gallery_item_styles) . ' width:' . intval($image_width) . 'px;">';
			
			// Handle on click events
			if (!empty($on_click)) {
				switch ($on_click) {
					case 'prettyphoto':
						$html .= '<a itemprop="image" class="prettyphoto" data-rel="' . esc_attr($pretty_photo_rel) . '" href="' . esc_url($image_src) . '">';
						break;
					case 'use_custom_links':
						if ( ! empty( $custom_links_array[ $i ] ) ) {
							$current_item_custom_link = esc_url( $custom_links_array[ $i ] );
							$html                     .= '<a itemprop="url" ' . esc_attr($custom_links_target) . ' href="' . esc_url( $current_item_custom_link ) . '">';
						}
						break;
					case 'new_tab':
						$html .= '<a itemprop="url" href="' . esc_url( $image_src ) . '" target="_blank">';
						break;
					default:
						break;
				}
			}
			
			$html .= '<img itemprop="image" src="' . esc_url( $image_src ) . '" alt="' . esc_attr( $gimage_alt ) . '" />';
			
			// Close link if applicable
			if (in_array($on_click, ['prettyphoto', 'new_tab']) || ($on_click === 'use_custom_links' && ! empty( $current_item_custom_link ) )) {
				$html .= '</a>';
			}
			
			$html .= '</div></li>';
		}
	}
	
	$html .= "</ul>
          </div>
          <div class='controls'>
              <a class='prev-slide' href='#'><span><i class='fa fa-angle-left'></i></span></a>
              <a class='next-slide' href='#'><span><i class='fa fa-angle-right'></i></span></a>
          </div>
      </div>";
	
	echo bridge_qode_get_module_part($html);
