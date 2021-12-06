var ScrollMagic = require( 'scrollmagic' );

export default function( element ) {
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

	const _update = () => {
		// scene.removePin( element, true );
		// scene.setPin( element, { pushFollowers: false } )
		// scene.update();
		// scene.duration( 0 );
	}

	/**
	 * Calculate the offset from the top
	 */
	const _getOffset = () => {
		// Maybe use data element
		let offset = element.dataset.pinOffset !== undefined ? parseInt( element.dataset.pinOffset ) : 0;
		// Account for admin bar
		offset = offset + document.querySelector('html').offsetTop;
		// Make it negative
		return 0 - offset;
	}
	/**
	 * Required scrollmagic scene
	 */
	let scene = new ScrollMagic.Scene({
		triggerElement : element,
		triggerHook    : 0, // 0 -1 value in decimal range. 0 is top of page, 1 is bottom, .5 is middle
		offset         : _getOffset(), // Set the distance from the top
		duration       : 0 // Sets the number of pixels to scroll
	})
	.setClassToggle( element, 'pinned')
	.addTo( controller );
	/**
	 * add event listener and kick it off
	 */
	window.addEventListener( 'resize', _update, false );
	/**
	 * Set the pin
	 */
	scene.setPin( element, { pushFollowers: false } );
}