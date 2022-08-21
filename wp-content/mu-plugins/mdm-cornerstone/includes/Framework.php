<?php
/**
 * Base class fromework
 *
 * Common functionality shared by other core classes
 *
 * @link https://midwestfamilymadison.com
 * @package mdm_wp_cornerstone/classes
 */

namespace Mdm\Cornerstone;

defined( 'ABSPATH' ) || exit;

abstract class Framework
{
	/**
	 * Check if already registered, and run functions to register filters and actions
	 *
	 * @method __construct
	 * @return $this
	 */
	public function __construct()
	{
		/**
		 * Conditionally add actions/filters, but only if they haven't already
		 * been added
		 *
		 * The subscriber class will keep track of the classes added, and always return
		 * the first instance of the object created using any individual class, so
		 * filters, actions, and shortcodes are not duplicated across multiple instances
		 */
		if ( Subscriber::getInstance( $this ) === $this )
		{
			/**
			 * Register actions
			 */
			$this->addActions();
			/**
			 * Register filters
			 */
			$this->addFilters();
			/**
			 * Register shortcodes
			 */
			$this->addShortcodes();
		}
		/**
		 * Return the object for use
		 */
		return $this;
	}
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addActions() : void {}
	/**
	 * Register filters
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addFilters() : void {}
	/**
	 * Register shortcodes
	 *
	 * Uses the subscriber class to ensure only shortcodes of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 */
	public function addShortcodes() : void {}
}