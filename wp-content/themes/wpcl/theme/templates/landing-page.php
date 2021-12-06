<?php
/**
 *
 * Template Name: Landing Page
 * Template Post Type: page, post, acl-layout
 *
 */
/**
 * Caches the template being used, for use in other functions
 */
wp_cache_set( '_s_template', 'landing-page' );
/**
 * Remove all actions from undesired areas
 */
function _s_template_remove_actions() {

	/**
	 * Remove all actions from undesired areas
	 */
	$hooks = array(
		'_s_primary_before_open' => 'Primary Before Open',
		'_s_primary_after_open' => 'Primary After Open',
		'_s_primary_before_close' => 'Primary Before Close',
		'_s_primary_after_close' => 'Primary After Close',
		'_s_header_before' => 'Header Before',
		'_s_masthead_before' => 'Masthead Before',
		'_s_masthead' => 'Masthead',
		'_s_masthead_after' => 'Masthead After',
		'_s_header_after' => 'Header After',
		'_s_loop_before' => 'Loop Before',
		'_s_while_before' => 'While Before',
		'_s_content_before' => 'Content Before',
		'_s_content_after' => 'Content After',
		'_s_while_after' => 'While After',
		'_s_loop_after' => 'Loop After',
		'_s_entry_before' => 'Entry Before',
		'_s_entry_header_before' => 'Entry Header Before',
		'_s_entry_header' => 'Entry Header',
		'_s_entry_header_after' => 'Entry Header After',
		'_s_entry_content_before' => 'Entry Content Before',
		// '_s_entry_content' => 'Entry Content',
		'_s_entry_content_after' => 'Entry Content After',
		'_s_entry_footer_before' => 'Entry Footer Before',
		'_s_entry_footer' => 'Entry Footer',
		'_s_entry_footer_after' => 'Entry Footer After',
		'_s_entry_after' => 'Entry After',
		'_s_comments' => 'Include Comments',
		'_s_comments_before' => 'Comments Before',
		'_s_comments_header_before' => 'Comments Header Before',
		'_s_comments_header_after' => 'Comments Header After',
		'_s_comments_list_before' => 'Comments List Before',
		'_s_comments_list_after' => 'Comments List After',
		'_s_comments_form_before' => 'Comments Form Before',
		'_s_comments_form_after' => 'Comments Form After',
		'_s_comments_after' => 'Comments After',
		'_s_sidebar' => 'Include Sidebar',
		'_s_sidebar_before' => 'Sidebar Before',
		'_s_sidebar_primary_before' => 'Sidebar Primary Before',
		'_s_sidebar_primary_after' => 'Sidebar Primary After',
		'_s_sidebar_after' => 'Sidebar After',
		'_s_footer_before' => 'Footer Before',
		'_s_colophon_before' => 'Colophon Before',
		// '_s_colophon' => 'Colophon',
		'_s_colophon_after' => 'Colophon After',
		'_s_footer_after' => 'Footer After',
	);

	foreach( $hooks as $hook => $name ) {
		remove_all_actions( $hook );
	}
}
add_action( 'wp_body_open', '_s_template_remove_actions' );


/**
 * Add some additional body classes
 */
add_filter( 'body_class', function( $classes ) {
	$classes[] = 'landing-page';
	$classes[] = 'full-width-stretched';
	return $classes;
} );

/**
 * Include the main index file
 */
get_template_part( 'index' );
