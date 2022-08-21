<?php
/**
 * Navigation Panel Document Type
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/extensions
 */
namespace Wpcl\Scaffolding\Extensions\Elementor\Documents;

use \ElementorPro\Modules\ThemeBuilder\Module as ThemeBuilder;
use \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;

use \Wpcl\Scaffolding\Utilities;
use \Wpcl\Scaffolding\Subscriber;

defined( 'ABSPATH' ) || exit;

class NavPane extends ThemeBuilderDocument {
	/**
	 * Location key
	 *
	 * @var string lowercase underscrore seperate key for this document
	 * @access protected
	 */
	protected const S_LOCATION_KEY = 'navpane';
	/**
	 * Constructor
	 *
	 * Add additional actions necessary for this type of document
	 * @param array $data
	 */
	public function __construct( array $data = [] )
	{
		add_filter( 'scaffolding/markup/atts/navpane', [$this, 'navpaneAtts'] );
		parent::__construct( $data );
	}
	public static function get_title() {
		return __( 'NavPane', '_s' );
	}
	/**
	 * Register document specific controls
	 *
	 * @return void
	 */
	public function register_controls()
	{
		parent::register_controls();

		$this->start_controls_section(
			'navpane_options',
			[
				'label' => esc_html__( 'Options', 'scaffolding' ),
				'tab' => \Elementor\Controls_Manager::TAB_SETTINGS,
			]
		);

		$this->add_control(
			'display_type',
			[
				'label' => esc_html__( 'Display Type', 'scaffolding' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'overlay',
				'options' => [
					'overlay'  => esc_html__( 'Overlay', 'scaffolding' ),
					'slideout'  => esc_html__( 'Slide Out', 'scaffolding' ),
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'navpane_style',
			[
				'label' => __( 'Navigation Pane', 'scaffolding' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Background::get_type(),
			[
				'name' => 'navpane_background_options',
				'types' => [ 'classic', 'gradient' ],
				'selector' => '#navpane',
				'fields_options' => [
					'background' => [
						'frontend_available' => true,
					]
				],
			]
		);

		$this->end_controls_section();
	}
	/**
	 * Add markup attributes to the navpage element
	 *
	 * 1. Make sure the navpane is activated on single view
	 * 2. Add data element for CSS styling
	 *
	 * @param array $atts Array of markup attributes
	 */
	public function navpaneAtts( $atts ) : array
	{
		if ( is_singular( 'elementor_library' ) )
		{
			$document = ThemeBuilder::instance()->get_document( get_the_id() );

			if ( $document->get_location() === self::S_LOCATION_KEY )
			{
				$atts['class'] .= ' activated';
			}
		}

		if ( elementor_location_exits( self::S_LOCATION_KEY, true ) )
		{
			$atts['data-generator'] = 'elementor';
		}

		$settings = $this->get_settings_for_display();

		if ( isset( $settings['display_type'] ) && ! empty( $settings['display_type'] ) )
		{
			$atts['data-type'] = $settings['display_type'];
		}

		return $atts;
	}
}