<?php
/**
 * Program Finder block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

$is_featured = array( array( array( 'field' => 'field_mcrp_block_program_finder_mode', 'operator' => '==', 'value' => 'featured' ) ) );
$is_filter   = array( array( array( 'field' => 'field_mcrp_block_program_finder_mode', 'operator' => '==', 'value' => 'filter' ) ) );

return array_merge(
	array( array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ) ),
	mcrp_fields_heading( true, 'Find your program' ),
	array(
		array( 'label' => __( 'Mode', 'mcrp' ), 'name' => 'mode', 'type' => 'button_group', 'choices' => array( 'filter' => 'Search & filter', 'featured' => 'Hand-picked programs' ), 'default_value' => 'filter' ),
		array( 'label' => __( 'Programs', 'mcrp' ), 'name' => 'programs', 'type' => 'relationship', 'post_type' => array( 'program' ), 'filters' => array( 'search', 'taxonomy' ), 'return_format' => 'id', 'conditional_logic' => $is_featured ),
		array( 'label' => __( 'Show filter bar', 'mcrp' ), 'name' => 'show_filters', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'conditional_logic' => $is_filter, 'wrapper' => array( 'width' => 30 ) ),
		array( 'label' => __( 'Programs per page', 'mcrp' ), 'name' => 'per_page', 'type' => 'number', 'default_value' => 6, 'min' => 3, 'max' => 24, 'conditional_logic' => $is_filter, 'wrapper' => array( 'width' => 30 ) ),
		array( 'label' => __( 'Pre-filter', 'mcrp' ), 'name' => 'prefilter_message', 'type' => 'message', 'message' => __( 'Lock the results to a subset - perfect for campaign pages (e.g. only Health Sciences at Riverside).', 'mcrp' ), 'conditional_logic' => $is_filter ),
		array( 'label' => __( 'Area of study', 'mcrp' ), 'name' => 'area', 'type' => 'select', 'mcrp_terms' => 'program_area', 'choices' => array(), 'allow_null' => 1, 'conditional_logic' => $is_filter, 'wrapper' => array( 'width' => 25 ) ),
		array( 'label' => __( 'Credential', 'mcrp' ), 'name' => 'credential', 'type' => 'select', 'mcrp_terms' => 'credential', 'choices' => array(), 'allow_null' => 1, 'conditional_logic' => $is_filter, 'wrapper' => array( 'width' => 25 ) ),
		array( 'label' => __( 'Delivery', 'mcrp' ), 'name' => 'delivery', 'type' => 'select', 'mcrp_terms' => 'delivery_mode', 'choices' => array(), 'allow_null' => 1, 'conditional_logic' => $is_filter, 'wrapper' => array( 'width' => 25 ) ),
		array( 'label' => __( 'Campus', 'mcrp' ), 'name' => 'campus', 'type' => 'post_object', 'post_type' => array( 'campus' ), 'return_format' => 'id', 'allow_null' => 1, 'conditional_logic' => $is_filter, 'wrapper' => array( 'width' => 25 ) ),
		array( 'label' => __( '"View all" link', 'mcrp' ), 'name' => 'view_all', 'type' => 'link' ),
	)
);
