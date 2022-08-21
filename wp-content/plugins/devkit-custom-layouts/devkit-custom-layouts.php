<?php
/**
 * @wordpress-plugin
 * Plugin Name: DevKit Custom Layouts
 * Plugin URI:  https://github.com/bob-moore/DevKit-Custom-Layouts
 * Description: Custom layouts for (almost) any site
 * Version:     0.1.0
 * Author:      Bob Moore
 * Author URI:  https://www.bobmoore.dev
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: devkit_custom_layouts
 */

namespace Devkit\CustomLayouts;

/**
 * If this file is called directly, abort
 */
if ( !defined( 'WPINC' ) ) {
	die( 'Abort' );
}

if ( ! class_exists( '\Devkit\CustomLayouts\Plugin' ) )
{

	require_once __DIR__ . '/vendor/autoload.php';

	class Plugin extends Framework {

		public function __construct() {
			/**
			 * Create plugin constants
			 */
			define( 'DEVKIT_CUSTOMLAYOUTS_VERSION', '0.1.0' );
			define( 'DEVKIT_CUSTOMLAYOUTS_ASSET_PREFIX', $this->isDev() ? '' : '.min' );
			define( 'DEVKIT_CUSTOMLAYOUTS_URL', plugin_dir_url( __FILE__ ) );
			define( 'DEVKIT_CUSTOMLAYOUTS_PATH', plugin_dir_path( __FILE__ ) );
			/**
			 * Register the text domain
			 */
			load_plugin_textdomain( 'devkit_custom_layouts', false, basename( dirname( __FILE__ ) ) . '/languages' );
			/**
			 * Register activation hook
			 */
			register_activation_hook( __FILE__, [$this, 'activate'] );
			/**
			 * Register deactivation hook
			 */
			register_deactivation_hook( __FILE__, [$this, 'deactivate'] );
			/**
			 * Kickoff the plugin
			 */
			$this->burnBabyBurn();
			/**
			 * Construct parent
			 */
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
			Subscriber::addAction( 'init', [$this, 'registerPostTypes'] );
		}

		private function burnBabyBurn() {
			/**
			 * Register the admin functions
			 */
			new Admin();
			/**
			 * Register the front end functions
			 */
			new FrontEnd();
			/**
			 * Register controller functions
			 */
			new Controller();
			/**
			 * Register utility functions
			 */
			new Utilities();
			/**
			 * Register components
			 */
			$components = $this->getClasses('includes/Components');

			foreach ($components as $component) {

				$class = __NAMESPACE__ . '\\Components\\' . $component;

				new $class;
			}
			/**
			 * Register theme addons
			 */
			$themes = $this->getClasses( 'includes/Themes' );

			foreach ( $themes as $theme ) {

				$class = __NAMESPACE__ . '\\Themes\\' . $theme;

				new $class;
			}
			/**
			 * Register plugin addons
			 */
			$plugins = $this->getClasses( 'includes/Plugins' );

			foreach ( $plugins as $plugin ) {

				$class = __NAMESPACE__ . '\\Plugins\\' . $plugin;

				new $class;
			}
			/**
			 * Register taxonomies
			 */
			$this->taxonomies();
		}

		/**
		 * Activate Plugin
		 *
		 * Register Post Types, Register Taxonomies, and Flush Permalinks
		 * @since 1.0.0
		 */
		public function activate() {
			/**
			 * Register custom post types
			 */
			$this->registerPostTypes();
			/**
			 * Register custom taxonomies
			 */
			$this->taxonomies();
			/**
			 * Flush permalinks
			 */
			$this->flushPermalinks();
		}
		/**
		 * Deactivate Plugin
		 *
		 * Remove unecessary data from database
		 * @since 1.0.0
		 */
		public function deactivate() {
			/**
			 * Flush permalinks
			 */
			$this->flushPermalinks();
		}

		/**
		 * Flush permalinks
		 */
		private function flushPermalinks() {
			global $wp_rewrite;
			$wp_rewrite->init();
			$wp_rewrite->flush_rules();
		}

		/**
		 * Register custom post types
		 */
		public function registerPostTypes() {

			$post_types = $this->getClasses( 'PostTypes' );

			foreach( $post_types as $post_type_name ) {

				$post_type = __NAMESPACE__ . '\\PostTypes\\' . $post_type_name;

				$post_type::register();

			}
		}
		/**
		 * Register custom taxonomies
		 */
		protected function taxonomies() {

			$taxonomies = $this->getClasses( 'Taxonomies' );

			foreach( $taxonomies as $taxonomy_name ) {

				$taxonomy =  __NAMESPACE__ . '\\Taxonomies\\' . $taxonomy_name;

				new $taxonomy();
			}
		}

	}
	new \Devkit\CustomLayouts\Plugin();
}
