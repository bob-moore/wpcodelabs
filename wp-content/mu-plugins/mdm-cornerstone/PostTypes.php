<?php

namespace mdm\cornerstone;

class PostTypes extends \mdm\cornerstone\Framework implements \mdm\cornerstone\interfaces\ActionHookSubscriber {
	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( 'init' => 'register_post_types' ),
		);
	}

	/**
	 * Register each custom post type with wordpress
	 */
	public static function register_post_types() {
		// Get all post types
		$post_types = self::get_classes( 'posttypes' );
		// Loop through each post type
		foreach( $post_types as $post_type_name ) {
			// // Append namespace to post type
			$post_type = '\\mdm\\cornerstone\\posttypes\\' . $post_type_name;
			// // Initialize post type
			$post_type = $post_type::register();
			// Register with wordpress
			$post_type->register_post_type();
		}
	}
}