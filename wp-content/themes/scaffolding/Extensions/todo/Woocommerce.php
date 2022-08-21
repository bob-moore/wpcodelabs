<?php
/**
 * Functionality specific to a woocommerce
 *
 * @link https://www.wpcodelabs.com
 * @since 1.0.0
 * @package _s
 */


namespace Wpcl\Scaffolding\Extensions;

use \Wpcl\Scaffolding\Framework;
use \Wpcl\Scaffolding\Subscriber;

class Woocommerce extends Framework {
	/**
	 * Last called template file
	 *
	 * Used to load template files from woocommerce
	 * @var string path to last called file
	 */
	public $_template;

	public function __construct() {
		if ( ! \Scaffolding::isPluginActive( 'woocommerce/woocommerce.php' ) ) {
			return false;
		}

		parent::__construct();
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @since 1.0.0
	 */
	public function addActions() {
		Subscriber::addAction( 'wp', [$this, 'replaceLoop'] );
		Subscriber::addAction( 'after_setup_theme', [$this, 'themeSupport'] );
		Subscriber::addAction( 'widgets_init', [$this, 'registerWidgets'] );
		/**
		 * Move links for better markup around product archive thumbnails
		 */
		Subscriber::removeAction( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 10 );
		Subscriber::addAction( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 12 );
		/**
		 * Wrap title in a link
		 */
		Subscriber::addAction( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 8 );
		Subscriber::addAction( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 12 );

		// /**
		//  * Remove some unnecessary template actions
		//  */
		Subscriber::removeAction( 'woocommerce_before_shop_loop' , 'woocommerce_result_count', 20 );
		Subscriber::removeAction( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
		Subscriber::removeAction( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );
	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @since 1.0.0
	 */
	public function addFilters() {
		Subscriber::addFilter( 'scaffolding/scope/global', [$this, 'globalScope'] );
		Subscriber::addFilter( 'woocommerce_template_loader_files', [$this, 'templatePaths'] );
		Subscriber::addFilter( 'theme_view', [$this, 'themeView'] );
		Subscriber::addFilter( 'wc_get_template_part', [$this, 'templateParts'], 99, 4 );
		Subscriber::addFilter( 'wc_get_template', [$this, 'templates'], 99, 5 );

	}
	/**
	 * Add theme support
	 *
	 * @since  1.0.0
	 * @return void
	 */
	public function themeSupport() {
		add_theme_support( 'woocommerce');
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}

	/**
	 * Global callback to run wp functions
	 */
	public function __call( $function, $args ) {
		if ( function_exists( $function ) ) {
			return call_user_func_array( $function, $args );
		}

		elseif ( function_exists( 'wc_' . $function ) ) {
			return call_user_func_array( 'wc_' . $function, $args );
		}
	}

	/**
	 * Add context elements to timber, globally
	 *
	 * @since 1.0.0
	 */
	public function globalScope( $scope ) {

		$scope['woocommerce'] = $this;

		// global $product, $post;



		// if ( ! $product && $this->isWoocommercePage() ) {
		// 	$product = wc_setup_product_data( $post );
		// 	$this->expose($post);
		// 	$this->expose($product);
		// }

		// if ( $product && is_object( $product ) ) {



		// 	if ( ! isset( $scope['product'] ) || $scope['product']->get_id() !== $product->get_id() ) {
		// 		$scope['product'] = $product;
		// 	}
		// }

		return $scope;
	}
	/**
	 * Add dynamic context
	 *
	 * Adds context when templates are included
	 */
	public function addDynamicContext( $context ) {
		return $this->addContext( $context );
	}
	/**
	 * Helper function to see if current page is a woocommerce page
	 *
	 * @since  1.0.0
	 * @return  bool : whether we are currently viewing a woocommerce page
	 */
	public function isWoocommercePage() {
		/**
		 * Make sure woocommerce exists at all
		 */
		if( ! class_exists( 'woocommerce' ) ) {
			return false;
		}
		/**
		 * See if on woocommerce page
		 */
		if( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
			return true;
		}
		/**
		 * Return default
		 */
		return false;
	}
	/**
	 * Create additional view classes
	 *
	 * Used to add context to functions, and load view templates in lib/views/woocommerce/{view}.php
	 * @param string $view : Standard view determined by theme
	 * @return string $view : Maybe modified view name
	 */
	function themeView( $view ) {

		if( $view === 'archive' && is_woocommerce() ) {
			$view = 'woocommerce/shop';
		}

		elseif( $view === 'single' && is_woocommerce() ) {
			$view = 'woocommerce/product';
		}

		elseif( $view === 'single' && is_cart() ) {
			$view = 'woocommerce/cart';
		}

		elseif( $view === 'single' && is_checkout() ) {
			$view = 'woocommerce/checkout';
		}

		elseif( $view === 'single' && is_account_page() ) {
			$view = 'woocommerce/account';
		}

		return $view;

	}
	/**
	 * Filter returned template from 'wc_get_template'
	 *
	 * Allows us to use our own templates from theme/template-parts
	 *
	 * @see  http://hookr.io/functions/wc_get_template/
	 * @since 1.0.0
	 * @param string $template : full path to template file
	 * @param string $name : name of template
	 * @param array $args : template arguments
	 * @param string $template_path : path to template directory
	 * @param string $default_path : default template directory
	 * @return string $template : maybe modified path to template file
	 */
	public function templates( $template, $name, $args, $template_path, $default_path ) {
		return $this->templateParts( $template, str_replace( '.php', '', $name ), '', $args );
	}
	/**
	 * Maybe replace woocommerce template parts with theme files
	 *
	 * Allows us to use our own templates from theme/template-parts
	 *
	 * @see http://hookr.io/functions/wc_get_template_part/
	 * @param  string $template : full path to template part
	 * @param  string $slug : basename of template file
	 * @param  string $name : name of template file
	 * @return string $template : maybe modified path to template file
	 */
	public function templateParts( $template = '', $slug = '', $name = '', $args = [] ) {

		$loader = Subscriber::getInstance( 'TemplateLoader' );

		if ( $loader ) {

			$override = $loader->getTemplatePart( 'woocommerce/' . $slug, $name );

			if ( $override ) {
				/**
				 * Check if twig template
				 */
				if ( in_array( pathinfo( $override, PATHINFO_EXTENSION ), [ 'twig', 'html' ] ) ) {

					$this->_template = $override;

					if ( ! empty( $args ) ) {

						foreach( $args as $arg => $value ) {

							$this->{$arg} = $value;

						}

					}

					return _S_ROOT_DIR . 'lib/timber/Woocommerce.php';

				}
				/**
				 * PHP Template
				 */
				else {
					return $override;
				}
			}
		}

		return $template;

	}

	public static function getCalledFile() {
		return self::$last_called_template;
	}
	/**
	 * Replace the default loop with the woocommerce provided loop
	 *
	 * Allows woocommerce to make content decisions and use the hooks/filters provided
	 * to override individual parts
	 *
	 * @see  https://docs.woocommerce.com/wc-apidocs/source-function-woocommerce_content.html#897-946
	 * @since 1.0.0
	 * @return void
	 */
	public function replaceLoop() {

		if( is_woocommerce() ) {
			/**
			 * Remove the default loop
			 */
			Subscriber::removeAction( 'loop', [ 'TemplateLoader', 'loop'] );
			/**
			 * Replace with woocommerce loop
			 */
			Subscriber::addAction( 'loop', 'woocommerce_content' );
		}
	}
	/**
	 * Set woocommerce template path
	 *
	 * Tell woocommerce where our main woocommerce file is located
	 * default is theme/woocommerce.php, but we want to force woocommerce to use
	 * our main index.php file Other mods will be handled using filters/actions
	 * @see http://hookr.io/filters/woocommerce_template_loader_files/
	 * @param array $search_paths : known paths for woocommerce to search for templates
	 * @return  array $search_paths : array with our path inserted
	 */
	function templatePaths( $search_paths ) {
		$search_paths[] = 'index.php';
		return $search_paths;
	}
	/**
	 * Register woocommerce sidebar
	 *
	 * @since  1.0.0
	 * @return  void
	 */
	public function registerWidgets() {
		register_sidebar( [
			'name'          => esc_html__( 'WooCommerce Sidebar', '_s' ),
			'id'            => 'sidebar-woocommerce',
			'description'   => esc_html__( 'Sidebar visible on woocommerce views like shop and products', '_s' ),
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h4 class="widget-title">',
			'after_title'   => '</h4>',
		] );
	}
	/**
	 * Replace primary sidebar
	 *
	 * @since  1.0.0
	 * @param string $name : $named template file being loaded
	 * @return string $name : maybe modified file name
	 */
	public function woocommerceSidebar( $name ) {
		if( $this->isWoocommercePage() && $name === 'sidebar' ) {
			$name = 'woocommerce';
		}
		return $name;
	}
	/**
	 * Use shop page title for some areas
	 * @todo  - move to functions.php, shouldn't be a default function
	 */
	public function shopTitle( $title ) {
		if ( in_array( \Scaffolding::getView(), ['woocommerce/shop', 'woocommerce/product' ] ) ) {
			$title = get_the_title( get_option( 'woocommerce_shop_page_id' ) );
		}
		return $title;
	}
}