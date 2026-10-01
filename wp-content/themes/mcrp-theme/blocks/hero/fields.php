<?php
/**
 * Hero block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

return array(
	array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ),
	array( 'label' => __( 'Layout', 'mcrp' ), 'name' => 'layout', 'type' => 'button_group', 'choices' => array( 'left' => 'Left aligned', 'centered' => 'Centered', 'form' => 'With inquiry form' ), 'default_value' => 'left' ),
	array( 'label' => __( 'Eyebrow', 'mcrp' ), 'name' => 'eyebrow', 'type' => 'text', 'wrapper' => array( 'width' => 30 ) ),
	array( 'label' => __( 'Heading', 'mcrp' ), 'name' => 'heading', 'type' => 'textarea', 'rows' => 2, 'new_lines' => 'br', 'required' => 1, 'wrapper' => array( 'width' => 70 ) ),
	array( 'label' => __( 'Supporting text', 'mcrp' ), 'name' => 'text', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '' ),
	mcrp_fields_buttons( 2 ),
	array( 'label' => __( 'Trust points', 'mcrp' ), 'name' => 'points', 'type' => 'repeater', 'layout' => 'table', 'max' => 4, 'button_label' => __( 'Add point', 'mcrp' ), 'instructions' => __( 'Short proof points shown under the buttons, e.g. "96% grad employment".', 'mcrp' ), 'sub_fields' => array(
		array( 'label' => __( 'Text', 'mcrp' ), 'name' => 'text', 'type' => 'text' ),
	) ),

	array( 'label' => __( 'Background', 'mcrp' ), 'name' => 'bg_tab', 'type' => 'tab' ),
	array( 'label' => __( 'Colour', 'mcrp' ), 'name' => 'variant', 'type' => 'button_group', 'choices' => array( 'navy' => 'Navy', 'coral' => 'Coral', 'light' => 'Light' ), 'default_value' => 'navy' ),
	array( 'label' => __( 'Background image', 'mcrp' ), 'name' => 'image', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium' ),
	array( 'label' => __( 'Image overlay strength', 'mcrp' ), 'name' => 'overlay', 'type' => 'range', 'min' => 0, 'max' => 90, 'step' => 5, 'default_value' => 60, 'append' => '%' ),
	array( 'label' => __( 'Height', 'mcrp' ), 'name' => 'height', 'type' => 'button_group', 'choices' => array( 'md' => 'Medium', 'lg' => 'Large', 'sm' => 'Compact' ), 'default_value' => 'md' ),

	array( 'label' => __( 'Form', 'mcrp' ), 'name' => 'form_tab', 'type' => 'tab' ),
	array( 'label' => __( 'Form heading', 'mcrp' ), 'name' => 'form_heading', 'type' => 'text', 'default_value' => 'Get program info' ),
	array( 'label' => __( 'Campaign name', 'mcrp' ), 'name' => 'campaign', 'type' => 'text', 'instructions' => __( 'Saved with every inquiry from this form, e.g. "fall-2027-open-house".', 'mcrp' ), 'wrapper' => array( 'width' => 50 ) ),
	array( 'label' => __( 'Pre-select program', 'mcrp' ), 'name' => 'program', 'type' => 'post_object', 'post_type' => array( 'program' ), 'return_format' => 'id', 'allow_null' => 1, 'wrapper' => array( 'width' => 50 ) ),
);
