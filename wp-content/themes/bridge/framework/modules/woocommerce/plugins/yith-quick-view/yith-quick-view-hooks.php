<?php

if (!function_exists('bridge_qode_woocommerce_yith_template_single_title')) {
	/**
	 * Function for overriding product title template in YITH Quick View plugin template
	 */
	function bridge_qode_woocommerce_yith_template_single_title() {

		the_title('<h2 itemprop="name" class="qode-yith-product-title entry-title">', '</h2>');
	}
}