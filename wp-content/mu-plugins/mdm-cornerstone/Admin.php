<?php

/**
 * The plugin file that controls the admin functions
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_wp_cornerstone
 */

namespace mdm\cornerstone;

class Admin extends Framework implements interfaces\ActionHookSubscriber, interfaces\FilterHookSubscriber {

	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		// Return our custom actions
		return array(
			array( 'admin_enqueue_scripts' => 'enqueue_scripts' ),
			array( 'admin_enqueue_scripts' => 'enqueue_styles' ),
			array( 'menu_order' => array( 'reorder_admin_menu', 99999 ) ),
			array( 'custom_menu_order' => '__return_true' ),
			array( 'admin_menu' => 'rename_menu_items' ),
			array( 'jetpack_just_in_time_msgs' => '_return_false' ),
			array( 'wp_dashboard_setup' => array( 'remove_unwanted_dashboard_widgets', 99 ) ),
			array( 'activate_bb-plugin/fl-builder.php' => 'set_flbuilder_license' ),
			array( 'init' => 'init_customizer' ),
		);

	}

	/**
	 * Get the filter hooks this class subscribes to.
	 * @return array
	 */
	public function get_filters() {
		return array(
			array( 'mce_buttons_2' => array( 'register_style_select', 99999 ) ),
			array( 'tiny_mce_before_init' => array( 'register_style_formats', 99999 ) ),
		);
	}

	public function enqueue_scripts() {
		// Register all public scripts, including dependencies
		wp_enqueue_script( sprintf( '%s_admin', 'mdm_cornerstone' ), self::url( 'assets/js/admin.js' ), array( 'jquery' ), MDM_CORNERSTONE_VERSION, true );
		// Localize public script
		wp_localize_script( sprintf( '%s_admin', 'mdm_cornerstone' ), 'mdm_cornerstone', array( 'wpajaxurl' => admin_url( 'admin-ajax.php') ) );
	}

	public function enqueue_styles() {
		// Register admin styles
		wp_enqueue_style( sprintf( '%s_admin', 'mdm_cornerstone' ), self::url( 'assets/css/admin.css' ), array(), MDM_CORNERSTONE_VERSION, 'all' );
	}

	public function reorder_admin_menu( $menu_items ) {
		// Our top level items
		$top_level = array();
		// Post Types
		$post_types = array();
		// Secondary
		$secondary = array();
		// Woocommerce
		$woo = array();
		// Our bottom level items
		$bottom_level = array();
		// And our penalty box...known that we want last
		$penalty_box = array();
		// Loop over each item
		foreach( $menu_items as $menu_item ) {
			// var_dump($menu_item);
			// 'edit.php?post_type=product'
			// 'edit.php?post_type=product'
			// woocommerce
			// 'wc-admin&path=/analytics/revenue'
			// 'separator-woocommerce'
			/**
			 * Known offenders that we want to stick as low as possible
			 */
			if( in_array( $menu_item, array( 'jetpack', 'genesis', 'edit.php?post_type=fl-builder-template', 'video-user-manuals/plugin.php', 'edit.php?post_type=acf-field-group', 'googlesitekit-dashboard' ) ) ) {
				$penalty_box[] = $menu_item;
				// 'woocommerce',
				// https://wiid.mdmserver.us/wp-admin/edit.php?post_type=acf-field-group
			}
			/**
			 * Woocommerce
			 */
			else if( is_plugin_active( 'woocommerce/woocommerce.php' ) && in_array( $menu_item, array( 'edit.php?post_type=product', 'woocommerce', 'separator-woocommerce', 'wc-admin&path=/analytics/revenue' ) ) ) {
				if( $menu_item === 'separator-woocommerce' ) {
					array_unshift( $woo, $menu_item );
				}
				else if( in_array( $menu_item, array( 'edit.php?post_type=product', 'woocommerce', 'wc-admin&path=/analytics/revenue' ) ) ) {
					$woo[] = $menu_item;
				}
			}
			/**
			 * Our top level items
			 * Dashboard, and the first seperator
			 */
			else if( in_array( $menu_item, array( 'index.php', 'separator1' ) ) ) {
				$top_level[] = $menu_item;
			}
			/**
			 * Content related items
			 * anything that starts with edit.php and not already in another area
			 * Nested pages plugin is whitelisted for this section
			 */
			else if( strripos( $menu_item , 'edit.php' ) !== false || $menu_item === 'nestedpages' ) {
				$post_types[] = $menu_item;
			}
			/**
			 * Secondary items
			 */
			else if( in_array( $menu_item, array( 'upload.php', 'gf_edit_forms', 'edit-comments.php' ) ) ) {
				$secondary[] = $menu_item;
			}
			/**
			 * Everything else
			 * Contains settings, users, appearence, etc.
			 */
			else {
				$bottom_level[] = $menu_item;
			}
		}
		// var_dump($post_types);
		// Mush it all together in a new order, and send it back
		return array_merge( $top_level, $post_types, $secondary, $woo, $bottom_level, $penalty_box );
	}

	public function register_style_select( $buttons ) {
		array_unshift( $buttons, 'styleselect' );
		return $buttons;
	}

	public function register_style_formats( $init_array ) {
		// Define the style_formats array
		$style_formats = array(
			// Each array child is a format with it's own settings
			'Headlines' => array(
				'title'  => 'Headlines',
				'items'  => array(
					array(
						'title' => 'Header 1',
						'format' => 'h1',
					),
					array(
						'title' => 'Header 2',
						'format' => 'h2',
					),
					array(
						'title' => 'Header 3',
						'format' => 'h3',
					),
					array(
						'title' => 'Header 4',
						'format' => 'h4',
					),
					array(
						'title' => 'Header 5',
						'format' => 'h5',
					),
					array(
						'title' => 'Header 6',
						'format' => 'h6',
					),
					array(
						'title'   => 'Sub-Head (inline)',
						'classes' => 'subhead',
						'inline' => 'span',
						'wrapper' => false,
					),
				),
			),
			'inline' => array(
				'title'  => 'Inline',
				'items'  => array(
					array(
						'title'  => 'Bold',
						'icon'   => 'bold',
						'format' => 'bold',
					),
					array(
						'title'  => 'Italic',
						'icon'   => 'italic',
						'format' => 'italic',
					),
					array(
						'title'  => 'Underline',
						'icon'   => 'underline',
						'format' => 'underline',
					),
					array(
						'title'  => 'Strikethrough',
						'icon'   => 'strikethrough',
						'format' => 'strikethrough',
					),
					array(
						'title'  => 'Superscript',
						'icon'   => 'superscript',
						'format' => 'superscript',
					),
					array(
						'title'  => 'Subscript',
						'icon'   => 'subscript',
						'format' => 'subscript',
					),
					array(
						'title'  => 'Code',
						'icon'   => 'code',
						'format' => 'code',
					),
					array(
						'title'   => 'Lead',
						'classes' => 'lead',
						'inline' => 'span',
						'wrapper' => false,
					),
					array(
						'title'   => 'Sub-Head',
						'classes' => 'subhead',
						'inline' => 'span',
						'wrapper' => false,
					),
					array(
						'title'   => 'Narrow Width',
						'classes' => 'narrow-width',
						'inline' => 'span',
						'wrapper' => false,
					),
					array(
						'title'   => 'Narrow Width Centered',
						'classes' => 'narrow-width centered-block',
						'inline' => 'span',
						'wrapper' => false,
					),
				),
			),
			'Blocks' => array(
				'title'  => 'Blocks',
				'items'  => array(
					array(
						'title' => 'Paragraph',
						'format' => 'p',
					),
					array(
						'title' => 'Blockquote',
						'format' => 'blockquote',
					),
					array(
						'title' => 'Div',
						'format' => 'div',
					),
					array(
						'title' => 'Pre',
						'format' => 'pre',
					),
					array(
						'title' => 'Section',
						'format' => 'section',
					),
				),
			),
			'Alignment' => array(
				'title'  => 'Alignment',
				'items'  => array(
					array(
						'title'  => 'Left',
						'icon'   => 'alignleft',
						'format' => 'alignleft',
					),
					array(
						'title'  => 'Right',
						'icon'   => 'alignright',
						'format' => 'alignright',
					),
					array(
						'title'  => 'Center',
						'icon'   => 'aligncenter',
						'format' => 'aligncenter',
					),
					array(
						'title'  => 'Justify',
						'icon'   => 'alignjustify',
						'format' => 'alignjustify',
					),
				),
			),
			'Unordered Lists' => array(
				'title'  => 'Unordered Lists',
				'items'  => array(
					array(
						'title' => '.nobullet',
						'selector' => 'ul',
						'classes' => 'nobullet',
						'wrapper' => false,
					),
					array(
						'title' => '.square',
						'selector' => 'ul',
						'classes' => 'square',
						'wrapper' => false,
					),
					array(
						'title' => '.circle',
						'selector' => 'ul',
						'classes' => 'circle',
						'wrapper' => false,
					),
					array(
						'title' => '.disc',
						'selector' => 'ul',
						'classes' => 'disc',
						'wrapper' => false,
					),
					array(
						'title' => '.bordered',
						'selector' => 'ul',
						'classes' => 'bordered',
						'wrapper' => false,
					),
				),
			),
			'Ordered Lists' => array(
				'title'  => 'Ordered Lists',
				'items'  => array(
					array(
						'title' => '.nobullet',
						'selector' => 'ol',
						'classes' => 'nobullet',
						'wrapper' => false,
					),
					array(
						'title' => '.decimal',
						'selector' => 'ol',
						'classes' => 'decimal',
						'wrapper' => false,
					),
					array(
						'title' => '.decimal-leading-zero',
						'selector' => 'ol',
						'classes' => 'decimal-leading-zero',
						'wrapper' => false,
					),
					array(
						'title' => '.lower-roman',
						'selector' => 'ol',
						'classes' => 'lower-roman',
						'wrapper' => false,
					),
					array(
						'title' => '.upper-roman',
						'selector' => 'ol',
						'classes' => 'upper-roman',
						'wrapper' => false,
					),
					array(
						'title' => '.lower-alpha',
						'selector' => 'ol',
						'classes' => 'lower-alpha',
						'wrapper' => false,
					),
					array(
						'title' => '.upper-alpha',
						'selector' => 'ol',
						'classes' => 'upper-alpha',
						'wrapper' => false,
					),
					array(
						'title' => '.counter',
						'selector' => 'ol',
						'classes' => 'counter',
						'wrapper' => false,
					),
					array(
						'title' => '.bordered',
						'selector' => 'ol',
						'classes' => 'bordered',
						'wrapper' => false,
					),
					array(
						'title' => '.strong',
						'selector' => 'ol',
						'classes' => 'strong',
						'wrapper' => false,
					),
				),
			),
			'Buttons' => array(
				'title'  => 'Buttons',
				'items'  => array(
					array(
						'title' => 'Default Button',
						'selector' => 'a',
						'classes' => 'button',
						'wrapper' => false,
					),
					array(
						'title' => 'Small Button',
						'selector' => 'a',
						'classes' => 'button small',
						'wrapper' => false,
					),
					array(
						'title' => 'Large Button',
						'selector' => 'a',
						'classes' => 'button large',
						'wrapper' => false,
					),
					array(
						'title' => 'Block (full width) Button',
						'selector' => 'a',
						'classes' => 'button block',
						'wrapper' => false,
					),
				),
			),
		);
		// Allow filtering further
		$style_formats = apply_filters( 'theme_style_formats', $style_formats );
		// Json encode and place in array
		$init_array['style_formats'] = json_encode( $style_formats );
		// return to editor
		return $init_array;
	}

	public function rename_menu_items() {
	    global $menu;
	    global $submenu;
	    // self::expose( $submenu );
	    $menu[5][0] = 'Blog';
	    $submenu['edit.php'][5][0] = 'Blog Posts';
	    $submenu['edit.php'][10][0] = 'Add Blog Post';
	}

	public function remove_unwanted_dashboard_widgets() {
		global $wp_meta_boxes;
		// jetpack
		if( isset( $wp_meta_boxes['dashboard']['normal']['core']['jetpack_summary_widget'] ) ) {
			unset( $wp_meta_boxes['dashboard']['normal']['core']['jetpack_summary_widget'] );
		}
		// gravity forms
		if( isset( $wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard'] ) ) {
			unset( $wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard'] );
		}
		// Quick Draft
		if( isset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] ) ) {
			unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
		}
	}

	public function set_flbuilder_license() {

		if( defined( 'FL_LICENSE_KEY' ) ) {
			\FLUpdater::save_subscription_license( FL_LICENSE_KEY );
		}
	}

	public function init_customizer() {
		// if( !is_user_logged_in() ) {
		// 	return;
		// }
		// /**
		//  * Include the kirki framework to create fields
		//  */
		// if( !class_exists( 'Kirki' ) ) {
		// 	include self::path( 'includes/venders/kirki/kirki.php' );
		// }
		// /**
		//  * Add Config
		//  */
		// \Kirki::add_config( 'mdm_cornerstone', array(
		// 	'capability'    => 'edit_theme_options',
		// 	'option_type'   => 'theme_mod',
		// ) );
		// /**
		//  * Add Section(s)
		//  */
		// \Kirki::add_section( 'featured_images', array(
		//     'title'          => esc_attr__( 'Featured Images', 'mdm_cornerstone' ),
		//     'priority'       => 160,
		// ) );
		// /**
		//  * Add Fields/Controls
		//  */
		// \Kirki::add_field( 'mdm_cornerstone', [
		// 	'type'        => 'image',
		// 	'settings'    => 'mdm_cornerstone[featured_image]',
		// 	'label'       => esc_html__( 'Default Featured Image', 'mdm_cornerstone' ),
		// 	'description' => esc_html__( 'Image to use if no featured image is specified', 'mdm_cornerstone' ),
		// 	'section'     => 'featured_images',
		// 	'default'     => '',
		// 	'choices'     => [
		// 		'save_as' => 'id',
		// 	],
		// ] );
	}


} // end class