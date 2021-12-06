<?php
/**
 * API_Manager handles registering actions and hooks with the
 * WordPress Plugin API.
 */

namespace mdm\cornerstone;

class Loader extends Framework {

	/**
	 * Registers an object with the WordPress Plugin API.
	 * @param mixed $object
	 */
	public function registerHooks( $object ) {
		// Register Actions
		if ( $object instanceof interfaces\ActionHookSubscriber ) {
			$this->register_actions( $object );
		}
		// Register Filters
		if ( $object instanceof interfaces\FilterHookSubscriber ) {
			$this->register_filters( $object );
		}
		// Register Shortcodes
		if ( $object instanceof interfaces\ShortcodeHookSubscriber ) {
			$this->register_shortcodes( $object );
		}
	}

	/**
	 * Register an object with a specific action hook.
	 * @param ActionHookSubscriber $object
	 * @param string $name
	 * @param mixed $parameters
	 */
	private function register_action( interfaces\ActionHookSubscriber $object, $name, $parameters ) {
		// For string params
		if( is_string( $parameters ) ) {
			// If a class method
			if( method_exists( $object, $parameters ) ) {
				add_action( $name, array( $object, $parameters ) );
			}
			// Else if a standard wordpress function
			else if( function_exists( $parameters ) ) {
				add_action( $name, $parameters );
			}
		}
		// For array of params (name, priority, args)
		elseif( is_array( $parameters ) && isset( $parameters[0] ) ) {
			// If a class method
			if( method_exists( $object, $parameters[0] ) ) {
				add_action( $name, array( $object, $parameters[0] ), isset( $parameters[1] ) ? $parameters[1] : 10, isset( $parameters[2] ) ? $parameters[2] : 1 );
			}
			// Else if a standard wordpress function
			else if( function_exists( $parameters[0] ) ) {
				add_action( $name, $parameters[0], isset( $parameters[1] ) ? $parameters[1] : 10, isset( $parameters[2] ) ? $parameters[2] : 1 );
			}
		}
	}

	/**
	 * Regiters an object with all its action hooks.
	 *
	 * @param ActionHookSubscriberInterface $object
	 */
	private function register_actions( interfaces\ActionHookSubscriber $object ) {
		foreach( $object->get_actions() as $action ) {
			$this->register_action( $object, key( $action ), current( $action ) );
		}
	}

	/**
	 * Register an object with a specific filter hook.
	 *
	 * @param FilterHookSubscriberInterface $object
	 * @param string                          $name
	 * @param mixed                           $parameters
	 */
	private function register_filter( interfaces\FilterHookSubscriber $object, $name, $parameters ) {

		// For string params
		if( is_string( $parameters ) ) {
			// If a class method
			if( method_exists( $object, $parameters ) ) {
				add_filter( $name, array( $object, $parameters ) );
			}
			// Else if a standard wordpress function
			else if( function_exists( $parameters ) ) {
				add_filter( $name, $parameters );
			}
		}
		// For array of params (name, priority, args)
		elseif( is_array( $parameters ) && isset( $parameters[0] ) ) {
			// If a class method
			if( method_exists( $object, $parameters[0] ) ) {
				add_filter( $name, array( $object, $parameters[0] ), isset( $parameters[1] ) ? $parameters[1] : 10, isset( $parameters[2] ) ? $parameters[2] : 1 );
			}
			// Else if a standard wordpress function
			else if( function_exists( $parameters[0] ) ) {
				add_filter( $name, $parameters[0], isset( $parameters[1] ) ? $parameters[1] : 10, isset( $parameters[2] ) ? $parameters[2] : 1 );
			}
		}
	}

	/**
	 * Regiters an object with all its filter hooks.
	 *
	 * @param FilterHookSubscriberInterface $object
	 */
	private function register_filters( interfaces\FilterHookSubscriber $object) {

		foreach( $object->get_filters() as $filter ) {
			$this->register_filter( $object, key( $filter ), current( $filter ) );
		}
	}

	/**
	 * Register an object with a specific shortcode hook.
	 *
	 * @param ShortcodeHookSubscriberInterface $object
	 * @param string                          $name
	 * @param mixed                           $parameters
	 */
	private function register_shortcode( interfaces\ShortcodeHookSubscriber $object, $name, $parameters ) {
		if( is_string( $parameters ) ) {
			// If a class method
			if( method_exists( $object, $parameters ) ) {
				add_shortcode( $name, array( $object, $parameters ) );
			}
			// Else if a standard wordpress function
			else if( function_exists( $parameters ) ) {
				add_shortcode( $name, $parameters );
			}
		}
	}

	/**
	 * Regiters an object with all its shortcode hooks.
	 *
	 * @param ShortcodeHookSubscriberInterface $object
	 */
	private function register_shortcodes( interfaces\ShortcodeHookSubscriber $object) {
		foreach( $object->get_shortcodes() as $shortcode ) {
			$this->register_shortcode( $object, key( $shortcode ), current( $shortcode ) );
		}
	}
}