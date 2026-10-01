<?php
/**
 * Fallbacks in case the MCRP Core plugin gets deactivated.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'mcrp_get' ) ) {
	function mcrp_get( string $name, $post_id = null, $default = null ) {
		$value = function_exists( 'get_field' ) ? get_field( $name, $post_id ?? false ) : get_post_meta( $post_id ? (int) $post_id : get_the_ID(), $name, true );
		return ( null === $value || '' === $value || false === $value || array() === $value ) ? $default : $value;
	}
}
if ( ! function_exists( 'mcrp_option' ) ) {
	function mcrp_option( string $name, $default = null ) {
		$value = function_exists( 'get_field' ) ? get_field( $name, 'option' ) : get_option( 'options_' . $name );
		return $value ? $value : $default;
	}
}
if ( ! function_exists( 'mcrp_ids' ) ) {
	function mcrp_ids( $value ): array {
		$value = is_array( $value ) ? $value : ( $value ? array( $value ) : array() );
		return array_values( array_filter( array_map( static fn( $v ) => $v instanceof WP_Post ? $v->ID : (int) $v, $value ) ) );
	}
}
if ( ! function_exists( 'mcrp_term_names' ) ) {
	function mcrp_term_names( int $post_id, string $taxonomy ): string {
		$terms = get_the_terms( $post_id, $taxonomy );
		return ( $terms && ! is_wp_error( $terms ) ) ? implode( ', ', wp_list_pluck( $terms, 'name' ) ) : '';
	}
}
if ( ! function_exists( 'mcrp_money' ) ) {
	function mcrp_money( $amount ): string {
		return '' === $amount || null === $amount ? '' : '$' . number_format_i18n( (float) $amount );
	}
}
if ( ! function_exists( 'mcrp_event_date' ) ) {
	function mcrp_event_date( int $event_id, string $part = 'full' ): string {
		$start = mcrp_get( 'start_datetime', $event_id );
		return $start ? wp_date( 'day' === $part ? 'j' : ( 'month' === $part ? 'M' : 'F j, Y g:i a' ), ( new DateTimeImmutable( $start, wp_timezone() ) )->getTimestamp() ) : '';
	}
}

/**
 * Call a MCRP\Queries method if the plugin is active.
 *
 * @param string $method Method name.
 * @param mixed  ...$args Arguments.
 * @return array
 */
function mcrp_q( string $method, ...$args ): array {
	if ( class_exists( '\MCRP\Queries' ) && method_exists( '\MCRP\Queries', $method ) ) {
		return (array) call_user_func_array( array( '\MCRP\Queries', $method ), $args );
	}
	return array();
}

/**
 * Whether the platform plugin is active.
 */
function mcrp_core_active(): bool {
	return class_exists( '\MCRP\Queries' );
}

add_action(
	'admin_notices',
	static function () {
		if ( mcrp_core_active() || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>' . esc_html__( 'The MCRP Campus theme requires the "MCRP Core" plugin (custom post types, fields and APIs). Please activate it.', 'mcrp' ) . '</p></div>';
	}
);
