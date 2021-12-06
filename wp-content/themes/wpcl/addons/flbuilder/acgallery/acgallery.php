<?php

/**
 * @class QueryEngine
 */
class ACGallery extends \FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __( 'Image Gallery', '_ac' ),
			'description'   	=> '',
			'category'      	=> __( 'Theme Components', '_ac' ),
			'editor_export' 	=> true,
			'partial_refresh'	=> true,
		));
	}

	private function get_image_sizes() {
		$sizes = get_intermediate_image_sizes();

		$settings = array();

		foreach( $sizes as $size ) {
			$settings[$size] = $size;
		}

		return $settings;
	}

	public function get_shortcode_args() {
		$args = '';

		if( !empty( $this->settings->cols ) ) {
			$args .= " columns='{$this->settings->cols}'";
		}
		if( !empty( $this->settings->orderby ) ) {
			$args .= " orderby='{$this->settings->orderby}'";
		}
		if( !empty( $this->settings->order ) ) {
			$args .= " order='{$this->settings->order}'";
		}
		if( !empty( $this->settings->size ) ) {
			$args .= " size='{$this->settings->size}'";
		}
		if( !empty( $this->settings->link_to ) ) {
			$args .= " link='{$this->settings->link_to}'";
		}
		if( !empty( $this->settings->photos ) ) {
			$ids = implode( ',', $this->settings->photos );

			$args .= " include='{$ids}'";
		}

		if( !empty( $this->settings->gallery_type ) ) {
			$args .= " type='{$this->settings->gallery_type}'";
		}

		$args = trim( $args );

		return $args;
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
			            'fields' => array(
			            	'photos' => array(
			            	    'type'          => 'multiple-photos',
			            	    'label'         => __( 'Multiple Photos Field', 'fl-builder' )
			            	),
			            	'link_to' => array(
			            	    'type'          => 'select',
			            	    'label'         => __( 'Link To', 'fl-builder' ),
			            	    'default'       => 'none',
			            	    'options'       => array(
			            	        'none'      => __( 'None', 'fl-builder' ),
			            	        'media'      => __( 'Media File', 'fl-builder' ),
			            	        'attachment'      => __( 'Attachment Page', 'fl-builder' )
			            	    ),
			            	),
			            	'cols' => array(
			            		'type'        => 'unit',
			            		'label'       => 'Columns',
			            		'description' => 'columns',
			            		'default'     => 3,
			            		'slider' => array(
			            			'min'  	=> 1,
			            			'max'  	=> 9,
			            			'step' 	=> 1,
			            		),
			            	),
			            	'orderby' => array(
			            	    'type'          => 'select',
			            	    'label'         => __( 'Order By', 'fl-builder' ),
			            	    'default'       => 'menu_order',
			            	    'options'       => array(
			            	        'menu_order'      => __( 'Menu Order', 'fl-builder' ),
			            	        'title'      => __( 'Title', 'fl-builder' ),
			            	        'post_date'      => __( 'Date', 'fl-builder' ),
			            	        'ID'      => __( 'ID', 'fl-builder' ),
			            	        'rand'      => __( 'Random', 'fl-builder' ),
			            	    ),
			            	),
			            	'order' => array(
			            		'type'    => 'button-group',
			            		'label'   => 'Order',
			            		'default' => 'ASC',
			            		'options' => array(
			            			'ASC'    => 'ASC',
			            			'DESC'    => 'DESC',
			            		),
			            	),
			            	'size' => array(
			            	    'type'          => 'select',
			            	    'label'         => __( 'Image Size', 'fl-builder' ),
			            	    'default'       => 'none',
			            	    'options'       => $this->get_image_sizes(),
			            	),
			            	'gallery_type' => array(
			            	    'type'          => 'select',
			            	    'label'         => __( 'Type', 'fl-builder' ),
			            	    'default'       => 'default',
			            	    'options'       => array(
			            	        'default'      => __( 'Default Grid', 'fl-builder' ),
			            	        'rectangular'      => __( 'Tiled Rectangular', 'fl-builder' ),
			            	        'square'      => __( 'Tiled Square', 'fl-builder' ),
			            	        'columns'      => __( 'Tiled Columns', 'fl-builder' ),
			            	        'circle'      => __( 'Circle', 'fl-builder' ),
			            	        'slider'     => 'Slider',
			            	        // 'block'      => __( 'Block', 'fl-builder' ),
			            	    ),
			            	    'toggle'  => array(
			            	    	'slider' => array(
			            	    		'tabs' => array( 'style' ),
			            	    	),
			            	    ),
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
			            	'centermode' => array(
			            		'type'    => 'button-group',
			            		'label'   => 'CenterMode',
			            		'default' => '1',
			            		'options' => array(
			            			'1'    => 'Enabled',
			            			'0'    => 'Disabled',
			            		),
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
			            	'icon' => array(
			            		'type'    => 'button-group',
			            		'label'   => 'Include Inspect Icon',
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
	}
}