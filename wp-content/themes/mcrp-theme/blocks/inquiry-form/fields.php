<?php
/**
 * Inquiry Form block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

return array(
	array( 'label' => __( 'Form', 'mcrp' ), 'name' => 'form_tab', 'type' => 'tab' ),
	array( 'label' => __( 'Form heading', 'mcrp' ), 'name' => 'heading', 'type' => 'text', 'default_value' => 'Request information', 'wrapper' => array( 'width' => 50 ) ),
	array( 'label' => __( 'Button label', 'mcrp' ), 'name' => 'button', 'type' => 'text', 'default_value' => 'Get program info', 'wrapper' => array( 'width' => 50 ) ),
	array( 'label' => __( 'Form intro', 'mcrp' ), 'name' => 'intro', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '' ),
	array( 'label' => __( 'Campaign name', 'mcrp' ), 'name' => 'campaign', 'type' => 'text', 'instructions' => __( 'Stored with each lead for reporting, e.g. "spring-2027-paid-social".', 'mcrp' ), 'wrapper' => array( 'width' => 50 ) ),
	array( 'label' => __( 'Redirect after submit', 'mcrp' ), 'name' => 'redirect', 'type' => 'page_link', 'post_type' => array( 'page' ), 'allow_null' => 1, 'instructions' => __( 'Optional thank-you page (useful for conversion tracking).', 'mcrp' ), 'wrapper' => array( 'width' => 50 ) ),
	array( 'label' => __( 'Pre-select program', 'mcrp' ), 'name' => 'program', 'type' => 'post_object', 'post_type' => array( 'program' ), 'return_format' => 'id', 'allow_null' => 1, 'wrapper' => array( 'width' => 35 ) ),
	array( 'label' => __( 'Hide program field', 'mcrp' ), 'name' => 'lock_program', 'type' => 'true_false', 'ui' => 1, 'wrapper' => array( 'width' => 15 ) ),
	array( 'label' => __( 'Pre-select campus', 'mcrp' ), 'name' => 'campus', 'type' => 'post_object', 'post_type' => array( 'campus' ), 'return_format' => 'id', 'allow_null' => 1, 'wrapper' => array( 'width' => 35 ) ),
	array( 'label' => __( 'Show message field', 'mcrp' ), 'name' => 'show_message', 'type' => 'true_false', 'ui' => 1, 'wrapper' => array( 'width' => 15 ) ),
	array( 'label' => __( 'Success message', 'mcrp' ), 'name' => 'success', 'type' => 'text', 'instructions' => __( 'Leave empty to use the global message.', 'mcrp' ) ),

	array( 'label' => __( 'Side content', 'mcrp' ), 'name' => 'side_tab', 'type' => 'tab' ),
	array( 'label' => __( 'Layout', 'mcrp' ), 'name' => 'layout', 'type' => 'button_group', 'choices' => array( 'split' => 'Text + form', 'centered' => 'Form only' ), 'default_value' => 'split' ),
	array( 'label' => __( 'Eyebrow', 'mcrp' ), 'name' => 'eyebrow', 'type' => 'text', 'wrapper' => array( 'width' => 30 ) ),
	array( 'label' => __( 'Side heading', 'mcrp' ), 'name' => 'side_heading', 'type' => 'text', 'wrapper' => array( 'width' => 70 ) ),
	array( 'label' => __( 'Side text', 'mcrp' ), 'name' => 'side_text', 'type' => 'textarea', 'rows' => 3, 'new_lines' => '' ),
	array( 'label' => __( 'What you\'ll receive', 'mcrp' ), 'name' => 'points', 'type' => 'repeater', 'layout' => 'table', 'max' => 6, 'button_label' => __( 'Add point', 'mcrp' ), 'sub_fields' => array(
		array( 'label' => __( 'Point', 'mcrp' ), 'name' => 'text', 'type' => 'text' ),
	) ),
);
