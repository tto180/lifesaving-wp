<?php

$qodeIconCollections = bridge_qode_return_icon_collections();
$icon_pack = bridge_qode_options()->getOptionValue('search_icon_pack');

?>
<form role="search" action="<?php echo esc_url(home_url('/')); ?>" class="qode_search_form_2" method="get">
	<?php if($header_in_grid){ ?>
    <div class="container">
        <div class="container_inner clearfix">
			<?php if($overlapping_content) {?><div class="overlapping_content_margin"><?php } ?>
				<?php } ?>
                <div class="form_holder_outer">
                    <div class="form_holder">
                        <input type="text" placeholder="<?php esc_html_e('Search', 'bridge'); ?>" name="s" class="qode_search_field" autocomplete="off" />
                        <a class="qode_search_submit" href="javascript:void(0)">
							<?php if( 'svg_path' !== $icon_pack ) {
								$qodeIconCollections->getSearchIcon( $icon_pack );
							} else {
								$search_icon_svg = bridge_qode_options()->getOptionValue('search_icon_svg_opener');
								if( ! empty( $search_icon_svg ) ) {
									echo bridge_qode_get_module_part( $search_icon_svg );
								}
							} ?>
                        </a>
                    </div>
                </div>
				<?php if($header_in_grid){ ?>
				<?php if($overlapping_content) {?></div><?php } ?>
        </div>
    </div>
<?php } ?>
</form>