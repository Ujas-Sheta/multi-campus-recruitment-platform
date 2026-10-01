<?php
/**
 * Testimonials block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

$src = static fn( string $v ) => array( array( array( 'field' => 'field_mcrp_block_testimonials_source', 'operator' => '==', 'value' => $v ) ) );

return array_merge(
	array( array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ) ),
	mcrp_fields_heading( false, 'Student stories' ),
	array(
		array( 'label' => __( 'Source', 'mcrp' ), 'name' => 'source', 'type' => 'button_group', 'choices' => array( 'latest' => 'Random', 'program' => 'By program', 'campus' => 'By campus', 'manual' => 'Hand-picked' ), 'default_value' => 'latest' ),
		array( 'label' => __( 'Program', 'mcrp' ), 'name' => 'program', 'type' => 'post_object', 'post_type' => array( 'program' ), 'return_format' => 'id', 'conditional_logic' => $src( 'program' ) ),
		array( 'label' => __( 'Campus', 'mcrp' ), 'name' => 'campus', 'type' => 'post_object', 'post_type' => array( 'campus' ), 'return_format' => 'id', 'conditional_logic' => $src( 'campus' ) ),
		array( 'label' => __( 'Testimonials', 'mcrp' ), 'name' => 'items', 'type' => 'relationship', 'post_type' => array( 'testimonial' ), 'return_format' => 'id', 'conditional_logic' => $src( 'manual' ) ),
		array( 'label' => __( 'Layout', 'mcrp' ), 'name' => 'layout', 'type' => 'button_group', 'choices' => array( 'slider' => 'Slider', 'grid' => 'Grid' ), 'default_value' => 'slider', 'wrapper' => array( 'width' => 50 ) ),
		array( 'label' => __( 'Maximum', 'mcrp' ), 'name' => 'limit', 'type' => 'number', 'default_value' => 6, 'min' => 1, 'max' => 12, 'wrapper' => array( 'width' => 50 ) ),
	)
);
