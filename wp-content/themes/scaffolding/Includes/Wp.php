<?php
/**
 * Wordpress function wrapper class
 *
 * Used to call functions
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding;

defined( 'ABSPATH' ) || exit;

class Wp extends Lib\Framework
{
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_filter/
	 */
	public function addFilters() : void
	{
		Subscriber::addFilter( 'timber/twig', [$this, 'twigFunctions'] );
		Subscriber::addFilter( 'timber/context', [$this, 'addScope'] );
		// timber/context
	}
	/**
	 * Add functions to twig
	 *
	 * @param object $twig Twig instance to add function to
	 */
	public function twigFunctions( object $twig ) : object
	{
		$twig->AddFunction( new \Timber\Twig_Function( 'do_action', 'do_action' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'wp_head', 'wp_head' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'wp_footer', 'wp_footer' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'has_action', 'has_action' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'the_post', 'the_post' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'the_content', 'the_content' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'the_excerpt', 'the_excerpt' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'apply_filters', 'apply_filters' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'is_singular', 'is_singular' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'get_search_query', 'get_search_query' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'the_custom_logo', 'the_custom_logo' ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'imageSize', [$this, 'imageSize'] ) );
		return $twig;
	}

	public function addScope( $scope )
	{
		$scope['wp'] = $this;
		return $scope;
	}
	/**
	 * Global callback to run wp functions
	 */
	public function __call( $function, $args ) {
		if ( function_exists( $function ) ) {
			return call_user_func_array( $function, $args );
		}
	}
	/**
	 * Get image size width and height
	 */
	public function imageSize( $url ) {
		$sizes = getimagesize( $url );
		return [
			'width' => $sizes[0],
			'height' => $sizes[1],
		];
	}
}