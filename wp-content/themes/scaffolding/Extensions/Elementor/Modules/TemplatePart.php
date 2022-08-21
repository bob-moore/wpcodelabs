<?php

namespace Wpcl\Scaffolding\Extensions\Elementor\Modules;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

class TemplatePart extends Widget_Base {

	/**
	 * Retrieve the widget name.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 *
	 * @return string Widget name.
	 */
	public function get_name() {
		return 'template-part';
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
	public function get_title() {
		return __( 'Template Part', '_s' );
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
	public function get_icon() {
		return 'fas fa-puzzle-piece';
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
	public function get_categories() {
		return array( 'theme-elements' );
	}

	/**
	 * Retrieve template parts
	 */
	public function get_template_parts() {
		$options = [
			'masthead' => 'Site Header',
			'colophon' => 'Site Footer',
		];

		return apply_filters( 'elementor_template_parts_option', $options );
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
				'label' => __( 'Content', '_s' ),
			]
		);

		$this->add_control(
			'template_part',
			[
				'label' => __( 'Template Part', '_s' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->get_template_parts(),
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
	protected function render() {
		$settings = $this->get_settings_for_display();


		if ( ! empty( $settings['template_part'] ) ) {
			\Scaffolding::getTemplatePart( $settings['template_part'] );
		}
	}

}