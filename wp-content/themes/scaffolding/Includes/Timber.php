<?php
/**
 * Frontend class
 *
 * Control the frontend rendering functions
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding;

// use \Wpcl\Scaffolding\Timber\Author;

defined( 'ABSPATH' ) || exit;

class Timber extends Lib\Framework
{
	protected $timber;
	/**
	 * Collection of nav menu objects
	 *
	 * @var array
	 * @access protected
	 */
	protected array $_menus = [];
	/**
	 * Collection of widget area objects
	 *
	 * @var array
	 * @access protected
	 */
	protected array $_widgets = [];
	/**
	 * Scope (context)
	 *
	 * @var array
	 * @access protected
	 */
	protected array $_scope = [];

	public function __construct()
	{
		$this->timber = new \Timber\Timber();
		// Utilities::log($this->timber::$dirname);
		// $this->timber::$locations = _S_DIR . 'template-parts';
		$this->timber::$dirname = 'template-parts';
		parent::__construct();
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'loop/enter', 'the_post', 0 );
	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addFilters() : void
	{
		Subscriber::addFilter( 'loop/enter', [$this, 'setPost'] );
		Subscriber::addFilter( 'timber/twig', [$this, 'twigFunctions'] );
		Subscriber::addFilter( 'timber_compile_result', 'trim' );
	}
	/**
	 * Add functions to twig
	 *
	 * @param object $twig Twig instance to add function to
	 */
	public function twigFunctions( object $twig ) : object
	{
		$twig->AddFunction( new \Timber\Twig_Function( 'widgets', [$this, 'getWidgets'] ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'menu', [$this, 'navMenu'] ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'parentTemplate', [$this, 'parentTemplatePath'] ) );

		return $twig;
	}
	/**
	 * Get the scope, either from cache or from Timber directly
	 */
	public function scope() : array
	{
		if ( empty( $this->_scope ) )
		{
			$this->_scope = apply_filters( 'scaffolding/scope', \Timber\Timber::context() );
		}

		return apply_filters( 'scaffolding/scope/context',$this->_scope );
	}
	/**
	 * Set specific context for the scope
	 *
	 * Check if the current post in scope is the same as the global post, and
	 * correct it if necessary
	 *
	 */
	public function setPost()
	{
		global $post;

		if ( is_int( $post ) )
		{
			$id = $post;
		}
		elseif ( is_object( $post ) )
		{
			$id = $post->ID;
		}
		else
		{
			$id = get_the_id();
		}

		if ( ! isset( $this->_scope['post'] ) || $this->_scope['post']->ID !== $id )
		{
			$this->_scope['post'] = apply_filters( 'scaffolding/current_post', \Timber\Timber::get_post( $id ) );
		}
	}
	/**
	 * Render a frontend template
	 *
	 * @param  array  $template name of template to render
	 * @param  array  $data     data to merge with $_scope
	 */
	public function render( $template = [], array $data = [] ) : void
	{
		/**
		 * Get scope
		 */
		$_scope = $this->scope();
		/**
		 * Maybe merge with passed in data
		 */
		if ( ! empty( $data ) )
		{
			$_scope = wp_parse_args( $data, $_scope );
		}
		/**
		 * Maybe render template
		 */
		if ( ! empty ( $template ) )
		{
			\Timber\Timber::render( $template, $_scope );
		}
		/**
		 * Render default template
		 */
		else
		{

			\Timber\Timber::render( ['template-parts/index.twig'], $_scope );
		}
	}
	/**
	 * Render a timber string
	 *
	 * @param  string $string   html/twig string to render
	 * @param  array  $data     data to merge with scope (context)
	 */
	public function renderString( string $string = '', array $data = [] ) : void
	{
		/**
		 * Get scope
		 */
		$_scope = $this->scope();
		/**
		 * Maybe merge with passed in data
		 */
		if ( ! empty( $data ) )
		{
			$_scope = wp_parse_args( $data, $_scope );
		}
		/**
		 * Render the string
		 */
		if ( ! empty( $string ) )
		{
			\Timber\Timber::render_string( $string, $_scope );
		}
	}

	public static function getWidgets( string $name )
	{
		return \Timber\Timber::get_widgets( $name );
	}

	public function navMenu( string $name, $args = [] )
	{

		if ( isset( $this->_menus[$name] ) )
		{
			return $this->_menus[$name];
		}

		elseif ( has_nav_menu( $name ) || is_nav_menu( $name ) )
		{
			$this->_menus[$name] = new \Timber\Menu( $name, $args );

			return $this->_menus[$name];
		}
	}

	public function getPosts( $args = [] )
	{
		return new \Timber\PostQuery( $args );
	}

	public function parentTemplatePath( string $path = '' ) : string
	{
		return Utilities::path( 'theme/template-parts/' . $path );
	}
}