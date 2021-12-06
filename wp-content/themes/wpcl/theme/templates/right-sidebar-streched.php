<?php
/**
 *
 * Template Name: Right Sidebar Strechted
 * Template Post Type: page, post, acl-layout
 *
 */
/**
 * Caches the template being used, for use in other functions
 */
wp_cache_set( '_s_template', 'right-sidebar-stretched' );
/**
 * Add some additional body classes
 */
add_filter( 'body_class', function( $classes ) {
	$classes[] = 'right-sidebar-stretched';
	return $classes;
} );

/**
 * Include the main index file
 */
get_template_part( 'index' );
