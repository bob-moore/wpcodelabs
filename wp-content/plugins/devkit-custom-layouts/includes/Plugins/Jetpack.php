<?php
/**
 * Jetpack control class
 *
 * @class jetpack
 * @package CustomLayouts\Plugins
 */
namespace Devkit\CustomLayouts\Plugins;

use Devkit\CustomLayouts\Framework;
use Devkit\CustomLayouts\Subscriber;

defined( 'ABSPATH' ) || exit;

class Jetpack extends Framework {
	/**
	 * Construct new instance
	 *
	 * @return object/bool $this or false
	 */
	public function __construct()
	{
		if ( ! $this->isPluginActive( 'jetpack/jetpack.php' ) ) {
			return false;
		}
		return parent::__construct();
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 * @see  https://developer.wordpress.org/reference/functions/add_filter/
	 */
	public function addActions()
	{
		Subscriber::addAction( 'loop_start', [$this, 'removeSharingInject'] );
	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addFilters()
	{
		Subscriber::addFilter( 'devkit/custom_layouts/template_parts', [$this, 'addTemplates'] );
		Subscriber::addFilter( 'devkit/custom_layouts/template_scope', [$this, 'templateScope'] );

	}
	/**
	 * Add template partials to select field
	 *
	 * @param array $templates List of template parts
	 * @return $templates
	 */
	public function addTemplates( array $templates ) : array
	{
		return array_merge(
			$templates,
			[
				'core/jetpack/sharing' => 'Social Sharing - Jetpack',
			]
		);
	}
	/**
	 * Stops jetpack from automatically injecting sharing buttons, so we can
	 * place them manually
	 *
	 * @return void
	 */
	public function removeSharingInject()
	{
		remove_filter( 'the_content', 'sharing_display', 19 );
		remove_filter( 'the_excerpt', 'sharing_display', 19 );

		if ( class_exists( 'Jetpack_Likes' ) ) {
			remove_filter( 'the_content', [ \Jetpack_Likes::init(), 'post_likes' ], 30, 1 );
		}
	}
	/**
	 * Inserts jetpack sharing module
	 *
	 * @return void
	 */
	public function sharing()
	{
		if ( function_exists( 'sharing_display' ) ) {
			sharing_display( '', true );
		}

		if ( class_exists( 'Jetpack_Likes' ) ) {
			$custom_likes = new \Jetpack_Likes;
			echo $custom_likes->post_likes( '' );
		}
	}
	/**
	 * Add this to scope, so we can call functions from template files
	 *
	 * @param  array  $_scope timber context
	 * @return array $_scope
	 */
	public function templateScope( array $_scope ) : array
	{
		$_scope['jetpack'] = $this;
		return $_scope;
	}
}