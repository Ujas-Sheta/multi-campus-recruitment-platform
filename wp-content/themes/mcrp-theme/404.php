<?php
/**
 * 404.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

mcrp_page_hero(
	array(
		'title'   => __( 'We couldn\'t find that page', 'mcrp' ),
		'intro'   => __( 'The page may have moved. Try searching for a program, or start from one of the links below.', 'mcrp' ),
		'actions' => mcrp_button( array( 'title' => __( 'Find a program', 'mcrp' ), 'url' => get_post_type_archive_link( 'program' ) ?: home_url( '/' ) ), 'primary', 'arrow-right' )
			. mcrp_button( array( 'title' => __( 'Back to home', 'mcrp' ), 'url' => home_url( '/' ) ), 'white' ),
	)
);
get_footer();
