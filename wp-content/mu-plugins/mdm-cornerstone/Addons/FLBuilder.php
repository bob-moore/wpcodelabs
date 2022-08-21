<?php
/**
 * Beaver Builder control class
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/classes
 */

namespace Mdm\Cornerstone\Addons;

use \Mdm\Cornerstone\Framework;
use \Mdm\Cornerstone\Subscriber;
use \Mdm\Cornerstone\Plugin;

defined( 'ABSPATH' ) || exit;

class FLBuilder extends Framework
{
	/**
	 * Check if Beaver Builder plugin (agency) is active and construct
	 *
	 * @method __construct
	 * @return $this
	 */
	public function __construct()
	{
		if ( ! Plugin::isPluginActive( 'bb-plugin/fl-builder.php' ) )
		{
			return false;
		}
		return parent::__construct();
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 */
	public function addActions() : void
	{
		Subscriber::addAction( 'init', [$this, 'registerModules'] );
	}
	/**
	 * Register custom Beaver Builder modules
	 *
	 * Glob includes/elementor directory for PHP files and registers them as modules
	 */
	public function registerModules() : void
	{
		$modules = glob( Plugin::path( 'Addons/FLBuilder/*' ), GLOB_ONLYDIR );

		foreach( $modules as $module ) {

			$module = basename( $module );

			$module = __NAMESPACE__ . '\\FLBuilder\\' . $module . '\\' . $module;

			$instance = new $module();

			$instance->register();
		}
	}
}