<?php

/**
 * The common functions
 * This file isn't instatiated directly, it acts as a shared parent for other classes
 * @link    http://midwestdigitalmarketing.com
 * @since   1.0.0
 * @package mdm_cornerstone
 */

namespace mdm\cornerstone;

class Framework {
	/**
	 * Whether or not to load a class
	 * @since 1.0.0
	 * @access protected
	 * @var (bool) $enabled : Flag to conditionally disable a class
	 */
	protected $enabled = true;

	/**
	 * Plugin Options
	 * @since 1.0.0
	 * @access protected
	 * @var (array) $settings : The array that holds plugin options
	 */
	protected $loader;

	/**
	 * Instances
	 * @since 1.0.0
	 * @access protected
	 * @var (array) $instances : Collection of instantiated classes
	 */
	protected static $instances = array();

	/**
	 * Registers our plugin with WordPress.
	 */
	public static function register( $class_name = null ) {
		// Get called class
		$class_name = !is_null( $class_name ) ? $class_name : get_called_class();
		// Instantiate class
		$class = $class_name::get_instance( $class_name );
		// Check that it's enabled
		if( $class->enabled ) {
			// Create API manager
			$class->loader = Loader::get_instance();
			// Register stuff
			$class->loader->registerHooks( $class );
		}
		// Return instance
		return $class;
	}

	/**
	 * Gets an instance of our class.
	 */
	public static function get_instance( $class_name = null ) {
		// Use late static binding to get called class
		$class = !is_null( $class_name ) ? $class_name : get_called_class();
		// Get instance of class
		if( !isset(self::$instances[$class] ) ) {
			self::$instances[$class] = new $class();
		}
		return self::$instances[$class];
	}

	/**
	 * Constructor
	 * @since 1.0.0
	 * @access protected
	 */
	protected function __construct() {
		// Nothing to do here
	}

	/**
	 * Helper function to use relative URLs
	 * @since 1.0.0
	 * @access protected
	 */
	public static function url( $url = '' ) {
		return MDM_CORNERSTONE_URL . ltrim( $url, '/' );
	}

	/**
	 * Helper function to use relative paths
	 * @since 1.0.0
	 * @access protected
	 */
	public static function path( $path = '' ) {
		return MDM_CORNERSTONE_DIR . ltrim( $path, '/' );
	}

	/**
	 * Helper function to get all classes inside a directory
	 */
	public static function get_classes( $shortpath = '' ) {

		if( empty( $shortpath ) ) {
			return array();
		}

		$classes = array();

		$files = glob( trailingslashit( self::path( $shortpath ) ) . '*.php' );

		foreach( $files as $file ) {
			$classes[] = str_replace( '.php', '', basename( $file ) );
		}

		return $classes;
	}
	/**
	 * Helper function to expose errors and objects and stuff
	 */
	public static function expose( $item ) {

		$hook = is_admin() ? 'admin_footer' : 'wp_footer';

		add_action( $hook, function() use( $item ) {
			printf( '<script>console.log(%s);</script>', json_encode( $item ) );
		});
	}

} // end class