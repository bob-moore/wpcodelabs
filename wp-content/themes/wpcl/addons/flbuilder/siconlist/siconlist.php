<?php

/**
 * @class QueryEngine
 */
class SIconList extends \FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'          	=> __( 'Icon List', '_s' ),
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

		$default_bullet_type = $settings->bullet_type;

		foreach( $settings->rows as $index => $row ) {

			$bullet_type = $row->bullet_type;

			if( !empty( $bullet_type ) && ( !empty( $row->$bullet_type ) ) ) {

				if( $bullet_type === 'icon' ) {
					$settings->rows[$index]->bullet = sprintf( '<span class="list-icon %s"></span>', $row->icon );
				}
				else {
					$settings->rows[$index]->bullet = sprintf( '<img class="list-icon" src="%s">', $row->image_src );
				}
			}

			elseif( !empty( $settings->$default_bullet_type ) ) {

				if( $default_bullet_type === 'icon' ) {
					$settings->rows[$index]->bullet = sprintf( '<span class="list-icon %s"></span>', $settings->icon );
				}
				else {
					$settings->rows[$index]->bullet = sprintf( '<img class="list-icon" src="%s">', $settings->image_src );
				}
			}
		}

		return $settings;
	}


	/**
	 * Register the module and its form settings.
	 */
	public function register_module() {
		\FLBuilder::register_module( __CLASS__, array(
			'general'       => array( // Tab
				'title'         => __( 'General', 'wpcl_beaver_extender' ), // Tab title
				'sections'      => array( // Tab Sections
					'general'       => array( // Section
						'title'         => '', // Section Title
						'fields'        => array( // Section Fields
							'bullet_type' => array(
							    'type'          => 'select',
							    'label'         => __( 'Bullet Type', 'fl-builder' ),
							    'default'       => 'icon',
							    'options'       => array(
							        'icon'      => __( 'Icon', 'fl-builder' ),
							        'image'      => __( 'Image', 'fl-builder' )
							    ),
							    'toggle'        => array(
							        'icon'      => array(
							            'fields'        => array( 'icon',),
							        ),
							        'image'      => array(
							            'fields'        => array( 'image',),
							        ),
							    )
							),
							'image' => array(
							    'type'          => 'photo',
							    'label'         => __('Image', 'fl-builder'),
							    'show_remove'   => true,
							),
							'icon' => array(
							    'type'          => 'icon',
							    'label'         => 'List Icon',
							    'show_remove'   => true
							),
							'columns' => array(
								'type'        => 'unit',
								'label'       => 'Columns',
								'units'	       => array( 'cols'),
								'default_unit' => 'cols', // Optional
								'default'     => 1,
								'responsive'  => true,
							),
							'rows' => array(
							    'type'          => 'form',
							    'label'         => __( 'List Item', 'wpcl_beaver_extender' ),
							    'form'   => 's_icon_list_item',
							    'preview_text'  => 'content',
							    'multiple'     => true,
							),
						),
					),
				),
			),
		) );
		/**
		 * Register a settings form to use in the "form" field type above.
		 */
		\FLBuilder::register_settings_form( 's_icon_list_item' , array(
			'title' => __( 'Add List Item', 'fl-builder' ),
			'tabs'  => array(
				'general'       => array( // Tab
					'title'         => __( 'General', '_s' ), // Tab title
					'sections'      => array( // Tab Sections
						'general'       => array( // Section
							'title'         => '', // Section Title
							'fields'        => array( // Section Fields
								'bullet_type' => array(
								    'type'          => 'select',
								    'label'         => __( 'Bullet Type', 'fl-builder' ),
								    'default'       => '',
								    'options'       => array(
								    	''      =>  __( 'Default', 'fl-builder' ),
								        'icon'      => __( 'Icon', 'fl-builder' ),
								        'image'      => __( 'Image', 'fl-builder' )
								    ),
								    'toggle'        => array(
								        'icon'      => array(
								            'fields'        => array( 'icon',),
								        ),
								        'image'      => array(
								            'fields'        => array( 'image',),
								        ),
								    )
								),
								'image' => array(
								    'type'          => 'photo',
								    'label'         => __('Image', 'fl-builder'),
								    'show_remove'   => true,
								),
								'icon' => array(
								    'type'          => 'icon',
								    'label'         => 'List Icon',
								    'show_remove'   => true
								),
								'content'          => array(
									'label'         => 'Content',
									'type'          => 'editor',
									'media_buttons' => false,
									'wpautop'       => true,
									'preview'         => array(
										'type'            => 'refresh',
									),
								),
							),
						),
					),
				),
			),
		) );
	}
}