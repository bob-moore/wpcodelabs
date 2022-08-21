<?php
/**
 * Elementor (free) Support
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/extensions
 */
namespace Wpcl\Scaffolding\Extensions;

use \Wpcl\Scaffolding\Subscriber;
use \Wpcl\Scaffolding\Utilities;
use \Wpcl\Scaffolding\Lib\Framework;

use \Elementor\Core\Documents_Manager;
use \Elementor\Core\Kits\Manager;
use \Elementor\Plugin;
use \Elementor\Settings;

defined( 'ABSPATH' ) || exit;

class Elementor extends Framework {

	public function __construct()
	{
		/**
		 * If elementor is not active, bail...
		 */
		if ( ! Utilities::isPluginActive( 'elementor/elementor.php' ) )
		{
			return false;
		}
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
	 * @see https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'elementor/frontend/after_enqueue_scripts', [$this, 'enqueueAssets'] );
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
		Subscriber::addAction( 'elementor/element/section/section_layout/before_section_end', [$this, 'registerColumnControls'], 10, 2 );
		Subscriber::addAction( 'elementor/element/column/layout/before_section_end', [$this, 'registerColumnControls'], 10, 2 );
		Subscriber::addAction( 'elementor/element/kit/section_settings-layout/before_section_end', [$this, 'registerColumnControls'], 10, 2 );
		Subscriber::addAction( 'body_class', [$this, 'bodyClass'] );
	}
	public function enqueueAssets() : void
	{
		wp_enqueue_style( '_s_elementor_styles', _S_URL . 'theme/assets/css/elementor' . _S_ASSET_PREFIX . '.css', [], _S_VERSION, 'all' );
	}

	public function registerColumnControls( $section, $args )
	{
		switch ( $section->get_name() ) {
			case 'section' :
				$section->update_control( 'gap_columns_custom', [
					'selectors' => [
						'{{WRAPPER}} .elementor-column-gap-custom' => '--s-gutter : {{SIZE}}{{UNIT}};',
					]
				] );
				break;
			case 'column':
				$section->update_control( 'space_between_widgets', [
					'selectors' => [
						'{{WRAPPER}} .elementor-widget:not(:last-child) .elementor-widget-container' => 'margin-bottom: {{SIZE}}px',
					]
				] );
				break;
			case 'kit':
				$section->update_control( 'space_between_widgets', [
					'selectors' => [
						'{{WRAPPER}}' => '--s-content-spacing : {{SIZE}}px',
					]
				] );
				break;
			default:
				break;
		}
	}

	public function bodyClass( array $classes ) : array
	{

		$kit = \Elementor\Plugin::$instance->kits_manager->get_kit_for_frontend();

		if ( $kit ) {

			$kit_class = 'elementor-kit-' . $kit->get_main_id();

			if ( ! array_search( $kit_class, $classes ) ) {

				$classes[] = $kit_class;

			}

		}

		return $classes;
	}
}