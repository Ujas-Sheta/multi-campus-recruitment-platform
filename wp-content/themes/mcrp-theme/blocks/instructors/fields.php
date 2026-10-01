<?php
/**
 * Faculty Grid block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

$src = static fn( string $v ) => array( array( array( 'field' => 'field_mcrp_block_instructors_source', 'operator' => '==', 'value' => $v ) ) );

return array_merge(
	array( array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ) ),
	mcrp_fields_heading( true, 'Meet our faculty' ),
	array(
		array( 'label' => __( 'Source', 'mcrp' ), 'name' => 'source', 'type' => 'button_group', 'choices' => array( 'manual' => 'Hand-picked', 'program' => 'By program', 'campus' => 'By campus', 'area' => 'By area of study' ), 'default_value' => 'manual' ),
		array( 'label' => __( 'Instructors', 'mcrp' ), 'name' => 'items', 'type' => 'relationship', 'post_type' => array( 'instructor' ), 'return_format' => 'id', 'conditional_logic' => $src( 'manual' ) ),
		array( 'label' => __( 'Program', 'mcrp' ), 'name' => 'program', 'type' => 'post_object', 'post_type' => array( 'program' ), 'return_format' => 'id', 'conditional_logic' => $src( 'program' ) ),
		array( 'label' => __( 'Campus', 'mcrp' ), 'name' => 'campus', 'type' => 'post_object', 'post_type' => array( 'campus' ), 'return_format' => 'id', 'conditional_logic' => $src( 'campus' ) ),
		array( 'label' => __( 'Area of study', 'mcrp' ), 'name' => 'area', 'type' => 'select', 'mcrp_terms' => 'program_area', 'choices' => array(), 'conditional_logic' => $src( 'area' ) ),
		array( 'label' => __( 'Maximum', 'mcrp' ), 'name' => 'limit', 'type' => 'number', 'default_value' => 4 ),
		array( 'label' => __( '"Directory" link', 'mcrp' ), 'name' => 'view_all', 'type' => 'link' ),
	)
);
