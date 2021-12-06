<?php

/**
 * The plugin file that controls core wp tweaks and configurations
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_wp_cornerstone
 */

namespace mdm\cornerstone;

class FrontEnd extends Framework implements interfaces\FilterHookSubscriber, interfaces\ActionHookSubscriber, interfaces\ShortcodeHookSubscriber {

	/**
	 * Get the filter hooks this class subscribes to.
	 * @return array
	 */
	public function get_filters() {

		return array(
			array( 'tiny_mce_plugins' => 'disable_emojis_tinymce' ),
			array( 'wp_resource_hints' => array( 'disable_emojis_remove_dns_prefetch', 10, 2 ) ),
			array( 'jetpack_lazy_images_blacklisted_classes' => 'disable_lazy_load_on_logo' ),
			array( 'gform_enable_field_label_visibility_settings' => '__return_true' ),
			array( 'fl_builder_render_assets_inline' => '__return_true' ),
		);
	}

	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( 'wp_enqueue_scripts' => 'enqueue_scripts' ),
			array( 'wp_enqueue_scripts' => 'enqueue_styles' ),
			array( 'init' => 'clean_head' ),
		);
	}

	/**
	 * Get the shortcode hooks this class subscribes to.
	 * @return array
	 */
	public function get_shortcodes() {
		return array(

		);
	}

	public function enqueue_scripts() {
		// // Enqueue public script
		// wp_enqueue_script( sprintf( '%s_public', 'mdm_cornerstone' ), self::url( 'assets/js/public.js' ), array( 'jquery' ), MDM_CORNERSTONE_VERSION, true );
		// // Localize public script
		// wp_localize_script( sprintf( '%s_public', 'mdm_cornerstone' ), 'mdm_cornerstone', apply_filters( 'mdm_cornerstone' . '_public_script_args', array(
		// 	'wpajaxurl' => admin_url( 'admin-ajax.php'),
		// 	'pluginurl' => self::url(''),
		// ) ) );
	}

	public function enqueue_styles() {
		// wp_enqueue_style( sprintf( '%s_public', 'mdm_cornerstone' ), self::url( 'assets/css/public.css' ), array( ), MDM_CORNERSTONE_VERSION, 'all' );
	}

	public function clean_head() {
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_head', 'edd_version_in_header' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}

	/**
	* Filter function used to remove the tinymce emoji plugin.
	*
	* @param array $plugins
	* @return array Difference betwen the two arrays
	*/
	function disable_emojis_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		} else {
			return array();
		}
	}

	/**
	* Remove emoji CDN hostname from DNS prefetching hints.
	*
	* @param array $urls URLs to print for resource hints.
	* @param string $relation_type The relation type the URLs are printed for.
	* @return array Difference betwen the two arrays.
	*/
	function disable_emojis_remove_dns_prefetch( $urls, $relation_type ) {
		if ( 'dns-prefetch' == $relation_type ) {
			/** This filter is documented in wp-includes/formatting.php */
			$emoji_svg_url = apply_filters( 'emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/' );
			$urls = array_diff( $urls, array( $emoji_svg_url ) );
		}
		return $urls;
	}

	public function disable_lazy_load_on_logo( $classes ) {
		$classes[] = 'custom-logo';
		return $classes;
	}

} // end class