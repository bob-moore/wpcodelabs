export default function( button, options ) {
	/**
	 * activation/deactivation classes
	 * @type {Array}
	 */
	const classes = ['activating', 'activated', 'deactivating', 'deactivated'];
	/**
	 * Transition states
	 * @type {String}
	 */
	const transitionEndTrigger = ['_toggle:stop', 'webkitAnimationEnd', 'mozAnimationEnd', 'MSAnimationEnd', 'oanimationend', 'animationend', 'transitionend'];
	/**
	 * Target elements
	 * @type {Collection}
	 */
	let targets = {};
	/**
	 * Number of transitions that have occured
	 * @type {int}
	 */
	let completed = 0;
	/**
	 * Default settings
	 */
	let defaults = { targets: false, timeout: 1000 };
	/**
	 * Merge defaults with passed in options
	 * @type {object}
	 */
	options = Object.assign( defaults, options );

	const _toggleStart = ( event ) => {
		event.target.removeEventListener( '_toggle:start', _toggleStart, false );
		/**
		 * Remove all uneeded classes
		 */
		event.target.classList.remove( 'activating', 'activated', 'deactivating', 'deactivated' );
		/**
		 * Add the appropriate class
		 */
		event.target.classList.add( classes[event.detail.state] );
		/**
		 * Set the aria attributd
		 */
		event.target.setAttribute( 'aria-expanded', event.detail.state < 2 );
		/**
		 * Add event listeners
		 */
		for( let i = 0; i < transitionEndTrigger.length; i++ ) {
			event.target.addEventListener( transitionEndTrigger[i], _toggleEnd, false );
		}
		/**
		 * Set timeout to remove hanging transitions
		 * If an element doesn't use a transition property, this will clean it up
		 */
		setTimeout( function(){
			event.target.dispatchEvent( new CustomEvent( '_toggle:stop', { detail : { state : event.detail.state } } ) );
		}, options.timeout );
	};
	const _toggleButton = ( event ) => {
		/**
		 * Add body classes
		 */
		if( event.target.id.length ) {
			for( let i = 0; i < classes.length; i++ ) {
				document.body.classList.remove( event.target.id + '-' + classes[i] );
			}

			document.body.classList.add( event.target.id + '-' + classes[event.detail.state] );
		}
		/**
		 * Remove all uneeded classes
		 */
		button.classList.remove( 'activating', 'activated', 'deactivating', 'deactivated' );
		// *
		//  * Add the appropriate class

		button.classList.add( classes[event.detail.state] );
		// /**
		//  * Set the aria attributd
		//  */
		button.setAttribute( 'aria-expanded', event.detail.state < 2 );
	};
	/**
	 * Finish a toggle event
	 *
	 * Remove / Add classes, depending on 'state'
	 * Iterate the 'completed' flag
	 */
	const _toggleEnd = ( event ) => {

		let state = event.target.classList.contains( classes[0] ) || event.target.classList.contains( classes[1] ) ? 0 : 2;
		/**
		 * Detach the event listener
		 */
		for( let i = 0; i < transitionEndTrigger.length; i++ ) {
			event.target.removeEventListener( transitionEndTrigger[i], _toggleEnd, false );
		}
		/**
		 * If we've already completed all transitions,
		 * we can bail
		 */
		if( completed === targets.length ) {
			return;
		}

		if( event.target.classList.contains( classes[state + 1] ) === false ) {
			/**
			 * Remove the classes first
			 */
			event.target.classList.remove( 'activating', 'activated', 'deactivating', 'deactivated' );
			/**
			 * Add the appropriate class
			 */
			event.target.classList.add( classes[state + 1] );
		}
		/**
		 * Maybe transition the button
		 */
		if( ++completed === targets.length ) {
			event.target.dispatchEvent( new CustomEvent( '_toggle:complete', { detail : { state : state + 1 } } ) );
		}
	};

	const _trigger = ( event ) => {
		/**
		 * Set our completed counter back to 0
		 */
		completed = 0;
		/**
		 * Prevent accidental clicking out in case of a href
		 */
		event.preventDefault();
		/**
		 * Blur the button
		 */
		button.blur();
		/**
		 * Get the current state
		 */
		let state = button.classList.contains( classes[0] ) || button.classList.contains( classes[1] ) ? 2 : 0;
		/**
		 * Trigger toggle event for each target
		 */

		for( let i = 0; i < targets.length; i++ ) {
			targets[i].addEventListener( '_toggle:start', _toggleStart, false );
			/**
			 * Start new toggle event
			 */
			targets[i].dispatchEvent( new CustomEvent( '_toggle:start', { detail : { state : state } } ) );
		}
	};
	/**
	 * Initialize the button
	 */
	const _init = () => {

		if( typeof options.targets === 'object' ) {
			/**
			 * Check for a direct html element
			 */
			targets = options.targets.length === undefined ? [ options.targets ] : options.targets;
		}

		else {
			targets = document.querySelectorAll( options.targets || button.dataset.triggers || false );
		}

		if( targets.length ) {
			/**
			 * Add event listener to button
			 */
			button.addEventListener( 'click', _trigger, true );

			for( let i = 0; i < targets.length; i++ ) {
				targets[i].addEventListener( '_toggle:complete', _toggleButton, false );
				targets[i].addEventListener( '_toggle:start', _toggleButton, false );
			}
		}

		return button;
	};
	return _init();
}