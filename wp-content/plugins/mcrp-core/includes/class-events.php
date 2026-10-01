<?php
/**
 * Event helpers: "Add to calendar" ICS downloads and admin columns.
 *
 * @package MCRP_Core
 */

namespace MCRP;

defined( 'ABSPATH' ) || exit;

/**
 * Events.
 */
class Events {

	public static function init(): void {
		add_action( 'template_redirect', array( __CLASS__, 'maybe_ics' ) );
		add_filter( 'manage_event_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_event_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
		add_filter( 'manage_edit-event_sortable_columns', array( __CLASS__, 'sortable' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'admin_sort' ) );
	}

	/**
	 * URL to download an .ics file for an event.
	 */
	public static function ics_url( int $event_id ): string {
		return add_query_arg( 'ical', '1', get_permalink( $event_id ) );
	}

	public static function maybe_ics(): void {
		if ( ! is_singular( 'event' ) || empty( $_GET['ical'] ) ) {
			return;
		}
		$id    = get_queried_object_id();
		$start = mcrp_get( 'start_datetime', $id );
		if ( ! $start ) {
			return;
		}
		$end = mcrp_get( 'end_datetime', $id ) ?: gmdate( 'Y-m-d H:i:s', strtotime( $start ) + HOUR_IN_SECONDS );
		$tz  = wp_timezone();
		$fmt = static fn( $d ) => ( new \DateTime( $d, $tz ) )->setTimezone( new \DateTimeZone( 'UTC' ) )->format( 'Ymd\THis\Z' );
		$esc = static fn( $s ) => addcslashes( wp_strip_all_tags( (string) $s ), ",;\\\n" );

		$campus   = mcrp_get( 'campus', $id );
		$location = mcrp_get( 'is_virtual', $id ) ? __( 'Online', 'mcrp' ) : trim( ( $campus ? get_the_title( $campus ) . ', ' . mcrp_get( 'address', $campus, '' ) : '' ) . ' ' . mcrp_get( 'location', $id, '' ) );

		$lines = array(
			'BEGIN:VCALENDAR',
			'VERSION:2.0',
			'PRODID:-//MCRP//Events//EN',
			'BEGIN:VEVENT',
			'UID:mcrp-event-' . $id . '@' . wp_parse_url( home_url(), PHP_URL_HOST ),
			'DTSTAMP:' . gmdate( 'Ymd\THis\Z' ),
			'DTSTART:' . $fmt( $start ),
			'DTEND:' . $fmt( $end ),
			'SUMMARY:' . $esc( get_the_title( $id ) ),
			'DESCRIPTION:' . $esc( get_the_excerpt( $id ) . ' ' . get_permalink( $id ) ),
			'LOCATION:' . $esc( $location ),
			'URL:' . get_permalink( $id ),
			'END:VEVENT',
			'END:VCALENDAR',
		);

		nocache_headers();
		header( 'Content-Type: text/calendar; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=' . sanitize_file_name( get_post_field( 'post_name', $id ) ) . '.ics' );
		echo implode( "\r\n", $lines );
		exit;
	}

	public static function columns( array $columns ): array {
		$date = $columns['date'];
		unset( $columns['date'] );
		$columns['event_start']  = __( 'Starts', 'mcrp' );
		$columns['event_campus'] = __( 'Campus', 'mcrp' );
		$columns['date']         = $date;
		return $columns;
	}

	public static function column_content( string $column, int $post_id ): void {
		if ( 'event_start' === $column ) {
			echo esc_html( mcrp_event_date( $post_id, 'date' ) . ' ' . mcrp_event_date( $post_id, 'time' ) );
		}
		if ( 'event_campus' === $column ) {
			$campus = mcrp_get( 'campus', $post_id );
			echo esc_html( mcrp_get( 'is_virtual', $post_id ) ? __( 'Virtual', 'mcrp' ) : ( $campus ? get_the_title( $campus ) : '-' ) );
		}
	}

	public static function sortable( array $columns ): array {
		$columns['event_start'] = 'event_start';
		return $columns;
	}

	public static function admin_sort( \WP_Query $q ): void {
		if ( is_admin() && $q->is_main_query() && 'event_start' === $q->get( 'orderby' ) ) {
			$q->set( 'meta_key', 'start_datetime' );
			$q->set( 'orderby', 'meta_value' );
		}
	}
}
