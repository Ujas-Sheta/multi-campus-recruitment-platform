<?php
/**
 * Pattern categories + helper for writing ACF blocks inside patterns.
 * (Pattern files in /patterns are registered by WP automatically.)
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

add_action(
	'init',
	static function () {
		register_block_pattern_category( 'mcrp-pages', array( 'label' => __( 'Campus - Full Pages', 'mcrp' ) ) );
		register_block_pattern_category( 'mcrp-sections', array( 'label' => __( 'Campus - Sections', 'mcrp' ) ) );
	},
	5
);

/**
 * Returns the block comment markup for an ACF block.
 * Adds the _fieldname => field_key refs, otherwise ACF doesn't format the values
 * (images, links etc). Pass repeaters as a list of rows.
 *
 * @param string $slug  Block slug (without "mcrp/").
 * @param array  $data  Field values keyed by field name.
 * @param array  $attrs Extra block attributes (align, className, anchor...).
 */
function mcrp_acf_block( string $slug, array $data = array(), array $attrs = array() ): string {
	$prefix = 'mcrp_block_' . str_replace( '-', '_', $slug );
	$flat   = array();

	$flatten = static function ( array $values, string $name_prefix, string $key_prefix ) use ( &$flatten, &$flat ) {
		foreach ( $values as $name => $value ) {
			$meta_name = $name_prefix . $name;
			$key       = 'field_' . $key_prefix . '_' . $name;

			$is_repeater = is_array( $value ) && array_is_list( $value ) && $value && is_array( $value[0] );
			if ( $is_repeater ) {
				$flat[ $meta_name ]       = count( $value );
				$flat[ '_' . $meta_name ] = $key;
				foreach ( $value as $i => $row ) {
					$flatten( $row, $meta_name . '_' . $i . '_', $key_prefix . '_' . $name );
				}
			} else {
				$flat[ $meta_name ]       = $value;
				$flat[ '_' . $meta_name ] = $key;
			}
		}
	};
	$flatten( $data, '', $prefix );

	return get_comment_delimited_block_content(
		'mcrp/' . $slug,
		array_merge(
			array(
				'name' => 'mcrp/' . $slug,
				'data' => $flat,
				'mode' => 'preview',
			),
			$attrs
		),
		''
	) . "\n\n";
}

/**
 * Shorthand for a link field value.
 */
function mcrp_link( string $title, string $url, string $target = '' ): array {
	return array( 'title' => $title, 'url' => $url, 'target' => $target );
}
