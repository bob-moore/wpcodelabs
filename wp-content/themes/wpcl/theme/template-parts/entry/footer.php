<?php
/**
 * Template part for displaying entry footer for a single entry
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package _s
 */

use Scaffolding\TemplateTags;
use Scaffolding\addons\Jetpack;

TemplateTags::getInstance()->taxonomyList( [ 'tax' => 'post_tag', 'sep' => ', ', 'before_item' => '#' ] );

Jetpack::getInstance()->sharing();