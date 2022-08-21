<?php
/**
 * Navigation Panel Document Type
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/extensions
 */
namespace Wpcl\Scaffolding\Extensions\Elementor\Documents;

use \ElementorPro\Modules\ThemeBuilder\Documents\Theme_Section_Document;
use \ElementorPro\Modules\ThemeBuilder\Module;

use \Wpcl\Scaffolding\Utilities;
use \Wpcl\Scaffolding\Subscriber;

defined( 'ABSPATH' ) || exit;

class ThemeBuilderDocument extends Theme_Section_Document {

	protected const S_LOCATION_KEY = '';

	/**
	 * Get the name/type location get
	 *
	 * @return string lowercase location key
	 */
	public static function get_type()
	{
		return static::S_LOCATION_KEY;
	}
	/**
	 * Get the location type for the new theme builder
	 *
	 * @return string lowercase location key
	 */
	protected static function get_site_editor_type()
	{
		return static::S_LOCATION_KEY;
	}
	/**
	 * Get the url of the thumbnail for the new theme builder
	 *
	 * @return string url to image
	 */
	protected static function get_site_editor_thumbnail_url()
	{
		$slug = file_exists( _S_DIR . 'theme/assets/images/elementor/' . static::S_LOCATION_KEY . '.svg' ) ? static::S_LOCATION_KEY : 'themebuilder';
		return _S_URL . "theme/assets/images/elementor/{$slug}.svg";
	}
	/**
	 * Get the document properties
	 *
	 * @return array of document properties
	 */
	public static function get_properties()
	{
		$properties = wp_parse_args( [
			'condition_type' => 'general',
			'location' => static::S_LOCATION_KEY,
			'support_kit' => false,
			'support_site_editor' => true,
		], parent::get_properties() );

		return $properties;
	}
}