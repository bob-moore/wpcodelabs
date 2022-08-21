<?php
/**
 * Image control class
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/classes
 */
namespace Wpcl\Scaffolding;

defined( 'ABSPATH' ) || exit;

class Images extends Lib\Framework
{
	protected array $_images_sizes = [];

	public function __construct()
	{
		$this->_image_sizes = [
			'post-thumbnail' =>
			[
				'width' => 1280,
				'height' => 1280 * 0.5625,
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

		return parent::__construct();
	}

	public function getImageSizes()
	{
		return $this->_image_sizes;
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
		Subscriber::addAction( 'after_setup_theme', [$this, 'registerImageSizes'] );
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

	public function registerImageSizes()
	{
		$settings = Subscriber::getInstance( 'Settings' )->get( 'imagesizes', [] );

		foreach ( $this->_image_sizes as $key => $args )
		{
			if ( isset( $settings[$key] ) && $settings[$key] === false )
			{
				continue;
			}
		}
	}
}