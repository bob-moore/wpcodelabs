<?php
/**
 * The plugin bootstrap file
 * This file is read by WordPress to generate the plugin information in the plugin admin area.
 * This file also defines plugin parameters, registers the activation and deactivation functions, and defines a function that starts the plugin.
 * @link    https://bitbucket.org/midwestdigitalmarketing/cornerstone/
 * @since   1.0.0
 * @package mdm_cornerstone
 *
 * @wordpress-plugin
 * Plugin Name: MDM Cornerstone
 * Plugin URI:  https://bitbucket.org/midwestdigitalmarketing/cornerstone/
 * Description: Site specific plugin functionality
 * Version:     0.1.0
 * Author:      Mid-West Digital Marketing
 * Author URI:  http://midwestdigitalmarketing.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: mdm_cornerstone
 */

namespace mdm\cornerstone;

// If this file is called directly, abort
if ( !defined( 'WPINC' ) ) {
    die( 'Cannot load file directly' );
}

if( !class_exists( 'MDMCornerStone' ) ) {

	define( 'MDM_CORNERSTONE_VERSION', '2.0.1' );

	define( 'MDM_CORNERSTONE_URL', plugin_dir_url( __FILE__ ) . 'mdm-cornerstone/' );

	define( 'MDM_CORNERSTONE_DIR', plugin_dir_path( __FILE__ ) . 'mdm-cornerstone/' );

	class MDMCornerStone {

		public function __construct() {

			require_once MDM_CORNERSTONE_DIR . 'vendor/autoload.php';

			Admin::register();

			FrontEnd::register();

			PostTypes::register();

			Taxonomies::register();

			Widgets::register();

			EventOrganizer::register();

			FLBuilder::register();

		}

		public function burn_baby_burn() {
			$classes = array(
				'Admin',
				'Frontend',
				'PostTypes',
				'Taxonomies',
				'Widgets',
				'FLBuilder',
				'EventOrganizer',
			);
			// Loop through each core class
			foreach( $classes as $class ) {

				$class = '\\Mdm\\Cornerstone\\' . $class;

				$class = $class::register();

			}
		}
	}

	$MDMCornerStone = new MDMCornerStone();
}