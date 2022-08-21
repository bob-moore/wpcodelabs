<?php
/**
 * Jetpack control class
 *
 * @class jetpack
 * @package CustomLayouts\Plugins
 */
namespace Devkit\CustomLayouts\Plugins;

use Devkit\CustomLayouts\Framework;
use Devkit\CustomLayouts\Subscriber;

defined( 'ABSPATH' ) || exit;

class Woocommerce extends Framework {
	/**
	 * Construct new instance
	 *
	 * @return object/bool $this or false
	 */
	public function __construct()
	{
		if ( ! $this->isPluginActive( 'woocommerce/woocommerce.php' ) ) {
			return false;
		}
		return parent::__construct();
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 * @see  https://developer.wordpress.org/reference/functions/add_filter/
	 */
	public function addActions()
	{

	}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addFilters()
	{
		Subscriber::addFilter( 'devkit/custom_layouts/display_conditions', [$this, 'addDisplayCondition'] );
		Subscriber::addFilter( 'devkit/validation/singular', [$this, 'validateShopPage'], 10, 2 );
	}

	public function addDisplayCondition( array $conditions ) : array
	{
		return array_merge( $conditions, [
			'is_shop' => __( 'Woocommerce Shop Page', 'devkit_custom_layouts' ),
		] );
	}

	public function validateShopPage( $valid, $rule )
	{
		if ( ! isset( $rule['post'] ) || ! is_array( $rule['post'] ) )
		{
			return $valid;
		}

		if ( is_shop() && in_array( get_option( 'woocommerce_shop_page_id' ), $rule['post'] ) )
		{
			$valid = true;
		}
		return $valid;
	}
}