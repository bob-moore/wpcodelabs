<?php
/**
 * Social Warfare control class
 *
 * @class SocialWarfare
 * @package CustomLayouts\Plugins
 */
namespace Devkit\CustomLayouts\Plugins;

use Devkit\CustomLayouts\Framework;
use Devkit\CustomLayouts\Subscriber;

defined( 'ABSPATH' ) || exit;

class SocialWarfare extends Framework
{
	/**
	 * Construct new instance
	 *
	 * @return object/bool $this or false
	 */
	public function __construct()
	{
		if (!$this->isPluginActive('social-warfare/social-warfare.php')) {
			return false;
		}
		return parent::__construct();
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
		Subscriber::addFilter('devkit/custom_layouts/template_parts', [$this, 'addTemplates']);
		Subscriber::addFilter('devkit/custom_layouts/template_scope', [$this, 'templateScope']);
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
				'core/socialwarfare/sharing' => 'Social Sharing - Social Warfare',
			]
		);
	}
	/**
	 * Inserts sharing module
	 *
	 * @return void
	 */
	public function sharing()
	{
		if ( function_exists('social_warfare' ) )
		{
			social_warfare();
		}
	}
	/**
	 * Add this to scope, so we can call functions from template files
	 *
	 * @param  array  $_scope timber context
	 * @return array $_scope
	 */
	public function templateScope( array $_scope) : array
	{
		$_scope['social_warfare'] = $this;
		return $_scope;
	}
}
