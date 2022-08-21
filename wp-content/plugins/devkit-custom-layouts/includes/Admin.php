<?php
/**
 * Admin controller class
 *
 * @class admin
 * @package CustomLayouts\Classes
 */

namespace Devkit\CustomLayouts;

use \Carbon_Fields\Carbon_Fields;
use \Carbon_Fields\Container;
use \Carbon_Fields\Field;
use \Carbon_Fields\Block;

defined( 'ABSPATH' ) || exit;

class Admin extends Framework
{
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see  https://developer.wordpress.org/reference/functions/add_filter/
	 * @return void
	 */
	public function addActions()
	{
		Subscriber::addAction( 'admin_enqueue_scripts', [$this, 'enqueueStyles'] );
		Subscriber::addAction( 'admin_enqueue_scripts', [$this, 'enqueueScripts']);
		Subscriber::addAction( 'carbon_fields_register_fields', [$this, 'registerMetaFields'] );
		Subscriber::addAction( 'carbon_fields_register_fields', [$this, 'createTemplateBlock'] );
		Subscriber::addAction( 'admin_menu', [$this, 'submenuPage'], 99 );
		Subscriber::addAction( 'menu_order', [$this, 'reorderAdminMenu'], 999 );
		Subscriber::addAction( 'custom_menu_order', '__return_true' );
		Subscriber::addAction( 'carbon_fields_post_meta_container_saved', [$this, 'processPostMeta'], 100, 2 );
		Subscriber::addAction( 'after_setup_theme', [ $this, 'bootCarbonFields' ] );
	}
	/**
	 * Ensure custom layouts and layout groups are last, and next to one another
	 *
	 * @param  array $menu_items Array of menu items
	 * @return array $menu_items
	 */
	public function reorderAdminMenu( array $menu_items ) : array
	{
		global $submenu;

		$post_type_item;

		$tax_item;

		foreach ( $submenu['themes.php'] as $index => $sub_menu_item )
		{
			if ( $sub_menu_item[0] === 'Custom Layouts' )
			{
				$post_type_item = $sub_menu_item;
				unset( $submenu['themes.php'][$index] );
			}
			elseif ( $sub_menu_item[0] === 'Layout Groups' )
			{
				$tax_item = $sub_menu_item;
				unset( $submenu['themes.php'][$index] );
			}
		}

		if ( ! empty( $post_type_item ) )
		{
			$submenu['themes.php'][] = $post_type_item;
		}
		if ( ! empty( $tax_item ) )
		{
			$submenu['themes.php'][] = $tax_item;
		}

		return $menu_items;
	}
	/**
	 * Kickoff carbon fields
	 *
	 * Limits starting carbon fields only to when we need it. Ideally, it would only
	 * be on admin, but on several actions carbon fields takes, is_admin() return false. Such
	 * as saving fields, and fails. is_logged_in is the closest we can get at this time
	 *
	 * @return void
	 */
	public function bootCarbonFields()
	{
		\Carbon_Fields\Carbon_Fields::boot();
	}
	/**
	 * Process all fields attached to our Carbon Fields container into a single
	 * usable array of values, and save as post meta
	 *
	 * This lets us offload the logic of getting the values onto the save action
	 * instead of during a frontend request
	 *
	 * @param int $id The post ID
	 * @param object $container Carbon Fields container object
	 * @return void
	 */
	public function processPostMeta( $id, $container )
	{
		/**
		 * If not our container, we can bail before even starting
		 */
		if ( $container->get_id() !== 'carbon_fields_container_cl_layout_meta' )
		{
			return;
		}
		/**
		 * Array to hold processed meta data
		 * @var array
		 */
		$meta = [];
		/**
		 * Fields attached to this container
		 * @var array
		 */
		$fields = $container->get_fields();
		/**
		 * Loop through and process each field individually
		 */
		foreach ( $fields as $index => $field )
		{
			$field_name = $this->getFieldName( $field );

			$value = $this->cleanMeta( carbon_get_post_meta( $id, $field_name ) );

			if ( $field_name === 'cl_css' && ! empty( $value ) )
			{
				$meta[$field_name] = Subscriber::getInstance( 'Utilities' )->compileCss( $value, $id );
			}
			else {
				$meta[$field_name] = $value;
			}
		}

		update_post_meta( $id, 'layout_meta', $meta);
	}
	/**
	 * Get the processed name of a single field
	 *
	 * @param object $field Mixed classes of carbon fields Field object
	 * @return string processed name
	 */
	protected function getFieldName( $field )
	{
		/**
		 * Name of the field, previxed with '_'
		 * @var [type]
		 */
		$name = $field->get_name();
		/**
		 * Replace prefix '_'
		 */
		$prefix = '_';

		if ( substr( $name, 0, strlen( $prefix ) ) === $prefix) {

			$name = substr( $name, strlen( $prefix ) );
		}

		return $name;
	}
	/**
	 * Clean undesired values from the meta before saving
	 *
	 * Carbon fields insert additional data useful to them, but not to us. We want
	 * to remove it now so we don't have to when requested on the frontend.
	 *
	 * Called recursively on arrays to check and remove extra indexes
	 *
	 * @param string/array $meta Post meta value, as returned by carbon fields
	 * @return string/array cleaned meta value
	 */
	protected function cleanMeta( $meta )
	{
		if ( ! is_array( $meta ) ) {
			return $meta;
		}

		unset( $meta['_type'] );

		foreach ( $meta as $index => $item ) {
			$meta[$index] = $this->cleanMeta( $item );
		}

		return $meta;
	}
	/**
	 * Add the submenu page for the groups taxonomy
	 *
	 * @return void
	 */
	function submenuPage()
	{
		add_submenu_page( 'themes.php', __( 'Layout Groups', 'devkit_custom_layouts' ), __( 'Layout Groups', 'devkit_custom_layouts' ), 'manage_categories', 'edit-tags.php?taxonomy=dkcl-group', null, 99 );
	}
	/**
	 * Add fields to carbon fields controller
	 *
	 * @return void
	 */
	public function registerMetaFields()
	{
		/**
		 * General Option
		 */
		Container::make( 'post_meta', 'cl_layout_meta', __( 'Display Settings', 'devkit_custom_layouts' ) )
			->set_context( 'normal' )
			->set_classes( 'custom_layout_metabox' )
			->where( 'post_type', 'IN', ['dk-custom-layout', 'dk-template'] )
			->add_tab( __( 'General', 'devkit_custom_layouts' ), $this->getEditorFields() )
			->add_tab( __( 'Location', 'devkit_custom_layouts' ), $this->getActionFields() )
			->add_tab( __( 'Conditions', 'devkit_custom_layouts' ), $this->getDisplayConditionFields() )
			->add_tab( __( 'CSS/JS', 'devkit_custom_layouts' ), $this->cssJsFields() )
			->add_tab( __( 'Help', 'devkit_custom_layouts' ), $this->getHelpFields()
		);
	}
	/**
	 * Get fields specific to the author screen
	 *
	 * @access protected
	 * @return array $fields array of carbon fields
	 */
	protected function getAuthorFields()
	{
		$fields = [
			Field::make( 'image', 'cl_author_image', __( ' Image', 'devkit_custom_layouts' ) )
				->set_help_text( __( 'Custom image to use in place of a gravatar', 'devkit_custom_layouts' ) ),
			Field::make( 'rich_text', 'cl_author_content', __( 'Content', 'devkit_custom_layouts' ) ),
		];
		/**
		 * Maybe add custom social links
		 */
		if ( ! $this->isPluginActive( 'wordpress-seo/wp-seo.php' ) )
		{
			$networks = [
				'facebook',
				'github',
				'googleplus',
				'instagram',
				'linkedin',
				'pinterest',
				'soundcloud',
				'tumblr',
				'twitter',
				'wikipedia',
				'wordpress',
				'youtube',
				'url'
			];

			foreach ( $networks as $network )
			{
				$fields[] = Field::make( 'text', "cl_network_{$network}", $network );
			}
		}
		return $fields;
	}
	/**
	 * Setup fields for help tab
	 *
	 * @access protected
	 * @return array $fields array of carbon fields
	 */
	protected function getHelpFields()
	{
		$fields = [
			Field::make( 'html', 'cl_help_tab_content', '' )
				->set_html(
					'<h4>' . __('Shortcode', 'devkit_custom_layouts') . '</h4>'
					. '<pre><code>[devkit_custom_layout id="' . $this->editScreen() . '"]</code></pre>'
					. '<p>' . __( 'The shortcode can be used anywhere, and unless used with the conditional argument ', 'devkit_custom_layouts' )
					. '<code>use_conditions="true"</code> '
					. __( 'will ignore all conditional rules', 'devkit_custom_layouts' ) . '</p>'
				)
		];
		return $fields;
	}
	/**
	 * Setup fields for CSS/JS
	 *
	 * @access protected
	 * @return array $fields array of carbon fields
	 */
	protected function cssJsFields()
	{
		$fields = [
			Field::make( 'textarea', 'cl_css', __( 'CSS' ) )
				->set_rows( 10 )
				->set_classes( 'cl_ace' )
				->set_attribute( 'data-editor-type', 'scss' )
				->set_help_text(
					'<p>' . __( 'Supports CSS and SCSS syntax.', 'devkit_custom_layouts' ) . '</p>'
					. '<p>' . __( 'Use', 'devkit_custom_layouts' ) . ' <strong>$SELECTOR</strong> ' . __( 'to target wrapper element.', 'devkit_custom_layouts' ) . '</p>'
				),
			Field::make( 'textarea', 'cl_js', __( 'JS' ) )
				->set_rows( 10 )
				->set_attribute( 'data-editor-type', 'javascript' )
				->set_classes( 'cl_ace' )
				->set_help_text(
					'<p>' . __( 'Do not include', 'devkit_custom_layouts' ) . '<code>' . htmlspecialchars('<script> or </script>') . '</code>' . __( ' tags, code will be wrapped automatically.', 'devkit_custom_layouts' ) .'</p>'
				),
		];
		return $fields;
	}
	/**
	 * Setup fields for group tab
	 *
	 * @access protected
	 * @return array $fields array of carbon fields
	 */
	protected function getExtrasFields()
	{
		/**
		 * Post ID
		 *
		 * Derived from either $_GET['post'] || $_POST['id'] || $_POST['post_id']
		 *
		 * @var int
		 */
		$id = $this->editScreen();
		/**
		 * Get all terms, in the case that the ID is empty
		 *
		 * This happens when carbon fields is doing a save action, and wont
		 * save the field correctly
		 */
		if ( empty( $id ) )
		{
			$terms = get_terms( [
				'taxonomy' => 'dkcl-group',
				'hide_empty' => false,
			] );
		}
		else
		{
			$terms = get_the_terms( $this->editScreen(), 'dkcl-group' );
		}
		/**
		 * Empty array to hold select options
		 *
		 * @var array
		 */
		$options = [];
		/**
		 * Empty array to hold labels, used for labeling the field
		 *
		 * @var array
		 */
		$group_lables = [];

		if ( ! is_wp_error( $terms ) && ! empty( $terms ) )
		{
			foreach ( $terms as $term )
			{

				$post_args = [
					'posts_per_page' => -1,
					'post_type' => [ 'custom-layout' ],
					'tax_query' => [
						[
							'taxonomy' => 'dkcl-group',
							'terms'    => $term->term_taxonomy_id,
						]
					],
				];

				if ( ! empty( $id ) )
				{
					$post_args['post__not_in'] = [ $id ];
				}

				$group_lables[] = $term->name;

				$posts = get_posts( $post_args );

				foreach ( $posts as $post ) {
					$options[$post->ID] = $post->post_title;
				}
			}
		}

		$fields = [];

		$fields[] = Field::make( 'multiselect', "cl_group_disable", __( 'Disable layouts in ', 'devkit_custom_layouts' ) . join( ', ', $group_lables ) )
			->set_classes( empty( $options ) ? 'cl-empty-metabox' : 'terms-metabox' )
			->set_options(
				$options
			)
			->set_help_text(
				 '<p>This option allow you to override some layouts on screens where this layout is preferred<p>'
				. '<p>For instance, you may have a layout to display on "entire site", which you can override for more specific circumstances</p>'
			);

		return $fields;
	}
	/**
	 * Setup fields for inclusion conditions
	 *
	 * @access protected
	 * @return array $fields array of carbon fields
	 */
	protected function getDisplayConditionFields()
	{
		$fields = [];

		$fields[] = Field::make( 'complex', 'cl_display_conditions', '' )
			->set_layout( 'tabbed-horizontal' )
			->setup_labels(
				[
					'singular_name' => 'Condition Group',
					'plural_name' => 'Condition Groups'
				]
			)
			->add_fields(
				[
					Field::make( 'select', 'cl_condition_type', __( 'Display' ) )
						->set_options( [
							'include' => __( 'Show On', 'scaffolding' ),
							'exclude' => __( 'Hide On', 'scaffolding' ),
						]
					),
					Field::make( 'complex', 'cl_condition_group', 'Match All' )
						->set_min( 1 )
						->set_layout( 'grid' )
						->setup_labels(
							[
								'singular_name' => 'Condition',
								'plural_name' => 'Conditions'
							]
						)
						->add_fields( $this->getConditionalFields() ),
				]
			);
		return $fields;
	}
	/**
	 * Get fields for conditions, used by `getDisplayConditionFields` & `getExclusionFields`
	 * to retrieve specific fields
	 *
	 * @access protected
	 * @return array $fields array of carbon fields
	 */
	protected function getConditionalFields()
	{

		$fields = [];

		$fields[] = Field::make( 'select', 'cl_condition', '' )
			->set_options( apply_filters( 'devkit/custom_layouts/display_conditions', [
				'__return_true' => __( 'Entire Site', 'scaffolding' ),
				'is_front_page' => __( 'Front Page', 'scaffolding' ),
				'is_home' => __( 'Blog Page', 'scaffolding' ),
				'is_404' => __( '404', 'scaffolding' ),
				'is_search' => __( 'Search Results', 'scaffolding' ),
				'singular' => __( 'Singular', 'scaffolding' ),
				'archive' => __( 'Archives', 'scaffolding' ),
				'datetime' => __( 'Date/Time', 'scaffolding' ),
				'user' => __( 'User Role', 'scaffolding' ),
				'custom' => __( 'custom', 'scaffolding' ),
			] )
		);
		/**
		 * Singular Fields
		 */
		$fields[] = Field::make( 'select', 'cl_condition_singular_subtype', '' )
			->set_options(
				[
					'post' => __( 'Choose', 'scaffolding' ),
					'post_type' => __( 'Post Type', 'scaffolding' ),
					'term' => __( 'Term', 'scaffolding' ),
					'author' => __( 'Author', 'scaffolding' ),
					'template' => __( 'Page Template', 'scaffolding' ),
				]
			)
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'singular',
						'compare' => '=',
					]
				]
			);
		$fields[] = Field::make( 'multiselect', 'cl_condition_singular_post_type', '' )
			->add_options( [$this, 'getPostTypes'] )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'singular',
						'compare' => '=',
					],
					[
						'field' => 'cl_condition_singular_subtype',
						'value' => 'post_type',
						'compare' => '=',
					]
				]
			);
		$fields[] = Field::make( 'multiselect', 'cl_condition_singular_template', '' )
			->add_options( [$this, 'getPageTemplates'] )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'singular',
						'compare' => '=',
					],
					[
						'field' => 'cl_condition_singular_subtype',
						'value' => 'template',
						'compare' => '=',
					]
				]
			);
		$fields[] = Field::make( 'multiselect', 'cl_condition_singular_post', '' )
			->add_options( [$this, 'getPosts'] )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'singular',
						'compare' => '=',
					],
					[
						'field' => 'cl_condition_singular_subtype',
						'value' => 'post',
						'compare' => '=',
					]
				]
			);
		$fields[] = Field::make( 'multiselect', 'cl_condition_singular_author', '' )
			->add_options( [$this, 'getAuthors'] )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'singular',
						'compare' => '=',
					],
					[
						'field' => 'cl_condition_singular_subtype',
						'value' => 'author',
						'compare' => '=',
					]
				]
			);
		$fields[] = Field::make( 'multiselect', 'cl_condition_singular_term', '' )
			->add_options( [$this, 'getTerms'] )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'singular',
						'compare' => '=',
					],
					[
						'field' => 'cl_condition_singular_subtype',
						'value' => 'term',
						'compare' => '=',
					]
				]
			);
		$fields[] = Field::make( 'html', 'cl_condition_custom_help' )
			->set_html( "<p><strong>Custom Conditions :</strong> Use the filter <code>'devkit/custom_layouts/conditions/{ID}'</code></p><p>The filter is passed 2 arguments. <strong>Value</strong>, the bool value to indicate if it's valid. And <strong>Type</strong> either 'include' or 'exlude' to determine which type of validation is being performed.</p>" )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'custom',
						'compare' => '=',
					]
				]
			);
		$fields[] = Field::make( 'text', 'cl_condition_custom', __( 'ID' ) )
			->set_help_text( 'Uniqueue identifier for filtering the rule' )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'custom',
						'compare' => '=',
					]
				]
			);
		/**
		 * Archive Terms
		 */
		$fields[] = Field::make( 'select', 'cl_condition_archive_subtype', '' )
			->set_options(
				[
					'archives' => __( 'All', 'scaffolding' ),
					'post_type' => __( 'Post Type', 'scaffolding' ),
					'term' => __( 'Term', 'scaffolding' ),
					'author' => __( 'Author', 'scaffolding' ),
				]
			)
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'archive',
						'compare' => '=',
					]
				]
			);
		$fields[] = Field::make( 'multiselect', 'cl_condition_archive_post_type', '' )
			->add_options( [$this, 'getPostTypes'] )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'archive',
						'compare' => '=',
					],
					[
						'field' => 'cl_condition_archive_subtype',
						'value' => 'post_type',
						'compare' => '=',
					]
				]
			);
		$fields[] = Field::make( 'multiselect', 'cl_condition_archive_author', '' )
			->add_options( [$this, 'getAuthors'] )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'archive',
						'compare' => '=',
					],
					[
						'field' => 'cl_condition_archive_subtype',
						'value' => 'author',
						'compare' => '=',
					]
				]
			);
		$fields[] = Field::make( 'multiselect', 'cl_condition_archive_term', '' )
			->add_options( [$this, 'getTerms'] )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'archive',
						'compare' => '=',
					],
					[
						'field' => 'cl_condition_archive_subtype',
						'value' => 'term',
						'compare' => '=',
					]
				]
			);
		/**
		 * User Role Fields
		 */
		$fields[] = Field::make( 'multiselect', 'cl_condition_role', '' )
			->add_options( [$this, 'getUserRoles'] )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'user',
						'compare' => '=',
					],
				]
			);
		/**
		 * Date / Type Fields
		 */

		$fields[] = Field::make( 'date_time', 'cl_condition_datetime', __( 'Date / Time', 'custom_layouts' ) )
			->set_help_text(
				  'If display is set to <strong>Show</strong>, display <strong>after</strong> this date</br>'
				. 'If display is set to <strong>Hide</strong>, display <strong>until</strong> this date'
			)
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_condition',
						'value' => 'datetime',
						'compare' => '=',
					]
				]
			);
		return $fields;
	}
	/**
	 * Get fields for the editor box
	 *
	 * @access protected
	 * @return array $fields array of carbon fields
	 */
	protected function getEditorFields()
	{
		$fields = [];

		$fields['type'] = Field::make( 'select', 'cl_type', __( 'Type' ) )
			->set_default_value( 'editor' )
			->set_classes( 'editor-type-select' )
			->set_options(
				[
					'editor' => __( 'Editor', 'scaffolding' ),
					'code' => __( 'Code', 'scaffolding' ),
					'partial' => __( 'Template Part', 'scaffolding' ),
				]
			);
		$fields['code'] = Field::make( 'textarea', 'cl_code', __( 'Custom Code' ) )
			->set_rows( 10 )
			->set_attribute( 'data-editor-type', 'twig' )
			->set_classes( 'cl_ace cl_code_editor' );
		$fields['partial'] = Field::make( 'select', 'cl_partial', __( 'Template Part' ) )
			->set_options( apply_filters( 'devkit/custom_layouts/template_parts',[] ))
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_type',
						'value' => 'partial',
						'compare' => '=',
					]
				]
			);
		/**
		 * Apply filter here to allow third party components to add fields based on template part selected
		 */
		$fields = apply_filters( 'devkit/custom_layouts/meta_fields/partial_extra_fields', $fields );

		$fields[] = Field::make( 'select', 'cl_container', __( 'Container' ) )
			->set_default_value( 'div' )
			->set_width( 30 )
			->set_options(
				[
					'div' => __( 'div', 'scaffolding' ),
					'section' => __( 'section', 'scaffolding' ),
					'header' => __( 'header', 'scaffolding' ),
					'footer' => __( 'footer', 'scaffolding' ),
					'aside' => __( 'aside', 'scaffolding' ),
					'span' => __( 'span', 'scaffolding' ),
					false => __( 'none', 'scaffolding' ),
				]
			)
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_type',
						'value' => 'code',
						'compare' => '!=',
					]
				]
			);
		$fields[] = Field::make( 'text', 'cl_classes', __( 'Container Class(s)' ) )
			->set_width( 70 )
			->set_conditional_logic(
				[
					'relation' => 'AND',
					[
						'field' => 'cl_container',
						'value' => '0',
						'compare' => '!=',
					],
					[
						'field' => 'cl_type',
						'value' => 'code',
						'compare' => '!=',
					]
				]
			);

		return apply_filters( 'devkit/custom_layouts/meta_fields/editor', $fields );
	}
	/**
	 * Get all the fields related to the action hook
	 *
	 * @access protected
	 * @return array $fields array of carbon fields
	 */
	protected function getActionFields()
	{

		/**
		 * Get all other layouts attached to this hook
		 */
		$fields = [];

		$subfields = [];

		$subfields[] = Field::make( 'select', 'action', __( 'Hook', 'devkit_custom_layouts' ) )
			->set_width( 80 )
			->set_options( [$this, 'getActionHooks']);

		$subfields[] = Field::make( 'number', 'priority', __( 'Priority', 'devkit_custom_layouts' ) )
			->set_default_value( 10 )
			->set_width( 10 );

		$subfields[] = Field::make( 'select', 'override', __( 'Remove / Override', 'devkit_custom_layouts' ) )
			->set_width( 50 )
			->set_help_text( __( 'Remove other actions and display this layout instead.' ) )
			->set_options( array(
				'0' => __( 'None', 'devkit_custom_layouts' ),
				'1' => __( 'All Actions', 'devkit_custom_layouts' ),
				'layouts' => __( 'All custom layouts', 'devkit_custom_layouts' ),
				'group' => __( 'All custom layouts in same Group', 'devkit_custom_layouts' ),
				'select' => __( 'Select', 'devkit_custom_layouts' )
			) );
		/**
		 * Add fields for each action
		 */
		// $post_args = [
		// 	'posts_per_page' => -1,
		// 	'post_type' => [ 'custom-layout' ],
		// ];

		// $posts = get_posts( $post_args );

		// $action_holder = [];

		// foreach ( $posts as $post )
		// {
		// 	$meta = get_post_meta( $post->ID, 'layout_meta', true );

		// 	if ( ! isset( $meta['cl_actions'] ) || empty( $meta['cl_actions'] ) )
		// 	{
		// 		continue;
		// 	}

		// 	foreach ( $meta['cl_actions'] as $action )
		// 	{
		// 		$action_holder[$action['action']] = isset( $action_holder[$action['action']] ) ? $action_holder[$action['action']] : [];
		// 		$action_holder[$action['action']][$post->ID] = $post->post_title;
		// 	}
		// }

		// foreach ( $hooks as $hook => $hook_label )
		// {
		// 	$hook_disable_options = isset( $action_holder[$hook] ) ? $action_holder[$hook] : [];
		// 	/**
		// 	 * Must encode, because slashes and other characters break carbon fields
		// 	 *
		// 	 * @var string
		// 	 */
		// 	$hook_save = bin2hex( $hook );

		// 	$subfields[] = Field::make( 'multiselect', "override_{$hook_save}", __( 'Select Layouts', 'devkit_custom_layouts' ) )
		// 		->set_options( $hook_disable_options )
		// 		->set_width( 50 )
		// 		->set_help_text( __( 'Remove other layouts attached to the', 'devkit_custom_layouts' ) . ' <strong>' . $hook_label . '</strong> ' . __( 'action', 'devkit_custom_layouts' ) )
		// 		->set_conditional_logic(
		// 			[
		// 				'relation' => 'AND',
		// 				[
		// 					'field' => 'action',
		// 					'value' => $hook,
		// 					'compare' => '=',
		// 				],
		// 				[
		// 					'field' => 'override',
		// 					'value' => 'select',
		// 					'compare' => '=',
		// 				]
		// 			]
		// 		);
		// }

		$fields[] = Field::make( 'complex', 'cl_actions', '' )
		->set_layout( 'tabbed-horizontal' )
		->setup_labels(
			[
				'singular_name' => 'Location',
				'plural_name' => 'Locations'
			]
		)
		->add_fields($subfields);

		return $fields;
	}
	public function getActionHooks()
	{
		/**
		 * Step 1 : Define default core hooks
		 */
		$default_hooks = [
			'wp_head' => 'WP Head',
			'wp_body_open' => 'WP Body Open',
			'wp_footer'    => 'WP Footer',
			'the_content' => 'The Content'
		];
		/**
		 * Step 2 : Theme Support
		 *
		 * Allows 3rd party themes to declare theme support, which will override theme hooks
		 */
		$theme_support = get_theme_support( 'devkit-custom-layouts' );
		/**
		 * If we have declared theme support, set them
		 */
		$theme_hooks = isset( $theme_support[0] ) && is_array( $theme_support[0] ) ? $theme_support[0] : [];
		/**
		 * Filter them
		 */
		$theme_hooks = apply_filters( 'devkit/custom_layouts/fields/hooks/theme', $theme_hooks );
		/**
		 * Filter for known addons
		 */
		$addon_hooks = apply_filters( 'devkit/custom_layouts/fields/hooks/addons', [] );
		/**
		 * Step 3 : Merge Hooks Together, filter, and return
		 */
		return apply_filters( 'devkit/custom_layouts/fields/hooks', array_merge( $default_hooks, $theme_hooks, $addon_hooks ) );
	}
	/**
	 * Enqeueue the javascript with WP
	 *
	 * @return void
	 */
	public function enqueueScripts()
	{
		wp_enqueue_script(__NAMESPACE__ . '\codemirror', DEVKIT_CUSTOMLAYOUTS_URL . '/assets/js/codemirror.js', [], DEVKIT_CUSTOMLAYOUTS_VERSION, true);
		wp_enqueue_script( __NAMESPACE__ . '\admin', DEVKIT_CUSTOMLAYOUTS_URL . '/assets/js/admin' . DEVKIT_CUSTOMLAYOUTS_ASSET_PREFIX . '.js', ['jquery', __NAMESPACE__ . '\codemirror'], DEVKIT_CUSTOMLAYOUTS_VERSION, true );
	}
	/**
	 * Enqeueue the css with WP
	 *
	 * @return void
	 */
	public function enqueueStyles()
	{
		wp_enqueue_style(__NAMESPACE__ . '\codemirror', DEVKIT_CUSTOMLAYOUTS_URL . '/assets/css/codemirror.css', [], DEVKIT_CUSTOMLAYOUTS_VERSION, 'all');
		wp_enqueue_style(  __NAMESPACE__ . '\admin', DEVKIT_CUSTOMLAYOUTS_URL . '/assets/css/admin' . DEVKIT_CUSTOMLAYOUTS_ASSET_PREFIX . '.css', [], DEVKIT_CUSTOMLAYOUTS_VERSION, 'all' );
	}
	/**
	 * Helper function to see if on the edit screen for a custom layout
	 *
	 * @access protected
	 * @return boolean
	 */
	protected function editScreen()
	{
		if ( isset( $_GET['post'] ) )
		{
			$post_id = $_GET['post'];
		}
		elseif ( isset( $_POST['id'] ) )
		{
			$post_id = $_POST['id'];
		}
		elseif ( isset( $_POST['post_ID'] ) )
		{
			$post_id = $_POST['post_ID'];
		}
		if ( empty( $post_id ) || get_post_type( $post_id ) !== 'custom-layout' ) {
			return false;
		}
		return $post_id;
	}
	/**
	 * Get terms for the terms for terms related fields
	 *
	 * @return array Associative array of terms
	 */
	public function getTerms() : array
	{
		$options = [];

		$tax_objects = get_taxonomies( [ 'public' => true ], 'objects' );

		foreach ( $tax_objects as $tax ) {

			if ( in_array(  $tax->name, ['fl-builder-template-category' ]) ) {
				continue;
			}

			$terms = get_terms( $tax->name, [ 'hide_empty' => false ] );

			foreach ( $terms as $term )
			{
				$options[$term->term_taxonomy_id] = $tax->label . ' - ' . $term->name;
			}
		}

		return $options;
	}
	/**
	 * Load page template options
	 *
	 * @return array Array of page templates
	 */
	public function getPageTemplates()
	{

		include_once ABSPATH . 'wp-admin/includes/theme.php';

		$values = [
			'' => 'Default'
		];

		$templates = get_page_templates();

		foreach( $templates as $name => $path ) {
			$values[$path] = $name;
		}
		/**
		 * Filter & Return
		 */
		return apply_filters( 'devkit/custom_layouts/fields/page_templates', $values );
	}
	/**
	 * Load user roles
	 *
	 * @return array Array of user roles
	 */
	public function getUserRoles()
	{

		global $wp_roles;

		$values = [
			'none' => 'Not Logged In',
			'all'  => 'All Logged In',
		];

		foreach( $wp_roles->roles as $value => $role ) {
			$values[$value] = $role['name'];
		}

		return apply_filters( 'devkit/custom_layouts/fields/user_roles', $values );
	}
	public function getAuthors() : array
	{
		$options = [];

		$users = get_users( [ 'who' => 'authors' ] );

		foreach ( $users as $user )
		{
			$options[$user->ID] = $user->data->display_name;
		}
		return apply_filters( 'devkit/custom_layouts/fields/authors', $options );
	}
	/**
	 * Load post types
	 *
	 * @return array Array of post types
	 */
	public function getPostTypes() : array
	{
		$values = [];

		$post_types = get_post_types( [ 'public' => true ], 'objects' );

		$post_types = apply_filters( 'devkit/custom_layouts/fields/post_types', $post_types );

		foreach( $post_types as $post_type ) {

			if( in_array( $post_type->name, array( 'fl-builder-template' ) ) ) {
				continue;
			}
			$values[$post_type->name] = $post_type->label;
		}
		return apply_filters( 'devkit/custom_layouts/fields/post_types', $values );
	}
	public function getPosts() : array
	{
		$values = [];

		$post_types = get_post_types( [ 'public' => true ], 'objects' );

		$post_types = apply_filters( 'devkit/custom_layouts/fields/post_types', $post_types );

		foreach( $post_types as $post_type ) {

			if( in_array( $post_type->name, array( 'fl-builder-template', 'dk-custom-layout' ) ) ) {
				continue;
			}
			$posts = get_posts(
				[
					'posts_per_page' => -1,
					'post_type' => [ $post_type->name ],
					'post_status' => 'publish'
				]
			);
			foreach ( $posts as $post )
			{
				$values[$post->ID] = $post_type->labels->singular_name . ' - ' . $post->post_title;
			}
		}
		return apply_filters( 'devkit/custom_layouts/fields/posts', $values );
	}
	/**
	 * Create block editor block to choose template part
	 *
	 * @return void
	 */
	public function createTemplateBlock()
	{

		$parts = apply_filters( 'devkit/custom_layouts/template_parts',[] );

		$fields = [];

		$fields[] = Field::make( 'select', 'cl_partial', __( 'Choose Template Part', 'devkit_custom_layouts' ) )
			->set_options( $parts )
			->set_width( '100%' );

		$fields = apply_filters( 'devkit/custom_layouts/template_part_fields', $fields );

		Block::make( __( 'Template Part', 'devkit_custom_layouts' ) )
			->add_fields( $fields )
			->set_category( 'layout' )
			->set_mode( 'edit' )
			->set_inner_blocks( false )
			->set_render_callback( function( $fields, $attributes, $inner_blocks ) {
				Subscriber::getInstance( 'FrontEnd' )->renderBlock( $fields, $attributes, $inner_blocks, $fields['cl_partial'] );
		});
	}
}