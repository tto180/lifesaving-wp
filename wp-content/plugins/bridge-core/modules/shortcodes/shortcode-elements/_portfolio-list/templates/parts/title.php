<?php

$custom_portfolio_link = get_post_meta(get_the_ID(), 'qode_portfolio-external-link', true);
$portfolio_link = $custom_portfolio_link != "" ? $custom_portfolio_link : get_permalink();
$target = bridge_core_generate_portfolio_link_target();

$title_styles = array();
if($title_color !== '') {
    $title_styles[] = 'color: '.$title_color;
}

if($title_font_size !== '') {
    $title_styles[] = 'font-size: '.$title_font_size.'px';
}

if($show_title !== 'no') { ?>

    <<?php esc_attr_e( $title_tag ); ?> itemprop="name" class="portfolio_title entry_title"><a itemprop="url" href="<?php echo esc_url( $portfolio_link ); ?>" <?php echo bridge_qode_get_inline_style($title_styles); ?> target="<?php esc_attr_e( $target ); ?>"> <?php echo get_the_title(); ?></a></<?php esc_attr_e( $title_tag ); ?>>

<?php }

