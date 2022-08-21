<?php
/**
 * Markup beaver builder module
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/flbuilder
 */

namespace Mdm\Cornerstone\Addons\FLBuilder\MWFMarkup;

use \Mdm\Cornerstone\Subscriber;
use \Mdm\Cornerstone\Plugin;

defined( 'ABSPATH' ) || exit;

class MWFMarkup extends \FLBuilderModule
{
	/**
	 * @method __construct
	 */
	public function __construct()
	{
		parent::__construct([
			'name' => __( 'Markup', 'mdm_wp_cornerstone' ),
			'description' => '',
			'category' => __( 'Custom', 'mdm_wp_cornerstone' ),
			'editor_export' => true,
			'partial_refresh' => true,
			'enabled' => true,
		]);
	}
	/**
	 * Render the frontend markup
	 *
	 * @return void
	 */
	public function render() : void
	{
		Subscriber::getInstance( 'FrontEnd' )->renderString(
			$this->settings->code,
			[
				'settings' => $this->settings,
				'module' => $this
			]
		);
	}
	/**
	 * Register the module and its form settings.
	 */
	public function register() : void
	{
		\FLBuilder::register_module( __CLASS__, [
			'general' => // tab
			[
				'title' => __('General', 'mdm_wp_cornerstone'),
				'sections' =>
				[
					'general' => // section
					[
						'title' => __( 'General Options', 'mdm_wp_cornerstone'), // Section Title
						'fields' =>
						[
							'code' => array(
								'type' => 'code',
								'editor' => 'twig',
								'rows' => '18'
							),
						],
					],
				]
			],
		]);
	}
}