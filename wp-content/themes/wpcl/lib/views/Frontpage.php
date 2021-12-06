<?php

namespace Scaffolding\views;

class Frontpage {

	public function __construct() {
		/**
		 * Maybe do single
		 */
		if( !is_home() ) {
			new Single();
		}
		/**
		 * Else include the blog
		 */
		else {
			new Blog();
		}
		/**
		 * Add actions
		 */

		/**
		 * Add filters
		 */
		add_filter( 'post_class', [$this, 'postClass'] );
	}

	public function postClass( $classes ) {
		$classes[] = 'entry-frontpage';
		return $classes;
	}
}

// /**
//  * Include the single if a page is assigned
//  */
// if( !is_home() ) {
// 	include _S_ROOT_DIR . 'inc/views/single.php';
// }
// /**
//  * Else include the blog
//  */
// else {
// 	include _S_ROOT_DIR . 'inc/views/blog.php';
// }