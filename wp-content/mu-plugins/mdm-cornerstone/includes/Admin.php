<?php
/**
 * Admin control class
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/classes
 */

namespace Mdm\Cornerstone;

defined( 'ABSPATH' ) || exit;

class Admin extends Framework
{
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'admin_enqueue_scripts', [$this, 'enqueueAsseets'] );
		Subscriber::addAction( 'menu_order', [$this, 'reorderAdminMenu'], 99999 );
		Subscriber::addAction( 'custom_menu_order', '__return_true' );
		Subscriber::addAction( 'admin_menu', [$this, 'renameMenuItems'] );
		Subscriber::addAction( 'jetpack_just_in_time_msgs', '__return_false' );
		Subscriber::addAction( 'wp_dashboard_setup', [$this, 'removedDashboardWidgets'], 99999 );
	}
	/**
	 * Enqueue admin assets
	 */
	public function enqueueAsseets() : void
	{
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_script( 'mdm_cornerstone_admin', Plugin::url( 'assets/js/admin.js' ), [ 'jquery' ], MDM_CORNERSTONE_VERSION, true );
		wp_enqueue_style( 'mdm_cornerstone_admin', Plugin::url( 'assets/css/admin.css' ), [], MDM_CORNERSTONE_VERSION, 'all' );
	}
	/**
	 * Reorder admin sidebar items
	 *
	 * @param  array $menu_items Array of menu items
	 * @return array reordered array of menu items
	 */
	public function reorderAdminMenu( array $menu_items ) : array
	{
		$ordered = [
			'top' => [],
			'posts' => [],
			'secondary' => [],
			'woocommerce' => [],
			'elementor' => [],
			'bottom' => [],
			'penalty' => [],
		];

		foreach( $menu_items as $menu_item )
		{
			/**
			 * Known offenders that we want to stick as low as possible
			 *
			 * These are known annoying plugins that hijack menu space better used for content
			 */
			if ( in_array( $menu_item, [ 'jetpack', 'genesis', 'edit.php?post_type=fl-builder-template', 'edit.php?post_type=acf-field-group', 'googlesitekit-dashboard' ] ) )
			{
				$ordered['penalty'][] = $menu_item;
			}
			/**
			 * Woocommerce
			 */
			else if ( is_plugin_active( 'woocommerce/woocommerce.php' ) && in_array( $menu_item, [ 'edit.php?post_type=product', 'woocommerce', 'separator-woocommerce', 'wc-admin&path=/analytics/revenue', 'woocommerce-marketing', 'wc-admin&path=/analytics/overview' ] ) )
			{
				if ( $menu_item === 'separator-woocommerce' )
				{
					array_unshift( $ordered['woocommerce'], $menu_item );
				}
				else
				{
					$ordered['woocommerce'][] = $menu_item;
				}
			}
			/**
			 * Elementor
			 */
			else if ( in_array( $menu_item, ['edit.php?post_type=elementor_library', 'separator-elementor', 'elementor'] ) )
			{
				$ordered['elementor'][] = $menu_item;
			}
			/**
			 * Our top level items
			 *
			 * Dashboard, and the first seperator
			 */
			else if ( in_array( $menu_item, [ 'index.php', 'separator1', 'video-user-manuals/plugin.php' ] ) )
			{
				$ordered['top'][] = $menu_item;
			}
			/**
			 * Content related items
			 *
			 * anything that starts with edit.php and not already in another area
			 * Nested pages plugin is whitelisted for this section
			 */
			else if ( strripos( $menu_item , 'edit.php' ) !== false || $menu_item === 'nestedpages' )
			{
				$ordered['posts'][] = $menu_item;
			}
			/**
			 * Secondary items
			 */
			else if ( in_array( $menu_item, [ 'upload.php', 'gf_edit_forms', 'edit-comments.php' ] ) )
			{
				$ordered['secondary'][] = $menu_item;
			}
			/**
			 * Everything else
			 * Contains settings, users, appearence, etc.
			 */
			else
			{
				$ordered['bottom'][] = $menu_item;
			}
		}
		return array_merge(
			$ordered['top'],
			$ordered['posts'],
			$ordered['secondary'],
			$ordered['woocommerce'],
			$ordered['elementor'],
			$ordered['bottom'],
			$ordered['penalty']
		);
	}
	/**
	 * Rename admin sidebar items
	 *
	 * Rename "posts" to "blog"
	 */
	public function renameMenuItems() : void
	{
		global $menu;
		global $submenu;

		$menu[5][0] = 'Blog';
		$submenu['edit.php'][5][0] = 'Blog Posts';
		$submenu['edit.php'][10][0] = 'Add Blog Post';
	}
	/**
	 * Remove unwanted dashboard widgets
	 *
	 * Get rid of some of the nagging dashboard widgets that aren't useful for users
	 */
	public function removedDashboardWidgets() : void
	{
		global $wp_meta_boxes;
		// jetpack
		if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['jetpack_summary_widget'] ) )
		{
			unset( $wp_meta_boxes['dashboard']['normal']['core']['jetpack_summary_widget'] );
		}
		// gravity forms
		if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard'] ) )
		{
			unset( $wp_meta_boxes['dashboard']['normal']['core']['rg_forms_dashboard'] );
		}
		// Quick Draft
		if ( isset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] ) )
		{
			unset( $wp_meta_boxes['dashboard']['side']['core']['dashboard_quick_press'] );
		}
		// Elementor overview
		if ( isset( $wp_meta_boxes['dashboard']['normal']['core']['e-dashboard-overview'] ) )
		{
			unset( $wp_meta_boxes['dashboard']['normal']['core']['e-dashboard-overview'] );
		}
	}
}