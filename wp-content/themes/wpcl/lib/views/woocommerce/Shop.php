<?php

namespace Scaffolding\views\woocommerce;

use \Scaffolding\views\Archive;

class Shop extends Archive {

	public function __construct() {
		parent::__construct();
	}

	public function postClass( $classes ) {
		$classes[] = 'entry-shop';
		return $classes;
	}

}