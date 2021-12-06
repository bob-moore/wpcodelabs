<?php

namespace Scaffolding;

class Theme extends Framework {

	protected function __construct() {
		/**
		 * Init classes
		 */
		Templates::getInstance();

		ThemeMods::getInstance();

		TemplateTags::getInstance();
		/**
		 * Init addons
		 */
		// $this->includeAddons();
		/**
		 * Add Filters
		 */
		add_filter( 'image_size_names_choose', [$this, 'setImageSizeNames'] );
		add_filter( 'walker_nav_menu_start_el', [$this, 'menuItemToggle'], 999, 4 );
		add_filter( 'walker_nav_menu_start_el', [$this, 'menuItemDescription'], 998, 2 );
		add_filter( 'wp_nav_menu_args', [$this, 'menuItemText'] );
		add_filter( 'nav_menu_css_class', [$this, 'menuItemClasses'], 10, 4 );
		add_filter( 'excerpt_length', [$this, 'excerptLength'] );
		add_filter( 'post_class', [$this, 'postClass'] );
		add_filter('excerpt_more', [$this, 'excerptMore'] );
		// add_filter( 'wp_resource_hints', [$this, 'preloadFonts'], 10, 2 );
		/**
		 * Add Actions
		 */
		add_action( 'after_setup_theme', [$this, 'setupTheme'] );
		add_action( 'wp_enqueue_scripts', [$this, 'enqueueAssets'] );
		add_action( 'wp', [$this, 'includeView'], 99 );
		add_action( 'widgets_init', [$this, 'registerWidgets'] );

	}
	/**
	 * Get View
	 *
	 * Return string representing the current view
	 *
	 * @param  string $modifier : string used to utilize a context specific filter
	 * @return [string]           The context string
	 */
	public function getView( $context = '' ) {
		$view = '';

		if( is_front_page() ) {
			$view = 'frontpage';
		}

		else if( is_home() ) {
			$view = 'blog';
		}

		else if( is_archive() ) {
			$view = 'archive';
		}

		else if( is_search() ) {
			$view = 'search';
		}

		else if( is_singular() ) {
			$view = 'single';
		}

		else if( is_404() ) {
			$view = '404';
		}
		/**
		 * Apply generic filter
		 */
		$view = apply_filters( "_s_view", $view, $context );
		/**
		 * Apply view specific filter
		 */
		if( !empty( $context ) ) {
			$view = apply_filters( "_s_view_{$context}", $view );
		}
		return $view;
	}

	public function includeView() {
		/**
		 * Set an array of names to prefix our final class name with
		 */
		$classes = ['Scaffolding', 'views'];
		/**
		 * Get the view, and break into array
		 */
		$view = explode( '/', $this->getView() );
		/**
		 * Make sure final class name is capatalized
		 */
		$view[ count( $view ) - 1 ] = ucwords( $view[ count( $view ) - 1 ] );
		/**
		 * Create fully qualified classname
		 */
		$class = '\\' . implode( "\\", array_merge( $classes, $view ) );
		/**
		 * Instantiate class
		 */
		if( class_exists( $class ) ) {
			new $class();
		}
	}

	public function includeAddons() {

		$files = glob( _S_ROOT_DIR . 'lib/addons/*.php' );

		foreach( $files as $file ) {

			$class = '\\Scaffolding\\addons\\' . str_replace('.php', '', basename($file) );

			if( class_exists($class) ) {
				new $class;
			}
		}
	}

	public function setupTheme() {
		/**
		 * Add theme supports
		 */
		add_theme_support( 'html5', array( 'caption', 'comment-form', 'comment-list', 'gallery', 'search-form' ) );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'video', 'chat', 'gallery', 'image', 'quote', 'status', 'link' ) );
		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'custom-background' );
		add_theme_support( 'custom-logo', array( 'flex-width'  => true, 'flex-height' => true, 'height' => 100, 'width' => 400 ) );
		add_theme_support( 'align-wide' );
		add_theme_support( 'custom-header', array(
			'flex-height'            => true,
			'flex-width'             => true,
			'header-text'            => false,
			'wp-head-callback'       => [Templates::getInstance(), 'customHeader'],
			'admin-head-callback'    => [Templates::getInstance(), 'customHeader'],
			'admin-preview-callback' => [Templates::getInstance(), 'customHeader'],
		));
		add_theme_support( 'woocommerce');
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
		/**
		 * Set post thumbnail size
		 */
		set_post_thumbnail_size( 960, 525, true );
		if ( ! isset( $content_width ) ) {
			$content_width = 1280;
		}
		/**
		 * Add additional image sizes
		 */
		add_image_size( 'medium-square', 500, 500, true );
		add_image_size( 'large-square', 1024, 1024, true );
		add_image_size( 'tablet-large', 768, 9999, false );
		add_image_size( 'featured', $content_width, $content_width * .5656, true );
		/**
		 * Register nav menus
		 */
		register_nav_menus( array(
			'primary' => esc_html__( 'Primary', '_s' ),
			'mobile' => esc_html__( 'Primary - Mobile', '_s' ),
			'footer' => esc_html__( 'Footer', '_s' ),
		) );
	}

	public function enqueueAssets() {
		$prefix = defined( 'WP_DEBUG' ) && WP_DEBUG === true ? '' : '.min';
		/**
		 * Enqueue Javascript Files
		 */
		// wp_enqueue_script( '_s_modernizer', _S_ROOT_URL . 'assets/js/modernizr.min.js', array(), '2.8.3', false );
		wp_enqueue_script( '_s_script', _S_ROOT_URL . "assets/js/public{$prefix}.js", array( 'jquery' ), _S_VERSION, true );
		/**
		 * Enqueue CSS Files
		 */
		wp_enqueue_style( '_s_typekit', '//use.typekit.net/hhn8jmw.css', array(), 'all' );
		wp_enqueue_style( '_s_styles', _S_ROOT_URL . "assets/css/public{$prefix}.css", array(), _S_VERSION, 'all' );
	}

	function preloadFonts( $hints, $relation_type ) {
		if( $relation_type === 'prerender' ) {
			$prerender = [
				'themeicon/ttf' => [
					'href' => _S_ROOT_URL . 'assets/fonts/_scaffolding.ttf?yxdczd',
					'as' => 'font',
					'crossorigin' => true,
					'type' => 'font/ttf',
				],
				'themeicon/woff' => [
					'href' => _S_ROOT_URL . 'assets/fonts/_scaffolding.woff?yxdczd',
					'as' => 'font',
					'crossorigin' => true,
					'type' => 'font/woff',
				],
				'typekit' => [
					'href' => 'https://use.typekit.net/hhn8jmw.css',
					'as' => 'style',
					'crossorigin' => true,
				],
			];
			$hints = array_merge( $hints, $prerender );
		}
		return $hints;
	}

	public function setImageSizeNames( $wp_sizes ) {
		$theme_sizes = [
			'medium-square' => __( 'Medium Square', '_s' ),
			'large-square' => __( 'Large Square', '_s' ),
			'tablet-large' => __( 'Tablet Large', '_s' ),
			'featured' => __( 'Featured', '_s' ),
		];
		return array_merge( $wp_sizes, $theme_sizes );
	}

	public function registerWidgets() {
		register_sidebar( array(
			'name'          => esc_html__( 'Primary Sidebar', '_s' ),
			'id'            => 'sidebar-primary',
			'description'   => esc_html__( 'Add widgets here.', '_s' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Mobile Menu Area', '_s' ),
			'id'            => 'sidebar-mobile',
			'description'   => esc_html__( 'Add widgets here.', '_s' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
		register_sidebar( array(
			'name'          => esc_html__( 'Footer - Right', '_s' ),
			'id'            => 'footer-right',
			'description'   => esc_html__( 'Add widgets here.', '_s' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		) );
	}

	/**
	 * Add submenu toggle buttons
	 */
	public function menuItemToggle( $output, $item, $depth, $args ) {
		/**
		 * Don't do jetpack
		 */
		if( $args->theme_location === 'jetpack-social-menu' ) {
			return $output;
		}
		// If this item has children, append the button
	    if( in_array( 'menu-item-has-children', $item->classes ) ){
	    	$output  = "<span class='nav-item-container'>{$output}";
	        $output .= '<button class="sub-menu-toggle _s_icon _s_icon-expand_more" aria-expanded="false" aria-pressed="false" role="button"><span class="screen-reader-text">Submenu</span></button>';
	        $output .= '</span>';
	    } else {
	    	$output = "<span class='nav-item-container'>{$output}</span>";
	    }
	    // Return the output
	    return $output;
	}
	/**
	 * Add spans around the description
	 */
	public function menuItemDescription( $item_output, $item ) {

		$description = trim( $item->post_content );

		if( !empty( $description ) ) {
			$item_output = str_replace( '</a>', '<span class="nav-description" itemprop="description">' . $item->description . '</span></a>', $item_output );
		}
		return $item_output;
	}
	/**
	 * Add spans around the link text
	 * Remove any fallback (rather just not have a menu)
	 */
	public function menuItemText( $args ) {
		/**
		 * Don't interfere with jetpack
		 */
		if( $args['theme_location'] === 'jetpack-social-menu' ) {
			return $args;
		}
		/**
		 * Maybe wrap links
		 */
		if( empty( $args['link_before'] ) ) {
			$args['link_before'] = '<span class="nav-text" itemprop="name">';
			$args['link_after']  = '</span>';
		}
		/**
		 * Ditch the fallbacks
		 */
		$args['fallback'] = '__return_false';
		return $args;
	}
	/**
	 * Add additional classes to menu items
	 * @param  [type] $classes [description]
	 * @param  [type] $item    [description]
	 * @param  [type] $args    [description]
	 * @param  [type] $depth   [description]
	 * @return [type]          [description]
	 */
	public function menuItemClasses( $classes, $item, $args, $depth ) {
		$classes[] = "menu-item-depth-{$depth}";
		return $classes;
	}
	/**
	 * Change the length of experpts
	 */
	function excerptLength( $length ) {
		return 45;
	}
	/**
	 * Add post classes
	 */
	public function postClass( $classes ) {
		$classes[] = 'entry';
		return $classes;
	}
	/**
	 * Helper function to retrieve current filter and priority
	 */
	public static function currentFilter() {
		global $wp_filter;

		$current = [
			'name' => current_filter(),
			'priority' => $wp_filter[ current_filter() ]->current_priority(),
		];

		return $current;
	}

	public function excerptMore( $more ) {
		global $post;

		ob_start();

		echo '...';

		include Templates::getTemplatePart( 'morelink', '', false );

		return ob_get_clean();

	}

}