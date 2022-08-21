var ScrollMagic = require( 'scrollmagic' );

export default function( element, options ) {
	/**
	 * Make sure we're using a dom node
	 */
	element = element instanceof jQuery ? element[0] : element;
	/**
	 * Our scrollmagic controlling
	 */
	const controller = new ScrollMagic.Controller();
	/**
	 * activation/deactivation classes
	 * @type {Array}
	 */
	const classes = {
		'enter' : [ 'activating', 'activated' ],
		'leave' : [ 'deactivating', 'deactivated' ]
	};
	/**
	 * Transition states
	 * @type {String}
	 */
	const transitionEndTrigger = [ '_toggle:end', 'webkitAnimationEnd', 'mozAnimationEnd', 'MSAnimationEnd', 'oanimationend', 'animationend', 'transitionend'];
	/**
	 * Default settings
	 */
	let defaults = {
		triggerElement : element,
		triggerHook    : element.dataset.triggerhook || 0, // 0 -1 value in decimal range. 0 is top of page, 1 is bottom, .5 is middle
		offset         : element.dataset.offset || 0, // Set the distance from the top
		duration       : 0, // Sets the number of pixels to scroll
		timeout        : element.dataset.timeout || 500 // How long to wait for a default change of state
	};
	/**
	 * Merge defaults with passed in options
	 * @type {object}
	 */
	options = Object.assign( defaults, options );
	/**
	 * Set flag for direction
	 */
	let doing = '';
	/**
	 * End the transition
	 */
	const _toggleEnd = ( event ) => {
		/**
		 * Detach the event listener
		 */
		for( let i = 0; i < transitionEndTrigger.length; i++ ) {
			element.removeEventListener( transitionEndTrigger[i], _toggleEnd, false );
		}
		/**
		 * Remove all uneeded classes
		 */
		if( element.classList.contains( classes[ doing ][1] ) === false ) {
			/**
			 * Remove all uneeded classes
			 */
			element.classList.remove( ...classes.enter );
			element.classList.remove( ...classes.leave );

			/**
			 * Add the appropriate class
			 */
			element.classList.add( classes[ doing ][1] );
		}
	}

	/**
	 * Sets up the sticky sidebar
	 */
	const _toggle = ( event ) => {
		/**
		 * Set if entering or leaving
		 */
		doing = event.type;
		/**
		 * Maybe remove previous event listeners
		 */
		 for( let i = 0; i < transitionEndTrigger.length; i++ ) {
		 	element.removeEventListener( transitionEndTrigger[i], _toggleEnd, false );
		 }
		/**
		 * Remove all uneeded classes
		 */
		element.classList.remove( ...classes.enter );
		element.classList.remove( ...classes.leave );
		/**
		 * Add the appropriate class
		 */
		element.classList.add( classes[event.type][0] );
		/**
		 * Add event listeners
		 */
		for( let i = 0; i < transitionEndTrigger.length; i++ ) {
			element.addEventListener( transitionEndTrigger[i], _toggleEnd , { once : true } );
		}
		/**
		 * Set timeout to remove hanging transitions
		 * If an element doesn't use a transition property, this will clean it up
		 */
		setTimeout( function(){
			element.dispatchEvent( new CustomEvent( '_toggle:end' ) );
		}, options.timeout );
	}
	/**
	 * Required scrollmagic scene
	 */
	 let scene = new ScrollMagic.Scene({
	 	triggerElement : element,
	 	triggerHook    : element.dataset.triggerhook || 1, // 0 -1 value in decimal range. 0 is top of page, 1 is bottom, .5 is middle
	 	offset         : element.dataset.offset || 0, // Set the distance from the top
	 	duration       : 0 // Sets the number of pixels to scroll
	 }).addTo( controller ).on( 'enter leave', _toggle );
}