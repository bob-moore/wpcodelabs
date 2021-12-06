<?php

/**
 * The plugin file that controls the admin functions
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_wp_cornerstone
 */

namespace mdm\cornerstone;

class FLBuilder extends Framework implements interfaces\ActionHookSubscriber {

	/**
	 * Constructor
	 * @since 1.0.0
	 * @access protected
	 */
	protected function __construct() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		// Only Activate if event organizer plugin is active
		$this->enabled = is_plugin_active('bb-plugin/fl-builder.php');
	}


	/**
	 * Get the action hooks this class subscribes to.
	 * @since 1.0.0
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( 'init' => 'register_modules' ),
		);
	}

	public function register_modules() {

		$modules = glob( self::path( 'flbuilder/*' ), GLOB_ONLYDIR );

		foreach( $modules as $module ) {

			$module = basename( $module );

			include self::path( 'flbuilder/' . $module . '/' . $module . '.php' );

			$instance = new $module();

			$instance->register_module();
		}
	}

} // end class