<?php

/**
 * @class QueryEngine
 */
class STemplatePart extends \FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __( 'Template Partial', '_s' ),
			'description'   	=> __( 'Insert a standard template part', '_s' ),
			'category'      	=> __( 'Layouts', '_s' ),
			'editor_export' 	=> true,
			'partial_refresh'	=> true,
		));
	}

	/**
	 * Register the module and its form settings.
	 */
	public function register_module() {
		\FLBuilder::register_module( __CLASS__, array(
			'general'       => array( // Tab
			    'title'         => __('General', '_s'), // Tab title
			    'sections'      => array( // Tab Sections
			        'general'       => array( // Section
			            'title'         => __('General Options', '_s'), // Section Title
			            'fields'        => array(
			            	'template_part' => array(
			            	    'type'          => 'select',
			            	    'label'         => __( 'Template Part', 'fl-builder' ),
			            	    'default'       => '',
			            	    'options'       => array(
			            	        'blocks/jumbotron'      => __( 'Jumbotron', '_s' ),
			            	    ),
			            	    'toggle'        => array(
			            	        'blocks/jumbotron' => array(
			            	            'sections' => array( 'jumbotron' ),
			            	        ),
			            	    )
			            	),
			            ),
			        ),
			        'jumbotron'       => array( // Section
			            'title'         => __('Jumbotron Options', '_s'), // Section Title
			            'fields'        => array(
			            	'jumbotron_background' => array(
			            	    'type'          => 'photo',
			            	    'label'         => __('Background Image', 'fl-builder'),
			            	    'show_remove'   => true,
			            	),
			            	'jumbotron_background_override' => array(
			            		'type'    => 'button-group',
			            		'label'   => 'Allow Override on Individual Posts',
			            		'default' => '1',
			            		'options' => array(
			            			'1'    => 'Enabled',
			            			'0'    => 'Disabled',
			            		),
			            	),
			            	'jumbotron_include_jumblink' => array(
			            		'type'    => 'button-group',
			            		'label'   => 'Jumplink',
			            		'default' => '0',
			            		'options' => array(
			            			'0'    => 'Disabled',
			            			'1'    => 'Enabled',
			            		),
			            		'toggle'        => array(
			            		    '1' => array(
			            		        'fields' => array( 'jumbotron_jumplink_offset', 'jumbotron_jumplink_link' ),
			            		    ),
			            		),
			            	),
			            	'jumbotron_jumplink_offset' => array(
			            		'type'         => 'unit',
			            		'label'        => 'Top Offset',
			            		'units'	       => array( 'px'),
			            		'default'      => '0',
			            	),
			            	'jumbotron_jumplink_link' => array(
			            	    'type'          => 'text',
			            	    'label'         => __( 'Jumplink Link', 'fl-builder' ),
			            	    'default'       => '#main',
			            	),
			            	'jumbotron_photo' => array(
			            	    'type'          => 'photo',
			            	    'label'         => __('Prefix Photo', 'fl-builder'),
			            	    'show_remove'   => true,
			            	),
			            	'jumbotron_prefix' => array(
			            	    'type'          => 'text',
			            	    'label'         => __( 'Heading Prefix', 'fl-builder' ),
			            	    'default'       => '',
			            	),
			            	'jumbotron_headline' => array(
			            	    'type'          => 'text',
			            	    'label'         => __( 'Headline', 'fl-builder' ),
			            	    'default'       => '',
			            	),
			            	'jumbotron_content' => array(
			            	    'type'          => 'editor',
			            	    'media_buttons' => true,
			            	    'wpautop'       => true,
			            	    'label'         => __( 'Content', 'fl-builder' ),
			            	    'default'       => '',
			            	),


			            ),
			        ),
			    )
			),
		));
	}
}