<?php

/**
 * Sample Post Type
 *
 * @link    https://www.wpcodelabs.com
 * @since   1.0.0
 * @package plugin_scaffolding
 */

namespace Devkit\CustomLayouts\PostTypes;

use \Devkit\CustomLayouts\Subscriber;
use \Devkit\CustomLayouts\Plugin;

class CustomLayout {
	/**
	 * Name of the custom post type
	 * @since 1.0.0
	 */
	const NAME = 'dk-custom-layout';
	/**
	 * Post ID
	 *
	 * @var int
	 */
	public $id = 0;
	/**
	 * Actions this is attached to
	 *
	 * @var array
	 */
	public $actions = [];
	/**
	 * Type of template part : editor, code, partial
	 *
	 * @var string
	 */
	public $type = 'editor';
	/**
	 * Display conditions
	 *
	 * @var exclude
	 */
	public $conditions = [
		'include' => [],
		'exclude' => []
	];
	/**
	 * Container type
	 *
	 * @var string
	 */
	public $container = 'div';
	/**
	 * Container classes
	 *
	 * @var string
	 */
	public $classes = '';
	/**
	 * Partial (template part) to render
	 *
	 * @var string - name of template part
	 */
	public $partial;
	/**
	 * Code to render
	 *
	 * @var string - raw code
	 */
	public $code = '';
	/**
	 * CSS to render
	 *
	 * @var string - raw code
	 */
	public $css = '';
	/**
	 * JS to render
	 *
	 * @var string - raw code
	 */
	public $js = '';
	/**
	 * Unprocessed layout metadata
	 *
	 * @var array - unprocessed metadata to hold
	 */
	public $meta = [];
	/**
	 * Constructer
	 *
	 * @param int $id - Post id of instance
	 * @return $this
	 */
	public function __construct( $id )
	{
		if ( get_post_type( $id ) !== self::NAME || get_post_status( $id ) !== 'publish' )
		{
			return false;
		}

		$this->id = $id;

		$this->setMeta();

		$this->setActions();

		$this->setConditions();

		foreach ( [ 'code', 'partial', 'classes', 'container', 'type', 'css', 'js'] as $property )
		{
			if ( property_exists( $this, $property ) && isset( $this->meta["cl_{$property}"] ) )
			{
				$this->{$property} = $this->meta["cl_{$property}"];
			}
		}

		return $this;
	}
	/**
	 * Set the meta object field
	 *
	 * Get the post meta, and merge with defaults
	 *
	 * @access protected
	 * @return void
	 */
	protected function setMeta()
	{
		$meta = get_post_meta( $this->id, 'layout_meta', true );

		$meta = is_array( $meta ) ? $meta : [];

		$this->meta = wp_parse_args( $meta, [
			'cl_actions' => [],
			'cl_classes' => '',
			'cl_code' => '',
			'cl_container' => 'div',
			'cl_display_conditions' => [],
			'cl_partial' => '',
			'cl_css' => '',
			'cl_js' => '',
			'cl_type' => 'editor',
		] );
	}
	/**
	 * Set all actions to hook to
	 *
	 * Get post meta for action/priority hooks at sets them
	 *
	 * @access protected
	 * @return void
	 */
	protected function setActions()
	{
		foreach ( $this->meta['cl_actions'] as $action )
		{
			$this->actions[ $action['action'] ] = [
				'priority' => $action['priority'],
				'disable' => '',
			];
			switch ( $action['override'] )
			{
				case 'select':
					$key = bin2hex( $action['action'] );

					if ( isset( $action[ "override_{$key}" ] ) )
					{
						$this->actions[ $action['action'] ]['disable'] = 'select';
						$this->actions[ $action['action'] ]['disabled'] = $action[ "override_{$key}" ];
					}
					break;
				case '1' :
					$this->actions[ $action['action'] ]['disable'] = 'all';
					break;
				case 'layouts' :
					$this->actions[ $action['action'] ]['disable'] = 'layouts';
					break;
				case 'group' :
					$this->actions[ $action['action'] ]['disable'] = 'group';
					$this->actions[ $action['action'] ]['disabled'] = $this->getGroupMembers();
					break;
				default:
					// code...
					break;
			}
		}
	}
	/**
	 * Get other posts in the same group
	 *
	 * @access protected
	 * @return array of post ID's belonging to the same group taxonomies
	 */
	protected function getGroupMembers()
	{
		$terms = get_the_terms( $this->id, 'dkcl-group' );

		$members = [];

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) )
		{
			foreach ( $terms as $term )
			{
				$posts = get_posts(
					[
						'posts_per_page' => -1,
						'post_type' => [ 'custom-layout' ],
						'fields' => 'ids',
						'post__not_in' => $this->id,
						'tax_query' => [
							[
								'taxonomy' => 'dkcl-group',
								'terms'    => $term->term_taxonomy_id,
							]
						]
					]
				);
				$members = array_merge( $members, $posts );
			}
		}
		return $members;
	}
	/**
	 * Set all conditions
	 *
	 * Gets post meta for display conditions, and sets parsed rules to object
	 *
	 * @access protected
	 * @return void
	 */
	protected function setConditions()
	{
		if ( ! empty( $this->meta['cl_display_conditions'] ) )
		{
			$this->conditions = $this->getConditionGroups( $this->meta['cl_display_conditions'] );
		}
	}
	/**
	 * Get & parse display conditions
	 *
	 * Parses groups of conditions into the simplest usable format needed for display
	 *
	 * @access protected
	 * @param array $conditions : Array containing all conditionals form the post meta
	 * @return array $groups : Array containing parsed conditions containing only relevant
	 */
	protected function getConditionGroups( $conditions )
	{
		$groups = [
			'include' => [],
			'exclude' => [],
		];

		foreach ( $conditions as $condition )
		{
			if ( ! isset( $condition['cl_condition_group'] ) || empty( $condition['cl_condition_group'] ) )
			{
				continue;
			}

			$group_rules = [];

			foreach ( $condition['cl_condition_group'] as $group )
			{
				$single_rule = [
					'stack' => $condition['cl_condition_type'],
					'type' => $group['cl_condition']
				];

				$subtype = isset ( $group["cl_condition_{$group['cl_condition']}_subtype"] ) ? $group["cl_condition_{$group['cl_condition']}_subtype"] : '';

				if ( ! empty( $subtype ) )
				{
					$single_rule['subtype'] = $subtype;
					$key = 'cl_condition_' . $group['cl_condition'] . '_' . $subtype;

					if ( isset( $group[$key] ) )
					{
						if ( $subtype === 'term' )
						{
							$single_rule[$subtype] = [];
							foreach( $group[$key] as $term )
							{
								$term_object = get_term_by( 'term_taxonomy_id', $term );

								if ( is_a( $term_object, 'WP_Term' ) ) {
									$single_rule[$subtype][] = [ 'id' => $term, 'taxonomy' => $term_object->taxonomy ];
								}
							}
						}
						else
						{
							$single_rule[$subtype] = $group[$key];
						}
					}
				}
				$group_rules[] = $single_rule;
			}
			$groups[ $condition['cl_condition_type']][] = $group_rules;
		}

		return $groups;
	}
	/**
	 * Register custom post type
	 *
	 * @see https://generatewp.com/post-type/
	 * @return void
	 */
	public static function register() {
		$labels = [
			'name'                  => _x( 'Custom Layouts', 'Post Type General Name', 'mwf_custom_layouts' ),
			'singular_name'         => _x( 'Custom Layout', 'Post Type Singular Name', 'mwf_custom_layouts' ),
			'menu_name'             => __( 'Custom Layouts', 'mwf_custom_layouts' ),
			'name_admin_bar'        => __( 'Custom Layouts', 'mwf_custom_layouts' ),
			'parent_item_colon'     => __( 'Parent Layout:', 'mwf_custom_layouts' ),
			'all_items'             => __( 'Custom Layouts', 'mwf_custom_layouts' ),
			'add_new_item'          => __( 'Add New Layout', 'mwf_custom_layouts' ),
			'add_new'               => __( 'Add New', 'mwf_custom_layouts' ),
			'new_item'              => __( 'New Layout', 'mwf_custom_layouts' ),
			'edit_item'             => __( 'Edit Layout', 'mwf_custom_layouts' ),
			'update_item'           => __( 'Update Layout', 'mwf_custom_layouts' ),
			'view_item'             => __( 'View Layout', 'mwf_custom_layouts' ),
			'search_items'          => __( 'Search Layouts', 'mwf_custom_layouts' ),
			'not_found'             => __( 'Not found', 'mwf_custom_layouts' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'mwf_custom_layouts' ),
			'items_list'            => __( 'Layout list', 'mwf_custom_layouts' ),
			'items_list_navigation' => __( 'Layout list navigation', 'mwf_custom_layouts' ),
			'filter_items_list'     => __( 'Filter block list', 'mwf_custom_layouts' ),
		];
		$rewrite = [
			'slug'                  => 'custom-layout',
			'with_front'            => true,
			'pages'                 => true,
			'feeds'                 => true,
		];
		$args = [
			'label'                 => __( 'Custom Layout', 'mwf_custom_layouts' ),
			'description'           => __( 'Custom Layouts', 'mwf_custom_layouts' ),
			'taxonomies'            => [ 'dkcl-group' ],
			'labels'                => $labels,
			'supports'              => [ 'title', 'editor', 'revisions' ],
			'hierarchical'          => true,
			'public'                => false,
			'show_ui'               => true,
			'show_in_menu'          => 'themes.php',
			'menu_position'         => 99999,
			'menu_icon'             => 'dashicons-text',
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => false,
			'can_export'            => true,
			'has_archive'           => false,
			'exclude_from_search'   => true,
			'publicly_queryable'    => is_user_logged_in(),
			'capability_type'       => 'page',
			'show_in_rest'          => true,
			'rewrite'               => $rewrite,
		];
		register_post_type( self::NAME, $args );
	}
}