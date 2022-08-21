<?php
/**
 * Framework class
 *
 * Shared functionality to be inherited by other classes
 *
 * @class Framework
 * @package CustomLayouts\Classes
 */

namespace Devkit\CustomLayouts;

use \wpcl\wpconsole\Console;

defined( 'ABSPATH' ) || exit;

class Framework
{
	/**
	 * Construct new instance
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
	 * @return void
	 */
	public function addActions() {}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addFilters() {}
	/**
	 * Register shortcodes
	 *
	 * Uses the subscriber class to ensure only shortcodes of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addShortcodes() {}
	/**
	 * Helper function to determine if this is a dev environment or not
	 *
	 * @access protected
	 * @return bool
	 */
	protected static function isDev()
	{
		if ( function_exists('wp_get_environment_type') )
		{
			return in_array( wp_get_environment_type(), ['staging', 'development', 'local'] ) || WP_DEBUG === true;
		}
		else
		{
			return WP_DEBUG;
		}
	}
	/**
	 * Helper function to get all classes inside a directory
	 *
	 * @param string $dir Relative path to a directory within this plugin
	 * @return array Either empty array or an array of found classes
	 */
	public function getClasses( string $dir = '' ) : array
	{
		if ( empty( $dir ) )
		{
			return [];
		}

		$classes = [];

		$files = glob( trailingslashit( DEVKIT_CUSTOMLAYOUTS_PATH  ) . trailingslashit( $dir ) . '*.php' );

		foreach ( $files as $file )
		{
			$classes[] = str_replace( '.php', '', basename( $file ) );
		}

		return $classes;
	}
	/**
	 * Helper function to expose errors and objects and stuff
	 *
	 * Prints PHP objects, errors, etc to the browswer console using either the
	 * 'wp_footer', or 'admin_footer' hooks. Which are the final hooks that run reliably.
	 *
	 * @param mixed $object : anything to be logged to console
	 * @param bool $include_stack : flag to include/exclude the backtrace so you know where
	 * @return void
	 */
	public static function log( $object, $include_stack = true )
	{
		if ( $include_stack )
		{
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
	public static function isPluginActive( string $plugin = '' ) : bool
	{
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( $plugin );
	}
	/**
	 * Helper function to split action names and get dynamic parts
	 *
	 * @param  string $name The string (name) we want to split into pieces
	 * @param  string $delimiter where to split at
	 * @param  int|integer $offset which instance form the end to split
	 * @return array split name
	 */
	public static function splitName( string $name, string $delimiter = '/', int $offset = 1 ) : array
	{

		$parts = explode( $delimiter, $name );

		$name = [];

		for ( $i = 0; $i < $offset; $i++ )
		{
			if ( empty( $parts ) ) {
				break;
			}
			$name[] = array_pop( $parts );
		}

		return [
			implode( $delimiter, $parts ),
			implode( $delimiter, $name )
		];
	}
}