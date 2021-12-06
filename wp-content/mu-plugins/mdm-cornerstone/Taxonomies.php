<?php

namespace mdm\cornerstone;

class Taxonomies extends \mdm\cornerstone\Framework implements \mdm\cornerstone\interfaces\ActionHookSubscriber {

	/**
	 * Get the action hooks this class subscribes to.
	 * @return array
	 */
	public function get_actions() {
		return array(
			array( 'init' => 'add_taxonomies' ),
		);
	}

	/**
	 * Register taxonomies
	 * @see https://codex.wordpress.org/Function_Reference/register_taxonomy_for_object_type
	 * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	public static function add_taxonomies() {
		// Get all taxonomies
		$taxonomies = self::get_classes( 'taxonomies/' );
		// Iterate and register each
		foreach( $taxonomies as $taxonomy_name ) {
			// Append namespace to taxonomy
			$taxonomy = '\\mdm\\cornerstone\\taxonomies\\' . $taxonomy_name;
			// Initialize post type
			$taxonomy = $taxonomy::register();
			// Register with wordpress
			$taxonomy->register_taxonomy();
		}
	}
}