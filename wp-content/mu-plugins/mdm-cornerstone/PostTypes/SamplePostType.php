<?php
/**
 * "Sample Post Type" post type class
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/posttypes
 */
namespace Mdm\Cornerstone\PostTypes;

use \Mdm\Cornerstone\Framework;
use \Mdm\Cornerstone\Subscriber;

defined( 'ABSPATH' ) || exit;

class SamplePostType extends Framework
{
	/**
	 * Name of the custom post type
	 *
	 * @var (string) NAME : name of the post type
	 */
	const NAME = 'sample-post-type';
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 */
	public function addActions() : void
	{
		// Uncomment to use
		// Subscriber::addAction( 'init', [$this, 'register'] );
	}
	/**
	 * Register custom post type
	 *
	 * I recommend using a tool such as GenerateWP to easily generate post type arguments
	 *
	 * @see https://generatewp.com/post-type/
	 */
	public function register() : void
	{
		$labels = [
			'name'                  => _x( 'Help Docs', 'Post Type General Name', 'mdm_cornerstone' ),
			'singular_name'         => _x( 'Help Doc', 'Post Type Singular Name', 'mdm_cornerstone' ),
			'menu_name'             => __( 'Help Docs', 'mdm_cornerstone' ),
			'name_admin_bar'        => __( 'Help Doc', 'mdm_cornerstone' ),
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
		];
		$rewrite = [
			'slug'                  => 'docs',
			'with_front'            => false,
			'pages'                 => true,
			'feeds'                 => true,
		];
		$args = [
			'label'                 => __( 'Help Doc', 'mdm_cornerstone' ),
			'description'           => __( 'Documentation Article', 'mdm_cornerstone' ),
			'labels'                => $labels,
			'supports'              => array( 'title', 'editor', 'revisions', 'custom-fields', 'page-attributes' ),
			'taxonomies'            => array( 'doc-category' ),
			'hierarchical'          => true,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 5,
			'menu_icon'             => 'dashicons-media-document',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
			'show_in_rest'               => true,
			'rewrite'               => $rewrite,
		];
		register_post_type( self::NAME, $args );
	}
}