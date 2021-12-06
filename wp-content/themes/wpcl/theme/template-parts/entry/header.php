<?php
/**
 * Template part for displaying entry header for a single entry
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package _s
 */

use Scaffolding\TemplateTags;

use Scaffolding\addons\Jetpack;

// TemplateTags::postThumbnail();

if( is_singular() ) :

	the_title( '<h1 class="entry-title">', '</h1>' );

else :

	the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );

endif;