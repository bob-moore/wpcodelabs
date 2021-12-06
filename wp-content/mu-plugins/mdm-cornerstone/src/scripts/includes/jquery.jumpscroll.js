( function( $ ) {
	'use strict';
	$.fn.JumpScroll = function( options ) {

		var defaults = {
			scrollSpeed : 'progressive',
			target : 'body',
			topOffset : 0,
		};

		options = $.extend( {}, defaults, options );

		return $.map( this, function( el ) {
			return new JumpScroll( el );
		});

		function JumpScroll( el ) {
				var $el, $target, settings = {};

			/**
			 * cache DOM elements and options
			 * Set variables and assign DOM elements to reusable vars
			 * @return (null) blank return to maintain consitancy
			 */
			var cacheDom = function() {
				$el     = $( el );
				$target = el.hash !== '' ? $( el.hash ) : $( options.target );
				$target = $target.length !== 0 ? $target : false;
				settings = {
					topOffset : $el.data( 'topOffset' ) || options.topOffset || 0,
					scrollSpeed : getScrollSpeed(),
				};
				return;
			};

			/**
			 * Binds events
			 * @return (null) blank return to maintain consistancy
			 */
			var bindEvents = function() {
				$el.on( 'click', scroll );
				return;
			};

			/**
			 * Get scroll speed option in this order:
			 * 1. If able calculate distance and use progressive speed
			 * 2. If elements has data-scrollSpeed set, use that.
			 * 3. Else, if scrollspeed option is set globally, use that.
			 * 4. Final fallback, just use 'slow'
			 * @return (int or string) Speed at which to scroll
			 */
			var getScrollSpeed = function() {
				if( options.speed === 'progressive' && $target !== false ) {
					return Math.abs( window.pageYOffset - $target.offset().top ) / 2;
				} else {
					return $el.data( 'scrollSpeed' ) || options.speed || 'slow';
				}
			};

			/**
			 * How to handle scroll events
			 * First, capture event to prevent page reloading
			 * Then, scroll page to specified point using options
			 * Last, blur the link focus and return self
			 * @param  (object) event : Event object
			 * @return ( object ) $el : return self for chaining
			 */
			var scroll = function( event ) {
				event.preventDefault();
				$el.trigger( 'jumpscroll:start' );
				$( 'body, html' ).stop().animate( { scrollTop : ( $target.offset().top - parseInt( settings.topOffset ) ) }, settings.ScrollSpeed, function() {
					$el.trigger( 'jumpscroll:done' );
					$el.blur();
				});
				return $el;
			};

			/**
			 * Anonomous init function
			 * Organize execution of module
			 * @type self invoking function to kickoff the module
			 */
			( function() {
				cacheDom();
				if( settings.$target !== false ) {
					bindEvents();
				}
			})();
			return $el;
		}
	};

})( jQuery );