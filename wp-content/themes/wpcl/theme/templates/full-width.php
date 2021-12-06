<?php
/**
 *
 * Template Name: Full Width
 * Template Post Type: page, post, acl-layout
 *
 */
/**
 * Caches the template being used, for use in other functions
 */
wp_cache_set( '_s_template', 'full-width' );
/**
 * Remove all actions from undesired areas
 */
function _s_template_remove_actions() {
	remove_all_actions( '_s_sidebar' );
}
add_action( 'wp_body_open', '_s_template_remove_actions' );

/**
 * Add some additional body classes
 */
add_filter( 'body_class', function( $classes ) {
	$classes[] = 'full-width-content';
	return $classes;
} );

/**
 * Include the main index file
 */
get_template_part( 'index' );
