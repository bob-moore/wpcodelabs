<?php
/**
 * Controller class
 *
 * Sets up all the layout queuing and controls their placement
 *
 * @class Controller
 * @package CustomLayouts\Classes
 */

namespace Devkit\CustomLayouts;

defined( 'ABSPATH' ) || exit;

class Controller extends Framework
{
	/**
	 * Post ID of edit screen
	 *
	 * @var int
	 * @access protected
	 */
	protected $_layouts = [];
	/**
	 * Queue of template parts to enqueue on this page
	 *
	 * @var array
	 * @access protected
	 */
	protected $_queue = [];
	/**
	 * Number of seconds to cache the queue, default 24 hours
	 *
	 * Updated during validation
	 *
	 * @var int
	 * @access protected
	 */
	protected $cache_in_seconds = 86400;
	/**
	 * False / Linux datetime to recheck cache at
	 *
	 * Set / Updated during validation
	 *
	 * @var int/bool
	 * @access protected
	 */
	protected $cache_until = false;
	/**
	 * Additional CSS from active layouts
	 *
	 * @var string
	 */
	public string $active_css = '';
	/**
	 * Additional JS from active layouts
	 *
	 * @var string
	 */
	public string $active_js = '';
	/**
	 * Register actions
	 *
	 * Uses the subscriber class to ensure only actions of this instance are added
	 * and the instance can be referenced via subscriber
	 *
	 * @return void
	 * @see  https://developer.wordpress.org/reference/functions/add_action/
	 */
	public function addActions()
	{
		Subscriber::addAction( 'wp', [$this, 'setQueue'], 20 );
		Subscriber::addAction( 'template_redirect', [$this, 'setActions'], 10 );
		Subscriber::addFilter( 'devkit/custom_layouts/queue', [$this, 'filterDisabled'], 6 );
		Subscriber::addAction( 'save_post_dk-custom-layout', [$this, 'invalidateCache'] );
	}
	/**
	 * Get all valid layouts for this display
	 *
	 * Get and validate all layouts, and remove those with an override
	 *
	 * @return array $this->_layouts : array of layouts for this display
	 */
	public function getLayouts()
	{

		if ( ! empty ( $this->_layouts ) ) {
			// return $this->_layouts;
		}

		$layouts = get_posts( [
			'posts_per_page' => -1,
			'post_type' => [ 'dk-custom-layout' ],
			'fields' => 'ids'
		]);

		$disabled = [];
		/**
		 * Do initial validation
		 */
		foreach( $layouts as $id ){
			$layout = new PostTypes\CustomLayout( $id );
			/**
			 * If it's not attached to an action, we can bail
			 */
			if ( empty( $layout->actions ) ) {
				continue;
			}
			/**
			 * See if it passes conditions to be displayed
			 */
			if ( $this->isValid( $layout ) ) {
				/**
				 * Add to set of all layouts
				 */
				$this->_layouts[$layout->id] = $layout;
			}
		}

		return $this->_layouts;
	}
	/**
	 * Set the queue
	 *
	 * Get Layouts from cache, or get from scratch and revalidate
	 *
	 * @return void
	 */
	public function setQueue()
	{
		$cached = Subscriber::getInstance( 'Cache' )->get();

		if ( $cached && ! $this->isDev() ) {
			$this->_queue = $cached;
			return;
		}

		else {
			$layouts = $this->getLayouts();

			foreach ( $layouts as $layout ) {

				foreach ( $layout->actions as $action => $args ) {
					/**
					 * Ensure we have an array to work with
					 */
					if ( ! isset( $this->_queue[$action] ) ) {
						$this->_queue[$action] = [];
					}
					/**
					 * Add the layout
					 */
					$this->_queue[$action][$layout->id] = $layout;
				}
			}

			$this->_queue = apply_filters( 'devkit/custom_layouts/queue', $this->_queue );

			Subscriber::getInstance( 'Cache' )->set( $this->_queue, $this->cache_until );
		}
	}
	/**
	 * Filter queue to disable layouts based on override settings
	 *
	 * @param array $queue Array of action/layout pairs
	 * @return array filtered $queue
	 */
	public function filterDisabled( $queue )
	{
		if ( empty( $queue ) )
		{
			return $queue;
		}

		foreach ( $queue as $action => $layouts )
		{
			foreach ( $layouts as $id => $layout )
			{
				if ( empty ( $layout->actions[$action]['disable'] ) )
				{
					continue;
				}

				switch ( $layout->actions[$action]['disable'] )
				{
					case 'all' :
					case 'layouts' :
						$queue[$action] = [];
						$queue[$action][$id] = $layout;
						break;
					case 'group' :
					case 'select' :
						foreach ( $layout->actions[$action]['disabled'] as $disabled_id )
						{
							unset( $queue[$action][$disabled_id] );
						}
						break;
					default:
						// No default action
						break;
				}
			}
		}

		return $queue;
	}
	/**
	 * Check if a layout should be displayed, based on conditions set
	 *
	 * @param object $layout TemplatePart object
	 * @return boolean whether is passes validation
	 */
	public function isValid( $layout )
	{
		if ( empty( $layout->conditions['include'] ) )
		{
			return false;
		}
		/**
		 * See if supposed to be exluded
		 */
		if ( $this->validate( $layout->conditions['exclude'] ) === true ) {
			return false;
		}
		/**
		 * See if supposed to be included
		 */
		if ( $this->validate( $layout->conditions['include'] ) === true ) {
			return true;
		}
		/**
		 * Default return
		 */
		return false;
	}
	/**
	 * Validate a set of rules for a template part
	 *
	 * Called recursivly to validate nested rules
	 *
	 * @param array $groups An array of rules or rule groups
	 * @param integer $depth  At what depth of the array are we at.
	 * @param string $type Whether doing include or exclude type of validation
	 * @return bool $valid  Whether or not all rules are valid in the set
	 * @since 1.0.0
	 */
	private function validate( $data, $depth = 0 )
	{
		/**
		 * Assume false when validating any, true if valuating all
		 * @var boolean
		 */
		$valid = $depth === 1;
		/**
		 * If empty just bail
		 */
		if( empty( $data ) ) {
			return $valid;
		}
		/**
		 * Maybe do top level
		 */
		if( $depth < 2 ) {

			/**
			 * Increment depth
			 */
			$recursive_depth = $depth + 1;
			/**
			 * If any of the groups are true, it's valid
			 */
			foreach( $data as $index => $group ) {
				/**
				 * At the groups (top) level, any ruleset can be valid
				 * The first TRUE condition validates the ruleset
				 *
				 * Evaluates an OR condition
				 */

				if ( $depth === 0 && $valid === true ) {
					break;
				}
				/**
				* At the group level, all rules must be valid
				* The first FALSE value invalidates the group
				*
				* Evaluates an AND condition
				*/
				if ( $depth === 1 && $valid === false ) {
					break;
				}
				/**
				 * Re-enter loop recursively
				 */
				$valid = $this->validate( $group, $recursive_depth );
			}

		}
		/**
		 * This level evaluates a single rule
		 */
		elseif ( $depth === 2 ) {
			/**
			 * Bail if no rule
			 */
			if ( empty( $data['type'] ) ) {
				$valid = false;
			}
			/**
			 * Setup method name
			 */
			$method = 'condition' . ucfirst( $data['type'] );

			/**
			 * Maybe call from class method
			 */
			if ( method_exists( $this , $method ) ) {
				$valid = call_user_func( [ $this, $method ], $data );
			}
			/**
			 * Maybe call from inbuilt
			 */
			elseif ( function_exists( $data['type'] ) ) {
				$valid = call_user_func( $data['type'] );
			}
			/**
			 * Return false otherwise
			 */
			else {
				$valid = false;
			}
		}

		return $valid;
	}
	/**
	 * Determine if singular conditions are met
	 *
	 * @param  array $rule Ruleset for condition
	 * @return bool
	 */
	public function conditionSingular( $rule )
	{
		$valid = false;

		if ( is_singular() )
		{
			switch ( $rule['subtype'] ) {
				case 'post' :
					/**
					 * Display on all singular
					 */
					if ( empty( $rule['post'] ) ) {

						$valid = true;
					}
					/**
					 * Display on some singular
					 */
					else {
						$valid = in_array( get_the_id(), $rule['post'] );
					}
					break;
				case 'post_type' :
					/**
					 * If empty...
					 */
					if ( empty( $rule['post_type'] ) ) {
						$valid = false;
					}
					/**
					 * Check each post type
					 */
					else {
						$valid = in_array( get_post_type(), $rule['post_type'] );
					}
					break;
				case 'term' :
					/**
					 * If empty...
					 */
					if ( empty( $rule['term'] ) ) {
						$valid = false;
					}
					/**
					 * Check each term
					 */
					else {
						foreach ( $rule['term'] as $term ) {
							if ( has_term( $term['id'], $term['taxonomy'], get_the_id() ) ) {
								$valid = true;
								break;
							}
						}
					}
					break;
				case 'author' :
					/**
					 * If empty...
					 */
					if ( empty( $rule['author'] ) ) {
						$valid = false;
					}
					/**
					 * Check each author
					 */
					else {
						$page_author = get_post_field( 'post_author', get_the_id() );

						$valid = in_array( $page_author, $rule['author'] );
					}
					break;
				case 'template' :
					/**
					 * If empty...
					 */
					if ( empty( $rule['template'] ) ) {
						$valid = false;
					}
					/**
					 * Check each template
					 */
					else {
						$template = get_page_template_slug( get_the_id() );
						$valid = in_array( $template, $rule['template'] );
					}
					break;

				default:
					$valid = false;
					break;
			}
		}
		return apply_filters( 'devkit/validation/singular', $valid, $rule );
	}
	/**
	 * Determine if archive conditions are met
	 *
	 * @param  array $rule Ruleset for condition
	 * @return bool
	 */
	public function conditionArchive( $rule )
	{
		$valid = false;

		if ( ! is_archive() ) {
			return false;
		}

		switch ( $rule['subtype'] ) {
			case 'archives': // Show on all archive
				$valid = true;
				break;
			case 'post_type':
				$valid = is_post_type_archive( $rule['post_type'] );

				if ( ! $valid ) {

					if ( is_tax() )
					{
						$term = get_queried_object();

						$tax = get_taxonomy( $term->taxonomy );

						if ( ! empty( array_intersect( $rule['post_type'], $tax->object_type ) ) ) {
							$valid = true;
						}
					}
				}
				break;
			case 'term':
				if ( ! empty( $rule['term'] ) ) {
					foreach ( $rule['term'] as $term ) {
						/**
						 * Check categories
						 */
						if ( $term['taxonomy'] == 'category' ) {
							if ( is_category( $term['id'] ) ) {
								$valid = true;
								break;
							}
						}
						/**
						 * Check tags
						 */
						elseif ( $term['taxonomy'] == 'post_tag' ) {
							if ( is_tag( $term['id'] ) ) {
								$valid = true;
								break;
							}
						}
						/**
						 * Check everything else
						 */
						elseif ( is_tax( $term['id'], $term['taxonomy'] ) ) {
							$valid = true;
							break;
						}
					}
				}
				break;
			case 'author':
				if ( ! empty( $rule['author'] ) ) {
					foreach ( $rule['author'] as $author ) {
						if ( is_author( $author['id'] ) ) {
							$valid = true;
							break;
						}
					}
				}
				break;
			default:
				$valid = false;
				break;
		}

		return $valid;
	}
	/**
	 * Determine if date conditions are met
	 *
	 * @param  array $rule Ruleset for condition
	 * @return bool
	 */
	public function conditionDatetime( $rule )
	{
		if ( empty( $rule['datetime'] ) ) {
			return true;
		}
		/**
		 * Linux time of current date/time
		 * @var int
		 */
		$now = strtotime( wp_date( 'm/d/Y h:i:s a' ) );
		/**
		 * Linux time of condition date/time
		 * @var int
		 */
		$condition = strtotime( $rule['datetime'] );
		/**
		 * Linux time of the difference between $condition and $now
		 * @var int
		 */
		$diff = $condition - $now;
		/**
		 * Maybe setup cache timeout, if lower than existing timeout
		 */
		if ( $diff > 0 && $diff < $this->cache_in_seconds ) {

			$this->cache_in_seconds = $diff;

			$this->cache_until = $condition;
		}
		/**
		 * Valid if we've met or passed the conditional time
		 */
		return strtotime( $now >= $condition );
	}
	/**
	 * Determine if user role conditions are met
	 *
	 * @param  array $rule Ruleset for condition
	 * @return bool
	 */
	public function conditionUser( $rule )
	{
		$valid = false;

		if ( ! empty( $rule['user'] ) ) {
			foreach ( $rule['user'] as $role ) {
				/**
				 * All NOT logged in users
				 */
				if ( $role === 'none' && ! is_user_logged_in() ) {
					$valid = true;
					break;
				}
				/**
				 * All Logged in users
				 */
				elseif ( $role === 'all' && is_user_logged_in() ) {
					$valid = true;
					break;
				}
				/**
				 * Per role
				 */
				else {
					$user = wp_get_current_user();
					if ( in_array( $role, $user->roles ) ) {
						$valid = true;
						break;
					}
				}
			}
		}

		return $valid;
	}
	/**
	 * Determine if custom conditions are met
	 *
	 * @param  array $rule Ruleset for condition
	 * @return bool
	 */
	public function conditionCustom( $rule )
	{
		return apply_filters( "devkit/custom_layouts/conditions/{$rule['custom']}", false, $rule );
	}
	/**
	 * Get object(s) from the queue
	 *
	 * @param string  $hook Action hook to look in
	 * @param  integer $id ID of post type object
	 * @return false/array/object Return group of layout objects, or single. False on failure
	 */
	public function getQueued( $hook = '', $id = 0 )
	{
		/**
		 * Don't waste time if queue is empty
		 */
		if ( empty( $this->_queue ) ) {
			return false;
		}
		/**
		 * If no hook, return entire queue
		 */
		if ( empty( $hook ) ) {
			return $this->_queue;
		}
		/**
		 * If hook not set in queue, bail
		 */
		if ( ! isset( $this->_queue[$hook] ) || empty( $this->_queue[$hook] ) ) {
			return false;
		}
		/**
		 * If ID not set return entire hook
		 */
		if ( ! $id ) {
			return $this->_queue[$hook];
		}
		/**
		 * Else return specific layout
		 */
		elseif ( isset( $this->_queue[$hook][$id] ) ) {
			return $this->_queue[$hook][$id];
		}
		/**
		 * Default return
		 */
		return false;
	}

	/**
	 * Set action on hook
	 *
	 * Loops through the queue and adds_action for each
	 *
	 * @return void
	 */
	public function setActions()
	{
		if ( empty( $this->_queue ) ) {
			return;
		}

		foreach ( $this->_queue as $action => $instance )
		{
			foreach ( $instance as $layout )
			{
				if ( $layout->type === 'partial' && strpos( $layout->partial, 'core' ) !== false )
				{
					if ( ! wp_script_is( 'cl-frontend', 'enqueued' ) )
					{
						Subscriber::addAction( 'wp_enqueue_scripts', [ Subscriber::getInstance( 'FrontEnd' ), 'enqueueFrontendAssets'] );
					}
				}

				$priority = $action === 'the_content' ? 10 : intval( $layout->actions[$action]['priority'] );

				if ( $layout->actions[$action]['disable'] === 'all' )
				{
					remove_all_actions( $layout->actions[$action] );
				}

				$this->active_css .= trim( $layout->css );

				$this->active_js .= trim( $layout->js );

				Subscriber::addAction( $action, [ Subscriber::getInstance('FrontEnd'), "devkit/custom_layouts/render/{$layout->id}"], $priority );
			}
		}
	}
	/**
	 * Invalidates the display transient
	 *
	 * @return void
	 */
	public function invalidateCache() {
		Subscriber::getInstance( 'Cache' )->delete();
	}
}