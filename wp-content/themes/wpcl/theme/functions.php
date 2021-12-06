<?php
/*******************************************************************************
 *                 ______                 __  _
 *                / ____/_  ______  _____/ /_(_)___  ____  _____
 *               / /_  / / / / __ \/ ___/ __/ / __ \/ __ \/ ___/
 *              / __/ / /_/ / / / / /__/ /_/ / /_/ / / / (__  )
 *             /_/    \__,_/_/ /_/\___/\__/_/\____/_/ /_/____/
 *
 ******************************************************************************/
use Scaffolding\Theme;
/**
 * Setup some theme constants
 *
 * Constants used throughout the theme
 * @since version 1.0.0
 */
define( '_S_BASENAME', basename( realpath( __DIR__ . '/..') ) );
define( '_S_ROOT_DIR', str_replace( _S_BASENAME . '/theme', _S_BASENAME . '/', get_stylesheet_directory() ) );
define( '_S_ROOT_URL', str_replace( _S_BASENAME . '/theme', _S_BASENAME . '/', get_stylesheet_directory_uri() ) );
define( '_S_SLUG', '_scaffolding' );
define( '_S_URL', 'https://www.wpcodelabs.com/' );
define( '_S_VERSION', WP_DEBUG === true ? time() : '1.0.0' );

if ( ! isset( $content_width ) ) {
	$content_width = 1280;
}

require _S_ROOT_DIR . 'vendor/autoload.php';

$theme = Theme::getInstance();

function custom_user_social( $contact_methods ) {
	return array_merge( $contact_methods, [
		'github' => 'Github profile URL',
		'wordpress' => 'WordPress profile URL'
	] );
}
add_filter( 'user_contactmethods', 'custom_user_social' );
