<?php

namespace Scaffolding\addons;

use Scaffolding\Framework;

class Jetpack extends Framework {

	public function __construct() {

		if( !class_exists( 'Jetpack' ) ) {
			return false;
		}

		add_action( 'loop_start', [$this, 'removeJPShare'] );

		add_theme_support( 'jetpack-social-menu', 'genericons' );
		add_theme_support( 'jetpack-responsive-videos' );
		add_theme_support( 'infinite-scroll', array(
			'container' => 'primary',
			'render'    => [$this, 'infiniteScroll'],
			'footer'    => false,
			'wrapper' => false,
			'type' => 'scroll',
		) );
	}

	/**
	 * Custom render function for Infinite Scroll.
	 */
	public function infiniteScroll() {
		// Check for woocommerce, which has it's own support
		if( !function_exists( 'is_woocommerce' ) || is_woocommerce() === false ) {
			do_action( '_s_loop' );
		}
	}
	/**
	 * Removes default jetpack sharing placement, so we can place manually
	 */
	function removeJPShare() {
		remove_filter( 'the_content', 'sharing_display', 19 );
		remove_filter( 'the_excerpt', 'sharing_display', 19 );
		if ( class_exists( 'Jetpack_Likes' ) ) {
			remove_filter( 'the_content', array( \Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
		}
	}
	/**
	 * Insert Jetpack sharing icons
	 */
	public function sharing() {
		if ( function_exists( 'sharing_display' ) ) {
			sharing_display( '', true );
		}

		if ( class_exists( 'Jetpack_Likes' ) ) {
			$custom_likes = new \Jetpack_Likes;
			echo $custom_likes->post_likes( '' );
		}
	}
}