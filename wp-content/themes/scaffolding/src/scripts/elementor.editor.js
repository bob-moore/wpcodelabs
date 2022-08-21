jQuery(function ($) {
	'use strict';
	require( 'arrive' );

    const _removeFatalError = ( dialog ) => {
    	/**
    	 * Hide the dialog
    	 */
    	$( dialog ).hide();
    	/**
    	 * Remove the listener, so we don't catch all errors
    	 */
    	$(document).unbindArrive( '#elementor-fatal-error-dialog', _removeFatalError );
    }

    const _refreshPreview = ( event ) => {
    		/**
    		 * Make sure elementor exists
    		 */
    		if ( typeof elementor === 'undefined' ) {
    			return false;
    		}
    		/**
    		 * Make sure it's the correct post message
    		 */
    		if ( event.originalEvent.data.caller !== 'theme_hook' || event.originalEvent.data.action !== 'error' ) {
    			return false;
    		}
    		/**
    		 * Attach event listener
    		 */
    		$( document ).arrive( '#elementor-fatal-error-dialog', _removeFatalError );
    		/**
    		 * Reload the preview
    		 */
    		elementor.channels.editor.trigger('elementorThemeBuilder:ApplyPreview');
    }

    $( window ).on( 'message', _refreshPreview );

});