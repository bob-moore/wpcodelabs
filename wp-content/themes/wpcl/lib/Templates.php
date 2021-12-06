<?php

namespace Scaffolding;

class Templates extends Framework {

	protected function __construct() {
		add_action( '_s_masthead', [ $this, 'masthead' ] );
		add_action( '_s_hero', [ $this, 'hero' ] );
		add_action( '_s_loop', [ $this, 'loop' ] );
		add_action( '_s_entry_header', [ $this, 'entryHeader' ] );
		add_action( '_s_entry_content', [ $this, 'entryContent' ] );
		add_action( '_s_entry_footer', [ $this, 'entryFooter' ] );
		add_action( '_s_sidebar', [ $this, 'sidebar' ] );
		add_action( '_s_colophon', [ $this, 'colophon' ] );
		add_action( '_s_comments', [ $this, 'comments' ] );
		add_action( '_s_page_after', [$this, 'mobileNav'] );
		add_action( '_s_content_after', [$this, 'authorBox'] );
		add_action( '_s_content_after', [$this, 'postNavigation'] );
		add_action( '_s_while_after', [$this, 'postsNavigation'] );


		add_filter( '_s/classes/masthead', [$this, 'mastheadClasses' ] );
	}

	public static function getPostType() {
		if( is_singular() || in_the_loop() ) {
			return get_post_type();
		}
	}

	/**
	 * Wrapper for get_template_part
	 *
	 * Expand get template part to incude post types and views
	 *
	 * @param  string $modifier : string used to utilize a context specific filter
	 * @return [string]           The context string
	 */
	public static function getTemplatePart( $slug = '', $name = '', $include = true, $relative_path = false, $require_once = false ) {
		/**
		 * Dont waste time if path is empty
		 */
		if( empty( $slug ) ) {
			return;
		}

		// $queried_object = get_queried_object();

		// var_dump( is_main_query() );

		$name = apply_filters( "_s/get_template_part/slug={$slug}", $name );

		$view = Theme::getInstance()->getView();

		$type = get_post_type();

		$type = self::getPostType();

		$templates = array();

		$template = false;

		$page_template = '';

		if( is_page_template() ) {

			$page_template =  explode( '/', get_page_template_slug() );

			$page_template = end( $page_template );

			$page_template = str_ireplace( '.php', '', $page_template );

		}

		/**
		 * Named templates take priority
		 */
		if( !empty( $name ) ) {
			$templates[] = "template-parts/{$slug}/{$name}-{$view}-{$type}.php";
			$templates[] = "template-parts/{$slug}/{$name}-{$type}.php";
			$templates[] = "template-parts/{$slug}/{$name}-{$view}.php";
			$templates[] = "template-parts/{$slug}/{$name}.php";
			$templates[] = "template-parts/{$slug}-{$name}-{$view}-{$type}.php";
			$templates[] = "template-parts/{$slug}-{$name}-{$type}.php";
			$templates[] = "template-parts/{$slug}-{$name}-{$view}.php";
			$templates[] = "template-parts/{$slug}-{$name}.php";
		}

		if( !empty( $page_template ) ) {
			$templates[] = "template-parts/{$slug}/{$page_template}-{$type}.php";
			$templates[] = "template-parts/{$slug}/{$page_template}.php";
			$templates[] = "template-parts/{$slug}-{$page_template}-{$type}.php";
			$templates[] = "template-parts/{$slug}-{$page_template}.php";
		}

		/**
		 * View/context based templates
		 */
		$templates[] = "template-parts/{$slug}/{$view}-{$type}.php";
		$templates[] = "template-parts/{$slug}/{$type}.php";
		$templates[] = "template-parts/{$slug}/{$view}.php";
		$templates[] = "template-parts/{$slug}-{$view}-{$type}.php";
		$templates[] = "template-parts/{$slug}-{$type}.php";
		$templates[] = "template-parts/{$slug}-{$view}.php";
		$templates[] = "template-parts/{$slug}/default.php";
		$templates[] = "template-parts/{$slug}-default.php";
		$templates[] = "template-parts/{$slug}.php";

		/**
		 * Search for, and assign first template found
		 */
		foreach( $templates as $template_path ) {

			$template_found = locate_template( $template_path, false, false );

			if( $template_found ) {
				$template = $relative_path === true ? "/{$template_path}" : $template_found;
				break;
			}
		}
		/**
		 * Maybe include
		 */
		if( $template && $include ) {
			if( $require_once ) {
				require_once $template;
			}
			else {
				require $template;
			}
		}
		/**
		 * Or just return it
		 * Useful for when variables need to be accessible
		 */
		else {
			return $template;
		}
	}

	public function masthead() {
		self::getTemplatePart( 'masthead' );
	}

	public function mastheadClasses( $classes ) {
		if( $this->getHeroImage() && has_action( '_s_hero' ) ) {
			$classes .= ' hero';
		}
		return trim( $classes );
	}

	public function hero() {
		self::getTemplatePart( 'hero' );
	}

	public static function loop() {
		self::getTemplatePart( 'loop' );
	}

	public function entryHeader() {
		self::getTemplatePart( 'entry', 'header' );
	}

	public function entryContent() {
		self::getTemplatePart( 'entry', 'content' );
	}

	public function entryFooter() {
		self::getTemplatePart( 'entry', 'footer' );
	}

	public function sidebar() {
		self::getTemplatePart( 'sidebar', 'primary' );
	}

	public function colophon() {
		self::getTemplatePart( 'colophon' );
	}

	public function mobileNav() {
		self::getTemplatePart( 'sidebar', 'mobile' );
	}

	public function postNavigation() {
		if( is_singular( 'post' ) ) {
			TemplateTags::getInstance()->postNavigation();
		}
	}

	public function authorBox() {
		if( is_singular( 'post' ) ) {
			self::getTemplatePart( 'author-box' );
		}
	}

	public function postsNavigation() {
		// echo 'lajsdflkjasdlkf';
		// previous_post();
		// posts_nav_link('separator','prelabel','nextlabel');
		the_posts_navigation(); // Does prev/next
		// the_posts_pagination(); // Does pagination
	}

	public function comments() {
		/*
		 * If the current post is protected by a password and
		 * the visitor has not yet entered the password we will
		 * return early without loading the comments.
		 */
		if ( post_password_required() ) {
			return;
		}
		if ( comments_open() || get_comments_number() ) {
			comments_template( self::getTemplatePart( 'comments', '', false, true ) );
		}
	}
	/**
	 * Include a single comment
	 *
	 * Not hooked to an action, rather called from templates-parts/comments
	 */
	public function comment( $comment, $args, $depth ) {
		include self::getTemplatePart( 'comment', 'single', false );
	}

	public function getHeroImage() {

		$hero_image = get_header_image();

		$hero_image = apply_filters( '_s_hero_image', $hero_image );

		return empty( $hero_image ) ? false : $hero_image;
	}

	public function customHeader() {

		if( !current_theme_supports( 'custom-header' ) ) {
			return;
		}

		$hero_image = $this->getHeroImage();

		if( $hero_image ) {
			printf( '<style type="text/css">.hero { background-image: url(%s); }</style>', esc_url( $hero_image ) );
		}
	}

	public static function closeDiv() {
		echo '</div>';
	}

}