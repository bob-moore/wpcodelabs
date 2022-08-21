<?php
/**
 * Functionality specific to a woocommerce
 *
 * @link https://www.wpcodelabs.com
 * @since 1.0.0
 * @package _s
 */
namespace Wpcl\Scaffolding\Extensions;

use \Wpcl\Scaffolding\Framework;
use \Wpcl\Scaffolding\Subscriber;

class Jetpack extends Framework {

	public function __construct() {
		if ( ! \Scaffolding::isPluginActive( 'jetpack/jetpack.php' ) ) {
			return false;
		}
		parent::__construct();
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @since 1.0.0
	 */
	public function addActions() {
		Subscriber::addAction( 'after_setup_theme', [$this, 'themeSupport'] );
		Subscriber::addAction( 'loop_start', [$this, 'removeDefaultSharing'] );
		Subscriber::addAction( '_s_jetpack_sharing', [$this, 'sharing'] );
	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @since 1.0.0
	 */
	public function addFilters() {
		Subscriber::addFilter( 'tiled_gallery_content_width', [$this, 'galleryWidth'] );

	}

	public function themeSupport() {

		add_theme_support( 'jetpack-social-menu', 'genericons' );
		add_theme_support( 'jetpack-responsive-videos' );
	}

	public function removeDefaultSharing() {

		remove_filter( 'the_content', 'sharing_display', 19 );
		remove_filter( 'the_excerpt', 'sharing_display', 19 );
		remove_action( 'woocommerce_share', 'jetpack_woocommerce_social_share_icons' );

		if ( class_exists( 'Jetpack_Likes' ) ) {
			remove_filter( 'the_content', array( \Jetpack_Likes::init(), 'post_likes' ), 30, 1 );
		}
	}

	public function sharing() {
		if ( function_exists( 'sharing_display' ) ) {
			sharing_display( '', true );
		}

		if ( class_exists( 'Jetpack_Likes' ) ) {
			$custom_likes = new \Jetpack_Likes;
			echo $custom_likes->post_likes( '' );
		}
	}

	public function galleryWidth( $width = '' ) {
		return isset( $content_width ) ? $content_width : '1200';
	}
}