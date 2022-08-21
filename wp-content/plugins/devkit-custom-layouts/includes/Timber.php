<?php
/**
 * Wrapper for Timber
 *
 * @class Timber
 * @package CustomLayouts\Classes
 */

namespace Devkit\CustomLayouts;

use \Timber\Timber as TimberCore;

defined( 'ABSPATH' ) || exit;

class Timber extends Framework
{
	/**
	 * Render a template file
	 *
	 * @param  string $file Full path to template file
	 * @param  array  $context additional context to add (optional)
	 * @return false | void
	 */
	public function renderFile( string $file, array $context = [] )
	{
		if ( ! is_file( $file ) )
		{
			return false;
		}
		if ( in_array( pathinfo( $file, PATHINFO_EXTENSION ), [ 'twig', 'html' ] ) )
		{
			/**
			 * Get from filter first, to allow themes to pass scope
			 */
			$_scope = apply_filters( 'devkit/custom_layouts/scope', [] );

			if ( empty( $_scope ) )
			{
				/**
				 * Attempt to get from cache
				 */
				$_scope = wp_cache_get('template_scope', 'devkit/custom_layouts');
				/**
				 * Maybe get from timber if not cached
				 */
				$_scope = ! empty( $_scope ) ? $_scope : TimberCore::context();
				/**
				 * Allow filtering
				 */
				$_scope = apply_filters( 'devkit/custom_layouts/template_scope', $_scope );
				/**
				 * Set cache
				 */
				wp_cache_set( 'template_scope', $_scope, 'devkit/custom_layouts', 60 * 60 );
			}
			else {
				$_scope = apply_filters( 'devkit/custom_layouts/template_scope', $_scope );
			}
			/**
			 * Send flyin'
			 */
			TimberCore::render( [$file], $_scope );
		}
		elseif ( in_array( pathinfo( $file, PATHINFO_EXTENSION ), [ 'php' ] ) )
		{
			require $file;
		}
	}
	/**
	 * Render a string using timber
	 *
	 * @param string $string HTML/TWIG string to be rendered by timber
	 * @return void
	 */
	public function renderString( string $string )
	{
		/**
		 * Get from filter first, to allow themes to pass scope
		 */
		$_scope = apply_filters( 'devkit/custom_layouts/scope', [] );

		if ( empty( $_scope ) )
		{
			/**
			 * Attempt to get from cache
			 */
			$_scope = wp_cache_get('template_scope', 'devkit/custom_layouts');
			/**
			 * Maybe get from timber if not cached
			 */
			$_scope = ! empty( $_scope ) ? $_scope : TimberCore::context();
			/**
			 * Allow filtering
			 */
			$_scope = apply_filters( 'devkit/custom_layouts/template_scope', $_scope );
			/**
			 * Set cache
			 */
			wp_cache_set( 'template_scope', $_scope, 'devkit/custom_layouts', 60 * 60 );
		}
		else {
			$_scope = apply_filters( 'devkit/custom_layouts/template_scope', $_scope );
		}
		/**
		 * Send flyin'
		 */
		TimberCore::render_string($string, $_scope);
	}
}