<?php

namespace Scaffolding\addons;

use \Scaffolding\Theme;
use \Scaffolding\Templates;

class WooCommerce {

	public function __construct() {
		if( !class_exists( 'woocommerce' ) ) {
			return false;
		}
		add_action( 'wp', [$this, 'replaceLoop'] );
		add_action( 'wp_enqueue_scripts', [$this, 'dequeueAssets'], 99 );

		add_filter( 'woocommerce_template_loader_files', [$this, 'templatePaths'] );
		add_filter( '_s_view', [$this, 'filterView'] );
		add_filter( 'wc_get_template_part', [$this, 'templateParts'], 10, 3 );
		add_filter( 'wc_get_template', [$this, 'templates'], 10, 5 );
		/**
		 * Move links for better markup around product archive thumbnails
		 */
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 10 );
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 12 );
		/**
		 * Wrap title in a link
		 */
		add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 8 );
		add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 12 );


		/**
		 * Wrapper around account navigation to create columns
		 */
		// add_action( 'woocommerce_account_navigation', [$this, 'insertWrappers'], 5 );
		// add_action( 'woocommerce_account_content', ['\\Scaffolding\\Templates', 'closeDiv'], 15 );
	}
	/**
	 * Helper function to see if current page is a woocommerce page
	 */
	public static function isWoocommercePage() {
		/**
		 * Make sure woocommerce exists at all
		 */
		if( !class_exists( 'woocommerce' ) ) {
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

	function filterView( $view ) {

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

	public function templates( $template, $name, $args, $template_path, $default_path ) {
		return $this->templateParts( $template, str_replace( '.php', '', $name ) );
	}

	public function templateParts( $template = '', $slug = '', $name = '' ) {

		$slug = "template-parts/woocommerce/{$slug}";

		$slug .= !empty( $name ) ? "-{$name}.php" : '.php';

		$override = locate_template( $slug, false, false );

		return !empty( $override ) ? $override : $template;

	}


	/**
	 * Replace the default loop with the woocommerce provided loop
	 *
	 * Allows woocommerce to make content decisions and use the hooks/filters provided
	 * to override individual parts
	 *
	 * @see  https://docs.woocommerce.com/wc-apidocs/source-function-woocommerce_content.html#897-946
	 */
	public function replaceLoop() {
		if( is_woocommerce() ) {
			/**
			 * Remove the default loop
			 */
			remove_action( '_s_loop', [ Templates::getInstance(), 'loop' ] );
			/**
			 * Replace with woocommerce loop
			 */
			add_action( '_s_loop', 'woocommerce_content' );
		}
	}
	/**
	 * Dequeue woocommerce assets on pages they are not needed
	 */
	public function dequeueAssets() {
		if ( self::isWoocommercePage() === false ) {
			wp_dequeue_style('woocommerce-layout');
			wp_dequeue_style('woocommerce-general');
			wp_dequeue_style('woocommerce-smallscreen');
			wp_dequeue_script('wc-cart-fragments');
			wp_dequeue_script('woocommerce');
			wp_dequeue_script('wc-add-to-cart');
		}
	}

	/**
	 * Set woocommerce template path
	 *
	 * Tell woocommerce where our main woocommerce file is located
	 * default is theme/woocommerce.php, but we want to force woocommerce to use
	 * our main index.php file Other mods will be handled using filters/actions
	 * @see http://hookr.io/filters/woocommerce_template_loader_files/
	 */
	function templatePaths( $search_paths ) {
		$search_paths[] = 'index.php';
		return $search_paths;
	}
	/**
	 * Insert additional divs in specific areas around woocommerce templates
	 */
	public function insertWrappers() {

		// var_dump( Theme::currentFilter() );

		switch( current_filter() ) {
			case 'woocommerce_before_shop_loop':
				echo '<div class="_s_woocommerce_loop_wrapper">';
				break;
			case 'woocommerce_before_shop_loop_item':
				echo '<div class="_s_woocommerce_product_wrapper">';
				break;
			case 'woocommerce_before_shop_loop_item_title':
				echo '<div class="_s_woocommerce-loop-product-body">';
				break;
			case 'woocommerce_before_single_product_summary':
				echo '<div class="_s_entry_summary_wrapper">';
				break;
			case 'woocommerce_checkout_before_order_review_heading':
				echo '<div class="column scol-tablet-6">';
				break;
			case 'woocommerce_checkout_before_customer_details':
				echo '<div class="row"><div class="column scol-tablet-6">';
				break;
			case 'woocommerce_account_navigation':
				echo '<div class="_s_woocommerce_account">';
				break;
			default:
				break;
		}
	}
}