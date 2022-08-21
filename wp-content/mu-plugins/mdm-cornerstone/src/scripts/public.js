( function( $ ) {
	'use strict';
})( jQuery );

jQuery( function( $ ) {
	'use strict';
	/**
	 * No click (static) menu item fix
	 */

	$.map( $( '.menu-item a[href="#"]' ), ( el ) => {
		let $el = $( el );
		$el.on( 'click', ( e ) => {
			e.preventDefault;
			return false;
		} );
	} );

	$.map( $( 'a[href*="location=newtab"]' ), ( el ) => {
		$( el ).attr( 'target', '_blank' ).attr( 'rel', 'noreferrer noopener' );
	} );

});