<?php
/**
 * Base Theme Class
 *
 * Register all supporting and extension classes, and add base theme supports
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */

namespace Wpcl\Scaffolding;

class Theme extends Lib\Framework
{
	/**
	 * Construct an instance of this class
	 *
	 * Create necessary definitions & register additional classes
	 * @return $this
	 */
	public function __construct()
	{
		if ( Subscriber::getInstance( $this ) === $this )
		{
			define( '_S_DIR', Utilities::path() );

			define( '_S_URL', Utilities::url() );

			define( '_S_VERSION', Utilities::version() );

			define( '_S_ASSET_PREFIX', Utilities::isDev() ? '.min' : '' );

			$this->registerCoreClasses();

			$this->registerExtensionsClasses();
		}

		return parent::__construct();
	}
	/**
	 * Static init function
	 */
	public static function init()
	{
		return new self();
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
		Subscriber::addAction( 'wp', [$this, 'registerViews'] );
		Subscriber::addAction( 'after_setup_theme', [$this, 'themeSupport'] );
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
		// Subscriber::addAction( 'template_redirect', [$this, 'setupLayout'] );
		// Subscriber::addFilter( 'template_include', [$this, 'setLayout'], 99 );
	}
	/**
	 * Register core theme classes
	 */
	protected function registerCoreClasses() : void
	{
		$classes = [
			__NAMESPACE__ . '\\FrontEnd',
			__NAMESPACE__ . '\\Admin',
			__NAMESPACE__ . '\\Layout',
			__NAMESPACE__ . '\\Wp',
			__NAMESPACE__ . '\\Markup',
			__NAMESPACE__ . '\\Customizer',
			__NAMESPACE__ . '\\Navigation',
			__NAMESPACE__ . '\\Widgets',
			__NAMESPACE__ . '\\Timber',

		];

		foreach ( $classes as $class )
		{
			if ( class_exists( $class ) ) {
				new $class;
			}
		}
	}
	/**
	 * Register extension (plugin) support
	 */
	protected function registerExtensionsClasses() : void
	{
		$classes = [
			__NAMESPACE__ . '\\Extensions\\Elementor',
			__NAMESPACE__ . '\\Extensions\\ElementorPro',
			// __NAMESPACE__ . '\\Extensions\\Jetpack',
			// __NAMESPACE__ . '\\Extensions\\Woocommerce',
			__NAMESPACE__ . '\\Extensions\\DevkitCustomLayouts',
		];

		foreach ( $classes as $class )
		{
			if ( class_exists( $class ) ) {
				new $class;
			}
		}
	}
	/**
	 * Register theme views
	 *
	 * Views are found under the Vviews folder. Must be ran at 'wp' action
	 * when the current context is available
	 *
	 */
	public function registerViews() : void
	{
		$views = array_reverse( Subscriber::getInstance( 'FrontEnd' )->views() );

		foreach ( $views as $view ) {
			/**
			 * Fix 404 class conflict
			 */
			if ( $view === '404' )
			{
				$view = 'Error404';
			}
			$classname = __NAMESPACE__ . '\\Views\\' . ucfirst( $view );
			/**
			 * Instantiate the first (most specific) view found
			 */
			if ( class_exists( $classname ) )
			{
				new $classname();
				break;
			}
		}
	}
	/**
	 * Add theme support for all supported featured
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_theme_support/
	 */
	public function themeSupport() : void
	{
		add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'video', 'chat', 'gallery', 'image', 'quote', 'status', 'link' ) );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'custom-background' );
		add_theme_support( 'custom-logo', array( 'flex-width'  => true, 'flex-height' => true, 'height' => 100, 'width' => 400 ) );
		add_theme_support( 'align-wide' );
		add_theme_support( "responsive-embeds" );
	}
	/**
	 * Set the template to use (found in theme/templates)
	 *
	 * @param string $template [description]
	 * @see https://developer.wordpress.org/reference/hooks/template_include/
	 */
	public function setLayout( string $template ) : string
	{
		/**
		 * If user has specified a template to use, we can bail
		 */
		if ( is_page_template() != false || basename( $template ) !== 'index.php' )
		{
			return $template;
		}
		/**
		 * Let classes / child themes filter the template
		 */
		$layout = apply_filters( 'scaffolding/template', '' );
		/**
		 * Set a default, just in case
		 */
		if ( empty( $layout ) )
		{
			$layout = apply_filters( 'scaffolding/template/default', 'right-sidebar' );
		}
		/**
		 * Get the file and return
		 */
		$file = locate_template( "templates/{$layout}.php", false, false );

		return ! empty( $file ) ? $file : $template;
	}



	public function setContentWidth()
	{

	}
}