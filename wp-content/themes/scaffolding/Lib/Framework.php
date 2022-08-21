<?php
/**
 * Class Framework
 *
 * This file isn't instatiated directly, it acts as a shared parent for other classes
 *
 * @link https://www.wpcodelabs.com
 * @since 1.0.0
 * @package _s
 */

namespace Wpcl\Scaffolding\Lib;

use \Wpcl\Scaffolding\Subscriber;

use \wpcl\wpconsole\Console;

abstract class Framework {
	/**
	 * Get subscriber instance
	 * Check if already registered, and run functions to register filters and actions
	 *
	 * @method __construct
	 * @return $this
	 */
	public function __construct()
	{
		/**
		 * Conditionally add actions/filters, but only if they haven't already
		 * been added
		 *
		 * The subscriber class will keep track of the classes added, and always return
		 * the first instance of the object created using any individual class, so
		 * filters, actions, and shortcodes are not duplicated across multiple instances
		 *
		 */
		if ( Subscriber::getInstance( $this ) === $this )
		{
			/**
			 * Register actions
			 */
			$this->addActions();
			/**
			 * Register filters
			 */
			$this->addFilters();
			/**
			 * Register shortcodes
			 */
			$this->addShortcodes();

		}
		/**
		 * Return the object for use
		 */
		return $this;
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function addActions() : void {}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_filter/
	 */
	public function addFilters() : void {}
	/**
	 * Register shortcodes
	 *
	 * Uses the subscriber class to ensure only shortcodes of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_shortcode/
	 */
	public function addShortcodes() : void {}
	/**
	 * Helper function to determine if this is a dev environment or not
	 */
	public static function isDev() {
		if( function_exists('wp_get_environment_type') ) {
			return in_array( wp_get_environment_type(), ['staging', 'development', 'local'] ) || WP_DEBUG === true;
		} else {
			return WP_DEBUG;
		}
	}

	public function isChildTheme() {
		if ( get_template_directory() !== get_stylesheet_directory() ) {
			return get_stylesheet_directory();
		}
		return false;
	}
	/**
	 * Helper function to expose errors and objects and stuff
	 *
	 * Prints PHP objects, errors, etc to the browswer console using either the
	 * 'wp_footer', or 'admin_footer' hooks. Which are the final hooks that run reliably.
	 * @since  2.1.0
	 */
	public static function log( $object, $include_stack = true ) {

		if ( $include_stack ) {

			$backtrace = debug_backtrace();

			$object = [
				'stack' => [
					'class' => isset( $backtrace[1]['class'] ) ? $backtrace[1]['class'] : '',
					'file' => $backtrace[0]['file'],
					'line' => $backtrace[0]['line']
				],
				'object' => $object
			];
		}

		Console::log( $object );
	}
	/**
	 * Helper function to determine if plugin is active or not
	 * Wrapper function for is_plugin_active core WP function
	 *
	 * @see https://developer.wordpress.org/reference/functions/is_plugin_active/
	 * @param string  $plugin : Path to the plugin file relative to the plugins directory
	 * @return boolean True, if in the active plugins list. False, not in the list.
	 */
	public static function isPluginActive( $plugin = '' ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( $plugin );
	}
}