<?php
/**
 * Custom post types.
 *
 * @package MCRP_Core
 */

namespace MCRP;

defined( 'ABSPATH' ) || exit;

/**
 * Registers programs, campuses, instructors, events, testimonials, FAQs and (private) leads.
 */
class Post_Types {

	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register' ) );
		add_filter( 'enter_title_here', array( __CLASS__, 'title_placeholder' ), 10, 2 );
	}

	/**
	 * Post type definitions: slug => [ singular, plural, args ].
	 */
	public static function definitions(): array {
		return array(
			'program'     => array(
				__( 'Program', 'mcrp' ),
				__( 'Programs', 'mcrp' ),
				array(
					'menu_icon'    => 'dashicons-welcome-learn-more',
					'rewrite'      => array( 'slug' => 'programs', 'with_front' => false ),
					'has_archive'  => 'programs',
					'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ),
					'menu_position' => 20,
				),
			),
			'campus'      => array(
				__( 'Campus', 'mcrp' ),
				__( 'Campuses', 'mcrp' ),
				array(
					'menu_icon'    => 'dashicons-building',
					'rewrite'      => array( 'slug' => 'campuses', 'with_front' => false ),
					'has_archive'  => 'campuses',
					'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions', 'page-attributes' ),
					'hierarchical' => false,
					'menu_position' => 21,
				),
			),
			'instructor'  => array(
				__( 'Instructor', 'mcrp' ),
				__( 'Instructors', 'mcrp' ),
				array(
					'menu_icon'   => 'dashicons-businessperson',
					'rewrite'     => array( 'slug' => 'faculty', 'with_front' => false ),
					'has_archive' => 'faculty',
					'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
					'menu_position' => 22,
				),
			),
			'event'       => array(
				__( 'Event', 'mcrp' ),
				__( 'Events', 'mcrp' ),
				array(
					'menu_icon'   => 'dashicons-calendar-alt',
					'rewrite'     => array( 'slug' => 'events', 'with_front' => false ),
					'has_archive' => 'events',
					'supports'    => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
					'menu_position' => 23,
				),
			),
			'testimonial' => array(
				__( 'Testimonial', 'mcrp' ),
				__( 'Testimonials', 'mcrp' ),
				array(
					'menu_icon'          => 'dashicons-format-quote',
					'public'             => false,
					'show_ui'            => true,
					'publicly_queryable' => false,
					'has_archive'        => false,
					'rewrite'            => false,
					'supports'           => array( 'title', 'thumbnail', 'revisions' ),
					'menu_position'      => 24,
				),
			),
			'faq'         => array(
				__( 'FAQ', 'mcrp' ),
				__( 'FAQs', 'mcrp' ),
				array(
					'menu_icon'          => 'dashicons-editor-help',
					'public'             => false,
					'show_ui'            => true,
					'publicly_queryable' => false,
					'has_archive'        => false,
					'rewrite'            => false,
					'supports'           => array( 'title', 'editor', 'page-attributes', 'revisions' ),
					'menu_position'      => 25,
				),
			),
			'mcrp_lead'   => array(
				__( 'Inquiry', 'mcrp' ),
				__( 'Inquiries', 'mcrp' ),
				array(
					'menu_icon'          => 'dashicons-email-alt',
					'public'             => false,
					'show_ui'            => true,
					'show_in_rest'       => false,
					'publicly_queryable' => false,
					'exclude_from_search' => true,
					'has_archive'        => false,
					'rewrite'            => false,
					'supports'           => array( 'title' ),
					'capability_type'    => 'post',
					'capabilities'       => array( 'create_posts' => 'do_not_allow' ),
					'map_meta_cap'       => true,
					'menu_position'      => 26,
				),
			),
		);
	}

	public static function register(): void {
		foreach ( self::definitions() as $slug => list( $singular, $plural, $args ) ) {
			$labels = array(
				'name'                  => $plural,
				'singular_name'         => $singular,
				/* translators: %s: singular post type name */
				'add_new_item'          => sprintf( __( 'Add New %s', 'mcrp' ), $singular ),
				/* translators: %s: singular post type name */
				'edit_item'             => sprintf( __( 'Edit %s', 'mcrp' ), $singular ),
				/* translators: %s: singular post type name */
				'new_item'              => sprintf( __( 'New %s', 'mcrp' ), $singular ),
				/* translators: %s: singular post type name */
				'view_item'             => sprintf( __( 'View %s', 'mcrp' ), $singular ),
				/* translators: %s: plural post type name */
				'search_items'          => sprintf( __( 'Search %s', 'mcrp' ), $plural ),
				/* translators: %s: plural post type name */
				'not_found'             => sprintf( __( 'No %s found', 'mcrp' ), strtolower( $plural ) ),
				/* translators: %s: plural post type name */
				'all_items'             => sprintf( __( 'All %s', 'mcrp' ), $plural ),
				'menu_name'             => $plural,
				'featured_image'        => 'instructor' === $slug ? __( 'Headshot', 'mcrp' ) : __( 'Featured image', 'mcrp' ),
			);

			register_post_type(
				$slug,
				array_merge(
					array(
						'labels'        => $labels,
						'public'        => true,
						'show_in_rest'  => true,
						'has_archive'   => true,
						'menu_position' => 20,
					),
					$args
				)
			);
		}
	}

	/**
	 * Context-aware title placeholders for editors.
	 *
	 * @param string   $text Default text.
	 * @param \WP_Post $post Post.
	 */
	public static function title_placeholder( $text, $post ) {
		$map = array(
			'program'     => __( 'Program name, e.g. Business Administration - Marketing', 'mcrp' ),
			'campus'      => __( 'Campus name, e.g. Downtown Campus', 'mcrp' ),
			'instructor'  => __( 'Instructor full name', 'mcrp' ),
			'event'       => __( 'Event name, e.g. Fall Open House', 'mcrp' ),
			'testimonial' => __( 'Student or graduate name', 'mcrp' ),
			'faq'         => __( 'Question', 'mcrp' ),
		);
		return $map[ $post->post_type ] ?? $text;
	}
}
