<?php


namespace Scaffolding\views;

class Single {

	public function __construct() {
		/**
		 * Add actions
		 */

		/**
		 * Add filters
		 */
		add_filter( 'post_class', [$this, 'postClass'] );
		add_filter( 'template_include', [$this, 'defaultTemplate'] );
	}

	public function postClass( $classes ) {
		$classes[] = 'entry-single';
		return $classes;
	}

	public function defaultTemplate( $template ) {
		/**
		 * If a selected page template exists, bail
		 */
		if( is_page_template() != false ) {
			return $template;
		}
		/**
		 * Set default template for post types
		 */
		switch ( get_post_type() ) {
			case 'page':
				$template = _S_ROOT_DIR . 'theme/templates/full-width.php';
				break;
			case 'post':
				$template = _S_ROOT_DIR . 'theme/templates/right-sidebar.php';
				break;
			case 'fl-builder-template':
				$template = _S_ROOT_DIR . 'theme/templates/full-width-stretched.php';
				break;
			case 'acl-layout':
				$template = _S_ROOT_DIR . 'theme/templates/full-width-stretched.php';
				break;
			default:
				$template = _S_ROOT_DIR . 'theme/templates/right-sidebar.php';
				break;
		}
		return $template;
	}
}

// /**
//  * Add post classes specific to "single"
//  */
// function _s_post_class( $classes ) {
// 	$classes[] = 'single-entry';
// 	return $classes;
// }
// add_filter( 'post_class', '_s_post_class' );
// /**
//  * Se the default template for various post types
//  */
// function _s_default_templates( $template ) {
// 	/**
// 	 * If a selected page template exists, bail
// 	 */
// 	if( is_page_template() != false ) {
// 		return $template;
// 	}
// 	/**
// 	 * Set default template for post types
// 	 */
// 	switch ( get_post_type() ) {
// 		case 'page':
// 			$template = _S_ROOT_DIR . 'templates/full-width.php';
// 			break;
// 		case 'post':
// 			$template = _S_ROOT_DIR . 'templates/right-sidebar.php';
// 			break;
// 		case 'fl-builder-template':
// 			$template = _S_ROOT_DIR . 'templates/full-width-stretched.php';
// 			break;
// 		case 'acl-layout':
// 			$template = _S_ROOT_DIR . 'templates/full-width-stretched.php';
// 			break;
// 		default:
// 			$template = _S_ROOT_DIR . 'templates/right-sidebar.php';
// 			break;
// 	}

// 	return $template;
// }
// add_filter( 'template_include', '_s_default_templates' );
// /**
//  * Do default template options for single entries
//  */
// function _s_theme_template_options() {

// 	if( get_post_meta( get_the_id(), 'entry_header', true ) === 'disabled' ) {
// 		remove_all_actions( '_s_entry_header' );
// 	}

// 	if( get_post_meta( get_the_id(), 'entry_footer', true ) === 'disabled' ) {
// 		remove_all_actions( '_s_entry_footer' );
// 	}
// }
// add_action( 'template_redirect', '_s_theme_template_options' );
// /**
//  * Remove entry footer for anything but posts
//  */
// if( get_post_type() !== 'post' ) {
// 	remove_action( '_s_entry_footer', '_s_entry_footer' );
// }