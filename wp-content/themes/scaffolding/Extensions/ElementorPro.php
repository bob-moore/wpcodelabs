<?php
/**
 * Elementor (pro) Support
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding\Extensions;

use \Wpcl\Scaffolding\Subscriber;
use \Wpcl\Scaffolding\Utilities;
use \Wpcl\Scaffolding\Lib\Framework;

use \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;
use \ElementorPro\Plugin;
use \ElementorPro\Modules\ThemeBuilder\Module as ThemeBuilder;
use \Elementor\Core\Documents_Manager;

use \Kirki;

defined( 'ABSPATH' ) || exit;

class ElementorPro extends Framework
{

	public function __construct()
	{
		/**
		 * If elementor is not active, bail...
		 */
		if ( ! Utilities::isPluginActive( 'elementor-pro/elementor-pro.php' ) ) {
			return false;
		}
		/**
		 * Load extensions
		 */
		new Elementor\ThemeHooks();

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
	public function addActions() : void
	{
		Subscriber::addAction( 'wp', [$this, 'replaceThemeComponent'], 99 );
		Subscriber::addAction( 'elementor/theme/register_locations', [$this, 'registerLocations'] );
		// Subscriber::addAction( 'elementor/widgets/widgets_registered', [$this, 'registerModules'] );
		Subscriber::addAction( 'elementor/documents/register', [$this, 'registerDocuments'], 2 );
		Subscriber::addAction( 'elementor/frontend/after_enqueue_scripts', [$this, 'enqueueAssets'] );

	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @since 1.0.0
	 */
	public function addFilters() : void
	{
		Subscriber::addFilter( 'scaffolding/layout', [$this, 'themeLayout'], 999 );
		Subscriber::addFilter( 'scaffolding/views', [$this, 'editorView'] );
	}

	public function enqueueAssets()
	{
		wp_enqueue_style( '_s_elementor_styles', _S_URL . 'theme/assets/css/elementor.min.css', [], _S_VERSION, 'all' );
	}

	/**
	 * Register custom document types
	 */
	public function registerDocuments( Documents_Manager $documents_manager ) {
		$documents_manager->register_document_type( 'hero', Elementor\Documents\Hero::get_class_full_name() );
		$documents_manager->register_document_type( 'navpane', Elementor\Documents\NavPane::get_class_full_name() );
		$documents_manager->register_document_type( 'sidebar', Elementor\Documents\Sidebar::get_class_full_name() );
	}

	public function registerModules() {

		// $modules = glob( _S_ROOT_DIR . 'lib/Extensions/Elementor/Modules/*.php' );

		// $elementor = \Elementor\Plugin::instance();

		// foreach( $modules as $module ) {

		// 	$module = str_replace( '.php', '', basename( $module ) );

		// 	$module = __NAMESPACE__ . '\\Elementor\\Modules\\' . $module;

		// 	$elementor->widgets_manager->register_widget_type( new $module() );
		// }
	}
	/**
	 * Set the default template used for a singular view
	 */
	public function themeLayout( $template )
	{
		if ( ! is_singular( 'elementor_library' ) ) {
			return $template;
		}

		if ( empty( $template ) ) {
			$template = 'full-width';
		}

		return $template;
	}
	/**
	 * Get the type of preview
	 *
	 * @return [type] [description]
	 */
	public function getPreviewType() : array
	{
		$preview = [
			'type' => false,
			'location' => false,
		];

		if ( ! is_singular( 'elementor_library' ) )
		{
			return $preview;
		}

		$document = ThemeBuilder::instance()->get_document( get_the_id() );

		if ( $document )
		{
			$parts = explode( "\\", get_class( $document ) );
			$preview = [
				'type' => strtolower( end( $parts ) ),
				'location' => strtolower( $document->get_location() ),
			];
		}
		return $preview;
	}

	function editorView( array $views ) : array
	{
		$preview = $this->getPreviewType();

		if ( ! $preview['type'] ) {
			return $views;
		}

		if ( $preview['type'] === 'archive' )
		{
			return ['archive'];
		}
		elseif ( $preview['type'] )
		{
			return ['404'];
		}

		return $view;
	}

	function replaceThemeComponent( $template ) {

		if ( elementor_location_exits( 'header', true ) ) {
			Subscriber::removeAction( 'masthead', ['Layout', 'components/masthead'] );
		}

		if ( elementor_location_exits( 'footer', true ) ) {
			Subscriber::removeAction( 'colophon', ['Layout', 'components/colophon'] );
		}

		if ( elementor_location_exits( 'navpane', true ) ) {
			Subscriber::removeAction( 'navpane', ['Layout', 'components/navpane'] );
		}

		if ( elementor_location_exits( 'sidebar', true ) ) {
			Subscriber::removeAction( 'sidebar/primary', ['Layout', 'components/sidebar-primary'] );
		}
		if ( elementor_location_exits( 'archive', true ) ) {
			Subscriber::removeAction( 'loop', ['Layout', 'loop'] );
			Subscriber::removeAction( 'loop', 'woocommerce_content' );
		}
	}

	/**
	 * Register custom theme builder locations
	 */
	public function registerLocations( Locations_Manager $location_manager ) : void
	{
		$location_manager->register_location(
			'header',
			[
				'hook' => 'masthead',
				'edit_in_content' => false,

			]
		);
		$location_manager->register_location(
			'footer',
			[
				'hook' => 'colophon',
				'edit_in_content' => false,
			]
		);
		$location_manager->register_location(
			'archive',
			[
				'hook' => 'loop',
			],

		);
		$location_manager->register_location(
			'single',
			[
				'hook' => 'loop',
			],
		);
		/**
		 * Custom Locations
		 */
		$location_manager->register_location(
			'hero',
			[
				'label' => __( 'Hero', 'plugin_scaffolding' ),
				'multiple' => false,
				'edit_in_content' => false,
				'hook' => 'hero',
			]
		);
		$location_manager->register_location(
			'navpane',
			[
				'label' => __( 'Navpane', 'plugin_scaffolding' ),
				'multiple' => false,
				'edit_in_content' => false,
				'hook' => 'navpane',
			]
		);
		$location_manager->register_location(
			'sidebar',
			[
				'label' => __( 'Sidebar', 'plugin_scaffolding' ),
				'multiple' => false,
				'edit_in_content' => false,
				'hook' => 'sidebar/primary',
			]
		);
	}

}