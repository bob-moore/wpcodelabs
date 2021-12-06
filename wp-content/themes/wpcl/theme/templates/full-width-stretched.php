<?php
/**
 *
 * Template Name: Full Width Stretched
 * Template Post Type: page, post, acl-layout
 *
 */


/**
 * Caches the template being used, for use in other functions
 */
wp_cache_set( '_s_template', 'full-width-stretched' );
/**
 * Remove all actions from undesired areas
 *
 * Do late, do get rid of actions that get attached
 */
function _s_template_remove_actions() {
	remove_all_actions( '_s_sidebar' );
}
add_action( 'wp_body_open', '_s_template_remove_actions' );
/**
 * Remove entry header and footer unless specifically enabled
 */
if( get_post_meta( get_the_id(), 'entry_header', true ) !== 'enabled' ) {
	remove_all_actions( '_s_entry_header' );
}

if( get_post_meta( get_the_id(), 'entry_footer', true ) !== 'enabled' ) {
	remove_all_actions( '_s_entry_footer' );
}

/**
 * Add some additional body classes
 */
add_filter( 'body_class', function( $classes ) {
	$classes[] = 'full-width-stretched';
	return $classes;
} );

/**
 * Include the main index file
 */
get_template_part( 'index' );
