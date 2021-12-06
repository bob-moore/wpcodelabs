<?php

/**
 * @class QueryEngine
 */
class SBoxTabs extends \FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __( 'Box Tabs', '_s' ),
			'description'   	=> __( '', '_s' ),
			'category'      	=> __( 'Theme Components', '_s' ),
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
			            'title'         => __('Contact & Description', '_s'), // Section Title
			            'fields'        => array(
			            	'tabs' => array(
			            	    'type'          => 'form',
			            	    'label'         => __( 'Tabs', '_s' ),
			            	    'form'          => '_s_box_tabs_form',
			            	    'preview_text'  => 'title',
			            	    'multiple'      => true,
			            	),
			            ),
			        ),
			    )
			),
		));

		\FLBuilder::register_settings_form( '_s_box_tabs_form' , array(
			'title' => __( 'Add Tab', '_s' ),
			'tabs'  => array(
				'general'       => array( // Tab
					'title'         => __( 'General', '_s' ), // Tab title
					'sections'      => array( // Tab Sections
						'general'       => array( // Section
							'title'         => '', // Section Title
							'fields'        => array( // Section Fields
								'title' => array(
								    'type'          => 'text',
								    'label'         => __( 'Tab Title', '_s' ),
								    'default'       => '',
								),
								'content' => array(
								    'type'          => 'editor',
								    'label'         => __( 'Content', 'fl-builder' ),
								    'media_buttons' => true,
								    'wpautop'       => true
								),
								'link' => array(
									'type'          => 'link',
									'label'         => 'Link',
									'show_target'	=> true,
									'show_nofollow'	=> true,
								),
								'linktext' => array(
								    'type'          => 'text',
								    'label'         => __( 'Link Text', 'fl-builder' ),
								    'default'       => '',
								),
								'linkicon' => array(
								    'type'          => 'icon',
								    'label'         => __( 'Link Icon', 'fl-builder' ),
								    'show_remove'   => true
								),
							),
						),
					),
				),
			),
		));
	}
}