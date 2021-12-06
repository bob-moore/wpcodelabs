<?php

/**
 * @class QueryEngine
 */
class ACImageSlider extends \FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __( 'Image Slider', '_ac' ),
			'description'   	=> '',
			'category'      	=> __( 'Theme Components', '_ac' ),
			'editor_export' 	=> true,
			'partial_refresh'	=> true,
		));
	}

	public function get_image_sizes() {

		$sizes = get_intermediate_image_sizes();

		$options = array();

		foreach( $sizes as $size ) {
			$options[$size] = $size;
		}
		return $options;
	}

	/**
	 * Register the module and its form settings.
	 */
	public function register_module() {
		\FLBuilder::register_module( __CLASS__, array(
			'general'       => array( // Tab
			    'title'         => __('General', '_ac'), // Tab title
			    'sections'      => array( // Tab Sections
			        'general'       => array( // Section
			            'title'         => __('General Options', '_ac'), // Section Title
			            'fields'        => array(
			            	'images' => array(
			            		'type'          => 'form',
			            		'label'         => __( 'Photos', '_ac' ),
			            		'form'          => 'ac_slider_image_form',
			            		'preview_text'  => 'title',
			            		'multiple'      => true,
			            	),
			            ),
			        ),
			    )
			),
			'style'       => array( // Tab
			    'title'         => __('Style', '_ac'), // Tab title
			    'sections'      => array( // Tab Sections
			        'display'       => array( // Section
			            'title'         => __('Display Options', '_ac'), // Section Title
			            'fields'        => array(
			            	'centermode' => array(
			            		'type'    => 'button-group',
			            		'label'   => 'CenterMode',
			            		'default' => '1',
			            		'options' => array(
			            			'1'    => 'Enabled',
			            			'0'    => 'Disabled',
			            		),
			            	),
			            	'slides_to_show' => array(
			            		'type'        => 'unit',
			            		'label'       => 'Slides to Show',
			            		'default'     => 3,
			            		'responsive'  => true,
			            	),
			            	'speed' => array(
			            		'type'        => 'unit',
			            		'default'     => 300,
			            		'label'       => 'Animation Speed',
			            	),
			            	'autoplaySpeed' => array(
			            		'type'        => 'unit',
			            		'default'     => 3000,
			            		'label'       => 'Play Speed',
			            	),
			            	'dots' => array(
			            		'type'    => 'button-group',
			            		'label'   => 'Dots',
			            		'default' => '1',
			            		'options' => array(
			            			'1'    => 'Enabled',
			            			'0'    => 'Disabled',
			            		),
			            	),
			            	'arrows' => array(
			            		'type'    => 'button-group',
			            		'label'   => 'Arrows',
			            		'default' => '1',
			            		'options' => array(
			            			'1'    => 'Enabled',
			            			'0'    => 'Disabled',
			            		),
			            	),
			            	'autoplay' => array(
			            		'type'    => 'button-group',
			            		'label'   => 'Autoplay',
			            		'default' => '1',
			            		'options' => array(
			            			'1'    => 'Enabled',
			            			'0'    => 'Disabled',
			            		),
			            	),
			            ),
			        ),
			    )
			),
		));

		\FLBuilder::register_settings_form( 'ac_slider_image_form' , array(
			'title' => __( 'Add Image', '_s' ),
			'tabs'  => array(
				'general'       => array( // Tab
					'title'         => __( 'General', 'wpcl_beaver_extender' ), // Tab title
					'sections'      => array( // Tab Sections
						'general'       => array( // Section
							'title'         => '', // Section Title
							'fields'        => array( // Section Fields
								'title' => array(
									'type'          => 'text',
									'label'         => __('Title', '_ac'),
								),
								'subtitle' => array(
									'type'          => 'text',
									'label'         => __('Sub Title', '_ac'),
								),
								'discription' => array(
									'type'          => 'text',
									'label'         => __('Discription', '_ac'),
								),
								'photo' => array(
									'type'          => 'photo',
									'label'         => __('Photo', '_ac'),
									'show_remove'   => true,
								),
								'link' => array(
									'type'          => 'link',
									'label'         => 'Link',
									'show_target'	=> true,
									'show_nofollow'	=> true,
								),
							),
						),
					),
				),
			),
		));
	}
}