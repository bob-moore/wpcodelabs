<?php
/**
 * Jetpack setup function.
 *
 * See: https://jetpack.com/support/infinite-scroll/
 * See: https://jetpack.com/support/responsive-videos/
 * See: https://jetpack.com/support/content-options/
 */
function _s_jetpack_theme_support() {

	add_theme_support( 'jetpack-social-menu', 'genericons' );

	add_theme_support( 'jetpack-responsive-videos' );

	// add_theme_support( 'infinite-scroll', array(
	// 	'container' => 'main',
	// 	'render'    => '_s_infinite_scroll_render',
	// 	'footer'    => false, // false, or ID of element to match width
	// 	'wrapper' => false,
	// 	'type' => 'scroll', // click or scroll, or emit
	// ) );
}
add_action( 'after_setup_theme', '_s_jetpack_theme_support' );
/**
 * Custom render function for Infinite Scroll.
 */
function _s_infinite_scroll_render() {
	// Check for woocommerce, which has it's own support
	if( !function_exists( 'is_woocommerce' ) || is_woocommerce() === false ) {
		do_action( '_s_loop' );
	}
}
/**
 * Jetpack content options default image support
 */
function _s_jetpack_default_thumbnail( $default ) {
	/**
	 * See if we're using jetpack content options
	 */
	if( !get_theme_support( 'jetpack-content-options' ) ) {
		return $default;
	}

	$view = _s_get_view();

	if( $view === 'archive' ) {
		$default = get_option( 'jetpack_content_featured_images_archive' ) === '' ? false : $default;
	}

	return $default;
}
// add_filter( '_s_default_thumbnail', '_s_jetpack_default_thumbnail' );
/**
 * Remove automatic sharing icon insertion
 *
 * Allows us to place the sharing icons manually
 */
function _s_remove_jetpack_share() {
	remove_filter( 'the_content', 'sharing_display', 19 );
	remove_filter( 'the_excerpt', 'sharing_display', 19 );

	if ( class_exists( 'Jetpack_Likes' ) ) {
		remove_filter( 'the_content', array( Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
	}
}
add_action( 'loop_start', '_s_remove_jetpack_share' );
/**
 * Insert Jetpack sharing icons
 */
function _s_jetpack_sharing() {
	if ( function_exists( 'sharing_display' ) ) {
		sharing_display( '', true );
	}

	if ( class_exists( 'Jetpack_Likes' ) ) {
		$custom_likes = new Jetpack_Likes;
		echo $custom_likes->post_likes( '' );
	}
}

/**
 * Add social menu to footer
 */
