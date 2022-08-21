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

namespace Wpcl\Scaffolding;

use \wpcl\wpconsole\Console;

class Utilities {
	/**
	 * Helper function to determine if this is a dev environment or not
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_get_environment_type/
	 * @see https://wordpress.org/support/article/debugging-in-wordpress/
	 */
	public static function isDev() : bool
	{
		if ( function_exists('wp_get_environment_type') )
		{
			return in_array( wp_get_environment_type(), ['staging', 'development', 'local'] );
		}
		else
		{
			return WP_DEBUG === true || SCRIPT_DEBUG === true;
		}
	}
	/**
	 * Helper function to determine if a child theme is being used
	 */
	public function childThemeActive() : bool
	{
		return get_template_directory() !== get_stylesheet_directory();
	}
	/**
	 * Helper function to expose errors and objects and stuff
	 *
	 * Prints PHP objects, errors, etc to the browswer console using either the
	 * 'wp_footer', or 'admin_footer' hooks. Which are the final hooks that run reliably.
	 */
	public static function log( $object, bool $include_stack = true ) : void
	{

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
	public static function isPluginActive( string $plugin = '' )
	{
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		return is_plugin_active( $plugin );
	}
	/**
	 * Helper function to use construct URLs relative to the theme
	 */
	public static function url( string $url = '' ) : string
	{
		$base = defined( '_S_URL' ) ? _S_URL : str_replace(
			basename( dirname( get_template_directory() ) ) . '/' . basename( get_template_directory() ),
			basename( dirname( get_template_directory() ) ),
			get_template_directory_uri()
		);

		return trailingslashit( $base ) . ltrim( $url, '/' );
	}
	/**
	 * Helper function to use relative paths
	 */
	public static function path( string $path = '' ) : string
	{
		$base = defined( '_S_DIR' ) ? _S_DIR : trailingslashit( dirname( get_template_directory() ) );

		return $base . ltrim( $path, '/' );
	}
	/**
	 * Helper function to retrieve theme version
	 */
	public static function version() : string
	{
		$theme = wp_get_theme(
			basename( dirname( get_template_directory() ) ) . '/' . basename( get_template_directory() )
		);

		if ( ! is_a( $theme, 'WP_Theme' ) || empty( $theme->Version ) || self::isDev() )
		{
			return time();
		}
		else
		{
			return $theme->Version;
		}
	}

	public static function postMeta( $id, $name, $default = '' ) {

		$meta = get_post_meta( $id, $name, true );

		if ( empty( $meta ) )
		{
			return $default;
		}

		elseif ( is_array( $default ) )
		{
			$meta = is_array( $meta ) ? $meta : [$name => $meta ];

			return wp_parse_args( $meta, $default );
		}

		return $meta;
	}

	public static function termMeta( int $id, string $name, $default = '' )
	{
		$meta = get_term_meta( $id, $name, true );

		if ( empty( $meta ) )
		{
			return $default;
		}

		elseif ( is_array( $default ) )
		{
			$meta = is_array( $meta ) ? $meta : [$name => $meta ];

			return wp_parse_args( $meta, $default );
		}

		return $meta;
	}
}