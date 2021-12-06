var ScrollMagic = require( 'scrollmagic' );

export default function( element ) {
	/**
	 * Our inner wrapper to wrap the inserted elements around
	 */
	const inner = element.querySelector( '.sidebar-main' );
	/**
	 * Our outer container used for measuring scroll distance
	 */
	const container = element.closest( '#content' );
	/**
	 * Our scrollmagic controlling
	 */
	const controller = new ScrollMagic.Controller();
	/**
	 * Calculate the duration
	 *
	 * uses heights + padding/margin to calculate how far to scroll
	 */
	const _calcDuration = () => {
		return container.offsetHeight - inner.offsetHeight - element.offsetTop;
	}

	/**
	 * Sets up the sticky sidebar
	 */
	const _stick = () => {
		if( _calcDuration() <= 0 ) {
			return;
		}
		/**
		 * If the element is not 100% width, stick it...
		 */
		if( element.offsetWidth < container.offsetWidth ) {
			/**
			 * Check if already pinned or not
			 */
			if( scene.state() !== 'DURING' ) {
				scene.setPin( inner, { pushFollowers: false } );
			}
			/**
			 * Update the duration
			 */
			scene.duration( _calcDuration() );
		}
		/**
		 * Else unstick it
		 */
		else {
			scene.removePin( inner, true );
		}
	}
	/**
	 * Required scrollmagic scene
	 */
	let scene = new ScrollMagic.Scene({
		triggerElement : inner,
		triggerHook    : 0, // 0 -1 value in decimal range. 0 is top of page, 1 is bottom, .5 is middle
		offset         : 0 - element.offsetTop, // Set the distance from the top
		duration       : _calcDuration() // Sets the number of pixels to scroll
	}).addTo( controller );
	/**
	 * add event listener and kick it off
	 */
	window.addEventListener( 'resize', _stick, false );
	/**
	 * Update the duration after a period of time, to compensate for other lazy loading and stuff
	 */
	setTimeout( () => {
		scene.duration( _calcDuration() );
	}, 500 );

	_stick()
}