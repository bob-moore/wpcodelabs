<?php
/**
 * Sidebar Document Type
 *
 * @link https://midwestfamilymadison.com
 * @package scaffolding/extensions
 */
namespace Wpcl\Scaffolding\Extensions\Elementor\Documents;

use \Wpcl\Scaffolding\Subscriber;
use \Wpcl\Scaffolding\Utilities;

use \ElementorPro\Modules\ThemeBuilder\Module as ThemeBuilder;

defined( 'ABSPATH' ) || exit;

class Sidebar extends ThemeBuilderDocument
{
	/**
	 * Location key
	 *
	 * @var string lowercase underscrore seperate key for this document
	 * @access protected
	 */
	protected const S_LOCATION_KEY = 'sidebar';
	/**
	 * Constructor
	 *
	 * Add additional actions necessary for this type of document
	 * @param array $data
	 */
	public function __construct( array $data = [] )
	{
		add_filter( 'scaffolding/layout', [$this, 'forceLayout'] );
		add_filter( 'scaffolding/markup/atts/sidebar', [$this, 'atts'] );
		parent::__construct( $data );
	}
	/**
	 * Get the name of the element
	 *
	 * @return string name to show to users
	 */
	public static function get_title()
	{
		return __( 'Sidebar', 'scaffolding' );
	}

	public function forceLayout( $layout )
	{
		if ( is_singular( 'elementor_library' ) )
		{
			$document = ThemeBuilder::instance()->get_document( get_the_id() );

			if ( $document->get_location() === self::S_LOCATION_KEY )
			{
				$layout = 'right-sidebar';
			}
		}
		return $layout;
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
