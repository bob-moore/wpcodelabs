<?php
/**
 * Hero Document Type
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/extensions
 */

namespace Wpcl\Scaffolding\Extensions\Elementor\Documents;

defined( 'ABSPATH' ) || exit;

class Hero extends ThemeBuilderDocument {
	/**
	 * Location key
	 *
	 * @var string lowercase underscrore seperate key for this document
	 * @access protected
	 */
	protected const S_LOCATION_KEY = 'hero';
	/**
	 * Constructor
	 *
	 * Add additional actions necessary for this type of document
	 * @param array $data
	 */
	public function __construct( array $data = [] )
	{
		add_filter( 'scaffolding/markup/atts/hero', [$this, 'atts'] );
		parent::__construct( $data );
	}
	/**
	 * Get the name of the element
	 *
	 * @return string name to show to users
	 */
	public static function get_title()
	{
		return __( 'Hero', 'scaffolding' );
	}
	/**
	 * Add markup attributes to the sidebar element
	 *
	 *
	 * @param array $atts Array of markup attributes
	 */
	public function atts( $atts ) : array
	{
		if ( elementor_location_exits( self::S_LOCATION_KEY, true ) )
		{
			$atts['data-generator'] = 'elementor';
		}

		return $atts;
	}
}
