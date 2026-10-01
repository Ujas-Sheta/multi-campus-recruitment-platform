<?php
/**
 * Reusable queries for the relationships between content types, plus
 * main-query adjustments for archives.
 *
 * @package MCRP_Core
 */

namespace MCRP;

defined( 'ABSPATH' ) || exit;

/**
 * Query helpers.
 */
class Queries {

	public static function init(): void {
		add_action( 'pre_get_posts', array( __CLASS__, 'adjust_main_query' ) );
	}

	/**
	 * Archive ordering / filtering.
	 *
	 * @param \WP_Query $q Query.
	 */
	public static function adjust_main_query( \WP_Query $q ): void {
		if ( is_admin() || ! $q->is_main_query() ) {
			return;
		}

		if ( $q->is_post_type_archive( 'program' ) || $q->is_tax( array( 'program_area', 'credential', 'delivery_mode' ) ) ) {
			$filters = self::filters_from_request();
			$args    = self::program_args( $filters );
			foreach ( array( 'tax_query', 'meta_query', 's', 'orderby', 'order', 'posts_per_page' ) as $key ) {
				if ( isset( $args[ $key ] ) ) {
					$q->set( $key, $args[ $key ] );
				}
			}
		}

		if ( $q->is_post_type_archive( array( 'campus', 'instructor' ) ) ) {
			$q->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
			$q->set( 'posts_per_page', 24 );
			if ( $q->is_post_type_archive( 'instructor' ) && ! empty( $_GET['campus'] ) ) {
				$q->set( 'meta_query', array( array( 'key' => 'campus', 'value' => absint( $_GET['campus'] ) ) ) );
			}
		}

		if ( $q->is_post_type_archive( 'event' ) || $q->is_tax( 'event_type' ) ) {
			$args = self::event_args(
				array(
					'campus' => absint( $_GET['campus'] ?? 0 ),
					'limit'  => 12,
				)
			);
			$q->set( 'meta_query', $args['meta_query'] );
			$q->set( 'meta_key', 'start_datetime' );
			$q->set( 'orderby', 'meta_value' );
			$q->set( 'order', 'ASC' );
			$q->set( 'posts_per_page', 12 );
		}
	}

	/**
	 * Read program filters from the query string.
	 */
	public static function filters_from_request(): array {
		$filters = array(
			'search'     => sanitize_text_field( wp_unslash( $_GET['q'] ?? '' ) ),
			'area'       => sanitize_title( wp_unslash( $_GET['area'] ?? '' ) ),
			'credential' => sanitize_title( wp_unslash( $_GET['credential'] ?? '' ) ),
			'delivery'   => sanitize_title( wp_unslash( $_GET['delivery'] ?? '' ) ),
			'campus'     => absint( $_GET['campus'] ?? 0 ),
		);

		// On a taxonomy archive, the current term acts as a locked filter.
		$map = array( 'program_area' => 'area', 'credential' => 'credential', 'delivery_mode' => 'delivery' );
		foreach ( $map as $tax => $key ) {
			if ( is_tax( $tax ) ) {
				$filters[ $key ] = get_queried_object()->slug;
			}
		}
		return $filters;
	}

	/**
	 * Build WP_Query args for program search/filtering.
	 *
	 * @param array $f Filters: search, area, credential, delivery, campus, per_page, page, ids.
	 */
	public static function program_args( array $f ): array {
		$args = array(
			'post_type'      => 'program',
			'post_status'    => 'publish',
			'posts_per_page' => (int) ( $f['per_page'] ?? 12 ),
			'paged'          => max( 1, (int) ( $f['page'] ?? 1 ) ),
			'orderby'        => array( 'menu_order' => 'ASC', 'title' => 'ASC' ),
			'order'          => 'ASC',
		);

		if ( ! empty( $f['ids'] ) ) {
			$args['post__in']       = mcrp_ids( $f['ids'] );
			$args['orderby']        = 'post__in';
			$args['posts_per_page'] = count( $args['post__in'] );
			return $args;
		}

		if ( ! empty( $f['search'] ) ) {
			$args['s'] = $f['search'];
		}

		$tax = array();
		foreach ( array( 'area' => 'program_area', 'credential' => 'credential', 'delivery' => 'delivery_mode' ) as $key => $taxonomy ) {
			if ( ! empty( $f[ $key ] ) ) {
				$tax[] = array( 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => (array) $f[ $key ] );
			}
		}
		if ( $tax ) {
			$args['tax_query'] = array_merge( array( 'relation' => 'AND' ), $tax );
		}

		if ( ! empty( $f['campus'] ) ) {
			$args['meta_query'] = array( self::relationship_clause( 'campuses', (int) $f['campus'] ) );
		}

		return $args;
	}

	/**
	 * Meta clause matching an ID inside a serialized ACF relationship value.
	 */
	public static function relationship_clause( string $key, int $id ): array {
		return array(
			'key'     => $key,
			'value'   => '"' . $id . '"',
			'compare' => 'LIKE',
		);
	}

	/**
	 * Programs offered at a campus.
	 *
	 * @return \WP_Post[]
	 */
	public static function programs_at_campus( int $campus_id, int $limit = -1 ): array {
		return get_posts(
			array(
				'post_type'      => 'program',
				'posts_per_page' => $limit,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'meta_query'     => array( self::relationship_clause( 'campuses', $campus_id ) ),
			)
		);
	}

	/**
	 * Campuses that offer a program.
	 *
	 * @return \WP_Post[]
	 */
	public static function campuses_for_program( int $program_id ): array {
		$ids = mcrp_ids( mcrp_get( 'campuses', $program_id ) );
		return $ids ? get_posts( array( 'post_type' => 'campus', 'post__in' => $ids, 'orderby' => 'menu_order title', 'order' => 'ASC', 'posts_per_page' => -1 ) ) : array();
	}

	/**
	 * Instructors for a program.
	 *
	 * @return \WP_Post[]
	 */
	public static function instructors_for_program( int $program_id ): array {
		$ids = mcrp_ids( mcrp_get( 'instructors', $program_id ) );
		return $ids ? get_posts( array( 'post_type' => 'instructor', 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => -1 ) ) : array();
	}

	/**
	 * Programs an instructor teaches (reverse relationship).
	 *
	 * @return \WP_Post[]
	 */
	public static function programs_for_instructor( int $instructor_id ): array {
		return get_posts(
			array(
				'post_type'      => 'program',
				'posts_per_page' => -1,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'meta_query'     => array( self::relationship_clause( 'instructors', $instructor_id ) ),
			)
		);
	}

	/**
	 * Instructors at a campus.
	 *
	 * @return \WP_Post[]
	 */
	public static function instructors_at_campus( int $campus_id, int $limit = 8 ): array {
		return get_posts(
			array(
				'post_type'      => 'instructor',
				'posts_per_page' => $limit,
				'meta_key'       => 'campus',
				'meta_value'     => $campus_id,
			)
		);
	}

	/**
	 * Upcoming events query args.
	 *
	 * @param array $a campus, program, type (slug), limit, featured.
	 */
	public static function event_args( array $a = array() ): array {
		$meta = array(
			'relation' => 'AND',
			'upcoming' => array(
				'key'     => 'start_datetime',
				'value'   => current_time( 'mysql' ),
				'compare' => '>=', // 'Y-m-d H:i:s' strings sort chronologically - portable and index-friendly.
			),
		);
		if ( ! empty( $a['campus'] ) ) {
			$meta[] = array( 'key' => 'campus', 'value' => (int) $a['campus'] );
		}
		if ( ! empty( $a['program'] ) ) {
			$meta[] = self::relationship_clause( 'programs', (int) $a['program'] );
		}
		if ( ! empty( $a['featured'] ) ) {
			$meta[] = array( 'key' => 'featured', 'value' => '1' );
		}

		$args = array(
			'post_type'      => 'event',
			'post_status'    => 'publish',
			'posts_per_page' => (int) ( $a['limit'] ?? 3 ),
			'meta_key'       => 'start_datetime',
			'orderby'        => 'meta_value',
			'order'          => 'ASC',
			'meta_query'     => $meta,
		);

		if ( ! empty( $a['type'] ) ) {
			$args['tax_query'] = array( array( 'taxonomy' => 'event_type', 'field' => 'slug', 'terms' => (array) $a['type'] ) );
		}
		return $args;
	}

	/**
	 * @return \WP_Post[]
	 */
	public static function upcoming_events( array $a = array() ): array {
		return get_posts( self::event_args( $a ) );
	}

	/**
	 * Testimonials by explicit IDs, program or campus.
	 *
	 * @param array $a ids, program, campus, limit.
	 * @return \WP_Post[]
	 */
	public static function testimonials( array $a = array() ): array {
		$args = array(
			'post_type'      => 'testimonial',
			'posts_per_page' => (int) ( $a['limit'] ?? 6 ),
			'orderby'        => 'rand',
		);
		if ( ! empty( $a['ids'] ) ) {
			$args['post__in'] = mcrp_ids( $a['ids'] );
			$args['orderby']  = 'post__in';
		} else {
			$meta = array();
			if ( ! empty( $a['program'] ) ) {
				$meta[] = array( 'key' => 'program', 'value' => (int) $a['program'] );
			}
			if ( ! empty( $a['campus'] ) ) {
				$meta[] = array( 'key' => 'campus', 'value' => (int) $a['campus'] );
			}
			if ( $meta ) {
				$args['meta_query'] = $meta;
			}
		}
		return get_posts( $args );
	}

	/**
	 * FAQs by explicit IDs or category slugs.
	 *
	 * @param array $a ids, category (string|array), limit.
	 * @return \WP_Post[]
	 */
	public static function faqs( array $a = array() ): array {
		$args = array(
			'post_type'      => 'faq',
			'posts_per_page' => (int) ( $a['limit'] ?? -1 ),
			'orderby'        => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
		);
		if ( ! empty( $a['ids'] ) ) {
			$args['post__in'] = mcrp_ids( $a['ids'] );
			$args['orderby']  = 'post__in';
		} elseif ( ! empty( $a['category'] ) ) {
			$args['tax_query'] = array( array( 'taxonomy' => 'faq_category', 'field' => is_numeric( current( (array) $a['category'] ) ) ? 'term_id' : 'slug', 'terms' => (array) $a['category'] ) );
		}
		return get_posts( $args );
	}
}
