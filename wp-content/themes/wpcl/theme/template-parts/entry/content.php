<?php
/**
 * Template part for displaying entry content for a single entry
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package _s
 */

use Scaffolding\TemplateTags;

if( is_singular() ) :

	the_content();

	wp_link_pages();

else :

	the_excerpt();

endif;