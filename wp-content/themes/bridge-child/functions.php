<?php

// enqueue the child theme stylesheet

if(!function_exists('bridge_qode_child_theme_enqueue_scripts')) {

	Function bridge_qode_child_theme_enqueue_scripts() {
		wp_register_style('bridge-childstyle', get_stylesheet_directory_uri() . '/style.css');
		wp_enqueue_style('bridge-childstyle');
	}

	add_action('wp_enqueue_scripts', 'bridge_qode_child_theme_enqueue_scripts', 11);
}

/**
 * Remove related products output
 */
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

/** Disable Ajax Call from WooCommerce on front page and posts
add_action( 'wp_enqueue_scripts', 'dequeue_woocommerce_cart_fragments', 11);
function dequeue_woocommerce_cart_fragments() {
if (is_front_page() || is_single() || is_page ( 'aquatic-forensic-expert-witness-services') ) wp_dequeue_script('wc-cart-fragments');
}
*/

/** Disable All WooCommerce  Styles and Scripts Except Shop Pages*/
add_action( 'wp_enqueue_scripts', 'dequeue_woocommerce_styles_scripts', 99 );
function dequeue_woocommerce_styles_scripts() {
if ( function_exists( 'is_woocommerce' ) ) {
if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() ) {
# Styles
wp_dequeue_style( 'woocommerce-general' );
wp_dequeue_style( 'woocommerce-layout' );
wp_dequeue_style( 'woocommerce-smallscreen' );
wp_dequeue_style( 'woocommerce_frontend_styles' );
wp_dequeue_style( 'woocommerce_fancybox_styles' );
wp_dequeue_style( 'woocommerce_chosen_styles' );
wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
# Scripts
wp_dequeue_script( 'wc_price_slider' );
wp_dequeue_script( 'wc-single-product' );
wp_dequeue_script( 'wc-add-to-cart' );
wp_dequeue_script( 'wc-cart-fragments' );
wp_dequeue_script( 'wc-checkout' );
wp_dequeue_script( 'wc-add-to-cart-variation' );
wp_dequeue_script( 'wc-single-product' );
wp_dequeue_script( 'wc-cart' );
wp_dequeue_script( 'wc-chosen' );
wp_dequeue_script( 'woocommerce' );
wp_dequeue_script( 'prettyPhoto' );
wp_dequeue_script( 'prettyPhoto-init' );
wp_dequeue_script( 'jquery-blockui' );
wp_dequeue_script( 'jquery-placeholder' );
wp_dequeue_script( 'fancybox' );
wp_dequeue_script( 'jqueryui' );
}
}
}


//=============================================================================
// ADD CUSTOM FIELDS BOX BACK TO ALL SCREENS
// =============================================================================

function add_parent_theme_metaboxes() {
    remove_action( 'do_meta_boxes', 'bridge_qode_remove_default_custom_fields' );
}
add_action( 'after_setup_theme', 'add_parent_theme_metaboxes', 10 );

//=============================================================================
// Limit site search to pages and posts only - exclude woo products
// =============================================================================
function searchfilter($query) {
     if ($query->is_search && !is_admin() ) {
        $query->set('post_type',array('post','page'));
    }
 
return $query;
}
 
add_filter('pre_get_posts','searchfilter');


//=============================================================================
// Prevent the following pages from appreaing in search
// =============================================================================
function jp_search_filter( $query ) {
if ( $query->is_search && $query->is_main_query() ) {
$query->set( 'post__not_in', array( 6892, 6891, 6867 ) );
}
}

add_action( 'pre_get_posts', 'jp_search_filter' );


//=============================================================================
// Add form 6 to specific Woocommerce pages
// =============================================================================
add_action( 'woocommerce_product_thumbnails', 'wooform_custom_action', 10 );
 
function wooform_custom_action() {
	if (is_single(6883)) 	{
		echo '<div class="woo_page_form">';	
	gravity_form( 6, $display_title = true, $display_description = true, $display_inactive = false, $field_values = null, $ajax = false, $tabindex, $echo = true );
		echo '</div>';	
}}

//=============================================================================
// ADD TAX EXEMPT CHECKMARK
// =============================================================================
add_action( 'woocommerce_after_order_notes', 'qd_tax_exempt');

function qd_tax_exempt( $checkout ) {

  echo '<div id="qd-tax-exempt"><h3>'.__('Tax Exempt Status').'</h3>';

  woocommerce_form_field( 'shipping_method_tax_exempt', array(
      'type'          => 'checkbox',
      'name'          => 'is_exempt',
      'class'         => array(),
      'label'         => __('My organization is tax exempt. '),
      'required'  => false,
      ), $checkout->get_value( 'shipping_method_tax_exempt' ));
    


  echo '</div>';
}

add_action( 'woocommerce_checkout_update_order_review', 'taxexempt_checkout_update_order_review');
function taxexempt_checkout_update_order_review( $post_data ) {
  global $woocommerce;

  $woocommerce->customer->set_is_vat_exempt(FALSE);

  parse_str($post_data);

  if ( isset($shipping_method_tax_exempt) && $shipping_method_tax_exempt == '1')
    $woocommerce->customer->set_is_vat_exempt(true);                
}

//=============================================================================
// ADD TAX EXEMPT ID FIELD
// =============================================================================
    
/**
 * Add the field to the checkout
 */

add_action( 'woocommerce_after_order_notes', 'my_custom_checkout_field' );

function my_custom_checkout_field( $checkout ) {

    echo '<div id="my_custom_checkout_field"><h2>' . __('') . '</h2>';

    woocommerce_form_field( 'my_field_name', array(
        'type'          => 'text',
        'class'         => array('my-field-class form-row-wide'),
        'label'         => __('Fill in this field'),
        'placeholder'   => __('Enter Tax ID Number Here If Tax Exempt'),
        ), $checkout->get_value( 'my_field_name' ));

    echo '</div>';

}
    /**
 * Update the order meta with field value
 */
add_action( 'woocommerce_checkout_update_order_meta', 'my_custom_checkout_field_update_order_meta' );

function my_custom_checkout_field_update_order_meta( $order_id ) {
    if ( ! empty( $_POST['my_field_name'] ) ) {
        update_post_meta( $order_id, 'My Field', sanitize_text_field( $_POST['my_field_name'] ) );
    }
}
    /**
 * Display field value on the order edit page
 */
add_action( 'woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );

function my_custom_checkout_field_display_admin_order_meta($order){
    echo '<p><strong>'.__('My Field').':</strong> ' . get_post_meta( $order->id, 'My Field', true ) . '</p>';
}


/**
 * Change the default state and country on the checkout page
 */
add_filter( 'default_checkout_billing_country', 'change_default_checkout_country' );

function change_default_checkout_country() {
  return 'US'; // country code
}

/**
 * Remove all product structured data.
 */
function ace_remove_product_structured_data( $types ) {
	if ( ( $index = array_search( 'product', $types ) ) !== false ) {
		unset( $types[ $index ] );
	}

	return $types;
}
add_filter( 'woocommerce_structured_data_type_for_page', 'ace_remove_product_structured_data' );


//=============================================================================
// ADD CUSTOM JSON-LD SCHEMA TO PAGE 
//=============================================================================
//

//add_action('wp_head', 'insert_schema');
//function insert_schema(){
//	$schema = get_post_meta(get_the_ID(), 'schema', true);
//	if(!empty($schema)) {
//		echo $schema;
//		}
//};

//* Remove Font Awesome from WordPress theme
add_action( 'wp_print_styles', 'tn_dequeue_font_awesome_style' );
function tn_dequeue_font_awesome_style() {
      wp_dequeue_style( 'fontawesome' );
      wp_deregister_style( 'fontawesome' );
}	
	
//=============================================================================
// ADD DATE POST WAS LAST EDITED TO SINGLE POST PAGE
//=============================================================================
//	
//		function wpb_last_updated_date( $content ) {
//		$u_time = get_the_time('U'); 
//		$u_modified_time = get_the_modified_time('U'); 
//		if ($u_modified_time >= $u_time + 86400) { 
//		$updated_date = get_the_modified_time('F jS, Y');
//		$custom_content .= '<p class="last-updated">Last updated on '. $updated_date . ' </p>';  
//		} 
//
//			$custom_content .= $content;
//			return $custom_content;
//		}
//	add_filter( 'the_content', 'wpb_last_updated_date' );	

//=============================================================================
// HIDE ADDITIONAL INFORMATION TAB IN WOOCOMMERCE INDIVIDUAL PRODUCT LISTINGS
//=============================================================================
//	

add_filter( 'woocommerce_product_tabs', 'remove_product_tabs', 9999 );
  
function remove_product_tabs( $tabs ) {
    unset( $tabs['additional_information'] ); 
    return $tabs;
}

//add back to store button after cart
add_action('woocommerce_single_product_summary', 'themeprefix_back_to_store', 35);
function themeprefix_back_to_store() { ?>
<a class="button wc-backward" href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>"><?php _e( 'Return to Store', 'woocommerce' ) ?></a>
<?php
}




add_filter('woocommerce_show_page_title', 'bbloomer_hide_shop_page_title');
 
function bbloomer_hide_shop_page_title($title) {
   if (is_product()) $title = false;
   return $title;
}


//=============================================================================
// FOR GRAVITY PERKS NESTED FIELD CALCULATION
//=============================================================================
//
/**
 * Gravity Perks // Nested Forms // Include Child Products Directly in Parent Form Order Summary
 * https://gravitywiz.com/documentation/gravity-forms-nested-forms/
 *
 * Instruction Video: https://www.loom.com/share/857f5bbc938041de85fc8e8cc1f2abc3
 *
 * 1. Add a Calculated Product to your parent form.
 * 2. Add your Nested Form field with the :total modifier.
 * 3. Copy and paste this snippet into your theme's functions.php file.
 *
 * Now the Calculated Product field on your parent form will be replaced with the products from each child entry.
 */
add_filter( 'gform_product_info', function( $product_info, $form, $entry ) {

	foreach ( $form['fields'] as $field ) {

		if ( ! is_a( $field, 'GF_Field_Calculation' ) ) {
			continue;
		}

		$child_products = array();

		preg_match_all( '/{[^{]*?:([0-9]+):(sum|total|count)=?([0-9]*)}/', $field->calculationFormula, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {

			list( ,$nested_form_field_id,, ) = $match;

			$nested_form_field = GFFormsModel::get_field( $form, $nested_form_field_id );
			if ( ! $nested_form_field ) {
				continue;
			}

			$child_form    = gp_nested_forms()->get_nested_form( $nested_form_field->gpnfForm );
			$_entry        = new GPNF_Entry( $entry );
			$child_entries = $_entry->get_child_entries( $nested_form_field_id );

			foreach ( $child_entries as $child_entry ) {
				$child_product_info = GFCommon::get_product_fields( $child_form, $child_entry );
				$_child_products    = array();
				foreach ( $child_product_info['products'] as $child_field_id => $child_product ) {
					$child_product['name'] = "{$product_info['products'][ $field->id ]['name']} — {$child_product['name']}";

					// If Nested Form fields have Live Merge Tags, process those.
					if ( method_exists( 'GP_Populate_Anything_Live_Merge_Tags', 'has_live_merge_tag' ) ) {
						$gppa_lmt = GP_Populate_Anything_Live_Merge_Tags::get_instance();
						if ( $gppa_lmt->has_live_merge_tag( $child_product['name'] ) ) {
							$gppa_lmt->populate_lmt_whitelist( $child_form );
							$child_product['name'] = $gppa_lmt->replace_live_merge_tags_static( $child_product['name'], $child_form, $child_entry );
						}
					}

					$_child_products[ "{$nested_form_field_id}.{$child_entry['id']}.{$child_field_id}" ] = $child_product;
				}
				$child_products = $child_products + $_child_products;
			}
		}

		if ( empty( $child_products ) ) {
			continue;
		}

		$product_keys = array_keys( $product_info['products'] );
		$products     = array_values( $product_info['products'] );

		// phpcs:ignore WordPress.PHP.StrictInArray.FoundNonStrictFalse
		$index = array_search( $field->id, $product_keys, false );

		array_splice( $product_keys, $index, 1, array_keys( $child_products ) );
		array_splice( $products, $index, 1, array_values( $child_products ) );

		$product_info['products'] = array_combine( $product_keys, $products );

	}

	return $product_info;
}, 10, 3 );

