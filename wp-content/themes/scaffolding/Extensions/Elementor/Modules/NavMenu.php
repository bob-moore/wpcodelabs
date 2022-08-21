<?php

namespace Wpcl\Scaffolding\Extensions\Elementor\Modules;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;

use \Wpcl\Scaffolding\Subscriber;

/**
 * @method add_control(string $string, array $array)
 */
class NavMenu extends Widget_Base {

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
		return 'scaffolding-nav-menu';
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
		return __( 'Nav Menu', '_s' );
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
		return 'fas fa-bars';
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
		return array( 'basic' );
	}

	private function getMenus() {

		$nav_menus = wp_get_nav_menus();

		$menus = [];

		foreach ( $nav_menus as $menu ) {
			$menus[$menu->slug] = $menu->name;
		}

		return $menus;
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
				'label' => __( 'Menu', '_s' ),
			]
		);

		$this->add_control(
			'menu',
			[
				'label' => __( 'Menu', '_s' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => '',
				'options' => $this->getMenus(),
			]
		);

		$this->add_control(
			'menu_type',
			[
				'label' => __( 'Layout', '_s' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'dropdown-menu',
				'frontend_available' => true,
				'prefix_class' => '',
				'options' => [
					'default-menu' => 'Vertical Default',
					'dropdown-menu' => 'Vertical Drop Down',
					'hover-menu' => 'Horizontal'
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'menu_style',
			[
				'label' => __( 'Menu', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'menu_typography',
				'label' => __( 'Typography', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .menu-item',
			]
		);

		$this->add_control(
			'menu_item_padding',
			[
				'label' => __( 'Padding', 'plugin-domain' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .menu' => '--menu-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( "menu_item_states" );

		$this->start_controls_tab(
			"tabs_menu_normal",
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);


		$this->add_control(
			'item_color',
			[
				'label' => __( 'Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu' => '--s-link-color : {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_background_color',
			[
				'label' => __( 'Background Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu' => '--menu-item-background : {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			"tabs_menu_hover",
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'item_color_hover',
			[
				'label' => __( 'Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu' => '--s-link-color-hover : {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'item_background_color_hover',
			[
				'label' => __( 'Background Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu' => '--menu-item-background-hover : {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'submenu_style',
			[
				'label' => __( 'Sub-Menu', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Typography::get_type(),
			[
				'name' => 'submenu_typography',
				'label' => __( 'Typography', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .menu .sub-menu .menu-item',
			]
		);

		$this->add_control(
			'submenu_item_padding',
			[
				'label' => __( 'Padding', 'plugin-domain' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .sub-menu' => '--menu-item-padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'submenu_box_shadow',
				'label' => __( 'Box Shadow', 'plugin-domain' ),
				'selector' => '{{WRAPPER}} .sub-menu',
			]
		);

		$this->start_controls_tabs( "submenu_item_states" );

		$this->start_controls_tab(
			"tabs_submenu_normal",
			[
				'label' => esc_html__( 'Normal', 'elementor' ),
			]
		);

		$this->add_control(
			'subitem_color',
			[
				'label' => __( 'Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu .sub-menu' => '--s-link-color : {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'subitem_background_color',
			[
				'label' => __( 'Background Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu .sub-menu' => '--menu-item-background : {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			"tabs_submenu_hover",
			[
				'label' => esc_html__( 'Hover', 'elementor' ),
			]
		);

		$this->add_control(
			'subitem_color_hover',
			[
				'label' => __( 'Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu .sub-menu' => '--s-link-color-hover : {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'subitem_background_color_hover',
			[
				'label' => __( 'Background Color', 'plugin-domain' ),
				'type' => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .menu .sub-menu' => '--menu-item-background-hover : {{VALUE}}',
				],
			]
		);

		$this->end_controls_tabs();

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

		$menu = new \Timber\Menu( $settings['menu'], [] );

		if ( ! empty( $menu ) ) {
			echo '<nav class="scaffolding-menu">';

			Subscriber::getInstance( 'TemplateLoader' )->templatePart( 'components/menu', '', ['menu' => $menu], true );

			echo '</nav>';
		}

	}

}