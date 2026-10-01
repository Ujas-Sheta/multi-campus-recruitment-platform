<?php
/**
 * JSON-LD for programs, events and campuses.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Print a JSON-LD script.
 */
function mcrp_print_schema( array $data ): void {
	if ( ! $data ) {
		return;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( array_merge( array( '@context' => 'https://schema.org' ), $data ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "</script>\n";
}

add_action(
	'wp_head',
	static function () {
		$org = array(
			'@type' => 'CollegeOrUniversity',
			'name'  => get_bloginfo( 'name' ),
			'url'   => home_url( '/' ),
		);
		if ( has_custom_logo() ) {
			$org['logo'] = wp_get_attachment_image_url( get_theme_mod( 'custom_logo' ), 'full' );
		}

		if ( is_front_page() ) {
			$org['department'] = array_map(
				static fn( $c ) => array( '@type' => 'CollegeOrUniversity', 'name' => $c->post_title, 'url' => get_permalink( $c ) ),
				get_posts( array( 'post_type' => 'campus', 'posts_per_page' => 20 ) )
			);
			mcrp_print_schema( $org );
			return;
		}

		if ( is_singular( 'program' ) ) {
			$id   = get_the_ID();
			$data = array(
				'@type'                 => 'EducationalOccupationalProgram',
				'name'                  => get_the_title(),
				'description'           => wp_strip_all_tags( get_the_excerpt() ),
				'url'                   => get_permalink(),
				'provider'              => $org,
				'educationalCredentialAwarded' => mcrp_term_names( $id, 'credential' ),
				'timeToComplete'        => mcrp_get( 'duration', $id ),
				'programPrerequisites'  => wp_strip_all_tags( (string) mcrp_get( 'admission_requirements', $id, '' ) ),
				'occupationalCategory'  => wp_list_pluck( (array) mcrp_get( 'career_outcomes', $id, array() ), 'title' ),
			);
			if ( $tuition = mcrp_get( 'tuition_domestic', $id ) ) {
				$data['offers'] = array( '@type' => 'Offer', 'category' => 'Tuition', 'price' => (float) $tuition, 'priceCurrency' => 'CAD' );
			}
			mcrp_print_schema( array_filter( $data ) );
		}

		if ( is_singular( 'event' ) ) {
			$id      = get_the_ID();
			$virtual = (bool) mcrp_get( 'is_virtual', $id );
			$campus  = mcrp_get( 'campus', $id );
			$tz      = wp_timezone();
			$iso     = static fn( $d ) => $d ? ( new DateTime( $d, $tz ) )->format( 'c' ) : null;
			mcrp_print_schema(
				array_filter(
					array(
						'@type'               => 'EducationEvent',
						'name'                => get_the_title(),
						'description'         => wp_strip_all_tags( get_the_excerpt() ),
						'startDate'           => $iso( mcrp_get( 'start_datetime', $id ) ),
						'endDate'             => $iso( mcrp_get( 'end_datetime', $id ) ),
						'eventAttendanceMode' => $virtual ? 'https://schema.org/OnlineEventAttendanceMode' : 'https://schema.org/OfflineEventAttendanceMode',
						'eventStatus'         => 'https://schema.org/EventScheduled',
						'location'            => $virtual
							? array( '@type' => 'VirtualLocation', 'url' => mcrp_get( 'registration_url', $id, get_permalink() ) )
							: array(
								'@type'   => 'Place',
								'name'    => $campus ? get_the_title( $campus ) : get_bloginfo( 'name' ),
								'address' => $campus ? trim( mcrp_get( 'address', $campus, '' ) . ', ' . mcrp_get( 'city', $campus, '' ) ) : '',
							),
						'organizer'           => $org,
						'isAccessibleForFree' => true,
						'url'                 => get_permalink(),
					)
				)
			);
		}

		if ( is_singular( 'campus' ) ) {
			$id = get_the_ID();
			mcrp_print_schema(
				array_filter(
					array(
						'@type'              => 'CollegeOrUniversity',
						'name'               => get_bloginfo( 'name' ) . ' - ' . get_the_title(),
						'url'                => get_permalink(),
						'telephone'          => mcrp_get( 'phone', $id ),
						'email'              => mcrp_get( 'email', $id ),
						'parentOrganization' => $org,
						'address'            => array(
							'@type'           => 'PostalAddress',
							'streetAddress'   => mcrp_get( 'address', $id ),
							'addressLocality' => mcrp_get( 'city', $id ),
							'addressRegion'   => mcrp_get( 'region', $id ),
							'postalCode'      => mcrp_get( 'postal_code', $id ),
						),
						'geo'                => mcrp_get( 'lat', $id ) ? array( '@type' => 'GeoCoordinates', 'latitude' => mcrp_get( 'lat', $id ), 'longitude' => mcrp_get( 'lng', $id ) ) : null,
					)
				)
			);
		}
	}
);
