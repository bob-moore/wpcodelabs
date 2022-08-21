<?php
/**
 * Image control class
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding;

defined( 'ABSPATH' ) || exit;

class Customizer extends Lib\Framework
{
	/**
	 * Collection of public post types we want settings for
	 *
	 * @var array
	 * @access protected
	 */
	protected array $_post_types = [];
	/**
	 * Construct instance
	 *
	 * Check if we want this constructed, setup post types, and construct parent
	 *
	 * @method __construct
	 * @return $this
	 */
	public function __construct()
	{
		if ( ! is_customize_preview() )
		{
			return false;
		}
		/**
		 * Allow child themes to turn off customizer
		 * Useful to manually set the options using filters instead of customizer
		 * and keep users from mucking about
		 */
		if ( ! apply_filters( 'scaffolding/customizer/enabled', true ) )
		{
			return false;
		}
		/**
		 * Kirki controls must be included immediately
		 */
		if( ! class_exists( 'Kirki' ) )
		{
			include_once _S_DIR . 'Lib/vendors/aristath/kirki/kirki.php';
		}
		/**
		 * Construct parent
		 */
		return parent::__construct();
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'init', [$this, 'generalControls'], 12 );
		Subscriber::addAction( 'init', [$this, 'layoutControls'], 12 );
		Subscriber::addAction( 'init', [$this, 'navigationControls'], 12 );
		Subscriber::addAction( 'init', [$this, 'sidebarControls'], 12 );
	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @see https://developer.wordpress.org/reference/functions/add_filter/
	 */
	public function addFilters() : void {}
	/**
	 * Get post types to work with
	 */
	public function getPostTypes() : array
	{
		$options = [];
		$post_types = get_post_types( [ 'public' => true ] );

		foreach ( $post_types as $post_type ) {
			/**
			 * Bail on post types we don't want
			 */
			if ( in_array( $post_type, ['attachment', 'elementor_library'] ) ) {
				continue;
			}
			/**
			 * Add them to options
			 * @var [type]
			 */
			$pto = get_post_type_object( $post_type );
			$options[$pto->name] = $pto->label;
		}
		return $options;
	}
	/**
	 * Register theme options panel and general controls
	 */
	public function generalControls() : void
	{
		/**
		 * Setup post types
		 */
		$this->_post_types = $this->getPostTypes();
		/**
		 * Theme Options Panel
		 */
		new \Kirki\Panel(
			'_s_theme_options',
			[
				'priority' => 10,
				'title' => esc_html__( 'Theme Options', 'scaffolding' ),
				// 'description' => esc_html__( '_S Theme Options', 'scaffolding' ),
			]
		);
		/**
		 * General Section
		 */
		new \Kirki\Section(
			'_s_general',
			[
				'priority' => 5,
				'title' => esc_html__( 'General', 'scaffolding' ),
				'panel' => '_s_theme_options',
			]
		);
		/**
		 * Featured Image
		 */
		new \Kirki\Field\Image(
			[
				'option_type' => 'theme_mod',
				'capability'  => 'manage_options',
				'settings'    => '_s[post-thumbnail]',
				'label'       => esc_html__( 'Featured Image', 'scaffolding' ),
				'description' => esc_html__( 'Featured image to use if none set on the post', 'scaffolding' ),
				'section'     => '_s_general',
				'default'     => '',
				'choices'     => [
					'save_as' => 'array',
				],
			]
		);
	}

	public function layoutControls() : void
	{
		new \Kirki\Panel(
			'_s_layouts',
			[
				'priority' => 10,
				'title' => esc_html__( 'Layouts', 'scaffolding' ),
				'panel' => '_s_theme_options',
			]
		);

		$this->viewLayoutFields( 'default', __( 'Default', 'scaffolding' ), true );

		$views = apply_filters( 'scaffolding/customizer/layout_views', $this->_post_types );

		foreach ( $views as $slug => $label )
		{
			$this->viewLayoutFields( $slug, $label, true );
		}

		foreach( ['search' => __( 'Search', 'scaffolding' ), '404' => __( '404', 'scaffolding' )] as $slug => $label )
		{
			$this->viewLayoutFields( $slug, $label, false );
		}

	}

	protected function viewLayoutFields( string $slug, string $label, bool $archive = true ) : void
	{
		new \Kirki\Section(
			"_s_layouts_{$slug}",
			[
				'title' => $label,
				'panel' => '_s_layouts',
			]
		);

		$panel_title = $archive === true ? __( 'Singular Layout', 'scaffolding' ) : __( 'Layout', 'scaffolding' );

		new \Kirki\Field\Custom([
			'settings'    => "layout_single_{$slug}_label",
			'option_type' => 'theme_mod',
			'capability'  => 'manage_options',
			'section'     => "_s_layouts_{$slug}",
				'default'         => '<h3 style="padding:15px 10px; background:#fff; margin:0 -12px;">' . $panel_title . '</h3>',
		]);

		new \Kirki\Field\Select(
			[
				'option_type' => 'theme_mod',
				'capability'  => 'manage_options',
				'settings'    => "_s[singular][layout][{$slug}]",
				'label'       => esc_html__( 'Layout', 'scaffolding' ),
				'section'     => "_s_layouts_{$slug}",
				'default'     => '',
				'placeholder' => esc_html__( 'Choose an option', 'scaffolding' ),
				'choices'     => apply_filters( 'scaffolding/customizer/layouts',
					[
					'right-sidebar' => esc_html__( 'Right Sidebar', 'scaffolding' ),
					'left-sidebar' => esc_html__( 'Left Sidebar', 'scaffolding' ),
					'duel-sidebar' => esc_html__( 'Deul Sidebar', 'scaffolding' ),
					'full-width' => esc_html__( 'Full Width', 'scaffolding' ),
				] ),
			]
		);

		new \Kirki\Field\Select(
			[
				'option_type' => 'theme_mod',
				'capability'  => 'manage_options',
				'settings'    => "_s[singular][width][{$slug}]",
				'label'       => esc_html__( 'Width', 'scaffolding' ),
				'section'     => "_s_layouts_{$slug}",
				'default'     => '',
				'placeholder' => esc_html__( 'Default', 'scaffolding' ),
				'choices'     => apply_filters( 'scaffolding/customizer/layout_width',
					[
						'narrow' => __( 'Narrow', 'scaffolding' ),
						'wide' => __( 'Wide', 'scaffolding' ),
						'full' => __( 'Full', 'scaffolding' ),
				] ),
			]
		);

		if ( $archive !== true )
		{
			return;
		}

		new \Kirki\Field\Custom([
			'settings'    => "layout_archive_{$slug}_label",
			'option_type' => 'theme_mod',
			'capability'  => 'manage_options',
			'section'     => "_s_layouts_{$slug}",
				'default'         => '<h3 style="padding:15px 10px; background:#fff; margin:0 -12px;">' . __( 'Archive Layout', '_s' ) . '</h3>',
		]);
		new \Kirki\Field\Select(
			[
				'option_type' => 'theme_mod',
				'capability'  => 'manage_options',
				'settings'    => "_s[archive][layout][{$slug}]",
				'label'       => esc_html__( 'Layout', 'scaffolding' ),
				'section'     => "_s_layouts_{$slug}",
				'default'     => '',
				'placeholder' => esc_html__( 'Choose an option', 'scaffolding' ),
				'choices'     => apply_filters( 'scaffolding/customizer/layouts',
					[
					'right-sidebar' => esc_html__( 'Right Sidebar', 'scaffolding' ),
					'left-sidebar' => esc_html__( 'Left Sidebar', 'scaffolding' ),
					'duel-sidebar' => esc_html__( 'Deul Sidebar', 'scaffolding' ),
					'full-width' => esc_html__( 'Full Width', 'scaffolding' ),
				] ),
			]
		);

		new \Kirki\Field\Select(
			[
				'option_type' => 'theme_mod',
				'capability'  => 'manage_options',
				'settings'    => "_s[archive][width][{$slug}]",
				'label'       => esc_html__( 'Width', 'scaffolding' ),
				'section'     => "_s_layouts_{$slug}",
				'default'     => '',
				'placeholder' => esc_html__( 'Default', 'scaffolding' ),
				'choices'     => apply_filters( 'scaffolding/customizer/layout_width',
					[
						'narrow' => __( 'Narrow', 'scaffolding' ),
						'wide' => __( 'Wide', 'scaffolding' ),
						'full' => __( 'Full', 'scaffolding' ),
				] ),
			]
		);
	}

	public function navigationControls() : void
	{
		new \Kirki\Section(
			'_s_navigation',
			[
				'title' => esc_html__( 'Navigation', 'scaffolding' ),
				'panel' => '_s_theme_options',
			]
		);
		new \Kirki\Field\Custom([
			'settings'    => 'primary_nav_label',
			'option_type' => 'theme_mod',
			'capability'  => 'manage_options',
			'section'     => '_s_navigation',
				'default'         => '<h3 style="padding:15px 10px; background:#fff; margin:0 -12px;">' . __( 'Primary Navigation', '_s' ) . '</h3>',
		]);
		new \Kirki\Field\Select(
			[
				'option_type' => 'theme_mod',
				'capability'  => 'manage_options',
				'settings'    => '_s[components][site-navigation][breakpoint]',
				'label'       => esc_html__( 'Breakpoint', 'scaffolding' ),
				'section'     => '_s_navigation',
				'default'     => 'tablet',
				'placeholder' => esc_html__( 'Choose an option', 'scaffolding' ),
				'choices'     => apply_filters( 'scaffolding/customizer/breakpoints',
					[
					''  => esc_html__( 'Show on All', 'scaffolding' ),
					'phone' => esc_html__( 'Phone', 'scaffolding' ),
					'tablet-small' => esc_html__( 'Tablet - Small', 'scaffolding' ),
					'tablet' => esc_html__( 'Tablet', 'scaffolding' ),
					'tablet-wide' => esc_html__( 'Tablet - Wide', 'scaffolding' ),
					'laptop' => esc_html__( 'Laptop', 'scaffolding' ),
					'desktop' => esc_html__( 'Desktop', 'scaffolding' ),
					'desktop-hd' => esc_html__( 'Desktop - HD', 'scaffolding' ),
				] ),
			]
		);
		new \Kirki\Field\Custom([
			'settings'    => 'navpane_label',
			'option_type' => 'theme_mod',
			'capability'  => 'manage_options',
			'section'     => '_s_navigation',
				'default'         => '<h3 style="padding:15px 10px; background:#fff; margin:0 -12px;">' . __( 'Navpane', '_s' ) . '</h3>',
		]);
		new \Kirki\Field\Toggle(
			[
				'settings'    => '_s[components][navpane][enabled]',
				'label'       => esc_html__( 'Enabled', 'scaffolding' ),
				'description' => esc_html__( 'Enable/Disable Navpane Menu', 'scaffolding' ),
				'section'     => '_s_navigation',
				'default'     => false,
			]
		);
		new \Kirki\Field\Select(
			[
				'option_type' => 'theme_mod',
				'capability'  => 'manage_options',
				'settings'    => '_s[components][navpane][breakpoint]',
				'label'       => esc_html__( 'Breakpoint', 'scaffolding' ),
				'section'     => '_s_navigation',
				'default'     => false,
				'placeholder' => esc_html__( 'Default', 'scaffolding' ),
				'choices'     => apply_filters( 'scaffolding/customizer/breakpoints',
					[
					''  => esc_html__( 'Default', 'scaffolding' ),
					'all'  => esc_html__( 'Show on all', 'scaffolding' ),
					'phone' => esc_html__( 'Phone', 'scaffolding' ),
					'tablet-small' => esc_html__( 'Tablet - Small', 'scaffolding' ),
					'tablet' => esc_html__( 'Tablet', 'scaffolding' ),
					'tablet-wide' => esc_html__( 'Tablet - Wide', 'scaffolding' ),
					'laptop' => esc_html__( 'Laptop', 'scaffolding' ),
					'desktop' => esc_html__( 'Desktop', 'scaffolding' ),
					'desktop-hd' => esc_html__( 'Desktop - HD', 'scaffolding' ),
				] ),
			]
		);
	}
	/**
	 * Register sidebar controls
	 */
	public function sidebarControls() : void
	{
		/**
		 * General Section
		 */
		new \Kirki\Section(
			'_s_sidebars',
			[
				'title' => esc_html__( 'Sidebars', 'scaffolding' ),
				'panel' => '_s_theme_options',
			]
		);
		/**
		 * Label
		 */
		new \Kirki\Field\Custom([
			'settings'    => 'primary_sidebar_label',
			'option_type' => 'theme_mod',
			'capability'  => 'manage_options',
			'section'     => '_s_sidebars',
				'default'         => '<h3 style="padding:15px 10px; background:#fff; margin:0 -12px;">' . __( 'Primary Sidebar', '_s' ) . '</h3>',
		]);
		new \Kirki\Field\Select(
			[
				'option_type' => 'theme_mod',
				'capability'  => 'manage_options',
				'settings'    => '_s[components][sidebar][breakpoint]',
				'label'       => esc_html__( 'Breakpoint', 'scaffolding' ),
				'section'     => '_s_sidebars',
				'default'     => 'tablet',
				'placeholder' => esc_html__( 'Default', 'scaffolding' ),
				'choices'     => apply_filters( 'scaffolding/customizer/breakpoints',
					[
					''  => esc_html__( 'Default', 'scaffolding' ),
					'phone' => esc_html__( 'Phone', 'scaffolding' ),
					'tablet-small' => esc_html__( 'Tablet - Small', 'scaffolding' ),
					'tablet' => esc_html__( 'Tablet', 'scaffolding' ),
					'tablet-wide' => esc_html__( 'Tablet - Wide', 'scaffolding' ),
					'laptop' => esc_html__( 'Laptop', 'scaffolding' ),
					'desktop' => esc_html__( 'Desktop', 'scaffolding' ),
					'desktop-hd' => esc_html__( 'Desktop - HD', 'scaffolding' ),
				] ),
			]
		);
		/**
		 * Label
		 */
		new \Kirki\Field\Custom([
			'settings'    => 'secondary_sidebar_label',
			'option_type' => 'theme_mod',
			'capability'  => 'manage_options',
			'section'     => '_s_sidebars',
				'default'         => '<h3 style="padding:15px 10px; background:#fff; margin:0 -12px;">' . __( 'Secondary Sidebar', '_s' ) . '</h3>',
		]);
		new \Kirki\Field\Select(
			[
				'option_type' => 'theme_mod',
				'capability'  => 'manage_options',
				'settings'    => '_s[components][alt-sidebar][breakpoint]',
				'label'       => esc_html__( 'Breakpoint', 'scaffolding' ),
				'section'     => '_s_sidebars',
				'default'     => 'tablet',
				'placeholder' => esc_html__( 'Default', 'scaffolding' ),
				'choices'     => apply_filters( 'scaffolding/customizer/breakpoints',
					[
					''  => esc_html__( 'Default', 'scaffolding' ),
					'phone' => esc_html__( 'Phone', 'scaffolding' ),
					'tablet-small' => esc_html__( 'Tablet - Small', 'scaffolding' ),
					'tablet' => esc_html__( 'Tablet', 'scaffolding' ),
					'tablet-wide' => esc_html__( 'Tablet - Wide', 'scaffolding' ),
					'laptop' => esc_html__( 'Laptop', 'scaffolding' ),
					'desktop' => esc_html__( 'Desktop', 'scaffolding' ),
					'desktop-hd' => esc_html__( 'Desktop - HD', 'scaffolding' ),
				] ),
			]
		);
	}
}