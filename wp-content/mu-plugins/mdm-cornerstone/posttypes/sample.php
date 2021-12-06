<?php

namespace mdm\cornerstone\posttypes;

use \mdm\cornerstone\Framework;

class Sample extends Framework {

	/**
	 * Get post type arguments
	 *
	 * I recommend using a tool such as GenerateWP to easily generate post type arguments
	 *
	 * @see https://generatewp.com/post-type/
	 * @since 1.0.0
	 */
	public function register_post_type() {
		$labels = array(
			'name'                  => _x( 'Samples', 'Post Type General Name', 'mdm_cornerstone' ),
			'singular_name'         => _x( 'Sample', 'Post Type Singular Name', 'mdm_cornerstone' ),
			'menu_name'             => __( 'Sample Post Type', 'mdm_cornerstone' ),
			'name_admin_bar'        => __( 'Sample Post Type', 'mdm_cornerstone' ),
			'archives'              => __( 'Item Archives', 'mdm_cornerstone' ),
			'attributes'            => __( 'Item Attributes', 'mdm_cornerstone' ),
			'parent_item_colon'     => __( 'Parent Item:', 'mdm_cornerstone' ),
			'all_items'             => __( 'All Items', 'mdm_cornerstone' ),
			'add_new_item'          => __( 'Add New Item', 'mdm_cornerstone' ),
			'add_new'               => __( 'Add New', 'mdm_cornerstone' ),
			'new_item'              => __( 'New Item', 'mdm_cornerstone' ),
			'edit_item'             => __( 'Edit Item', 'mdm_cornerstone' ),
			'update_item'           => __( 'Update Item', 'mdm_cornerstone' ),
			'view_item'             => __( 'View Item', 'mdm_cornerstone' ),
			'view_items'            => __( 'View Items', 'mdm_cornerstone' ),
			'search_items'          => __( 'Search Item', 'mdm_cornerstone' ),
			'not_found'             => __( 'Not found', 'mdm_cornerstone' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'mdm_cornerstone' ),
			'featured_image'        => __( 'Featured Image', 'mdm_cornerstone' ),
			'set_featured_image'    => __( 'Set featured image', 'mdm_cornerstone' ),
			'remove_featured_image' => __( 'Remove featured image', 'mdm_cornerstone' ),
			'use_featured_image'    => __( 'Use as featured image', 'mdm_cornerstone' ),
			'insert_into_item'      => __( 'Insert into item', 'mdm_cornerstone' ),
			'uploaded_to_this_item' => __( 'Uploaded to this item', 'mdm_cornerstone' ),
			'items_list'            => __( 'Items list', 'mdm_cornerstone' ),
			'items_list_navigation' => __( 'Items list navigation', 'mdm_cornerstone' ),
			'filter_items_list'     => __( 'Filter items list', 'mdm_cornerstone' ),
		);
		$rewrite = array(
			'slug'                  => 'samples',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		);
		$args = array(
			'label'                 => __( 'Sample', 'mdm_cornerstone' ),
			'description'           => __( 'Post Type Description', 'mdm_cornerstone' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'author', 'thumbnail', 'revisions', 'excerpt', 'genesis-seo', 'genesis-cpt-archives-settings', 'genesis-layouts', 'genesis-scripts' ),
			'taxonomies'            => array( 'category', 'post_tag', 'sample' ),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-admin-post',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'rewrite'               => $rewrite,
		);
		// Uncomment to register post type

		// register_post_type( 'sample', $args );
	}
}