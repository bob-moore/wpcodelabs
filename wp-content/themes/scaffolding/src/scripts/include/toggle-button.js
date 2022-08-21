import Target from './toggle-element.js';

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
	let targets = [];
	/**
	 * Number of transitions that have occured
	 * @type {int}
	 */
	let completed = 0;
	/**
	 * Default settings
	 */
	let defaults = { targets: false };
	/**
	 * Merge defaults with passed in options
	 * @type {object}
	 */
	options = Object.assign( defaults, options );
	/**
	 * Respond to subscribed element events
	 */
	const _respond = ( event ) => {

		/**
		 * Remove all uneeded classes
		 */
		button.classList.remove( ...classes );
		/**
		 * If a start event, set our completed counter back to 0, and set necessary
		 * classes to continue
		 */
		if( event.detail.state === 0 || event.detail.state === 2 ) {
			completed = 0;
			button.classList.add( classes[event.detail.state] );
		}
		/**
		 * If a completion event, AND all elements have completed
		 */
		else if( ++completed >= targets.length ) {
			button.classList.add( classes[event.detail.state] );
		}
	};

	const _trigger = ( event ) => {
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
			targets[i].trigger( state );
		}
	};
	/**
	 * Initialize the button
	 */
	const _init = () => {

		// console.log(App);

		if( typeof options.targets === 'object' ) {
			/**
			 * Check for a direct html element
			 */
			var rawTargets = options.targets.length === undefined ? [ options.targets ] : options.targets;
		}

		else {
			var rawTargets = document.querySelectorAll( options.targets || button.dataset.triggers || false );
		}

		if( rawTargets.length ) {
			/**
			 * Add event listener to button to trigger the element(s)
			 */
			button.addEventListener( 'click', _trigger, true );
			/**
			 * Create new target element
			 * Subscribe to toggle events
			 */
			for( let i = 0; i < rawTargets.length; i++ ) {

				targets[i] = new Target( rawTargets[i], button );

				targets[i].subscribe( _respond );
			}
		}

		return button;
	};
	return _init();
}