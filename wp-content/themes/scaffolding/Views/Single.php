<?php
/**
 * Functionality specific to the singular view type
 *
 * @link https://www.wpcodelabs.com
 * @since 1.0.0
 * @package _s
 */

namespace Wpcl\Scaffolding\Views;

use \Wpcl\Scaffolding\Subscriber;
use \Wpcl\Scaffolding\Utilities;
use \Wpcl\Scaffolding\Lib\Framework;

class Single extends Framework {
	/**
	 * Post ID of current post
	 *
	 * @var id
	 * @access protected
	 */
	protected int $_id = 0;
	/**
	 * The type of archive
	 *
	 * @var type
	 * @access protected
	 */
	protected string $_type = 'singular';
	/**
	 * Construct parent and fire hook for child themes
	 */
	public function __construct()
	{
		parent::__construct();

		if ( is_singular() )
		{
			$this->_id = get_the_id();
		}

		do_action( 'scaffolding/view/single/init' );
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'template_redirect', [$this, 'disabledComponents'] );
	}
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
		Subscriber::addFilter( 'post_class', [$this, 'postClass'] );
		Subscriber::addFilter( 'body_class', [$this, 'bodyClass'] );
		Subscriber::addFilter( 'scaffolding/layout', [$this, 'setLayout'] );
	}
	/**
	 * Set a single entry class
	 *
	 * @param  array $classes Collection of post classes for the article element
	 * @see https://developer.wordpress.org/reference/functions/post_class/
	 */
	public function postClass( array $classes ) : array
	{
		return array_merge(
			[
				'entry-single'
			],
			$classes
		);
	}
	/**
	 * Set the body class
	 *
	 * @param  array  $classes Collection of classes for the body element
	 * @see https://developer.wordpress.org/reference/functions/body_class/
	 */
	public function bodyClass( array $classes ) : array
	{
		$meta = Utilities::postMeta( $this->_id, 'scaffolding_options', [ 'body_class' => '', 'content_width' => '' ] );
		/**
		 * Custom body classes
		 */
		if ( ! empty( $meta['body_class'] ) )
		{
			$classes[] = trim( $meta['body_class'] );
		}
		/**
		 * Custom content width
		 */
		if ( ! empty( $meta['content_width'] ) )
		{
			$classes[] = "content-container-{$meta['content_width']}";
		}
		else {
			$settings = Subscriber::getInstance( 'Settings' )->get( 'singular/width', [ 'default' => '' ] );

			if ( ! empty( $settings['default'] ) )
			{
				$classes[] = "content-container-{$settings['default']}";
			}
		}

		return $classes;
	}

	public function setLayout( $layout )
	{
		if ( ! empty( $layout ) )
		{
			return $layout;
		}

		$settings = Subscriber::getInstance( 'Settings' )->get( 'singular/layout' );

		if ( $this->_type === 'singular' )
		{
			$meta = Utilities::postMeta( $this->_id, 'scaffolding_options', [ 'layout' => '' ] );

			if ( ! empty( $meta['layout'] ) )
			{
				$layout = $meta['layout'];
			}
			elseif ( isset( $settings[ get_post_type() ] ) )
			{
				$layout = $settings[ get_post_type() ];
			}
		}
		elseif ( isset(  $settings[ $this->_type ] ) )
		{
			$layout = $settings[ $this->_type ];
		}

		return apply_filters( 'scaffolding/layout/singular', $layout ?: $settings['default'] );
	}

	public function disabledComponents()
	{
		$meta = Utilities::postMeta( $this->_id, 'scaffolding_options', [ 'disabled_components' => [] ] );

		foreach ( $meta['disabled_components'] as $component )
		{
			remove_all_actions( $component );
		}
	}
}