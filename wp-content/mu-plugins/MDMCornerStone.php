<?php
/**
 * The plugin bootstrap file
 * This file is read by WordPress to generate the plugin information in the plugin admin area.
 * This file also defines plugin parameters, registers the activation and deactivation functions, and defines a function that starts the plugin.
 * @link    https://bitbucket.org/midwestdigitalmarketing/cornerstone/
 * @since   1.0.0
 * @package mdm_cornerstone
 *
 * @wordpress-plugin
 * Plugin Name: MDM Cornerstone
 * Plugin URI:  https://bitbucket.org/midwestdigitalmarketing/cornerstone/
 * Description: Site specific plugin functionality
 * Version:     3.0.1
 * Author:      Mid-West Digital Marketing
 * Author URI:  https://midwestfamilymadison.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: mdm_cornerstone
 */

namespace Mdm\Cornerstone;

use \Carbon_Fields\Carbon_Fields;

use \wpcl\wpconsole\Console;

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( '\Mdm\Cornerstone\Plugin' ) )
	{
	/**
	 * Version of the plugin
	 */
	define( 'MDM_CORNERSTONE_VERSION', '3.0.1' );
	/**
	 * URL to the appropriate root directory
	 */
	define( 'MDM_CORNERSTONE_URL', plugin_dir_url( __FILE__ ) . 'mdm-cornerstone/' );
	/**
	 * Path to the appropriate root directory
	 * @since 1.0.0
	 */
	define( 'MDM_CORNERSTONE_DIR', plugin_dir_path( __FILE__ ) . 'mdm-cornerstone/' );
	/**
	 * Include composer autoload file
	 */
	require_once MDM_CORNERSTONE_DIR . 'vendor/autoload.php';
	/**
	 * Main plugin class
	 * Only used to instantiate the individual classes, and to provide an easier
	 * way to access framework methods within our themes
	 *
	 */
	class Plugin extends Framework
	{
		/**
		 * Get subscriber instance
		 * Check if already registered, and run functions to register filters and actions
		 *
		 * @method __construct
		 */
		public function __construct()
		{
			/**
			 * Construct parent
			 */
			parent::__construct();
			/**
			 * Kickoff the plugin
			 */
			$this->burnBabyBurn();
		}
		/**
		 * Register actions
		 *
		 * Uses the subscriber class to ensure only actions of this instance are added
		 * and the instance can be referenced via subscriber
		 *
		 * @since 1.0.0
		 */
		public function addActions() : void
		{
			Subscriber::addAction( 'after_setup_theme', ['\\Carbon_Fields\\Carbon_Fields', 'boot'] );
		}
		/**
		 * Kickoff operation of the plugin
		 * Light the fires, and burn the tires
		 *
		 * @access private
		 */
		private function burnBabyBurn()
		{
			/**
			 * Register core classes
			 */
			$classes = self::getClasses( 'includes' );

			foreach( $classes as $class )
			{
				$class = __NAMESPACE__ . '\\' . $class;
				if ( ! is_subclass_of( $class, __NAMESPACE__ . '\\Framework' ) )
				{
					continue;
				}
				new $class();
			}
			/**
			 * Register custom post types
			 */
			$post_types = self::getClasses( 'PostTypes' );

			foreach( $post_types as $post_type_name )
			{
				$post_type = __NAMESPACE__ . '\\PostTypes\\' . $post_type_name;
				$post_type = new $post_type();
			}
			/**
			 * Register custom taxonomies
			 */
			$taxonomies = self::getClasses( 'Taxonomies' );

			foreach( $taxonomies as $taxonomy_name )
			{
				$taxonomy =  __NAMESPACE__ . '\\Taxonomies\\' . $taxonomy_name;
				$taxonomy = new $taxonomy();
			}
			/**
			 * Register custom blocks
			 */
			$blocks = self::getClasses( 'Blocks' );

			foreach( $blocks as $block ) {
				$block = __NAMESPACE__ . '\\Blocks\\' . $block;
				new $block();
			}
			/**
			 * Register addon (plugin support) classes
			 */
			$addons = self::getClasses( 'Addons' );

			foreach( $addons as $addon ) {
				$addon = __NAMESPACE__ . '\\Addons\\' . $addon;
				new $addon();
			}
		}
		/**
		 * Helper function to use relative URLs
		 *
		 * @return $url relative to this plugin
		 */
		public static function url( string $url = '' ) : string
		{
			return MDM_CORNERSTONE_URL . ltrim( $url, '/' );
		}
		/**
		 * Helper function to use relative paths
		 *
		 * @return $path relative to this plugin
		 */
		public static function path( string $path = '' ) : string
		{
			return MDM_CORNERSTONE_DIR . ltrim( $path, '/' );
		}
		/**
		 * Helper function to get all classes inside a directory
		 *
		 * @param string $dir Relative path to a directory within this plugin
		 * @return array Either empty array or an array of found classes
		 */
		public static function getClasses( string $dir = '' ) : array
		{
			if ( empty( $dir ) )
			{
				return [];
			}

			$classes = [];

			$files = glob( trailingslashit( MDM_CORNERSTONE_DIR  ) . trailingslashit( $dir ) . '*.php' );

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
		public static function log( $object, bool $include_stack = true ) : void
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
		 * Helper function to determin if in a development environment
		 * Checks against an array of development types
		 *
		 * @see  https://developer.wordpress.org/reference/functions/wp_get_environment_type/
		 */
		public static function isDev() : bool
		{
			if ( function_exists( 'wp_get_environment_type' ) )
			{
				return in_array( wp_get_environment_type(), ['staging', 'development', 'local'] ) || WP_DEBUG === true;
			}
			else {
				return WP_DEBUG;
			}
		}
	}
	new \Mdm\Cornerstone\Plugin();
}
