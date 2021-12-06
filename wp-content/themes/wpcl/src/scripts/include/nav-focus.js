export default function( nav, options ) {
	/**
	 * The menu that belongs to the nav
	 */
	let menu = {};
	/**
	 * Collection of links
	 */
	let links = [];
	/**
	 * MenuItems
	 */
	let menuItems;
	/**
	 * Toggle focus on list items
	 */
	const _toggleFocus = ( event ) => {

		let self = event.target || event.srcElement;

		while ( self.classList.contains( 'nav-menu' ) === false ) {
			if ( 'li' === self.tagName.toLowerCase() ) {
				if ( self.classList.contains( 'focus' ) ) {
					self.classList.remove( 'focus' );
				} else {
					self.classList.add( 'focus' );
				}
			}
			self = self.parentElement;
		}
	};
	/**
	 * Kickoff functionality
	 */
	const _init = () => {

		let menu = ( nav.tagName === 'UL' && nav.classList.contains( 'menu' ) ) ? nav : nav.getElementsByClassName( 'menu' )[0];

		/**
		 * Bail if we cant find a menu
		 */
		if( menu.length === 0 ) {
			return;
		}
		/**
		 * Finish setting up our elements
		 */
		menuItems = menu.getElementsByTagName( 'li' );
		links     = menu.getElementsByTagName( 'a' );
		/**
		 * Make sure we have the necessary class
		 */
		if( !menu.classList.contains( 'nav-menu' ) ) {
			menu.classList.add( 'nav-menu' )
		}
		/**
		 * bind the necessary events
		 */
		for ( let i = 0, len = links.length; i < len; i++ ) {
			links[i].addEventListener( 'focus', _toggleFocus, true );
			links[i].addEventListener( 'blur', _toggleFocus, true );
		}
	};
	return _init();
}