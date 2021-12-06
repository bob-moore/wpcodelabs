var ScrollMagic = require( 'scrollmagic' );

export default function( element ) {
	/**
	 * Our inner wrapper to wrap the inserted elements around
	 */
	const inner = element.querySelector( '.fl-col-content' );
	/**
	 * Our outer container used for measuring scroll distance
	 */
	const container = element.closest( '.fl-col-group' );
	/**
	 * Our scrollmagic controlling
	 */
	const controller = new ScrollMagic.Controller();
	/**
	 * Required scrollmagic scene
	 */
	let scene = new ScrollMagic.Scene({
		triggerElement : inner,
		triggerHook    : 0, // 0 -1 value in decimal range. 0 is top of page, 1 is bottom, .5 is middle
		offset         : 0 - element.offsetTop, // Set the distance from the top
		duration       : container.offsetHeight - element.offsetHeight, // Sets the number of pixels to scroll
		pushFollowers  : false
	}).addTo( controller );
	/**
	 * Sets up the sticky sidebar
	 */
	const _stick = () => {
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
			scene.duration( container.offsetHeight - element.offsetHeight );
		}
		/**
		 * Else unstick it
		 */
		else {
			scene.removePin( inner, true );
		}
	}
	/**
	 * add event listener and kick it off
	 */
	window.addEventListener( 'resize', _stick, false );

	_stick()
}