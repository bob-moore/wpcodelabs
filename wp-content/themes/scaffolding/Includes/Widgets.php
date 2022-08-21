<?php
/**
 * Register and control widget areas
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */

namespace Wpcl\Scaffolding;

class Widgets extends Lib\Framework
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
		Subscriber::addAction( 'widgets_init', [$this, 'registerSidebars'] );
		Subscriber::addAction( 'sidebar/primary/includes', [$this, 'widgets'] );
		Subscriber::addAction( 'sidebar/secondary/includes', [$this, 'widgets'] );
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
	{}
	/**
	 * Register Sidebars
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_sidebar/
	 */
	public function registerSidebars()
	{
		register_sidebar( [
			'name' => esc_html__( 'Sidebar', 'scaffolding' ),
			'id' => 'sidebar-primary',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h4 class="widget-title">',
			'after_title' => '</h4>',
		] );
		register_sidebar( [
			'name' => esc_html__( 'Alternate Sidebar', 'scaffolding' ),
			'id' => 'sidebar-secondary',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget' => '</section>',
			'before_title' => '<h4 class="widget-title">',
			'after_title' => '</h4>',
		] );
	}
	public function widgets( string $sidebar = '' ) : void
	{
		$action = current_action();

		if ( $action === 'sidebar/primary/includes' )
		{
			$sidebar = 'sidebar-primary';
		}
		elseif ( $action === 'sidebar/secondary/includes' )
		{
			$sidebar = 'sidebar-secondary';
		}

		if ( ! empty( $sidebar ) && is_active_sidebar( $sidebar ) )
		{
			do_action( "sidebar/{$sidebar}/widgets/before" );

			dynamic_sidebar( $sidebar );

			do_action( "sidebar/{$sidebar}/widgets/after" );
		}
	}
}