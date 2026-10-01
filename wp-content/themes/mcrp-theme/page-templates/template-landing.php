<?php
/**
 * Template Name: Campaign Landing Page
 * Template Post Type: page
 *
 * For paid/social campaign pages. Only logo, phone and a "Request info" button
 * in the header, no nav. Start from the "Campaign landing page" pattern.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header( 'landing' );

while ( have_posts() ) :
	the_post();
	echo '<div class="entry-content entry-content--blocks">';
	the_content();
	echo '</div>';
endwhile;

get_footer( 'landing' );
