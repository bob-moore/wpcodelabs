<?php
/*******************************************************************************
 *                 ______                 __  _
 *                / ____/_  ______  _____/ /_(_)___  ____  _____
 *               / /_  / / / / __ \/ ___/ __/ / __ \/ __ \/ ___/
 *              / __/ / /_/ / / / / /__/ /_/ / /_/ / / / (__  )
 *             /_/    \__,_/_/ /_/\___/\__/_/\____/_/ /_/____/
 *
 ******************************************************************************/
use \Wpcl\Scaffolding\Subscriber;
use \Wpcl\Scaffolding\Utilities;

define( 'CHILD_THEME_ROOT_DIR', get_stylesheet_directory() );
define( 'CHILD_THEME_ROOT_URL', get_stylesheet_directory_uri() );
define( 'CHILD_THEME_VERSION', time() );

add_theme_support( 'yoast-seo-breadcrumbs' );

/**
 * Enqueue scripts and styles
 *
 * @since  1.0.0
 * @return void
 */
function theme_enqueue_assets()
{
	/**
	 * Scripts
	 */
	wp_enqueue_script( 'child-theme-scripts', CHILD_THEME_ROOT_URL . "/assets/js/public.min.js", ['jquery', '_s_scripts'], CHILD_THEME_VERSION, true );
	/**
	 * Styles
	 */
	wp_enqueue_style( 'child-theme-google-fonts', "//fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700;900&display=swap", [], CHILD_THEME_VERSION, 'all' );
	wp_enqueue_style( 'child-theme-styles', CHILD_THEME_ROOT_URL . "/assets/css/public.min.css", [], CHILD_THEME_VERSION, 'all' );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_assets', 12 );

function theme_image_sizes()
{
	$image_sizes = [
		'post-thumbnail' =>
		[
			'width' => 1600,
			'height' => 1600 * 0.5625,
			'crop' => true,
			'name' => __( 'Post Thumbnail', 'scaffolding' )
		],
		'small_16x9' =>
		[
			'width' => 300,
			'height' => 300 * 0.5625,
			'crop' => true,
			'name' => __( 'Small 16x9', 'scaffolding' )
		],
		'small_4x3' =>
		[
			'width' => 300,
			'height' => 300 * 0.75,
			'crop' => true,
			'name' => __( 'Small 4x3', 'scaffolding' )
		],
		'small_1x1' =>
		[
			'width' => 300,
			'height' => 300,
			'crop' => true,
			'name' => __( 'Small Square', 'scaffolding' )
		],
		'medium_16x9' =>
		[
			'width' => get_option( 'medium_size_w' ),
			'height' => get_option( 'medium_size_w' ) * 0.5625,
			'crop' => true,
			'name' => __( 'Medium 16x9', 'scaffolding' )
		],
		'medium_4x3' =>
		[
			'width' => get_option( 'medium_size_w' ),
			'height' => get_option( 'medium_size_w' ) * 0.75,
			'crop' => true,
			'name' => __( 'Medium 4x3', 'scaffolding' )
		],
		'medium_1x1' =>
		[
			'width' => get_option( 'medium_size_w' ),
			'height' => get_option( 'medium_size_w' ),
			'crop' => true,
			'name' => __( 'Medium Square', 'scaffolding' )
		],
		'tablet_16x9' =>
		[
			'width' => 768,
			'height' => 768 * 0.5625,
			'crop' => true,
			'name' => __( 'Tablet 16x9', 'scaffolding' )
		],
		'tablet_4x3' =>
		[
			'width' => 768,
			'height' => 768 * 0.75,
			'crop' => true,
			'name' => __( 'Tablet 4x3', 'scaffolding' )
		],
		'tablet_1x1' =>
		[
			'width' => 768,
			'height' => 768,
			'crop' => true,
			'name' => __( 'Small Square', 'scaffolding' )
		],
		'large_16x9' =>
		[
			'width' => get_option( 'large_size_w' ),
			'height' => get_option( 'large_size_w' ) * 0.5625,
			'crop' => true,
			'name' => __( 'Small 16x9', 'scaffolding' )
		],
		'large_4x3' =>
		[
			'width' => get_option( 'large_size_w' ),
			'height' => get_option( 'large_size_w' ) * 0.75,
			'crop' => true,
			'name' => __( 'Small 4x3', 'scaffolding' )
		],
		'large_1x1' =>
		[
			'width' => get_option( 'large_size_w' ),
			'height' => get_option( 'large_size_w' ),
			'crop' => true,
			'name' => __( 'Small Square', 'scaffolding' )
		],
		'full_16x9' =>
		[
			'width' => 1600,
			'height' => 1600 * 0.5625,
			'crop' => true,
			'name' => __( 'HD 16x9', 'scaffolding' )
		],
		'full_4x3' =>
		[
			'width' => 1600,
			'height' => 1600 * 0.75,
			'crop' => true,
			'name' => __( 'HD 4x3', 'scaffolding' )
		],
		'full_1x1' =>
		[
			'width' => 1600,
			'height' => 1600,
			'crop' => true,
			'name' => __( 'HD Square', 'scaffolding' )
		],
		'hd_16x9' =>
		[
			'width' => 2560,
			'height' => 2560 * 0.5625,
			'crop' => true,
			'name' => __( 'HD 16x9', 'scaffolding' )
		],
		'hd_4x3' =>
		[
			'width' => 2560,
			'height' => 2560 * 0.75,
			'crop' => true,
			'name' => __( 'HD 4x3', 'scaffolding' )
		],
		'hd_1x1' =>
		[
			'width' => 2560,
			'height' => 2560,
			'crop' => true,
			'name' => __( 'HD Square', 'scaffolding' )
		],
	];
	return $image_sizes;
}

function theme_register_image_sizes()
{
	$image_sizes = theme_image_sizes();

	foreach ( $image_sizes as $key => $args ) {

		if ( $key === 'post-thumbnail' ) {
			set_post_thumbnail_size( $args['width'], $args['height'], $args['crop'] );
		}

		else {
			add_image_size( $key, $args['width'], $args['height'], $args['crop'] );
		}
	}
	// set_post_thumbnail_size( 1600, 1600 * 0.5625, $args['crop'] );
}
add_action('after_setup_theme', 'theme_register_image_sizes');

function theme_image_size_names( $wp_sizes ) {

	$image_sizes = theme_image_sizes();

	$image_size_names = [];

	foreach ( $image_sizes as $key => $args ) {
		$image_size_names[$key] = ucwords( str_replace( ['-', '_'], '', $key ) );
	}

	return array_merge( $image_size_names, $wp_sizes );
}
add_filter( 'image_size_names_choose', 'theme_image_size_names' );

function theme_parts()
{
	/**
	 * Add custom templates
	 */
	// Subscriber::addAction( 'hero', [ 'Layout', 'hero' ] );
	Subscriber::addAction( 'hero', [ 'Layout', 'breadcrumbs' ] );

	Subscriber::removeAction( 'entry/meta', ['Layout', 'entry/meta-author'], 4 );
	Subscriber::removeAction( 'entry/meta', ['Layout', 'entry/meta-categories' ], 8 );
	Subscriber::removeAction( 'entry/meta', ['Layout', 'entry/meta-comments' ] );



	if ( is_singular() )
	{
		Subscriber::addAction( 'entry/header/includes', ['Layout', 'entry/meta-categories' ], 8 );
		Subscriber::removeAction( 'entry/header/includes', ['Layout', 'entry/thumbnail' ], 5 );
		Subscriber::addAction( 'entry/content', ['Layout', 'entry/thumbnail' ], 5 );
	}
	else {
		Subscriber::removeAction( 'entry/meta', ['Layout', 'entry/meta-date'], 6 );
		Subscriber::removeAction( 'entry/footer/includes', ['Layout', 'entry/info'] );
		add_action( 'loop/start', function() {
			echo '<div class="loop-container">';
		});

		add_action( 'loop/end', function() {
			echo '</div>';
		});
	}
}
add_action( 'wp', 'theme_parts', 8 );

function theme_scope( $_scope )
{
	if ( function_exists( 'bcn_display_list' ) )
	{
		$_scope['breadcrumbs'] = bcn_display( true );
	}
	return $_scope;
}
add_filter( 'timber/context', 'theme_scope' );

/**
 * Add functions to twig
 *
 * @param object $twig Twig instance to add function to
 */
function theme_twig_functions( object $twig ) : object
{
	$twig->AddFunction( new \Timber\Twig_Function( 'add_to_any', 'theme_add_to_any' ) );
	return $twig;
}
add_filter( 'timber/twig', 'theme_twig_functions' );

function theme_add_to_any( $args = [] )
{
	if ( function_exists( 'ADDTOANY_SHARE_SAVE_KIT' ) )
	{
		ADDTOANY_SHARE_SAVE_KIT( $args );
	}
}