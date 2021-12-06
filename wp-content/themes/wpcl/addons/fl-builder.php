<?php
/**
 * Add our theme stylesheet as a dependency to beaver builder
 * @see  https://kb.wpbeaverbuilder.com/article/117-common-beaver-builder-plugin-filter-examples
 */
function _s_fl_builder_layout_style_dependencies( $deps ) {
	$deps[] = '_s_styles';
	return $deps;
}
add_filter( 'fl_builder_layout_style_dependencies', '_s_fl_builder_layout_style_dependencies' );
/**
 * Force inline rendering of css to fix caching issues
 * @see https://kb.wpbeaverbuilder.com/article/699-fix-for-some-caching-issues-load-css-and-javascript-inline
 */
add_filter( 'fl_builder_render_assets_inline', '__return_true' );
/**
 * Disable Gutenberg for beaver builder enabled post types
 *
 */
function _s_disable_gutenberg( $can_edit, $post_type ) {

	// Bail and do nothing if beaver builder isn't installed
	if( !class_exists( 'FLBuilderModel' ) ) {
		return $can_edit;
	}

	$activated_post_types = get_option( '_fl_builder_post_types', array( 'page' ) );

	if( in_array( get_post_type(), $activated_post_types ) && FLBuilderModel::is_builder_enabled() ) {

		$can_edit = false;
	}

	return $can_edit;

}
add_filter( 'gutenberg_can_edit_post_type', '_s_disable_gutenberg', 10, 2 );
add_filter( 'use_block_editor_for_post_type', '_s_disable_gutenberg', 10, 2 );

/**
 * Disable Classic Editor for beaver builder enabled post types
 *
 */
function _s_disable_classic_editor() {
	// Bail and do nothing if beaver builder isn't installed
	if( !class_exists( 'FLBuilderModel' ) ) {
		return false;
	}

	$screen = get_current_screen();
	$activated_post_types = get_option( '_fl_builder_post_types', array() );

	if( in_array( $screen->id , $activated_post_types ) && FLBuilderModel::is_builder_enabled() ) {
		remove_post_type_support( $screen->id, 'editor' );
	}
}
add_action( 'admin_head', '_s_disable_classic_editor' );

/**
 * Make beaver builder the default editor if post type is supported
 */
function _s_make_beaver_builder_default( $post_ID, $post, $update ) {
	// Bail and do nothing if beaver builder isn't installed
	if( !class_exists( 'FLBuilderModel' ) ) {
		return false;
	}

	$activated_post_types = get_option( '_fl_builder_post_types', array() );

	if( in_array( $post->post_type, $activated_post_types ) && !$update ) {
		update_post_meta( $post_ID, '_fl_builder_enabled', true );
	}

}
add_action( 'wp_insert_post', '_s_make_beaver_builder_default', 10, 3 );

function _s_register_fl_builder_modules() {

	$modules = glob( _S_ROOT_DIR . 'addons/flbuilder/*', GLOB_ONLYDIR );

	foreach( $modules as $module ) {

		$module = basename( $module );

		include _S_ROOT_DIR . 'addons/flbuilder/' . $module . '/' . $module . '.php';

		$instance = new $module();

		$instance->register_module();
	}

}
add_action( 'init', '_s_register_fl_builder_modules' );

/**
 * Parse flbuilder links
 */
function _s_flbuilder_link_markup( $settings, $class = '', $context = 'open' ) {
	/**
	 * Make sure we have a link
	 */
	if( empty( $settings->link ) ) {
		return;
	}
	/**
	 * Just close the link if we are closing
	 */
	if( $context === 'close' ) {
		return '</a>';
	}
	/**
	 * Begin constructing our markup
	 */
	$rel  = $settings->link_target === '_blank' ? ' noopener noreferrer' : '';
	$rel .= $settings->link_nofollow === 'yes' ? ' nofollow' : '';
	$rel  = trim( $rel );
	$rel  = !empty( $rel ) ? " rel='{$rel}'" : '';
	/**
	 * Return constructed markup
	 */
	return sprintf( '<a href="%s" class="%s" target="%s"%s>', $settings->link, $class, $settings->link_target, $rel );
}

function _s_flbuilder_defaults( $defaults, $form ) {

	if( $form == 'row' ) {
		$defaults->padding_left  = '26';
		$defaults->padding_right = '26';
	}

	elseif( $form === 'column' ) {
		$defaults->margin_bottom  = '26';
	}

	return $defaults;
}
add_filter('fl_builder_settings_form_defaults', '_s_flbuilder_defaults', 10, 2);