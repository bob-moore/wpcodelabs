<?php
/**
 * Markup elementor module
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/elementor
 */

namespace Mdm\Cornerstone\Addons\Elementor;

use \Mdm\Cornerstone\Subscriber;
use \Mdm\Cornerstone\Plugin;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

defined( 'ABSPATH' ) || exit;

class Markup extends Widget_Base
{
	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'mwf-markup';
	}

	/**
	 * Retrieve the widget title.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return __( 'Markup', 'mdm_wp_cornerstone' );
	}

	/**
	 * Retrieve the widget icon.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'eicon-code';
	}

	/**
	 * Retrieve the list of categories the widget belongs to.
	 *
	 * Used to determine where to display the widget in the editor.
	 *
	 * Note that currently Elementor supports only one category.
	 * When multiple categories passed, Elementor uses the first one.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return array Widget categories.
	 */
	public function get_categories()
	{
		return [ 'custom' ];
	}

	/**
	 * Register the widget controls.
	 *
	 * Adds different input fields to allow the user to change and customize the widget settings.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'General', '_s' ),
			]
		);


		$this->add_control(
			'code',
			[
				'label' => __( 'Markup', '_s' ),
				'type' => Controls_Manager::CODE,
				'language' => 'twig',
				'rows' => 40,
			]
		);

		$this->end_controls_section();
	}

	/**
	 * Render the widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 *
	 * @access protected
	 */
	protected function render()
	{
		$settings = $this->get_settings_for_display();

		Subscriber::getInstance( 'FrontEnd' )->renderString(
			$settings->code,
			[
				'settings' => $settings,
				'module' => $this
			]
		);
	}
}