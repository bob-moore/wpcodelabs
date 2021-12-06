<?php

namespace Scaffolding\views\woocommerce;

use \Scaffolding\views\Single;

class Product extends Single {

	public function __construct() {

		parent::__construct();
	}

	public function postClass( $classes ) {

		global $post;

		if( is_singular( 'product' ) && ( $post->ID === get_queried_object()->ID ) ) {
			$classes[] = 'entry-product';
		}

		return $classes;
	}

}