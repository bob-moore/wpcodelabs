/**
 * Object Assign Polyfill
 */
require( 'es6-object-assign' ).polyfill();
/**
 * Custom Event Polyfill
 */
require( 'custom-event-polyfill' );
/**
 * Element.closest Polyfill
 */
require( 'element-closest-polyfill' );
/**
 * Browser Updates
 */

import browserUpdate from 'browser-update';
import navFocus from './include/nav-focus.js';
import ToggleButton from './include/toggle-button.js';
// import StickyColumn from './include/sticky-columns.js';
import ScrollToggle from './include/scroll-toggle.js';
import StickySidebar from './include/sticky-sidebar.js';
import Pin from './include/stick.js';


/**
 * Browser Update Notification
 */
browserUpdate( {required:{e:-6,f:-6,o:-3,s:-3,c:-6},insecure:true,unsupported:true,api:2021.04 } );

/**
 * Init nav focus
 *
 * Provides keyboard access to navigation items
 */
const _initNavFocus = () => {
	/**
	 * Get all of the buttons on the page
	 */
	let navs = document.getElementsByClassName( 'theme-menu' );

	for( let i = 0; i < navs.length; i++ ) {
		new navFocus( navs[i] )
	}
};
document.addEventListener( 'DOMContentLoaded', _initNavFocus, false );

/**
 * Init Toggle Buttons
 *
 * Toggles elements such as hidden menus, submenus, etc
 */
const _initButtons = () => {
	/**
	 * Get all of the buttons on the page
	 */
	let buttons = document.getElementsByClassName( 'toggle-button' );

	for( let i = 0; i < buttons.length; i++ ) {
		let button = new ToggleButton( buttons[i] );
	}
	/**
	 * Sub menu toggle buttons
	 */
	let subbuttons = document.getElementsByClassName( 'sub-menu-toggle' );

	for( let i = 0; i < subbuttons.length; i++ ) {

		let submenu = subbuttons[i].closest( 'li' ).querySelector( '.sub-menu' );

		if( submenu ) {

			let button = new ToggleButton( subbuttons[i], { targets : submenu } );

		}

	}
};
document.addEventListener( 'DOMContentLoaded', _initButtons, false );

/**
 * Sticky Sidebar
 */
const _stickySidebar = () => {

	/**
	 * Sidebars
	 */
	let sidebar = document.querySelector( '#sidebar[data-sticky="1"]' );

	if( sidebar ) {
		new StickySidebar( sidebar );
	}
}
document.addEventListener( 'DOMContentLoaded', _stickySidebar, false );

/**
 * Scroll Toggle
 */
const _scrollToggle = () => {

	/**
	 * get all scrolltoggle elements
	 */
	let elements = document.getElementsByClassName( 'scrolltoggle' );
	/**
	 * Init
	 */
	for( let i = 0; i < elements.length; i++ ) {
		new ScrollToggle( elements[i] );
	}
}
document.addEventListener( 'DOMContentLoaded', _scrollToggle, false );

jQuery(function ($) {
	'use strict';

	( function(){

		let $menus = $.map( $( '.theme-menu .sub-menu' ), ( menu ) => {
			return $( menu );
		} );

		const _toggleStart = ( e ) => {
			/**
			 * Menu is opening
			 */
			if ( e.detail.state === 0 ) {
				e.data.submenu.slideDown( 300, 'linear', () => {
					e.data.submenu[0].dispatchEvent( new CustomEvent( '_toggle:stop', { detail : { state : e.detail.state } } ) );
				} );

			}
			/**
			 * Menu is closing
			 */
			else {
				e.data.submenu.slideUp( 300, 'linear', () => {
					e.data.submenu[0].dispatchEvent( new CustomEvent( '_toggle:stop', { detail : { state : e.detail.state } } ) );
				} );
			}
		}

		const _init = () => {
			for ( let i = 0; i < $menus.length; i++ ) {
				$menus[i].on( '_toggle:start', { 'submenu' : $menus[i] }, _toggleStart );
			}
		}

		// _init();

	} )();


});

