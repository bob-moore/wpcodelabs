<?php

do_action( '_s_sidebar_primary_before' );

if ( is_active_sidebar( 'sidebar-primary' ) ) :

	dynamic_sidebar( 'sidebar-primary' );

endif;

do_action( '_s_sidebar_primary_after' );