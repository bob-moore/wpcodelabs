
require( './includes/jquery.jumpscroll.js' );


( function( $ ) {
	'use strict';
	$.fn.WpImageAlignment = function( options ) {
		// Set the font size
		var fontsize = parseFloat( $( 'body' ).css( 'font-size' ) );

		return $.map( this, function( el ) {
			return new ResponsiveAlignedImage( $( el ) );
		});

		function ResponsiveAlignedImage( $img ) {

			var $container, classList;

			var _init = function() {
				// Get the parent element that contains our image
				$container = $img.closest( 'div' );
				// Save the original list of classes
				classList = $img.attr( 'class' );
				// Bind some events
				if( $container.length ) {
					// Check alignment and adjust if necessary
					_alignImg();
					// Check alignment on image resize
					$( window ).on( 'resize', _alignImg );
				}
				// Return the image
				return $img;
			};

			var _isTextSquashed = function() {
				// Is there less than 10x font size available for text to wrap?
				if( ( $container.width() - $img.width() ) < 10 * fontsize ) {
					return true;
				}
				return false;
			};

			var _isImageSquashed = function() {
				// Is the image at least 1/2 of the container width?
				if( $img.width() >= $container.width() / 2 ) {
					return true;
				}
				return false;
			};

			var _alignImg = function( event, data ) {
				// If squashed, let's unsquash it
				if( _isImageSquashed() && _isTextSquashed() ) {
					$img.removeClass( 'alignleft alignright' ).addClass( 'aligncenter' );
				}
				// Else, let's restore it to it's default state
				else {
					$img.removeClass( 'aligncenter' ).addClass( classList );
				}
				return;
			};

			return _init();

		}
	};

})( jQuery );

jQuery( function( $ ) {
	'use strict';
	// $( '.scrolltoggle' ).ScrollToggle();
	// $( '.inview' ).ScrollView();
	$( '.jumpscroll' ).JumpScroll();
	$( '.entry-content img.alignleft, .entry-content img.alignright' ).WpImageAlignment();


	$( '.menu-item.noclick > a' ).on( 'click', ( e ) => {
		e.preventDefault();
	} );

	$.map( $( 'a[href*="location=newtab"]' ), ( el ) => {
		$( el ).attr( 'target', '_blank' ).attr( 'rel', 'noreferrer noopener' );
	} );





});