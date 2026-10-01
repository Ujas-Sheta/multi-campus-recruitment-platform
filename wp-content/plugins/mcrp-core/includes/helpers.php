<?php
/**
 * Helpers used by both the plugin and the theme.
 *
 * Templates use mcrp_get() instead of get_field() so nothing fatals if
 * ACF gets switched off.
 *
 * @package MCRP_Core
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'mcrp_get' ) ) {
	/**
	 * Read a field value (ACF if available, raw post meta otherwise).
	 *
	 * @param string          $name    Field name.
	 * @param int|string|null $post_id Post ID, 'option', or null for current post.
	 * @param mixed           $default Default when empty.
	 * @return mixed
	 */
	function mcrp_get( string $name, $post_id = null, $default = null ) {
		if ( function_exists( 'get_field' ) ) {
			$value = get_field( $name, $post_id ?? false );
		} elseif ( 'option' === $post_id || 'options' === $post_id ) {
			$value = get_option( 'options_' . $name );
		} else {
			$value = get_post_meta( $post_id ? (int) $post_id : get_the_ID(), $name, true );
		}

		return ( null === $value || '' === $value || false === $value || array() === $value ) ? $default : $value;
	}
}

if ( ! function_exists( 'mcrp_option' ) ) {
	/**
	 * Read a value from the "Recruitment Settings" options page.
	 *
	 * @param string $name    Field name.
	 * @param mixed  $default Default value.
	 * @return mixed
	 */
	function mcrp_option( string $name, $default = null ) {
		return mcrp_get( $name, 'option', $default );
	}
}

if ( ! function_exists( 'mcrp_ids' ) ) {
	/**
	 * Normalise relationship/post-object values (objects, IDs, numeric strings) to an int[] of IDs.
	 *
	 * @param mixed $value Field value.
	 * @return int[]
	 */
	function mcrp_ids( $value ): array {
		if ( empty( $value ) ) {
			return array();
		}
		$value = is_array( $value ) ? $value : array( $value );
		$ids   = array_map(
			static fn( $item ) => $item instanceof WP_Post ? $item->ID : (int) $item,
			$value
		);
		return array_values( array_filter( $ids ) );
	}
}

if ( ! function_exists( 'mcrp_acf_keys' ) ) {
	/**
	 * Recursively assign deterministic field keys based on field names.
	 *
	 * Keys follow the convention `field_{prefix}_{name}` (sub fields:
	 * `field_{prefix}_{parent}_{name}`), which lets the seeder, block
	 * patterns and code reference fields without hard-coding random keys.
	 *
	 * @param array  $fields Field definitions (without keys).
	 * @param string $prefix Key prefix.
	 * @return array
	 */
	function mcrp_acf_keys( array $fields, string $prefix ): array {
		foreach ( $fields as $i => $field ) {
			$slug               = ! empty( $field['name'] ) ? $field['name'] : ( 'f' . $i );
			$fields[ $i ]['key'] = $field['key'] ?? 'field_' . $prefix . '_' . $slug;
			if ( ! isset( $field['name'] ) ) {
				$fields[ $i ]['name'] = '';
			}
			if ( ! empty( $field['sub_fields'] ) ) {
				$fields[ $i ]['sub_fields'] = mcrp_acf_keys( $field['sub_fields'], $prefix . '_' . $slug );
			}
			if ( ! empty( $field['layouts'] ) ) {
				foreach ( $field['layouts'] as $l => $layout ) {
					$fields[ $i ]['layouts'][ $l ]['key']        = 'layout_' . $prefix . '_' . $slug . '_' . $layout['name'];
					$fields[ $i ]['layouts'][ $l ]['sub_fields'] = mcrp_acf_keys( $layout['sub_fields'], $prefix . '_' . $slug . '_' . $layout['name'] );
				}
			}
		}
		return $fields;
	}
}

if ( ! function_exists( 'mcrp_money' ) ) {
	/**
	 * Format a tuition amount.
	 *
	 * @param mixed $amount Numeric amount.
	 * @return string
	 */
	function mcrp_money( $amount ): string {
		if ( '' === $amount || null === $amount ) {
			return '';
		}
		$symbol = (string) mcrp_option( 'currency_symbol', '$' );
		return $symbol . number_format_i18n( (float) $amount, 0 );
	}
}

if ( ! function_exists( 'mcrp_event_date' ) ) {
	/**
	 * Format an event's start (and optional end) date/time.
	 *
	 * @param int    $event_id Event post ID.
	 * @param string $part     'full' | 'day' | 'month' | 'time' | 'date'.
	 * @return string
	 */
	function mcrp_event_date( int $event_id, string $part = 'full' ): string {
		$start = mcrp_get( 'start_datetime', $event_id );
		if ( ! $start ) {
			return '';
		}
		// Stored values are local (site timezone) wall-clock times.
		$to_ts = static fn( $d ) => ( new DateTimeImmutable( $d, wp_timezone() ) )->getTimestamp();
		$ts    = $to_ts( $start );
		$end   = mcrp_get( 'end_datetime', $event_id );
		$te    = $end ? $to_ts( $end ) : null;

		switch ( $part ) {
			case 'day':
				return wp_date( 'j', $ts, wp_timezone() );
			case 'month':
				return wp_date( 'M', $ts, wp_timezone() );
			case 'date':
				return wp_date( get_option( 'date_format' ), $ts, wp_timezone() );
			case 'time':
				$time = wp_date( get_option( 'time_format' ), $ts, wp_timezone() );
				return $te ? $time . ' - ' . wp_date( get_option( 'time_format' ), $te, wp_timezone() ) : $time;
			default:
				return wp_date( 'l, F j, Y', $ts, wp_timezone() ) . ', ' . mcrp_event_date( $event_id, 'time' );
		}
	}
}

if ( ! function_exists( 'mcrp_term_names' ) ) {
	/**
	 * Comma-separated term names for a post.
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $taxonomy Taxonomy.
	 * @return string
	 */
	function mcrp_term_names( int $post_id, string $taxonomy ): string {
		$terms = get_the_terms( $post_id, $taxonomy );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}
		return implode( ', ', wp_list_pluck( $terms, 'name' ) );
	}
}

if ( ! function_exists( 'mcrp_acf_pro_active' ) ) {
	/**
	 * Whether ACF Pro (blocks, repeaters, options pages) is available.
	 */
	function mcrp_acf_pro_active(): bool {
		return function_exists( 'acf_register_block_type' ) && function_exists( 'acf_add_options_page' );
	}
}
