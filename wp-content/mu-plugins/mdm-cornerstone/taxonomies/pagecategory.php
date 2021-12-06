<?php

namespace mdm\cornerstone\taxonomies;

use \mdm\cornerstone\Framework;

class PageCategory extends Framework {

	/**
	 * Get taxomony arguments
	 *
	 * I recommend using a tool such as GenerateWP to easily generate taxonomy arguments
	 *
	 * @see  https://generatewp.com/taxonomy/
	 * @since 1.0.0
	 */
	public function register_taxonomy() {
		$labels = array(
			'name'                       => _x( 'Page Categories', 'Taxonomy General Name', 'mdm_cornerstone' ),
			'singular_name'              => _x( 'Page Category', 'Taxonomy Singular Name', 'mdm_cornerstone' ),
			'menu_name'                  => __( 'Page Categories', 'mdm_cornerstone' ),
			'all_items'                  => __( 'All Items', 'mdm_cornerstone' ),
			'parent_item'                => __( 'Parent Item', 'mdm_cornerstone' ),
			'parent_item_colon'          => __( 'Parent Item:', 'mdm_cornerstone' ),
			'new_item_name'              => __( 'New Item Name', 'mdm_cornerstone' ),
			'add_new_item'               => __( 'Add New Item', 'mdm_cornerstone' ),
			'edit_item'                  => __( 'Edit Item', 'mdm_cornerstone' ),
			'update_item'                => __( 'Update Item', 'mdm_cornerstone' ),
			'view_item'                  => __( 'View Item', 'mdm_cornerstone' ),
			'separate_items_with_commas' => __( 'Separate items with commas', 'mdm_cornerstone' ),
			'add_or_remove_items'        => __( 'Add or remove items', 'mdm_cornerstone' ),
			'choose_from_most_used'      => __( 'Choose from the most used', 'mdm_cornerstone' ),
			'popular_items'              => __( 'Popular Items', 'mdm_cornerstone' ),
			'search_items'               => __( 'Search Items', 'mdm_cornerstone' ),
			'not_found'                  => __( 'Not Found', 'mdm_cornerstone' ),
			'no_terms'                   => __( 'No items', 'mdm_cornerstone' ),
			'items_list'                 => __( 'Items list', 'mdm_cornerstone' ),
			'items_list_navigation'      => __( 'Items list navigation', 'mdm_cornerstone' ),
		);
		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
			'show_in_rest'               => true,
			'post_types'                 => array( 'page' ),
		);

		register_taxonomy( 'pagecategory', array( 'page' ), $args );

		register_taxonomy_for_object_type( 'pagecategory', 'page' );
	}
}