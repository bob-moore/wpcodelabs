<?php

/**
 * Functionality specific to the frontpage
 *
 * Extends the "archive" view type
 *
 * @link https://www.wpcodelabs.com
 * @since 1.0.0
 * @package _s
 */

namespace Wpcl\Scaffolding\Views;

use \Wpcl\Scaffolding\Subscriber;
use \Wpcl\Scaffolding\Utilities;
use \Wpcl\Scaffolding\Lib\Framework;

class Archive extends Framework {
	/**
	 * The type of archive
	 *
	 * @var type
	 * @access protected
	 */
	protected string $_type = 'archive';
	/**
	 * Construct parent and fire hook for child themes
	 */
	public function __construct()
	{
		$views = Subscriber::getInstance( 'FrontEnd' )->views();
		/**
		 * Post Type Archive
		 */
		if ( in_array( 'archive-posttype', $views ) )
		{
			$this->_type = 'posttype';
		}
		/**
		 * Term archive
		 */
		elseif ( array_intersect( ['archive-category', 'archive-tag', 'archive-tax'], $views ) )
		{
			$this->_type = 'term';
		}

		parent::__construct();

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
				'entry-archive'
			],
			$classes
		);
	}
	/**
	 * Set the default template used for a singular view
	 */
	public function setLayout( $layout )
	{
		if ( ! empty( $layout ) )
		{
			return $layout;
		}

		$settings = Subscriber::getInstance( 'Settings' )->get( "archive/layout", [] );

		switch ( $this->_type )
		{
			case 'posttype' :
				$post_type = get_post_type();

				if ( isset( $settings[ $post_type ] ) && ! empty( $settings[ $post_type ] ) )
				{
					$layout = $settings[ $post_type ];
				}
				break;
			case 'term' :
				$meta = Utilities::termMeta( get_queried_object()->term_id, 'scaffolding_options', [ 'layout' => '' ] );
				$layout = $meta['layout'];
				break;
			default :
				if ( isset( $settings[ $this->_type ] ) )
				{
					$layout = $settings[ $this->_type ];
				}
				break;
		}

		return apply_filters( 'scaffolding/layout/archive', $layout ?: $settings['default'] );
	}

	/**
	 * Add custom body classes
	 */
	public function bodyClass( array $classes ) : array
	{
		$width = '';

		if ( $this->_type === 'term' )
		{
			$meta = Utilities::termMeta( get_queried_object()->term_id, 'scaffolding_options', [ 'body_class' => '', 'content_width' => '' ] );

			$width = ! empty( $meta['content_width'] ) ? $meta['content_width'] : '';

			if ( ! empty( $meta['body_class'] ) )
			{
				$classes[] = trim( $meta['body_class'] );
			}
		}

		if ( empty( $width ) )
		{
			$settings = Subscriber::getInstance( 'Settings' )->get( 'archive/width', [ 'default' => '' ] );

			if ( ! empty( $settings['default'] ) )
			{
				$classes[] = "content-container-{$settings['default']}";
			}
		}

		return $classes;
	}

	public function disabledComponents()
	{
		if ( $this->_type !== 'term' )
		{
			return;
		}
		$meta = Utilities::termMeta( get_queried_object()->term_id, 'scaffolding_options', [ 'disabled_components' => [] ] );

		foreach ( $meta['disabled_components'] as $component )
		{
			remove_all_actions( $component );
		}
	}
}