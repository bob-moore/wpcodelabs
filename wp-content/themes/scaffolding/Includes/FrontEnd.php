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

defined( 'ABSPATH' ) || exit;

class FrontEnd extends Lib\Framework
{
	/**
	 * Collection of current views
	 *
	 * @var array
	 * @access protected
	 */
	protected array $_views = [];
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
		Subscriber::addAction( 'wp_enqueue_scripts', [$this, 'enqueueAssets'] );
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
		Subscriber::addFilter( 'timber/twig', [$this, 'twigFunctions'] );
		Subscriber::addFilter( 'scaffolding/current_post', [$this, 'postAttributes'] );
	}
	/**
	 * Add functions to twig
	 *
	 * @param object $twig Twig instance to add function to
	 */
	public function twigFunctions( object $twig ) : object
	{
		$twig->AddFunction( new \Timber\Twig_Function( 'templatePart', [$this, 'templatePart'] ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'template_part', [$this, 'templatePart'] ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'include', [$this, 'templatePart'] ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'log', ['\\Wpcl\\Scaffolding\\Utilities', 'log'] ) );
		$twig->AddFunction( new \Timber\Twig_Function( 'parentPath', ['\\Wpcl\\Scaffolding\\Utilities', 'path'] ) );
		return $twig;
	}
	/**
	 * Enqueue scripts and styles
	 */
	public function enqueueAssets() : void
	{
		/**
		 * Scripts
		 */
		wp_enqueue_script( '_s_scripts', _S_URL . 'theme/assets/js/public' . _S_ASSET_PREFIX . '.js', ['jquery'], _S_VERSION, true );

		if ( ( ! is_admin() ) && is_singular() && comments_open() && get_option('thread_comments') )
		{
			wp_enqueue_script( 'comment-reply' );
		}
		/**
		 * Styles
		 */
		wp_enqueue_style( '_s_icons', _S_URL . 'theme/assets/css/icons' . _S_ASSET_PREFIX . '.css', [], _S_VERSION, 'all' );
		wp_enqueue_style( '_s_styles', _S_URL . 'theme/assets/css/public' . _S_ASSET_PREFIX . '.css', [ '_s_icons' ], _S_VERSION, 'all' );
	}
	/**
	 * Get Current set of frontend views
	 *
	 * @return array - List of current views, from least to most specific
	 */
	public function views() : array
	{
		/**
		 * If we've already processed the current view once, we can bail
		 */
		if ( ! empty( $this->_views ) )
		{
			return $this->_views;
		}
		/**
		 * Process current view
		 */
		else if ( is_singular() )
		{
			$this->_views[] = 'single';

			if ( is_front_page() )
			{
				$this->_views[] = 'frontpage';
			}
		}
		else if ( is_home() )
		{
			$this->_views = [ 'archive', 'blog' ];
		}
		else if ( is_search() )
		{
			$this->_views = [ 'archive', 'search' ];
		}
		else if ( is_archive() )
		{
			$this->_views[] = 'archive';

			if ( is_post_type_archive() )
			{
				$this->_views[] = 'archive-posttype';
			}
			else if ( is_category() )
			{
				$this->_views[] = 'archive-category';
			}
			else if ( is_tag() )
			{
				$this->_views[] = 'archive-tag';
			}
			else if ( is_author() )
			{
				$this->_views[] = 'archive-author';
			}
			else if ( is_date() )
			{
				$this->_views[] = 'archive-date';
			}
			else if ( is_tax() )
			{
				$this->_views[] = 'archive-tax';
			}
		}
		else if ( is_404() )
		{
			$this->_views[] = '404';
		}

		$this->_views = apply_filters( 'scaffolding/views', $this->_views );

		return $this->_views;
	}
	public function postAttributes( object $post ) : object
	{
		/**
		 * Maybe setup default featured image
		 */
		if ( empty( $post->thumbnail )  )
		{
			$default = Subscriber::getInstance( 'Settings' )->get( 'post-thumbnail' );

			if ( ! empty( $default ) )
			{
				$post->thumbnail = new \Timber\Image( $default['id'] );
			}
		}
		return $post;
	}
	/**
	 * Render a frontend template
	 *
	 * Wrapper function for the Timber class
	 *
	 * @param  array  $template name of template to render
	 * @param  array  $data     data to merge with $_scope
	 */
	public static function render( $template = [], array $data = [] ) : void
	{
		Subscriber::getInstance( 'Timber' )->render( $template, $data );
	}
	/**
	 * Render a timber string
	 *
	 * Wrapper function for the Timber class
	 *
	 * @param  string $string   html/twig string to render
	 * @param  array  $data     data to merge with scope (context)
	 */
	public static function renderString( string $string = '', array $data = [] ) : void
	{
		Subscriber::getInstance( 'Timber' )->renderString( $string, $data );
	}
	/**
	 * Generate paths to search for template files
	 *
	 * @access protected
	 * @param  string $slug Template slug
	 * @param  string $name Template Name
	 * @param  array $views All views we are currently on
	 * @param  string $post_type post type of current screen
	 * @return array Array of formatted template paths to search
	 */
	protected function searchStrings( string $slug, string $name, array $views, string $post_type ) : array
	{
		$paths = [];

		$patterns = [
			[
				'%s/%s.twig',
				'%s-%s.twig',
			],
			[
				'%s/%s-%s.twig',
				'%s-%s-%s.twig',
			],
			[
				'%s/%s/%s-%s.twig',
				'%s/%s-%s-%s.twig',
				'%s-%s/%s-%s.twig',
				'%s-%s-%s-%s.twig',
			]

		];

		if ( ! empty( $name ) )
		{
			foreach ( $views as $view )
			{
				if ( ! empty( $post_type ) )
				{
					foreach ( $patterns[2] as $pattern )
					{
						$paths[0][] = sprintf( $pattern, $slug, $name, $view, $post_type );
						$paths[0][] = sprintf( $pattern, $slug, $name, $post_type, $view );
					}
					foreach ( $patterns[1] as $pattern )
					{
						$paths[1][] = sprintf( $pattern, $slug, $name, $view );
						$paths[1][] = sprintf( $pattern, $slug, $name, $post_type );
					}
				}
				else
				{
					foreach ( $patterns[1] as $pattern )
					{
						$paths[1][] = sprintf( $pattern, $slug, $name, $view );
					}
				}
			}
			foreach ( $patterns[0] as $pattern )
			{
				$paths[2][] = sprintf( $pattern, $slug, $name );
			}
		}
		else
		{
			foreach ( $views as $view )
			{
				if ( ! empty( $post_type ) )
				{
					foreach ( $patterns[1] as $pattern )
					{
						$paths[0][] = sprintf( $pattern, $slug, $view, $post_type );
						$paths[0][] = sprintf( $pattern, $slug, $post_type, $view );
					}

					foreach ( $patterns[0] as $pattern )
					{
						$paths[1][] = sprintf( $pattern, $slug, $view );
						$paths[1][] = sprintf( $pattern, $slug, $post_type );
					}
				}
				else
				{
					foreach ( $patterns[0] as $pattern )
					{
						$paths[1][] = sprintf( $pattern, $slug, $view );
					}
				}
			}

			$paths[2][] = $slug . '.twig';
		}
		$paths = array_unique( array_reduce( $paths, 'array_merge', [] ) );

		return $paths;
	}
	/**
	 * Wrapper for get_template_part using our own method to search for templates
	 *
	 * @param  string  $slug  Template slug
	 * @param  string  $name  Template name (optional)
	 * @param  boolean $search Whether to search using all paths (optional)
	 * @return string|false Template path for inclusion
	 */
	public function templatePaths( string $slug, string $name = '', bool $search = true )
	{
		/**
		 * Nothing to look for or already qualified file, don't waste time
		 */
		if ( empty( $slug ) || is_file( $slug ) )
		{
			return [ $slug ];
		}
		/**
		 * Filter the arguments
		 */
		$args = apply_filters( 'scaffolding/template_part/args', [
			'slug' => $slug,
			'name' => $name,
			'search' => $search,
			'posttype' => get_post_type(),
			'views' => $this->views()
		] );
		/**
		 * If `search` is false, only search a single path
		 * Useful to avoid infinite loops when calling template files recursivley
		 */
		if ( ! $args['search'] )
		{
			$paths = [ $slug ];
		}
		else
		{
			$paths = apply_filters( 'scaffolding/template_part/paths', $this->searchStrings(
				$args['slug'],
				$args['name'],
				$args['views'],
				$args['posttype']
			), $args );
		}

		return $paths;
	}
	/**
	 * Get And render a template part
	 *
	 * @param string  $slug  Template slug
	 * @param string  $name  Template name (optional)
	 * @param array $data Additional data to add to $_scope at render time (optional)
	 * @param boolean $search Whether to search using all paths (optional)
	 */
	public function templatePart( string $slug = '', string $name = '', array $data = [], bool $search = true ) : void
	{
		$templates = $this->templatePaths( $slug, $name, $search );

		if ( ! empty( $templates ) ) {
			Subscriber::getInstance( 'Timber' )->render( $templates, $data );
		}
	}
}