<?php
/**
 * Frontend control class
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/classes
 */

namespace Mdm\Cornerstone;

defined( 'ABSPATH' ) || exit;

class FrontEnd extends Framework
{
	/**
	 * Collection of current views
	 *
	 * @var array
	 * @access protected
	 */
	protected array $_views = [];
	/**
	 * Scope (context)
	 *
	 * @var array
	 * @access protected
	 */
	protected array $_scope = [];
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'wp_enqueue_scripts', [$this, 'enqueueScripts'] );
		Subscriber::addAction( 'wp_enqueue_scripts', [$this, 'enqueueStyles'] );
		Subscriber::addAction( 'init', [$this, 'cleanHead'] );
	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 */
	public function addFilters() : void
	{
		Subscriber::addFilter( 'wp_resource_hints', [$this, 'disableEmojisRemoveDnsPrefetch'], 10, 2 );
		Subscriber::addFilter( 'timber/locations', [$this, 'timberLocations'], 11 );
	}
	/**
	 * Enqueue Frontend Javascript Files
	 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/
	 */
	public function enqueueScripts() : void
	{
		wp_enqueue_script(
			'mdm_cornerstone_public',
			Plugin::url( 'assets/js/public.js' ),
			['jquery'],
			MDM_CORNERSTONE_VERSION,
			true
		);
	}
	/**
	 * Enqueue Frontend CSS Files
	 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_style/
	 */
	public function enqueueStyles()  : void
	{
		wp_enqueue_style(
			'mdm_cornerstone_public',
			Plugin::url( 'assets/css/public.css' ),
			[],
			MDM_CORNERSTONE_VERSION,
			'all'
		);
		wp_enqueue_style( 'dashicons' );
	}
	/**
	 * Remove unneccessary functions form the header
	 * Mostly things that are annoying at best, and a security issue at worse
	 */
	public function cleanHead() : void
	{
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_head', 'edd_version_in_header' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}

	/**
	* Remove emoji CDN hostname from DNS prefetching hints.
	*
	* @param array $urls URLs to print for resource hints.
	* @param string $relation_type The relation type the URLs are printed for.
	* @return array Difference betwen the two arrays.
	*/
	function disableEmojisRemoveDnsPrefetch( $urls, $relation_type )
	{
		if ( 'dns-prefetch' === $relation_type )
		{
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
			$urls = array_diff( $urls, [$emoji_svg_url] );
		}
		return $urls;
	}
	/**
	 * Set locations to look for twig files
	 *
	 * @param  array  $locations array of existing locations
	 */
	public function timberLocations( array $locations ) : array
	{
		return wp_parse_args(
			[
				MDM_CORNERSTONE_DIR . 'template-parts'
			]
		, $locations );
	}
	/**
	 * Render a php/twig template
	 *
	 * @param string $template Name of template part to include
	 * @param array $context Additional items to add to timber context
	 */
	public function render( string|array $templates = [], array $context = [] ) : void
	{
		if ( empty( $templates ) )
		{
			return;
		}

		$_scope = array_merge( $this->scope(), $context );

		$templates = apply_filters( 'cornerstone/include/templates', $templates );

		\Timber\Timber::render( (array)$templates, $_scope );
	}
	/**
	 * Render a string with timber/twig
	 *
	 * @param string $string Full markup string to render
	 * @param array $context Additional items to add to timber context
	 */
	public function renderString( string $string, array $context = [] ) : void
	{
		if ( empty( $string ) )
		{
			return;
		}

		$_scope = array_merge( $this->scope(), $context );

		\Timber\Timber::render_string( $string, $_scope );
	}
	/**
	 * Get scope (timber $context ) to render files
	 *
	 * @access protected
	 * @return mixed array/object
	 */
	protected function scope()
	{
		if ( empty( $this->_scope ) )
		{
			$this->_scope = apply_filters( 'cornerstone/timber/scope', \Timber\Timber::context() );
		}
		return apply_filters( 'cornerstone/timber/context', $this->_scope );
	}

	public function getPosts( $args = [] )
	{
		return new \Timber\PostQuery( $args );
	}
	/**
	 * Get And render a template part
	 *
	 * @param string  $slug  Template slug
	 * @param string  $name  Template name (optional)
	 * @param array $data Additional data to add to $_scope at render time (optional)
	 * @param boolean $search Whether to search using all paths (optional)
	 */
	public function templatePart( string $slug = '', string $name = '', array $data = [] ) : void
	{
		$templates = apply_filters( 'cornerstone/include/templates', [], $slug, $name );

		if ( ! empty( $name ) )
		{
			$templates = wp_parse_args( $templates, [
				"{$slug}/{$name}.twig",
				"{$slug}-{$name}.twig",
				"{$slug}.twig",
			] );
		}
		else
		{
			$templates = wp_parse_args( $templates, [
				"{$slug}.twig",
			] );
		}

		$this->render( array_unique( $templates ), $data );
	}
}