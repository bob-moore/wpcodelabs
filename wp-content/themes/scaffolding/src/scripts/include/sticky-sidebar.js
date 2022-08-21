const ScrollMagic = require( 'scrollmagic' );
const imagesLoaded = require('imagesloaded');

export default function( element ) {
	/**
	 * Our inner wrapper to wrap the inserted elements around
	 */
	const inner = element.querySelector( '.container' );
	/**
	 * Our outer container used for measuring scroll distance
	 */
	const container = element.closest( '#main' );
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
		return container.offsetHeight - inner.offsetHeight - ( _calcOffset() * 2 );
	}
	/**
	 * Calculate the offset from the top
	 *
	 * Uses the container element and the elements offset from top
	 */
	const _calcOffset = () => {
		return element.getBoundingClientRect().top - container.getBoundingClientRect().top;
	}
	/**
	 * Sets up the sticky sidebar
	 */
	const _stick = () => {
		/**
		 * If not a sidebar (floated or whatever) unpin and bail
		 */
		if( _calcDuration() - _calcOffset() <= 0 ) {
			scene.removePin( inner, true );
			return;
		}
		/**
		 * If the element is not 100% width, stick it...
		 */
		if( element.offsetWidth < container.offsetWidth && inner.offsetHeight < window.innerHeight ) {
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
			/**
			 * Update the offset
			 */
			scene.offset( 0 - _calcOffset() );
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
		offset         : 0 - _calcOffset(), // Set the distance from the top
		duration       : _calcDuration() // Sets the number of pixels to scroll
	}).addTo( controller );
	/**
	 * add event listener and kick it off
	 */
	window.addEventListener( 'resize', _stick, false );
	/**
	 * Update the duration after a period of time, to compensate for other lazy loading and stuff
	 */
	imagesLoaded( document.querySelector('#main'), function( instance ) {
		_stick();
	});
	/**
	 * Do initial stick
	 */
	_stick();
}