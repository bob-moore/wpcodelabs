<?php
/**
 * Layout class
 *
 * Control where/when specific templates are loaded
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding;

defined( 'ABSPATH' ) || exit;

class Layout extends Lib\Framework
{
	/**
	 * The layout to use, similar to page template
	 *
	 * @var string
	 * @access protected
	 */
	protected string $_layout = 'right-sidebar';
	/**
	 * Provide read access to the layout field
	 * @param  string $field Name of the field requested
	 */
	public function __get( string $field ) : ?string
	{
		if ( $field === 'layout' )
		{
			return $this->_layout;
		}
	}
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
		/**
		 * __call Functions
		 */
		// Subscriber::addAction( 'get_header', [ $this, 'header' ] );
		// Subscriber::addAction( 'get_footer', [ $this, 'footer' ] );
		Subscriber::addAction( 'primary/after', [ $this, 'sidebar-primary' ], 8 );
		Subscriber::addAction( 'primary/after', [ $this, 'sidebar-secondary' ], 12 );
		Subscriber::addAction( 'header/after', [ $this, 'page-header' ], 5 );
		Subscriber::addAction( 'entry', [ $this, 'entry' ] );
		Subscriber::addAction( 'content/404', [ $this, '404' ] );
		Subscriber::addAction( 'loop', [ $this, 'loop' ] );
		Subscriber::addAction( 'loop/after', [ $this, 'pagination' ], 20 );
		Subscriber::addAction( 'loop/after', [ $this, 'post-navigation' ], 12 );
		Subscriber::addAction( 'sidebar/primary', [ $this, 'components/sidebar-primary' ] );
		Subscriber::addAction( 'sidebar/secondary', [ $this, 'components/sidebar-secondary' ] );
		Subscriber::addAction( 'masthead/includes', [ $this, 'components/site-branding' ], 5 );
		Subscriber::addAction( 'masthead/includes', [ $this, 'components/site-navigation' ], 10 );
		Subscriber::addAction( 'masthead', [ $this, 'components/masthead' ] );
		Subscriber::addAction( 'colophon/includes', [ $this, 'components/site-info' ], 5 );
		Subscriber::addAction( 'colophon/includes', [ $this, 'components/footer-navigation' ], 10 );
		Subscriber::addAction( 'colophon', [ $this, 'components/colophon' ] );
		Subscriber::addAction( 'entry/content', [ $this, 'entry/content' ] );
		Subscriber::addAction( 'page/after', [ $this, 'offcanvas' ] );
		Subscriber::addAction( 'entry/header/includes', [ $this, 'entry/thumbnail' ], 5 );
		Subscriber::addAction( 'entry/header/includes', [ $this, 'entry/title' ] );
		Subscriber::addAction( 'entry/header/includes', [ $this, 'entry/meta' ], 15 );
		Subscriber::addAction( 'entry/meta', [ $this, 'entry/meta-author' ], 4 );
		Subscriber::addAction( 'entry/meta', [ $this, 'entry/meta-date' ], 6 );
		Subscriber::addAction( 'entry/meta', [ $this, 'entry/meta-categories' ], 8 );
		Subscriber::addAction( 'entry/meta', [ $this, 'entry/meta-comments' ] );
		Subscriber::addAction( 'entry/footer/includes', [ $this, 'entry/info' ] );
		Subscriber::addAction( 'entry/info', [ $this, 'entry/meta-tags' ] );
		Subscriber::addAction( 'entry/header', [ $this, 'entry/header' ] );
		Subscriber::addAction( 'entry/footer', [ $this, 'entry/footer' ] );

		/**
		 * Qualified functions
		 */
		Subscriber::addAction( 'template_redirect', [$this, 'setupLayout'] );
		Subscriber::addAction( 'loop/after', [ $this, 'comments' ], 15 );
		Subscriber::addAction( 'wp', [ $this, 'setupEntry' ], 20 );
		/**
		 * Conditional functions
		 */
		$components = Subscriber::getInstance( 'Settings' )->get( 'components' );

		if ( $components['navpane']['enabled'] === true )
		{
			Subscriber::addAction( 'navpane', [ $this, 'components/navpane' ] );
		}
	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_filter/
	 */
	public function addFilters() : void {
		Subscriber::addFilter( 'the_password_form', [$this, 'postPasswordForm'], 10, 2 );
		Subscriber::addFilter( 'body_class', [$this, 'bodyClass'] );
		Subscriber::addFilter( 'post_class', [$this, 'postClass'] );
		Subscriber::addFilter( 'scaffolding/markup/atts/main', [$this, 'mainBreakpoint'], 10, 2 );
		Subscriber::addFilter( 'scaffolding/markup/atts/primary-menu-toggle', [$this, 'toggleBreakpoint'], 10, 2 );
	}

	public function setupEntry() {

		if ( get_post_type() !== 'post' )
		{
			Subscriber::removeAction( 'entry/header/includes', [ $this, 'entry/meta' ], 15 );
			Subscriber::removeAction( 'entry/footer/includes', [ $this, 'entry/info' ] );
		}
		if ( ! has_action( 'entry/header/includes' ) )
		{
			Subscriber::removeAction( 'entry/header', [ $this, 'entry/header' ] );
		}
		if ( ! has_action( 'entry/footer/includes' ) )
		{
			Subscriber::removeAction( 'entry/footer', [ $this, 'entry/footer' ] );
		}
		if ( ! has_action( 'entry/info' ) )
		{
			Subscriber::removeAction( 'entry/footer/includes', [ $this, 'entry/info' ] );
		}
		if ( ! has_action( 'entry/meta' ) )
		{
			Subscriber::removeAction( 'entry/header/includes', [ $this, 'entry/meta' ], 15 );
		}
	}
	/**
	 * Breakpoint atts for site <main>
	 *
	 * @param  array  $atts Existing markup attributes for the main element
	 * @param  array  $ref Reference array
	 */
	public function mainBreakpoint(  array $atts, array $ref  ) : array
	{
		if ( in_array( $this->_layout, ['right-sidebar', 'left-sidebar', 'duel-sidebar'] ) )
		{
			$sidebar = Subscriber::getInstance( 'Settings' )->get( 'components/sidebar' );

			$atts['data-sidebar-breakpoint'] = $sidebar['breakpoint'];

		}
		if ( $this->_layout === 'duel-sidebar' )
		{
			$alt = Subscriber::getInstance( 'Settings' )->get( 'components/alt-sidebar', [] );

			$atts['data-alt-sidebar-breakpoint'] = $alt['breakpoint'];
		}
		return $atts;
	}
	/**
	 * Breakpoint atts for site navigation / navpane toggle
	 * @param  array  $atts Existing markup attributes for the toggle element
	 * @param  array  $ref Reference array
	 */
	public function toggleBreakpoint( array $atts, array $ref ) : array
	{
		$navigation = Subscriber::getInstance( 'Settings' )->get( "components/site-navigation", ['breakpoint' => ''] );
		$navpane = Subscriber::getInstance( 'Settings' )->get( "components/navpane", ['breakpoint' => ''] );

		if ( has_action( 'navpane' ) )
		{
			if ( $navpane['breakpoint'] === 'all' )
			{
				$atts['data-breakpoint'] = '';
			}
			elseif ( empty( $navpane['breakpoint'] ) )
			{
				$atts['data-breakpoint'] = $navigation['breakpoint'];
			}
			else {
				$atts['data-breakpoint'] = $navpane['breakpoint'];
			}

			$atts['data-triggers'] = '#navpane';
			$atts['aria-controls'] = 'navpane';
		}
		else
		{
			if ( empty( $navigation['breakpoint'] ) ) {
				$atts['class'] .= ' invisible';
			}
			$atts['data-breakpoint'] = $navigation['breakpoint'];
		}

		return $atts;
	}
	/**
	 * Default callback to run functions not specifically called out
	 */
	public function __call( string $call, $args ) : void
	{
		Subscriber::getInstance( 'FrontEnd' )->templatePart( $call );
	}
	/**
	 * Conditionally include comments template
	 */
	public function comments() : void
	{
		if ( post_password_required() || ! is_singular() || ! comments_open() )
		{
			return;
		}
		Subscriber::getInstance( 'FrontEnd' )->templatePart( 'comments' );
	}
	/**
	 * Return our post password form in place of the default
	 *
	 * @param  string  $output : the original HTMl output
	 * @param  WP_Post $post the WP post object
	 */
	function postPasswordForm( string $output, WP_Post $post ) : string
	{
		ob_start();
		Subscriber::getInstance( 'FrontEnd' )->templatePart( 'components/password-form' );
		return ob_get_clean();
	}
	/**
	 * Setup the page layout
	 */
	public function setupLayout() : void
	{
		$layout = apply_filters( 'scaffolding/layout', '' );

		$this->_layout = empty( $layout ) ? 'right-sidebar' : $layout;

		if ( $layout === 'full-width' )
		{
			remove_all_actions( 'sidebar/primary' );
			remove_all_actions( 'sidebar/secondary' );
		}
		if ( $layout !== 'duel-sidebar' )
		{
			remove_all_actions( 'sidebar/secondary' );
		}
	}
	/**
	 * Set the body class
	 *
	 * @param  array  $classes Collection of classes for the body element
	 * @see https://developer.wordpress.org/reference/functions/body_class/
	 */
	public function bodyClass( array $classes ) : array
	{
		$classes[] = $this->_layout . '-layout';
		return $classes;
	}
	/**
	 * Set a single entry class
	 *
	 * @param  array $classes Collection of post classes for the article element
	 * @see https://developer.wordpress.org/reference/functions/post_class/
	 */
	public function postClass( array $classes ) : array
	{
		return array_merge(
			[
				'entry'
			],
			$classes
		);
	}
}