<?php
/**
 * Check if certain addons exist
 */
function _s_is_addon_active( $addon = '' ) {

	switch ( $addon ) {
		case 'woocommerce':
			$active = class_exists( 'woocommerce' );
			break;
		case 'flbuilder':
			$active = class_exists( 'FLBuilderModel' );
			break;
		case 'jetpack':
			$active = class_exists( 'Jetpack' );
			break;
		case 'wpqueryengine' :
			$active = class_exists( '\\WPCL\\QueryEngine\\Plugin' );
			break;
		case 'acl' :
			$active = defined( 'ACL_ACTIVE' ) && ACL_ACTIVE === true;
			break;
		case 'edd' :
			$active = class_exists( 'Easy_Digital_Downloads' );
			break;
		default:
			$active = false;
			break;
	}

	return $active;
}
/**
 * Check if a page uses beaver builder
 */
function _s_is_fl_builder() {
	if( class_exists( 'FLBuilderModel' ) ) {
		return !empty( FLBuilderModel::is_builder_enabled() );
	}
	return '';
}
/**
 * Maybe include Woocommerce support
 */
if( _s_is_addon_active( 'woocommerce' ) ) {
	include_once _S_ROOT_DIR . 'addons/woocommerce.php';
}
/**
 * Maybe include Beaver Builder support
 */
if( _s_is_addon_active( 'flbuilder' ) ) {
	include_once _S_ROOT_DIR . 'addons/fl-builder.php';
}
/**
 * Maybe include Jetpack support
 */
if( _s_is_addon_active( 'jetpack' ) ) {
	include_once _S_ROOT_DIR . 'addons/jetpack.php';
}
/**
 * Maybe include WP Query Engine support
 */
if( _s_is_addon_active( 'wpqueryengine' ) ) {
	include_once _S_ROOT_DIR . 'addons/wp_query_engine.php';
}
/**
 * Maybe include Advanced Custom Layouts support
 */
if( _s_is_addon_active( 'edd' ) ) {
	include_once _S_ROOT_DIR . 'addons/easy-digital-downloads.php';
}
/**
 * Maybe include Easy Digital Downloads support
 */
if( _s_is_addon_active( 'acl' ) ) {
	include_once _S_ROOT_DIR . 'addons/acl.php';
}