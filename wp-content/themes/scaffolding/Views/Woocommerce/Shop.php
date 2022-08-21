<?php
/**
 * Functionality specific to the shop page
 *
 * @link https://www.wpcodelabs.com
 * @since 1.0.0
 * @package _s
 */

namespace Wpcl\Scaffolding\Views\Woocommerce;

use \Wpcl\Scaffolding\Views\Archive;

class Shop extends Archive {

	public function postClass( $classes ) {
		$classes[] = 'entry-shop';
		return $classes;
	}

}