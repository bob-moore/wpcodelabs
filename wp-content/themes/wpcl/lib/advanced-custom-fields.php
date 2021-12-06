<?php
/**
 * Maybe hide the admin menu item for ACF
 *
 * If a user has activated the plugin themselves, allow it to be shown. Otherwise hide *our* instance of it
 */
if( !class_exists('acf_pro') && !class_exists('ACF') && WP_DEBUG != true ) {
	/**
	 * Add Filter to not show admin
	 */
	add_filter( 'acf/settings/show_admin', '__return_false' );
	/**
	 * Add filter to change ACF URL
	 */
	add_filter( 'acf/settings/url', 'ac_acf_url' );
}
/**
 * Include advanced custom fields
 */
include_once _S_ROOT_DIR . 'vendor/acf/acf.php';
/**
 * Correct the ACF url
 */
function ac_acf_url( $url ) {
	return _S_ROOT_URL . 'vendor/acf/';
}
/**
 * Import fields
 */
function _s_import_acf_fields() {

	if( !function_exists( 'acf_add_local_field_group' ) ) {
		return false;
	}

	$files = glob( _S_ROOT_DIR . 'assets/json/acf-*.json' );

	foreach( $files as $file ) {

		$fields = json_decode( file_get_contents( $file ), true );

		acf_add_local_field_group( $fields[0] );

	}
}
add_action( 'acf/init', '_s_import_acf_fields' );

