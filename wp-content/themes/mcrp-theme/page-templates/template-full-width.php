<?php
/**
 * Template Name: Full Width (Blocks)
 * Template Post Type: page, program, campus
 *
 * No page hero, full width. For pages built only with blocks.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	echo '<div class="entry-content entry-content--blocks">';
	the_content();
	echo '</div>';
endwhile;

get_footer();
