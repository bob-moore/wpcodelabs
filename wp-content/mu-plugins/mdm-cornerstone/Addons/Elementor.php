<?php
/**
 * Elementor control class
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/classes
 */

namespace Mdm\Cornerstone\Addons;

use \Mdm\Cornerstone\Framework;
use \Mdm\Cornerstone\Subscriber;
use \Mdm\Cornerstone\Plugin;

defined( 'ABSPATH' ) || exit;

class Elementor extends Framework
{
	/**
	 * Check if Elementor plugin is active and construct
	 *
	 * @method __construct
	 * @return $this
	 */
	public function __construct()
	{
		if ( ! Plugin::isPluginActive('elementor/elementor.php') )
		{
			return false;
		}
		return parent::__construct();
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'elementor/widgets/widgets_registered', [$this, 'registerModules'] );
		Subscriber::addAction( 'elementor/init', [$this, 'initCategory'] );
		// Subscriber::addAction( 'elementor/frontend/after_enqueue_scripts', [ $this, 'enqueueScripts' ] );
	}
	/**
	 * Enqueue Frontend Javascript Files
	 *
	 * @see https://developer.wordpress.org/reference/functions/wp_enqueue_script/
	 */
	public function enqueueScripts() {
		wp_enqueue_script( 'cornerstone-elementor-controls', Plugin::url( 'assets/js/elementor.js' ), ['jquery'], MDM_CORNERSTONE_VERSION, true );
	}
	/**
	 * Register custom Elementor modules
	 *
	 * Glob includes/elementor directory for PHP files and registers them as modules
	 */
	public function registerModules() : void
	{
		$modules = Plugin::getClasses( 'Addons/Elementor' );

		$elementor = \Elementor\Plugin::instance();

		foreach( $modules as $module ) {

			$module = __NAMESPACE__ . '\\Elementor\\' . $module;

			$elementor->widgets_manager->register_widget_type( new $module() );
		}
	}
	/**
	 * Create elementor widget category of 'custom'
	 */
	public function initCategory() : void
	{
		\Elementor\Plugin::instance()->elements_manager->add_category( 'custom', [ 'title' => 'Custom' ], 1 );
	}
	/**
	 * Format links from elementor
	 *
	 * Elementor stores links as different parts, so some formatting is necessary
	 * before being output to a frontend template
	 *
	 * @param  array $settings The link settings from elementor
	 * @param  array $atts Any additional attributes passed from the widget
	 * @return string formatted link attributes
	 * @todo Refactor to return entire link with attributes attached for better rendering
	 */
	public static function linkAtts( $settings, $atts = [] ) {

		$atts = wp_parse_args( $atts, [
			'href' => esc_url_raw( $settings['url'] ),
			'target' => '',
			'rel' => '',
		] );

		if ( $settings['is_external'] )
		{
			$atts['target'] = '_blank';
			$atts['rel'] .= ' noreferrer noopener';
		}

		if ( $settings['nofollow'] )
		{
			$atts['rel'] .= ' nofollow';
		}

		if ( ! empty( $settings['custom_attributes'] ) )
		{
			$ca_pairs = array_map( 'trim', explode( ',', $settings['custom_attributes'] ) );
			foreach( $ca_pairs as $ca_pair )
			{
				$pair = array_map( 'trim', explode( '|', $ca_pair ) );
				$atts[$pair[0]] = $pair[1];
			}
		}

		$output = '';

		foreach ( $atts as $att => $value )
		{
			if ( empty( $value ) )
			{
				continue;
			}

			$output .= sprintf( ' %s="%s"', $att, trim( $value ) );
		}

		return $output;
	}
}