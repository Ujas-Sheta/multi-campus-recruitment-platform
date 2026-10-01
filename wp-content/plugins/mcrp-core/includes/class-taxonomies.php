<?php
/**
 * Taxonomies.
 *
 * @package MCRP_Core
 */

namespace MCRP;

defined( 'ABSPATH' ) || exit;

/**
 * Program areas, credentials, delivery modes, event types and FAQ categories.
 */
class Taxonomies {

	public static function init(): void {
		add_action( 'init', array( __CLASS__, 'register' ), 5 );
	}

	/**
	 * slug => [ singular, plural, object types, args ].
	 */
	public static function definitions(): array {
		return array(
			'program_area'  => array(
				__( 'Area of Study', 'mcrp' ),
				__( 'Areas of Study', 'mcrp' ),
				array( 'program', 'instructor' ),
				array(
					'hierarchical' => true,
					'rewrite'      => array( 'slug' => 'programs/area', 'with_front' => false ),
				),
			),
			'credential'    => array(
				__( 'Credential', 'mcrp' ),
				__( 'Credentials', 'mcrp' ),
				array( 'program' ),
				array(
					'hierarchical' => true,
					'rewrite'      => array( 'slug' => 'programs/credential', 'with_front' => false ),
				),
			),
			'delivery_mode' => array(
				__( 'Delivery Mode', 'mcrp' ),
				__( 'Delivery Modes', 'mcrp' ),
				array( 'program' ),
				array(
					'hierarchical' => true,
					'rewrite'      => array( 'slug' => 'programs/delivery', 'with_front' => false ),
				),
			),
			'event_type'    => array(
				__( 'Event Type', 'mcrp' ),
				__( 'Event Types', 'mcrp' ),
				array( 'event' ),
				array(
					'hierarchical' => true,
					'rewrite'      => array( 'slug' => 'events/type', 'with_front' => false ),
				),
			),
			'faq_category'  => array(
				__( 'FAQ Category', 'mcrp' ),
				__( 'FAQ Categories', 'mcrp' ),
				array( 'faq' ),
				array(
					'hierarchical'       => true,
					'public'             => false,
					'publicly_queryable' => false,
					'show_ui'            => true,
					'rewrite'            => false,
				),
			),
		);
	}

	public static function register(): void {
		foreach ( self::definitions() as $slug => list( $singular, $plural, $types, $args ) ) {
			register_taxonomy(
				$slug,
				$types,
				array_merge(
					array(
						'labels'            => array(
							'name'          => $plural,
							'singular_name' => $singular,
							/* translators: %s: taxonomy singular name */
							'add_new_item'  => sprintf( __( 'Add New %s', 'mcrp' ), $singular ),
							/* translators: %s: taxonomy plural name */
							'search_items'  => sprintf( __( 'Search %s', 'mcrp' ), $plural ),
							/* translators: %s: taxonomy plural name */
							'all_items'     => sprintf( __( 'All %s', 'mcrp' ), $plural ),
							'menu_name'     => $plural,
						),
						'public'            => true,
						'show_in_rest'      => true,
						'show_admin_column' => true,
					),
					$args
				)
			);
		}
	}
}
