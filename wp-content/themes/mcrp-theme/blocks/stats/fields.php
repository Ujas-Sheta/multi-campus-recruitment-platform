<?php
/**
 * Stats block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

return array_merge(
	array( array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ) ),
	mcrp_fields_heading( true ),
	array(
		array( 'label' => __( 'Stats', 'mcrp' ), 'name' => 'items', 'type' => 'repeater', 'layout' => 'table', 'min' => 1, 'max' => 6, 'button_label' => __( 'Add stat', 'mcrp' ), 'sub_fields' => array(
			array( 'label' => __( 'Prefix', 'mcrp' ), 'name' => 'prefix', 'type' => 'text', 'wrapper' => array( 'width' => 12 ) ),
			array( 'label' => __( 'Number', 'mcrp' ), 'name' => 'number', 'type' => 'number', 'wrapper' => array( 'width' => 18 ) ),
			array( 'label' => __( 'Suffix', 'mcrp' ), 'name' => 'suffix', 'type' => 'text', 'wrapper' => array( 'width' => 12 ) ),
			array( 'label' => __( 'Label', 'mcrp' ), 'name' => 'label', 'type' => 'text' ),
		) ),
		array( 'label' => __( 'Animate numbers', 'mcrp' ), 'name' => 'animate', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
		array( 'label' => __( 'Footnote', 'mcrp' ), 'name' => 'footnote', 'type' => 'text', 'placeholder' => 'Source: 2025 Graduate Outcomes Survey' ),
	)
);
