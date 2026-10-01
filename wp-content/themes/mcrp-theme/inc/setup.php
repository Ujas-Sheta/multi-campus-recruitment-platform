<?php
/**
 * Theme supports, menus, image sizes.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'after_setup_theme',
	static function () {
		load_theme_textdomain( 'mcrp', MCRP_THEME_DIR . '/languages' );

		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'html5', array( 'search-form', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
		add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 260, 'flex-width' => true, 'flex-height' => true ) );
		remove_theme_support( 'core-block-patterns' );

		add_editor_style( array( 'assets/dist/editor.css' ) );

		register_nav_menus(
			array(
				'primary' => __( 'Primary navigation', 'mcrp' ),
				'utility' => __( 'Utility bar', 'mcrp' ),
				'footer'  => __( 'Footer quick links', 'mcrp' ),
			)
		);

		add_image_size( 'mcrp-card', 720, 450, true );
		add_image_size( 'mcrp-hero', 1920, 1000, true );
		add_image_size( 'mcrp-square', 600, 600, true );
	}
);

add_filter(
	'body_class',
	static function ( array $classes ) {
		if ( is_page_template( 'page-templates/template-landing.php' ) ) {
			$classes[] = 'is-landing';
		}
		if ( is_singular() && has_block( 'mcrp/hero' ) ) {
			$classes[] = 'has-hero-block';
		}
		return $classes;
	}
);

/**
 * Excerpt tweaks.
 */
add_filter( 'excerpt_length', static fn() => 24 );
add_filter( 'excerpt_more', static fn() => '...' );

/**
 * Allow program-friendly search: include CPTs in site search.
 */
add_action(
	'pre_get_posts',
	static function ( WP_Query $q ) {
		if ( ! is_admin() && $q->is_main_query() && $q->is_search() && ! $q->get( 'post_type' ) ) {
			$q->set( 'post_type', array( 'program', 'campus', 'event', 'instructor', 'page', 'post' ) );
		}
	}
);
