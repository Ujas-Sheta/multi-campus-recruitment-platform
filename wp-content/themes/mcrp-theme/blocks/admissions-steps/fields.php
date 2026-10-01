<?php
/**
 * Admissions Steps block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

return array_merge(
	array( array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ) ),
	mcrp_fields_heading( true, 'How to apply' ),
	array(
		array( 'label' => __( 'Steps', 'mcrp' ), 'name' => 'steps', 'type' => 'repeater', 'layout' => 'block', 'min' => 1, 'max' => 8, 'button_label' => __( 'Add step', 'mcrp' ), 'collapsed' => 'field_mcrp_block_admissions_steps_steps_title', 'sub_fields' => array(
			array( 'label' => __( 'Title', 'mcrp' ), 'name' => 'title', 'type' => 'text', 'wrapper' => array( 'width' => 40 ) ),
			array( 'label' => __( 'Description', 'mcrp' ), 'name' => 'text', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '', 'wrapper' => array( 'width' => 40 ) ),
			array( 'label' => __( 'Link', 'mcrp' ), 'name' => 'link', 'type' => 'link', 'wrapper' => array( 'width' => 20 ) ),
		) ),
		array( 'label' => __( 'Layout', 'mcrp' ), 'name' => 'layout', 'type' => 'button_group', 'choices' => array( 'cards' => 'Cards', 'timeline' => 'Timeline' ), 'default_value' => 'cards' ),
		mcrp_fields_buttons( 2 ),
	)
);
