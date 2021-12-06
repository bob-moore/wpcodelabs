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

import navFocus from './include/nav-focus.js';
import ToggleButton from './include/toggle-button.js';
import StickyColumn from './include/sticky-columns.js';
import ScrollToggle from './include/scroll-toggle.js';
import StickySidebar from './include/sticky-sidebar.js';
import Pin from './include/stick.js';
import Slick from './include/slick.js';

/**
 * Init nav focus
 *
 * Provides keyboard access to navigation items
 */
const _initNavFocus = () => {
	/**
	 * Get all of the buttons on the page
	 */
	let navs = document.getElementsByClassName( 'menu' );

	for( let i = 0; i < navs.length; i++ ) {
		new navFocus( navs[i] );
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
			new ToggleButton( subbuttons[i], { targets : submenu } );
		}

	}
};
document.addEventListener( 'DOMContentLoaded', _initButtons, false );

/**
 * Sticky Beaver Builder Columns
 */
const _stickyColumns = () => {
	/**
	 * Columns
	 */
	let columns = document.getElementsByClassName( 'fl-col fl-sticky-column' );
	/**
	 * Init each
	 */
	for( let i = 0; i < columns.length; i++ ) {
		new StickyColumn( columns[i] );
	}
}
document.addEventListener( 'DOMContentLoaded', _stickyColumns, false );
/**
 * Sticky Sidebar
 */
const _stickySidebar = () => {

	/**
	 * Sidebars
	 */
	let sidebar = document.querySelector( '#secondary.sticky-sidebar' );
	/**
	 * Init
	 */
	if( sidebar ) {
		new StickySidebar( sidebar );
	}
}
document.addEventListener( 'DOMContentLoaded', _stickySidebar, false );

/**
 * Pin Sticky Elements
 *
 * Sidebars and some specific elements have their own specific pin functions
 */
const _pin = () => {

	/**
	 * Sidebars
	 */
	let elements = document.querySelectorAll( '.pin' );
	/**
	 * Init
	 */
	for( let i = 0; i < elements.length; i++ ) {
		new Pin( elements[i] );
	}
}
document.addEventListener( 'DOMContentLoaded', _pin, false );

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

});
