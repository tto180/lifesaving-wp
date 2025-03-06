<?php

$qodeIconCollections = bridge_qode_return_icon_collections();
$icon_pack = bridge_qode_options()->getOptionValue('search_icon_pack');

?>
<form role="search" id="searchform" action="<?php echo esc_url(home_url('/')); ?>" class="qode_search_form" method="get">
    <?php if($header_in_grid){ ?>
    <div class="container">
        <div class="container_inner clearfix">
            <?php } ?>

            <?php if( 'svg_path' !== $icon_pack ) {
				$qodeIconCollections->getSearchIcon( $icon_pack , array('icon_attributes' => array('class' => 'qode_icon_in_search')));
            } else {
	            $search_icon_svg = bridge_qode_options()->getOptionValue('search_icon_svg_opener');
	            if( ! empty( $search_icon_svg ) ) {
		            echo bridge_qode_get_module_part( $search_icon_svg );
	            }
            } ?>
            <input type="text" placeholder="<?php esc_html_e('Search', 'bridge'); ?>" name="s" class="qode_search_field" autocomplete="off" />
            <input type="submit" value="<?php esc_html_e('Search', 'bridge'); ?>" />

            <div class="qode_search_close">
                <a href="#">
                    <?php if( 'svg_path' !== $icon_pack ) {
						$qodeIconCollections->getSearchClose( $icon_pack , array('icon_attributes' => array('class' => 'qode_icon_in_search')));
                    } else {
	                    $search_icon_svg_closer = bridge_qode_options()->getOptionValue('search_icon_svg_closer');
	                    if( ! empty( $search_icon_svg_closer ) ) {
		                    echo bridge_qode_get_module_part( $search_icon_svg_closer );
	                    }
                    } ?>
                </a>
            </div>
            <?php if($header_in_grid){ ?>
        </div>
    </div>
<?php } ?>
</form>
