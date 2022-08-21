<?php
/**
 * Devkit Custom Layouts Support
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding\Extensions;

use \Wpcl\Scaffolding\Subscriber;
use \Wpcl\Scaffolding\Utilities;
use \Wpcl\Scaffolding\Lib\Framework;

class DevkitCustomLayouts extends Framework {
	/**
	 * Get subscriber instance
	 * Check if already registered, and run functions to register filters and actions
	 *
	 * @method __construct
	 * @return $this
	 */
	public function __construct()
	{
		if ( ! Utilities::isPluginActive( 'devkit-custom-layouts/devkit-custom-layouts.php' ) )
		{
			return false;
		}

		/**
		 * Construct parent
		 */
		parent::__construct();
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function addActions() : void {
		Subscriber::addAction( 'after_setup_theme', [$this, 'themeSupport'] );
	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_filter/
	 */
	public function addFilters() : void {
		// Subscriber::addFilter( 'devkit/custom_layouts/scope', [$this, 'scope'] );
	}

	public function themeSupport()
	{
		$hooks = [
			'scaffolding/end'  => __('Start', 'scaffolding'),
			'scaffolding/end'  => __('End', 'scaffolding'),
			'page/before' => __('Page - Before', 'scaffolding'),
			'page/after' => __('Page - After', 'scaffolding'),
			'header/before' => __('Header - Before', 'scaffolding'),
			'masthead' => __('Masthead', 'scaffolding'),
			'masthead/includes' => __('Masthead Includes', 'scaffolding'),
			'header/after' => __('Header - After', 'scaffolding'),
			'main/before' => __('Main - Before', 'scaffolding'),
			'main/open' => __('Main - Open', 'scaffolding'),
			'main/close' => __('Main - Close', 'scaffolding'),
			'main/after' => __('Main - After', 'scaffolding'),
			'primary/before' => __('Primary - Before', 'scaffolding'),
			'primary/after' => __('Primary - After', 'scaffolding'),
			'loop/before' => __('Loop - Before', 'scaffolding'),
			'loop/start' => __('Loop - Start', 'scaffolding'),
			'loop/enter' => __('Loop - Enter', 'scaffolding'),
			'loop' => __('Loop', 'scaffolding'),
			'loop/leave' => __('Loop - Leave', 'scaffolding'),
			'loop/end' => __('Loop - End', 'scaffolding'),
			'loop/after' => __('Loop - After', 'scaffolding'),
			'content/before' => __('Content - Before', 'scaffolding'),
			'content/404' => __('Content - 404', 'scaffolding'),
			'content/none' => __('Content - None', 'scaffolding'),
			'content/after' => __('Content - After', 'scaffolding'),
			'entry' => __('Entry', 'scaffolding'),
			'entry/before' => __('Entry - Before', 'scaffolding'),
			'entry/open' => __('Entry - Open', 'scaffolding'),
			'entry/header/before' => __('Entry Header - Before', 'scaffolding'),
			'entry/header' => __('Entry Header', 'scaffolding'),
			'entry/header/after' => __('Entry Header - After', 'scaffolding'),
			'entry/content/before' => __('Entry Content - Before', 'scaffolding'),
			'entry/content' => __('Entry Content', 'scaffolding'),
			'entry/content/after' => __('Entry Content - After', 'scaffolding'),
			'entry/footer/before' => __('Entry Footer - Before', 'scaffolding'),
			'entry/footer' => __('Entry Footer', 'scaffolding'),
			'entry/footer/after' => __('Entry Footer - After', 'scaffolding'),
			'entry/close' => __('Entry - Close', 'scaffolding'),
			'entry/after' => __('Entry - After', 'scaffolding'),
			'hero/before' => __('Hero - Before', 'scaffolding'),
			'hero' => __('Hero', 'scaffolding'),
			'hero/includes' => __('Hero Includes', 'scaffolding'),
			'hero/after' => __('Hero - After', 'scaffolding'),
			'sidebar/primary/before' => __('Primary Sidebar - Before', 'scaffolding'),
			'sidebar/primary' => __('Primary Sidebar', 'scaffolding'),
			'sidebar/primary/after' => __('Primary Sidebar - After', 'scaffolding'),
			'sidebar/secondary/before' => __('Secondary Sidebar - Before', 'scaffolding'),
			'sidebar/secondary' => __('Secondary Sidebar', 'scaffolding'),
			'sidebar/secondary/after' => __('Secondary Sidebar - After', 'scaffolding'),
			'sidebar/widgets' => __('Sidebar/widgets', 'scaffolding'),
			'footer/before' => __('Footer - Before', 'scaffolding'),
			'colophon' => __('Colophon', 'scaffolding'),
			'colophone/includes' => __('Colophon Includes', 'scaffolding'),
			'footer/after' => __('Footer - After', 'scaffolding'),
			'navpane' => __('Navpane', 'scaffolding'),
			'navpane/includes' => __('Navpane - Includes', 'scaffolding'),
		];
		add_theme_support( 'devkit-custom-layouts', $hooks );
	}

}