<?php
/**
 * Front page - content is all blocks (see patterns/home.php).
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( 'page' === get_option( 'show_on_front' ) ) {
	get_header();
	while ( have_posts() ) :
		the_post();
		echo '<div class="entry-content entry-content--blocks">';
		the_content();
		echo '</div>';
	endwhile;
	get_footer();
	return;
}

require __DIR__ . '/index.php';
