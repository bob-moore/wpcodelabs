<?php

namespace mdm\cornerstone\Taxonomies;

class Sample extends \mdm\cornerstone\Plugin {

	/**
	 * Get taxomony arguments
	 *
	 * I recommend using a tool such as GenerateWP to easily generate taxonomy arguments
	 *
	 * @see  https://generatewp.com/taxonomy/
	 * @since 1.0.0
	 */
	public static function get_tax_args() {
		$labels = array(
			'name'                       => _x( 'Sample Taxonomies', 'Taxonomy General Name', 'mdm_cornerstone' ),
			'singular_name'              => _x( 'Sample Taxonomy', 'Taxonomy Singular Name', 'mdm_cornerstone' ),
			'menu_name'                  => __( 'Sample Taxonomy', 'mdm_cornerstone' ),
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
			'post_types'                 => array( 'sample' ),
		);
		return $args;
	}
	/**
	 * Get taxomony arguments
	 *
	 * I recommend using a tool such as GenerateWP to easily generate taxonomy arguments
	 *
	 * @see  https://developer.wordpress.org/reference/functions/register_taxonomy/
	 * @since 1.0.0
	 */
	public function register_taxonomy() {
		register_taxonomy( 'sample', $post_types, $tax::get_tax_args() );
	}

	/**
	 * Return the post type(s) this taxonomy should attach to
	 *
	 * @return array : Array of all post types this taxonomy belongs to
	 */
	public static function get_tax_post_types() {
		return array( 'sample' );
	}
}