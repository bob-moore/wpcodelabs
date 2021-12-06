<?php

if( !empty( $settings->template_part ) ) {
	$part = explode( '/', $settings->template_part );

	/**
	 * If a two part setting
	 */
	if( isset( $part[0] ) && isset( $part[1] ) ) {
		include _s_get_template_part( $part[0], $part[1], false );
	}
	/**
	 * A one part setting
	 */
	else if( isset( $part[0] ) ) {
		include _s_get_template_part( $part[0], '', false );
	}
}



