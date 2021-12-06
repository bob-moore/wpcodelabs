<?php

namespace Scaffolding;

class ThemeMods extends Framework {

	protected function __construct() {
		/**
		 * Maybe hide the admin menu item for ACF
		 *
		 * If a user has activated the plugin themselves, allow it to be shown. Otherwise hide *our* instance of it
		 */
		if( !class_exists('acf_pro') && !class_exists('ACF') && WP_DEBUG != true ) {
			add_filter( 'acf/settings/show_admin', '__return_false' );
		}
		/**
		 * Load ACF
		 */
		$this->loadAcf();
		/**
		 * Load Kirki and customizer controls
		 */
		$this->registerCustomizerControls();
		/**
		 * Load Actions
		 */
		add_action( 'acf/init', [$this, 'registerCustomFields'] );
		add_action( '_s_start', [$this, 'layoutOptions'] );
		add_action( 'customize_register', [$this, 'registerCustomizerConfig'] );
		/**
		 * Add filters
		 */
		add_filter( 'get_the_archive_title', [$this, 'trimArchiveTitle'] );
		add_filter( '_s_hero_image', [$this, 'customHeroImage'] );
		add_filter( 'kirki_config', [$this, 'fixKirkiUrls'] );
		add_filter( '_s_default_thumbnail', [$this, 'defaultThumbnail'] );
	}

	public function loadAcf() {
		/**
		 * We don't need to load ACF on the frontend
		 */
		if( !is_admin() ) {
			return;
		}
		/**
		 * If it's already loaded, don't bother
		 */
		if ( class_exists('acf_pro') || class_exists('ACF') ) {
			return;
		}
		/**
		 * Include ACF
		 */
		include_once _S_ROOT_DIR . 'vendor/acf/acf.php';
		/**
		 * Correct ACF Url
		 */
		add_filter( 'acf/settings/url', function( $url ) {
			return _S_ROOT_URL . 'vendor/acf/';
		});
	}

	public function registerCustomFields() {
		// if( !function_exists( 'acf_add_local_field_group' ) ) {
		// 	return false;
		// }

		// $files = glob( _S_ROOT_DIR . 'assets/json/acf-*.json' );

		// foreach( $files as $file ) {

		// 	$fields = json_decode( file_get_contents( $file ), true );

		// 	// acf_add_local_field_group( $fields[0] );

		// }
	}

	public function fixKirkiUrls( $config ) {
		$config['url_path'] = _S_ROOT_URL . 'vendor/aristath/kirki';
		return $config;
	}

	public function registerCustomizerConfig() {
		include_once _S_ROOT_DIR . 'vendor/aristath/kirki/kirki.php';
		/**
		 * Create Kirki Config
		 */
		\Kirki::add_config( '_s', array(
			'capability'    => 'edit_theme_options',
			'option_type'   => 'theme_mod',
		) );
	}

	public function registerCustomizerControls() {
		/**
		 * We don't need to load Kirki on the frontend
		 */
		if( !is_customize_preview() ) {
			// return;
		}
		/**
		 * Include the file
		 */
		include_once _S_ROOT_DIR . 'vendor/aristath/kirki/kirki.php';
		/**
		 * Include customizer controls
		 */
		\Kirki::add_field( '_s', [
			'type'        => 'image',
			'settings'    => '_s_default_thumbnail',
			'label'       => esc_html__( 'Default Featured Image', '_s' ),
			'description' => esc_html__( 'Default image to be used if no featured image exists for a post', '_s' ),
			'section'     => 'title_tagline',
			'default'     => '',
			'priority'    => 99,
			'choices'     => [
				'save_as' => 'id',
			],
		] );
	}

	public function trimArchiveTitle( $title ) {
		$parts = explode( ':', $title );
		return isset( $parts[1] ) ? trim( $parts[1] ) : $title;
	}

	public function customHeroImage( $image ) {
		/**
		 * We can go ahead and bail, if global is set to not have a hero image
		 */
		if( empty( $image ) && get_post_meta( get_the_id(), '_s_layout_hero', true ) === 'Default' ) {
			return $image;
		}

		$custom_image = get_post_meta( get_the_id(), 'post_header_image', true );

		if( !empty( $custom_image ) ) {
			$image = wp_get_attachment_url( $custom_image );
		}

		return $image;
	}

	public function layoutOptions() {
		/**
		 * Hero options
		 */
		$hero = get_post_meta( get_the_id(), '_s_layout_hero', true );

		if( empty( Templates::getInstance()->getHeroImage() ) ) {
			remove_all_actions( '_s_hero' );
		}

		if( $hero === 'Disabled' ) {
			remove_all_actions( '_s_hero' );
		}
		elseif( $hero === 'Enabled' && empty( Templates::getInstance()->getHeroImage() ) ) {
			remove_all_actions( '_s_hero' );
		}
	}

	public function defaultThumbnail( $image ) {
		$default = [
			'image' => get_theme_mod( '_s_default_thumbnail' ),
		];

		return !empty( $default['image'] ) ? $default : $image;
	}

}