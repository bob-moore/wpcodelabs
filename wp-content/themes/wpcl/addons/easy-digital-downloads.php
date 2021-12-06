<?php
/**
 * Adds some extra styling wrappers
 */
function _s_edd_wrappers() {

	$filter = current_filter();

	switch( $filter ) {
		case 'edd_purchase_form_before_email':
			echo '<div class="_s_woocommerce_loop_wrapper">';
			break;
		case 'edd_before_checkout_cart':
			echo '<div class="_s_edd_checkout_cart">';
			break;
		default:
			break;
	}
}
// add_action( 'edd_before_checkout_cart', '_s_edd_wrappers', 5 );
// add_action( 'edd_after_checkout_cart', '_s_close_div', 15 );

// remove_action( 'edd_checkout_form_top', 'edd_discount_field', -1 );
// add_action( 'edd_checkout_cart_bottom', 'edd_discount_field' );
//
/**
 * Override EDD Templates
 */
function _s_edd_template_path() {
	return 'template-parts/edd';
}
add_filter( 'edd_templates_dir', '_s_edd_template_path' );
