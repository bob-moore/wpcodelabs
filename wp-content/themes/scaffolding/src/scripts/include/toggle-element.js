export default function( element ) {
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
	 * Method to end a triggering event
	 */
	const _toggleEnd = ( event ) => {
		/**
		 * Do not trigger on child element events
		 */
		if( event.target !== element ) {
			return false;
		}
		/**
		 * Determine the current state
		 */
		let state = event.target.classList.contains( classes[0] ) || event.target.classList.contains( classes[1] ) ? 0 : 2;
		/**
		 * Detach the event listener
		 */
		for( let i = 0; i < transitionEndTrigger.length; i++ ) {
			element.removeEventListener( transitionEndTrigger[i], _toggleEnd, false );
		}
		/**
		 * Maybe add the appropriate class
		 */
		if( element.classList.contains( classes[state + 1] ) === false ) {
			/**
			 * Remove the classes first
			 */
			event.target.classList.remove( ...classes );
			/**
			 * Add the appropriate class
			 */
			event.target.classList.add( classes[state + 1] );

			if ( element.id !== '' ) {
				/**
				 * Remove body classes
				 */
				for ( let i = 0; i < classes.length; i++ ) {
					document.body.classList.remove( element.id + '-' + classes[i] );
				}
				/**
				 * Add body class
				 */
				document.body.classList.add( element.id + '-' + classes[state + 1] );
			}
		}
		/**
		 * Dispatch complete event
		 */
		element.dispatchEvent( new CustomEvent( '_toggle:complete', { detail : { state : state + 1 } } ) );
	}
	/**
	 * Method to begin a triggering event
	 */
	const _toggleStart = ( state ) => {
		/**
		 * Dispatch custom event for buttons / triggering element to listen to
		 */
		element.dispatchEvent( new CustomEvent( '_toggle:start', { detail : { state : state } } ) );
		/**
		 * Remove unnecessary classes
		 */
		element.classList.remove( ...classes );
		/**
		 * Add the appropriate class
		 */
		element.classList.add( classes[state] );

		if ( element.id !== '' ) {
			/**
			 * Remove body classes
			 */
			for ( let i = 0; i < classes.length; i++ ) {
				document.body.classList.remove( element.id + '-' + classes[i] );
			}
			/**
			 * Add body class
			 */
			document.body.classList.add( element.id + '-' + classes[state] );
		}
		/**
		 * Add event listeners for animation completion
		 */
		for( let i = 0; i < transitionEndTrigger.length; i++ ) {
			element.addEventListener( transitionEndTrigger[i], _toggleEnd, false );
		}
		/**
		 * Set timeout to remove hanging transitions
		 * If an element doesn't use a transition property, this will clean it up
		 */
		setTimeout( function(){
			element.dispatchEvent( new CustomEvent( '_toggle:stop', { detail : { state : state } } ) );
		}, 1000 );

	};


	const _subscribe = ( callback ) => {
		element.addEventListener( '_toggle:start', callback, false );
		element.addEventListener( '_toggle:complete', callback, false );
	}

	/**
	 * Initialize the button
	 */
	return {
		trigger : _toggleStart,
		subscribe : _subscribe
	}
}