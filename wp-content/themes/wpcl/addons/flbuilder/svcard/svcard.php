<?php

/**
 * @class QueryEngine
 */
class SVCard extends \FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __( 'VCard', '_s' ),
			'description'   	=> '',
			'category'      	=> __( 'Layouts', '_s' ),
			'editor_export' 	=> true,
			'partial_refresh'	=> true,
		));
	}
	/**
	 * Update data before saving
	 *
	 * Formats the settings passed from beaver builder, into a usable settings
	 * Sets a rel setting for frontend.php
	 * Sets scss variables for frontend.css.php
	 *
	 * @param  [object] $settings : all settings passed from beaver builder
	 * @since version 1.0.0
	 */
	public function update( $settings ) {

		/**
		 * Link Settings
		 */
		if( !empty( $settings->address_link ) ) {
			$settings->address_link_rel  = $settings->address_link_target === '_blank' ? ' noopener noreferrer' : '';
			$settings->address_link_rel .= $settings->address_link_nofollow === 'yes' ? ' nofollow' : '';
		}

		if( !empty( $settings->phone ) ) {

			$phone = preg_replace( '/[^0-9]/', '', $settings->phone );

			if(strlen($phone) === 10) {
				$settings->phone_link = 'tel:+1' . $phone;
			}

			else {
				$settings->phone_link = 'tel:' . $phone;
			}

		}

		if( !empty( $settings->email ) ) {
			$settings->email_link = 'mailto:' . sanitize_email( $settings->email );
		}

		return $settings;
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
			            	'photo' => array(
			            	    'type'          => 'photo',
			            	    'label'         => __('Photo', 'fl-builder'),
			            	    'show_remove'   => true,
			            	),
			            	'phone' => array(
			            	    'type'          => 'text',
			            	    'label'         => __( 'Phone', 'fl-builder' ),
			            	    'default'       => '',
			            	),
			            	'email' => array(
			            	    'type'          => 'text',
			            	    'label'         => __( 'Email', 'fl-builder' ),
			            	    'default'       => '',
			            	),
			            	'address' => array(
			            	    'type'          => 'textarea',
			            	    'label'         => __( 'Address', 'fl-builder' ),
			            	    'default'       => '',
			            	    'rows'          => '6'
			            	),
			            	'address_link' => array(
			            		'type'          => 'link',
			            		'label'         => 'Address Link',
			            		'show_target'	=> true,
			            		'show_nofollow'	=> true,
			            	),
			            ),
			        ),
			    )
			),
		));
	}
}