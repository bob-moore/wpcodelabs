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
use \Wpcl\Scaffolding\Timber;

class Error404 extends Single {
	/**
	 * Construct parent and fire hook for child themes
	 */
	public function __construct() {

		$this->_type = '404';

		do_action( 'scaffolding/view/404/init' );

		parent::__construct();
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
		Subscriber::addFilter( 'timber/context', [$this, 'scope'] );

		parent::addFilters();
	}


	public function scope( array $_scope )
	{
		global $wp;

		$_scope['related'] = Subscriber::getInstance( 'Timber' )->getPosts( [ 's' => trim( $wp->request ), 'posts_per_page' => 4 ] );

		if ( empty( (array) $_scope['related'] ) )
		{
			$_scope['related'] = Subscriber::getInstance( 'Timber' )->getPosts( [ 'posts_per_page' => 4 ] );
		}

		return $_scope;
	}
}