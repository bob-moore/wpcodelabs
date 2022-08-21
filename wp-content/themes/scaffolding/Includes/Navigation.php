<?php
/**
 * Image control class
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding;

defined( 'ABSPATH' ) || exit;

class Navigation extends Lib\Framework
{
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'after_setup_theme', [$this, 'registerNavMenus'] );
	}
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
		Subscriber::addFilter( 'scaffolding/atts/menu-item-link', [$this, 'menuItemAtts'], 10, 2 );
		Subscriber::addFilter( 'widget_nav_menu_args', [$this, 'menuWidgetArgs'], 10, 4 );
		Subscriber::addFilter( 'pre_wp_nav_menu', [$this, 'menuWidget'], 10, 2 );
	}
	/**
	 * Register Nav Menus
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_nav_menus/
	 */
	public function registerNavMenus() : void
	{
		register_nav_menus( [
			'primary' => esc_html__( 'Primary', 'scaffolding' ),
			'secondary' => esc_html__( 'Secondary', 'scaffolding' ),
			'offcanvas' => esc_html__( 'Off Canvas', 'scaffolding' ),
		] );
	}
	public function menuItemAtts( $settings, $ref )
	{
		$settings['title'] = ! empty( $ref['item']->attr_title ) ? $ref['item']->attr_title : $ref['item']->name;

		if ( ! empty( $ref['item']->target ) )
		{
			$settings['target'] = $ref['item']->target;

			if ( $ref['item']->target === '_blank' )
			{
				$settings['rel'] = 'noopener noreferrer';
			}
		}

		if ( ! empty( $ref['item']->xfn ) )
		{
			$settings['rel'] = isset( $settings['rel'] ) ? $settings['rel'] : [];

			$settings['rel'] .= ' ' . $ref['item']->xfn;
		}
		return $settings;
	}
	/**
	 * Adds an argument so we know what menus are being called from a widget
	 */
	/**
	 * Adds an argument so we know what menus are being called from a widget
	 *
	 * @param  array $nav_menu_args An array of arguments passed to wp_nav_menu() to retrieve a navigation menu.
	 * @param  \WP_Term $nav_menu Nav menu object for the current menu.
	 * @param  array $args Display arguments for the current widget.
	 * @param  array $instance Array of settings for the current widget.
	 */
	public function menuWidgetArgs( array $nav_menu_args, \WP_Term $nav_menu, array $args, array $instance ) : array
	{
		$nav_menu_args['generator'] = 'theme';
		return $nav_menu_args;
	}

	public function menuWidget( $output, $args )
	{
		$args = (array) $args;

		if ( isset( $args['generator'] ) && $args['generator'] === 'theme' ) {

			$menu = Subscriber::getInstance( 'Timber' )->navMenu( $args['menu']->slug );

			if ( $menu )
			{
				ob_start();

				Subscriber::getInstance( 'FrontEnd' )->templatePart( 'components/menu', '', [ 'menu' => $menu ] );

				$output = ob_get_clean();
			}
		}

		return $output;
	}
}