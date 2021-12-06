<?php



do_action( '_s_loop_before' );

if ( have_posts() ) :

	do_action( '_s_while_before' );

	while ( have_posts() ) : the_post();

		do_action( '_s_content_before' );

		Scaffolding\Templates::getTemplatePart( 'content' );

		do_action( '_s_content_after' );

		do_action( '_s_comments' );

	endwhile;

	do_action( '_s_while_after' );

elseif( is_404() ) :

	do_action( '_s_content_before' );

	Scaffolding\Templates::getTemplatePart( 'content', '404' );

	do_action( '_s_content_after' );

else :

	do_action( '_s_content_before' );

	Scaffolding\Templates::getTemplatePart( 'content', 'none' );

	do_action( '_s_content_after' );

endif;

do_action( '_s_loop_after' );
