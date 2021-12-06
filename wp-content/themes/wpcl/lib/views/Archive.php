<?php

namespace Scaffolding\views;

class Archive {

	public function __construct() {
		/**
		 * Add actions
		 */

		/**
		 * Add filters
		 */
		add_filter( 'post_class', [$this, 'postClass'] );
	}

	public function postClass( $classes ) {
		$classes[] = 'entry-archive';
		return $classes;
	}

	public function defaultTemplate( $template ) {
		/**
		 * If a selected page template exists, bail
		 */
		if( is_page_template() != false ) {
			return $template;
		}

		$template = _S_ROOT_DIR . 'theme/templates/right-sidebar.php';

		return $template;
	}
}




// /**
//  * Add class to individual entries
//  */
// if( !function_exists( '_s_post_class' ) ) {
// 	function _s_post_class( $classes ) {
// 		$classes[] = 'archive-entry';
// 		return $classes;
// 	}
// 	add_filter( 'post_class', '_s_post_class' );
// }

// /**
//  * Set the default template for archive/search/blog
//  *
//  * May exist in blog.php or search.php
//  * For safety, we check if it exists first
//  */
// // if( !function_exists( '_s_default_template' ) ) {
// // 	function _s_default_template( $template ) {

// // 		$template = _S_ROOT_DIR . 'templates/full-width.php';
// // 		return $template;
// // 	}
// // 	// add_filter( 'template_include', '_s_default_template' );
// // }



// /**
//  * Wrap entire archive with a row class
//  */
// function _s_while_wrapper() {
// 	if( current_filter() === '_s_while_before' ) {
// 		echo '<div class="row flexrow flexwrap archive-loop-wrapper">';
// 	}
// 	else {
// 		echo '</div>';
// 	}
// }
// add_action( '_s_while_before', '_s_while_wrapper', 15 );
// add_action( '_s_while_after', '_s_while_wrapper', 5 );
// /**
//  * Wrap each article in a column
//  */
// function _s_article_column_wrapper() {
// 	if( current_filter() === '_s_content_before' ) {
// 		echo '<div class="entry-column scol-tablet-6">';
// 	}
// 	else {
// 		echo '</div>';
// 	}
// }
// add_action( '_s_content_before', '_s_article_column_wrapper', 5 );
// add_action( '_s_content_after', '_s_article_column_wrapper', 15 );
// /**
//  * Remove the footer
//  */
// // remove_action( '_s_entry_footer', '_s_entry_footer' );