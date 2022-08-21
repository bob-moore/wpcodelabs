<?php
/**
 * Settings class
 *
 * Control the specific theme settings
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding;

defined( 'ABSPATH' ) || exit;

class Settings extends Lib\Framework
{
	/**
	 * Default theme settings
	 */
	protected array $_defaults = [
		'singular' => [
			'layout' => [
				'default' => 'right-sidebar',
			],
			'width' => [
				'default' => '',
			],
		],
		'archive' => [
			'layout' => [
				'default' => 'right-sidebar'
			],
			'width' => [
				'default' => '',
			],
			'excerpt_length' => 55,
		],
		'components' => [
			'masthead' => [
				'atts' => [],
			],
			'primary' => [
				'atts' => [],
			],
			'sidebar' => [
				'breakpoint' => 'tablet',
				'atts' => [],
			],
			'alt-sidebar' => [
				'breakpoint' => 'tablet',
				'atts' => [],
			],
			'colophon' => [
				'breakpoint' => 'tablet',
				'atts' => [],
			],
			'navpane' => [
				'enabled' => false,
				'breakpoint' => '',
				'atts' => [],
			],
			'site-navigation' => [
				'breakpoint' => 'tablet'
			]
		],
	];
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_filter/
	 */
	public function addFilters() : void
	{

	}

	public function get( string $params = '', $default = false )
	{
		$settings = apply_filters(
			'scaffolding/settings',
			$this->merge( $this->_defaults, get_theme_mod( '_s', [] ) )
		);
		/**
		 * If params are empty, return all
		 */
		if ( empty( $params ) )
		{
			return $settings;
		}

		$params = explode( '/', $params );

		/**
		 * Search for specific value
		 */
		$setting = false;

		$search = $settings;

		foreach ( $params as $param )
		{

			if ( ! isset( $search[$param] ) )
			{
				$setting = false;
				break;
			}

			$setting = $search[$param];

			if ( is_array( $setting ) )
			{
				$search = $setting;
				continue;
			}
		}

		return ! empty( $setting ) ? $setting : $default;
	}

	protected function merge( array $defaults, array $merge ) : array
	{

		foreach ( $merge as $key => $value ) {

			if ( ! isset( $defaults[$key] ) ) {
				$defaults[$key] = $value;
			}

			elseif ( is_array( $value ) ) {

				if ( ! isset( $defaults[$key] ) ) {
					$defaults[$key] = $value;
				}

				elseif ( ! is_array( $defaults[$key] ) ) {
					continue;
				}

				else {
					$defaults[$key] = $this->merge( $defaults[$key], $value );
				}
			}

			elseif ( ! is_array( $defaults[ $key ] ) ) {
				$defaults[$key] = $value;
			}

		}
		return $defaults;
	}
}