<?php
/**
 * Frontend controller class
 *
 * Organize and display template parts
 *
 * @class FrontEnd
 * @package CustomLayouts\Classes
 */
namespace Devkit\CustomLayouts;

use \Timber\Timber;

defined( 'ABSPATH' ) || exit;

class FrontEnd extends Framework {
	protected $timber;
	/**
	 * Collection of current views
	 *
	 * @var array
	 * @access protected
	 */
	protected $_views = [];
	/**
	 * Whether or not we are currently doing `the_content` hook
	 *
	 * @var bool
	 * @access protected
	 */
	protected $_in_the_content = false;
	public function __construct()
	{
		$this->timber = new Timber();

		parent::__construct();
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 * @see  https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function addActions()
	{
		Subscriber::addAction( 'after_setup_theme', [$this, 'registerAssets'] );
		Subscriber::addAction( 'wp_print_scripts', [$this, 'outputActiveCSS'] );
		Subscriber::addAction( 'wp_footer', [$this, 'outputActiveJS'] );
		Subscriber::addAction( 'devkit/custom_layouts/before_render', [$this, 'renderContainer'], 1 );
		Subscriber::addAction( 'devkit/custom_layouts/after_render', [$this, 'renderContainer'], 20 );
	}
	/**
	 * Register shortcodes
	 *
	 * Uses the subscriber class to ensure only shortcodes of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addShortcodes()
	{
		add_shortcode( 'devkit_custom_layout', [$this, 'shortcode'] );
	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addFilters()
	{
		Subscriber::addFilter( 'devkit/custom_layouts/the_content', 'do_blocks' );
		Subscriber::addFilter( 'devkit/custom_layouts/the_content', 'wptexturize' );
		Subscriber::addFilter( 'devkit/custom_layouts/the_content', 'convert_smilies' );
		Subscriber::addFilter( 'devkit/custom_layouts/the_content', 'convert_chars' );
		Subscriber::addFilter( 'devkit/custom_layouts/the_content', 'shortcode_unautop' );
		Subscriber::addFilter( 'devkit/custom_layouts/the_content', 'do_shortcode' );
		Subscriber::addFilter( 'devkit/custom_layouts/the_content', 'wp_make_content_images_responsive' );
		Subscriber::addFilter( 'devkit/custom_layouts/the_content', 'prepend_attachment' );
		Subscriber::addFilter( 'devkit/custom_layouts/template_parts/args', [$this, 'filterTemplateArgs'], 1 );
		Subscriber::addFilter( 'devkit/custom_layouts/template_scope', [$this, 'setPost'], 8 );
		Subscriber::addFilter( 'the_content', [$this, 'toggleContentFilterOn'], 0 );
		Subscriber::addFilter( 'the_content', [$this, 'toggleContentFilterOff'], PHP_INT_MAX );
		Subscriber::addFilter( 'timber/locations', [$this, 'timberLocations'] );
	}

	public function timberLocations( array $locations ) : array
	{
		return wp_parse_args(
			[
				DEVKIT_CUSTOMLAYOUTS_PATH . 'template-parts'
			]
		, $locations );
	}
	/**
	 * Expose in the content flag
	 *
	 * `the_content` causes some bizzare behaviar, so we need to handle things differently
	 * in some cases. It also can be used as an escape condition for loops, in case
	 * a layout trys to render itself over and over again
	 *
	 * @return bool flag of whether we are in 'the_content'
	 */
	public function inTheContent()
	{
		return $this->_in_the_content;
	}
	/**
	 * Toogle in_the_content flag ON
	 *
	 * @param string $content Post content
	 * @return string $content
	 */
	public function toggleContentFilterOn( $content )
	{
		$this->_in_the_content = true;
		return $content;
	}
	/**
	 * Toogle in_the_content flag OFF
	 *
	 * @param string $content Post content
	 * @return string $content
	 */
	public function toggleContentFilterOff( $content )
	{
		$this->_in_the_content = false;
		return $content;
	}
	/**
	 * Check if scripts / styles need enqueed
	 *
	 * @param object $layout TemplatePart object
	 * @return void
	 */
	public function enqueueScripts( )
	{
		if ( ! wp_script_is( 'cl-frontend', 'enqueued' ) ) {
			$this->enqueueScripts();
			$this->enqueueStyles();
		}
	}
	/**
	 * Register the javascript & CSS
	 *
	 * @return void
	 */
	public function registerAssets() {
		wp_enqueue_script( 'cl-frontend', DEVKIT_CUSTOMLAYOUTS_URL . '/assets/js/frontend' . DEVKIT_CUSTOMLAYOUTS_ASSET_PREFIX . '.js', ['jquery'], DEVKIT_CUSTOMLAYOUTS_VERSION, true );
		wp_enqueue_style( 'cl-frontend', DEVKIT_CUSTOMLAYOUTS_URL . '/assets/css/frontend' . DEVKIT_CUSTOMLAYOUTS_ASSET_PREFIX . '.css', [], DEVKIT_CUSTOMLAYOUTS_VERSION, 'all' );
	}
	/**
	 * Global callback to run wp functions
	 *
	 * Allows us to use undefined functions on hooks, instead of closures. This
	 * allows us to also do `remove_action`, since the name gets defined but the function
	 * doesn't.
	 *
	 * @param string $function Name of the called function
	 * @param array $args function arguments passed
	 * @return void/string Return string if on `the_content` hook
	 */
	public function __call( $function, $args )
	{
		/**
		 * Get and check the name of what was called
		 *
		 * If not calling a specific render function, along with an ID, bail
		 * @var array
		 */
		$caller = self::splitName( $function );

		if ( $caller[0] !== 'devkit/custom_layouts/render' || ! is_numeric( $caller[1] ) )
		{
			return false;
		}

		$current_action = current_action();

		$layout = Subscriber::getInstance( 'Controller' )->getQueued( $current_action, $caller[1] );

		if ( $current_action === 'the_content' )
		{
			ob_start();

			$this->render( $layout );

			$output = ob_get_clean();

			if ( intval( $layout->priority ) < 5 )
			{
				$final = $output . $args[0];
			}

			else
			{
				$final = $args[0] . $output;
			}

			return $final;

		}
		else
		{
			$this->render( $layout );
		}
	}
	/**
	 * Render the layout
	 *
	 * @param object $layout TemplatePart object
	 * @access protected
	 * @return void
	 */
	protected function render( $layout )
	{
		if ( get_the_id() === $layout->id )
		{
			return false;
		}

		do_action( 'devkit/custom_layouts/before_render', $layout );

		switch ( $layout->type )
		{
			case 'code' :
				$this->renderCode( $layout );
				break;
			case 'partial' :
				$this->renderPartial( $layout );
				break;
			default :
				$this->renderEditor( $layout );
				break;
		}

		do_action( 'devkit/custom_layouts/after_render', $layout );
	}
	/**
	 * Render the layout container
	 *
	 * @param object $layout TemplatePart object
	 * @access protected
	 * @return void
	 */
	public function renderContainer( $layout )
	{
		if ( empty( $layout->container ) || $layout->type === 'code' )
		{
			return;
		}

		if ( current_action() === 'devkit/custom_layouts/before_render' )
		{
			printf( '<%s id="custom-layout-%s" class="%s">',
				$layout->container,
				$layout->id,
				trim( 'custom-layout ' . trim( $layout->classes ) )
			);
		}
		else
		{
			echo "</{$layout->container}>";
		}
	}
	/**
	 * Render the shortcode
	 *
	 * @param array $atts Shortcode attributes : ID, USE_CONDITIONS
	 * @return string content of render function
	 */
	public function shortcode( $atts = [] )
	{
		$atts = shortcode_atts( [ 'id' => '', 'use_conditions' => false ], $atts, 'template_part' );

		if ( empty( $atts['id'] ) ) {
			return;
		}

		$layout = new PostTypes\CustomLayout( $atts['id'] );

		if ( ! $layout ) {
			return;
		}

		ob_start();

		if ( $atts['use_conditions'] ) {
			if ( Subscriber::getInstance( 'Controller' )->isValid( $layout ) ) {
				$this->render( $layout );
			}
		} else {
			$this->render( $layout );
		}

		return ob_get_clean();
	}
	/**
	 * Render editor
	 *
	 * Other plugins, like beaver builder or elementor can set their own render
	 * function using the "custom_layout/before_render" hook. Else render the
	 * default WP editor
	 *
	 * @param object $layout TemplatePart object
	 * @access protected
	 * @return void
	 */
	protected function renderEditor( $layout )
	{
		/**
		 * Allow other builders (beaver builder, elementor, etc) to short circuit with
		 * their own content
		 */
		$content = apply_filters( 'devkit/custom_layouts/content', '', $layout );



		if ( $content )
		{
			echo $content;
		}
		else
		{
			echo apply_filters( 'devkit/custom_layouts/the_content', get_the_content( null, true, $layout->id ) );
		}
	}
	/**
	 * Render custom code
	 *
	 * @param object $layout TemplatePart object
	 * @access protected
	 * @return void
	 */
	protected function renderCode( $layout ) {
		$this->renderString( $layout->code );
	}
	/**
	 * Render a php/twig template
	 *
	 * @param object $layout TemplatePart object
	 * @access protected
	 * @return void
	 */
	protected function renderPartial( $layout, $context = [] ) {

		$template = is_a( $layout, '\\Mwf\\CustomLayouts\\TemplatePart' ) ? $layout->partial : $layout;

		$template = $this->getTemplatePart( $template );

		if ( empty( $template ) ) {
			return;
		}

		if ( in_array( pathinfo( $template, PATHINFO_EXTENSION ), [ 'twig', 'html' ] ) )
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
				$_scope = ! empty( $_scope ) ? $_scope : $this->timber::context();
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
			$this->timber::render( [$template], $_scope );
		}
		elseif ( in_array( pathinfo( $template, PATHINFO_EXTENSION ), [ 'php' ] ) )
		{
			require $template;
		}
	}
	/**
	 * Render a string using timber
	 *
	 * @param string $string HTML/TWIG string to be rendered by timber
	 * @return void
	 */
    public function renderString( $string ) {
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
        	$_scope = ! empty( $_scope ) ? $_scope : $this->timber::context();
        	/**
        	 * Allow filtering
        	 */
        	$_scope = apply_filters( 'devkit/custom_layouts/template_scope', $_scope );
        	/**
        	 * Set cache
        	 */
        	wp_cache_set( 'template_scope', $_scope, 'devkit/custom_layouts', 60 * 60 );
        }
        else
        {
        	$_scope = apply_filters( 'devkit/custom_layouts/template_scope', $_scope );
        }
        /**
         * Send flyin'
         */
        $this->timber::render_string($string, $_scope);
    }
    public function renderBlock( $fields, $attributes, $inner_blocks, $template )
    {
    	$context = $fields;
    	$context['attributes'] = $attributes;
    	$context['inner_blocks'] = $inner_blocks;
    	$this->renderPartial( $template, $context );
    }
    /**
     * Generate paths to search for template files
     *
     * @param  string $slug Template slug
     * @param  string $name Template Name
     * @param  array $views All views we are currently on
     * @param  string $post_type post type of current screen
     * @access protected
     * @return array Array of formatted template paths to search
     */
	protected function generatePaths( $slug, $name, $views, $post_type ) {
		$paths = [];

		$patterns = [
			[
				'%s/%s',
				'%s-%s',
			],
			[
				'%s/%s-%s',
				'%s-%s-%s',
			],
			[
				'%s/%s/%s-%s',
				'%s/%s-%s-%s',
				'%s-%s/%s-%s',
				'%s-%s-%s-%s',
			]

		];

		$temp = [];

		if ( ! empty( $name ) ) {

			foreach ( $views as $view ) {
				foreach ( $patterns[2] as $pattern ) {
					$paths[0][] = sprintf( $pattern, $slug, $name, $view, $post_type );
					$paths[0][] = sprintf( $pattern, $slug, $name, $post_type, $view );
				}
				foreach ( $patterns[1] as $pattern ) {
					$paths[1][] = sprintf( $pattern, $slug, $name, $view );
					$paths[1][] = sprintf( $pattern, $slug, $name, $post_type );
				}
			}
			foreach ( $patterns[0] as $pattern ) {
				$paths[2][] = sprintf( $pattern, $slug, $name );
			}

		} else {
			foreach ( $views as $view ) {
				foreach ( $patterns[1] as $pattern ) {

					$paths[0][] = sprintf( $pattern, $slug, $view, $post_type );
					$paths[0][] = sprintf( $pattern, $slug, $post_type, $view );

				}

				foreach ( $patterns[0] as $pattern ) {

					$paths[1][] = sprintf( $pattern, $slug, $view );
					$paths[1][] = sprintf( $pattern, $slug, $post_type );

				}
			}

			$paths[2][] = $slug;
		}

		$paths = array_unique( array_reduce( $paths, 'array_merge', [] ) );

		return $paths;
	}
	/**
	 * Filter the template paths
	 *
	 * @param array $args Slug, Name, and Force from `getTemplatePart`
	 * @access protected
	 * @return array Maybe modified arguments
	 */
	public function filterTemplateArgs( $args )
	{
		/**
		 * Check if it's a core template
		 */

		$core = strpos( $args['slug'], 'core/');

		if ( $core === false || $core > 0 ) {
			return $args;
		}

		$parts = explode('/', $args['slug']);

		$args['slug'] = $parts[1];

		if ( isset( $parts[2] ) ) {
			$args['name'] = $parts[2];
		}
		return $args;
	}
	/**
	 * Wrapper for get_template_part
	 * Expand get template part to incude post types and views, and to search the
	 * plugin. Searches theme first, and then plugin using a set of paths
	 *
	 * @param  string  $slug  Template slug
	 * @param  string  $name  Template name
	 * @param  boolean $force Whether to force NOT to search paths, and just use provided
	 * @return string Template path for inclusion
	 */
	public function getTemplatePart( $slug = '', $name = '', $force = false )
	{

		if( empty( $slug ) ) {
			return;
		}

		$args = apply_filters( 'devkit/custom_layouts/template_parts/args', [
			'slug' => $slug,
			'name' => $name,
			'force' => $force
		] );

		$slug = apply_filters( "custom_layouts/template/{$args['slug']}", $args['slug']);

		$name = apply_filters( "custom_layouts/template/{$args['slug']}/name", $args['name'] );

		$views = $this->getViews();

		$posttype = get_post_type();

		$cache_key = md5( 'template' . $slug . $name . join( '_', $views ) . $posttype . intval( $force ) );

		// $cache = wp_cache_get( $cache_key, 'custom_layout_templates' );

		// if ( $cache ) {
		// 	return $cache;
		// }
		/**
		 * Generate paths to search
		 */
		if ( $args['force'] ) {
			$paths = [ $slug ];
		} else {
			$paths = apply_filters( 'devkit/custom_layouts/templates/paths', $this->generatePaths(
				$slug,
				$name,
				$views,
				$posttype
			) );
		}
		/**
		 * Allow child themes to specify search directory
		 */
		$directory = apply_filters( 'devkit/custom_layouts/template/directory', 'template-parts' );

		$template = false;
		/**
		 * Loop through and look for first template
		 *
		 * Templates are most specific => least specific
		 */
		foreach ( $paths as $path ) {

			$template = $this->locateTemplate( $directory, $path );

			if ( $template ) {
				break;
			}
		}
		/**
		 * Look in a different directory, maybe
		 */
		if ( empty( $template) && $directory !== 'template-parts' ) {

			foreach ( $paths as $path ) {

				$template = $this->locateTemplate( 'template-parts', $path );

				if ( $template ) {
					break;
				}
			}
		}
		/**
		 * Finally, load plugin version
		 */
		if ( empty ( $template ) ) {
			foreach ( $paths as $path ) {
				if ( file_exists( DEVKIT_CUSTOMLAYOUTS_PATH .'template-parts/' . $path . '.twig') ) {
					$template = DEVKIT_CUSTOMLAYOUTS_PATH .'template-parts/' . $path . '.twig';
					break;
				}
			}
		}

		wp_cache_set( $cache_key, $template, 'custom_layout_templates' );

		return $template;
	}
	/**
	 * Locate a template, either in theme or plugin
	 *
	 * @param string $base Template directory name
	 * @param string $path Specific template name
	 * @return string Located template, or false
	 */
	public function locateTemplate( $base, $path )
	{
		/**
		 * First try twig files
		 */
		$template = locate_template( "{$base}/{$path}.twig", false, false );

		if ( $template ) {
			return $template;
		}
		/**
		 * Then HTML
		 */
		$template = locate_template( "{$base}/{$path}.html", false, false );

		if ( $template ) {
			return $template;
		}
		/**
		 * Then PHP
		 */
		$template = locate_template( "{$base}/{$path}.php", false, false );

		return $template;
	}
	/**
	 * Get the views we are currently on
	 *
	 * @return array All current views
	 */
	public function getViews()
	{
		if ( ! empty( $this->_views ) )
		{
			return $this->_views;
		}
		if ( is_singular() )
		{
			if ( is_front_page() )
			{
				$this->_views[] = 'frontpage';
			}
			$this->_views[] = 'single';
		}
		else if ( is_home() )
		{
			$this->_views = [ 'blog', 'archive' ];
		}
		else if ( is_search() )
		{
			$this->_views = [ 'search', 'archive' ];
		}
		else if ( is_archive() )
		{
			if ( is_category() )
			{
				$this->_views[] = 'archive/category';
			}
			else if ( is_tag() )
			{
				$this->_views[] = 'archive/tag';
			}
			else if ( is_author() )
			{
				$this->_views[] = 'archive/author';
			}
			else if ( is_date() )
			{
				$this->_views[] = 'archive/date';
			}
			else if ( is_post_type_archive() )
			{
				$this->_views[] = 'archive/posttype';
			}
			else if ( is_tax() )
			{
				$this->_views[] = 'archive/tax';
			}
			$this->_views[] = 'archive';
		}

		else if ( is_404() )
		{
			$this->_views[] = 'error404';
		}

		$this->_views = apply_filters( 'devkit/custom_layouts/views', $this->_views );

		return $this->_views;
	}
	/**
	 * Set the author in the timber context
	 *
	 * @param object/array $_scope The scope/context from Timber::get_context
	 * @return array Timber scope/context
	 */
	public function setPost( $_scope ) {

		global $post;

		if ( $post )
		{
			$post_id = is_object( $post ) ? $post->ID : $post;
		}
		elseif ( isset( $_GET['post'] ) )
		{
			$post_id = $_GET['post'];
		}
		else
		{
			$post_id = get_the_id();
		}
		if ( ! empty( $post_id ) ) {
			$_scope['post'] = Timber::get_post( $post_id );
			$_scope['post']->author = new \Devkit\CustomLayouts\Timber\Author( get_the_author_meta( 'ID', $_scope['post']->post_author ) );
		}

		return $_scope;
	}

	public function outputActiveCSS() : void
	{
		$css = Subscriber::getInstance( 'Controller' )->active_css;

		if ( ! empty( $css ) )
		{
			printf( '<style id="dk-custom-layout-active-css">%s</style>', $css );
		}
	}

	public function outputActiveJS() : void
	{
		$js = Subscriber::getInstance( 'Controller' )->active_js;

		if ( ! empty( $js ) )
		{
			printf( '<script id="dk-custom-layout-active-js">%s</script>', $js );
		}
	}
}