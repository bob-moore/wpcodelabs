<?php

use \Wpcl\Scaffolding\Subscriber;

global $post, $product;

if ( ! $product ) {
	$product = wc_setup_product_data( $post );

}

$controller = Subscriber::getInstance( 'Extensions\Woocommerce' );

if ( $controller->_template ) {

	\Scaffolding::render( $controller->_template, [ 'product' => $product ] );

}

