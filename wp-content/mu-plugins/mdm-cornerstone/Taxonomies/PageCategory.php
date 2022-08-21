<?php
/**
 * "Page Category" taxonomy class
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/taxonomies
 */

namespace Mdm\Cornerstone\taxonomies;

use \Mdm\Cornerstone\Framework;
use \Mdm\Cornerstone\Subscriber;

defined( 'ABSPATH' ) || exit;

class PageCategory extends Framework {
	/**
	 * Name of the custom taxonomy
	 *
	 * @var string NAME - name fo the taxonomy
	 */
	const NAME = 'page-category';
	/**
	 * Name of the custom taxonomy
	 *
	 * @var array POST_TYPES array of post types this taxonomy belongs to
	 */
	const POST_TYPES = ['page'];
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'init', [$this, 'register'] );
	}
	/**
	 * Register custom taxonomy
	 *
	 * I recommend using a tool such as GenerateWP to easily generate taxonomy arguments
	 *
	 * @see  https://generatewp.com/taxonomy/
	 */
	public function register() : void
	{
		$labels = [
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
		];
		$args = [
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => true,
			'show_in_rest'               => true,
			'post_types'                 => array( 'page' ),
		];

		register_taxonomy( self::NAME, self::POST_TYPES, $args );

		foreach ( self::POST_TYPES as $type )
		{
			register_taxonomy_for_object_type( self::NAME, $type );
		}
	}
}