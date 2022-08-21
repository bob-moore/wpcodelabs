<?php
namespace Devkit\CustomLayouts\Taxonomies;

use \Devkit\CustomLayouts\Framework;
use \Devkit\CustomLayouts\Subscriber;

class Groups extends Framework {
	/**
	 * Name of the custom taxonomy
	 * @since 1.0.0
	 */
	const NAME = 'dkcl-group';
	/**
	 * Name of the custom taxonomy
	 * @since 1.0.0
	 */
	const POST_TYPES = [ 'dk-custom-layout' ];
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addActions() {
		/**
		 * The action `carbon_fields_register_fields` is triggered on the init hook, with a priority of 0.
		 * Which means that we have to register taxonomies and post types earlier than init, if we want
		 * to check them during field creation
		 */
		Subscriber::addAction( 'carbon_fields_register_fields', [$this, 'register'], 5 );
	}
	/**
	 * Register custom taxonomy
	 *
	 * I recommend using a tool such as GenerateWP to easily generate taxonomy arguments
	 *
	 * @see  https://generatewp.com/taxonomy/
	 * @since 1.0.0
	 */
	public function register() {
		$labels = [
			'name'                       => _x( 'Layout Groups', 'Taxonomy General Name', 'mwf' ),
			'singular_name'              => _x( 'Layout Group', 'Taxonomy Singular Name', 'mwf' ),
			'menu_name'                  => __( 'Layout Groups', 'mwf' ),
			'all_items'                  => __( 'All Items', 'mwf' ),
			'parent_item'                => __( 'Parent Item', 'mwf' ),
			'parent_item_colon'          => __( 'Parent Item:', 'mwf' ),
			'new_item_name'              => __( 'New Item Name', 'mwf' ),
			'add_new_item'               => __( 'Add New Item', 'mwf' ),
			'edit_item'                  => __( 'Edit Item', 'mwf' ),
			'update_item'                => __( 'Update Item', 'mwf' ),
			'view_item'                  => __( 'View Item', 'mwf' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'mwf' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'mwf' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'mwf' ),
			'popular_items'              => __( 'Popular Items', 'mwf' ),
			'search_items'               => __( 'Search Items', 'mwf' ),
			'not_found'                  => __( 'Not Found', 'mwf' ),
			'no_terms'                   => __( 'No items', 'mwf' ),
			'items_list'                 => __( 'Items list', 'mwf' ),
			'items_list_navigation'      => __( 'Items list navigation', 'mwf' ),
		];
		$args = [
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => false,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => false,
			'show_tagcloud'              => false,
			'show_in_rest'               => true,
			'post_types'                 => self::POST_TYPES,
		];

		register_taxonomy( self::NAME, self::POST_TYPES, $args );

		foreach ( self::POST_TYPES as $type ) {
			register_taxonomy_for_object_type( self::NAME, $type );
		}
	}
}