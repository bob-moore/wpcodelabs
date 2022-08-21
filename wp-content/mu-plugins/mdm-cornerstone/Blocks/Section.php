<?php
/**
 * Page Archive custom block
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/blocks
 */

namespace Mdm\Cornerstone\Blocks;

use \Mdm\Cornerstone\Framework;
use \Mdm\Cornerstone\Subscriber;
use \Mdm\Cornerstone\Plugin;

defined( 'ABSPATH' ) || exit;

class Section extends Framework
{
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'acf/init', [$this, 'register'] );
	}
	/**
	 * Register Blocks
	 *
	 * @see https://www.advancedcustomfields.com/resources/blocks/
	 */
	public function register() : void
	{
		acf_register_block_type(array(
			'name' => 'section',
			'title' => __('Section'),
			'description' => __('A simple section container block'),
			'render_callback' => [ $this, 'render' ],
			'category' => 'formatting',
			'icon' => 'admin-comments',
			'keywords' => array( 'section', 'container' ),
			'supports' => [
				'align' => false,
				'anchor' => true,
				'customClassName' => true,
				'jsx' => true,
			]
		));
	}
	/**
	 * Renders the block HTML.
	 *
	 * @param   array    $attributes The block attributes.
	 * @param   string   $content The block content.
	 * @param   bool     $is_preview Whether or not the block is being rendered for editing preview.
	 * @param   int      $post_id The current post being edited or viewed.
	 * @param   WP_Block $wp_block The block instance (since WP 5.5).
	 * @param   array    $context The block context array.
	 * @return  void
	 */
	public function render( $block, $content, $is_preview, $post_id, $wp_block, $context ) : void
	{
		$context = [
			'fields' => get_fields()
		];

		Subscriber::getInstance( 'FrontEnd' )->templatePart( 'blocks/section', '', $context );
	}
}