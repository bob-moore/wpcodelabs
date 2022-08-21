<?php

namespace Wpcl\Scaffolding\Extensions\elementor;

use \Wpcl\Scaffolding\Subscriber;
use \Wpcl\Scaffolding\Utilities;
use \Wpcl\Scaffolding\Lib\Framework;

use \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;
use \ElementorPro\Modules\ThemeBuilder\Module as ThemeBuilder;
use \Elementor\Core\Documents_Manager;
use \Elementor\Controls_Manager;

class ThemeHooks extends Framework {
	protected array $_active_sections;
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @since 1.0.0
	 * @see  https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'elementor/theme/register_locations', [$this, 'registerLocations'] );
		Subscriber::addAction( 'elementor/theme/register_locations', [$this, 'rendorThemeHook'], 99999 );
		Subscriber::addAction( 'elementor/element/before_section_end', [$this, 'hookControls'], 10, 3 );
	}

	function themeHooks() {
		$hooks = [
			'scaffolding/end'  => __('Start', 'scaffolding'),
			'scaffolding/end'  => __('End', 'scaffolding'),
			'page/before' => __('Page - Before', 'scaffolding'),
			'page/after' => __('Page - After', 'scaffolding'),
			'header/before' => __('Header - Before', 'scaffolding'),
			'masthead' => __('Masthead', 'scaffolding'),
			'masthead/includes' => __('Masthead Includes', 'scaffolding'),
			'header/after' => __('Header - After', 'scaffolding'),
			'main/before' => __('Main - Before', 'scaffolding'),
			'main/open' => __('Main - Open', 'scaffolding'),
			'main/close' => __('Main - Close', 'scaffolding'),
			'main/after' => __('Main - After', 'scaffolding'),
			'primary/before' => __('Primary - Before', 'scaffolding'),
			'primary/after' => __('Primary - After', 'scaffolding'),
			'loop/before' => __('Loop - Before', 'scaffolding'),
			'loop/start' => __('Loop - Start', 'scaffolding'),
			'loop/enter' => __('Loop - Enter', 'scaffolding'),
			'loop' => __('Loop', 'scaffolding'),
			'loop/leave' => __('Loop - Leave', 'scaffolding'),
			'loop/end' => __('Loop - End', 'scaffolding'),
			'loop/after' => __('Loop - After', 'scaffolding'),
			'content/before' => __('Content - Before', 'scaffolding'),
			'content/404' => __('Content - 404', 'scaffolding'),
			'content/none' => __('Content - None', 'scaffolding'),
			'content/after' => __('Content - After', 'scaffolding'),
			'entry' => __('Entry', 'scaffolding'),
			'entry/before' => __('Entry - Before', 'scaffolding'),
			'entry/open' => __('Entry - Open', 'scaffolding'),
			'entry/header/before' => __('Entry Header - Before', 'scaffolding'),
			'entry/header' => __('Entry Header', 'scaffolding'),
			'entry/header/after' => __('Entry Header - After', 'scaffolding'),
			'entry/content/before' => __('Entry Content - Before', 'scaffolding'),
			'entry/content' => __('Entry Content', 'scaffolding'),
			'entry/content/after' => __('Entry Content - After', 'scaffolding'),
			'entry/footer/before' => __('Entry Footer - Before', 'scaffolding'),
			'entry/footer' => __('Entry Footer', 'scaffolding'),
			'entry/footer/after' => __('Entry Footer - After', 'scaffolding'),
			'hero/before' => __('Hero - Before', 'scaffolding'),
			'hero' => __('Hero', 'scaffolding'),
			'hero/includes' => __('Hero Includes', 'scaffolding'),
			'hero/after' => __('Hero - After', 'scaffolding'),
			'sidebar/primary/before' => __('Primary Sidebar - Before', 'scaffolding'),
			'sidebar/primary' => __('Primary Sidebar', 'scaffolding'),
			'sidebar/primary/after' => __('Primary Sidebar - After', 'scaffolding'),
			'sidebar/secondary/before' => __('Secondary Sidebar - Before', 'scaffolding'),
			'sidebar/secondary' => __('Secondary Sidebar', 'scaffolding'),
			'sidebar/secondary/after' => __('Secondary Sidebar - After', 'scaffolding'),
			'sidebar/widgets' => __('Sidebar/widgets', 'scaffolding'),
			'footer/before' => __('Footer - Before', 'scaffolding'),
			'colophon' => __('Colophon', 'scaffolding'),
			'colophone/includes' => __('Colophon Includes', 'scaffolding'),
			'footer/after' => __('Footer - After', 'scaffolding'),
			'navpane' => __('Navpane', 'scaffolding'),
			'navpane/includes' => __('Navpane - Includes', 'scaffolding'),
		];

		return $hooks;
	}

	function registerLocations( $location_manager )
	{
		$hooks = $this->themeHooks();
		foreach ( $hooks as $hook => $title )
		{
			$location_manager->register_location(
				"scaffolding/elementor/$hook",
				[
					'label' => $title,
					'multiple' => true,
					'edit_in_content' => true,
					'hook' => "scaffolding/elementor/$hook",
				]
			);
		}
	}

	function rendorThemeHook( $location_manager ) {

		// global $wp_filter;

		$hooks = $this->themeHooks();

		foreach( $hooks as $hook => $title ) {

			$templates = ThemeBuilder::instance()->get_conditions_manager()->get_location_templates( "scaffolding/elementor/$hook" );

			if ( empty( $templates ) )
			{
				continue;
			}
			/**
			 * Set our own hook with a priority
			 * @var [type]
			 */
			foreach( $templates as $id => $weight ) {

				$priority = $this->getHookPriority( $id );

				$document = ThemeBuilder::instance()->get_document( $id );

				$this->_active_sections[] = $hook;

				add_action( $hook, [$this, "elementor/section/{$id}"], $priority );
			}
		}
	}

	public function __call( $hook, $arguments )
	{
		if ( strpos( $hook, 'elementor/section/' ) !== false )
		{
			$id = intval( str_replace( 'elementor/section/', '', $hook ) );

			ThemeBuilder::instance()->get_document( $id )->print_content();
		}
	}

	function getHookPriority( $id ) {

		$preview = \Elementor\Plugin::$instance->preview->is_preview();

		$settings_manager = \Elementor\Core\Settings\Manager::get_settings_managers( 'page' );

		/**
		 * If we have a newer autosave, use that
		 */
		if ( $preview && \Elementor\Plugin::$instance->documents->get( $id )->get_autosave_id() ) {
			$settings = \Elementor\Plugin::$instance->documents->get( $id )->get_autosave()->get_settings();
		}
		/**
		 * Else we can use the standard settings
		 */
		else {
			$settings = $settings_manager->get_model( $id )->get_settings();
		}

		return isset( $settings['location_priority'] ) ? $settings['location_priority'] : 10;
	}

	function hookControls( $section, $section_id, $args ) {

		if ( $section_id !== 'location_settings' ) {
			return false;
		}

		$section->start_injection( [
			'at' => 'after',
			'of' => 'location',
		] );

		$section->add_control(
			'location_priority',
			[
				'label' => 'Priority',
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'default' => 10,
			]
		);

		$section->end_injection();
	}
}