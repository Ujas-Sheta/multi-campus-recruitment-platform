<?php
/**
 * CTA Banner block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

return array(
	array( 'label' => __( 'Eyebrow', 'mcrp' ), 'name' => 'eyebrow', 'type' => 'text', 'wrapper' => array( 'width' => 30 ) ),
	array( 'label' => __( 'Heading', 'mcrp' ), 'name' => 'heading', 'type' => 'text', 'required' => 1, 'wrapper' => array( 'width' => 70 ) ),
	array( 'label' => __( 'Text', 'mcrp' ), 'name' => 'text', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '' ),
	mcrp_fields_buttons( 2 ),
	array( 'label' => __( 'Colour', 'mcrp' ), 'name' => 'variant', 'type' => 'button_group', 'choices' => array( 'navy' => 'Navy', 'coral' => 'Coral', 'sunrise' => 'Sunrise gradient', 'teal' => 'Teal' ), 'default_value' => 'navy', 'wrapper' => array( 'width' => 50 ) ),
	array( 'label' => __( 'Layout', 'mcrp' ), 'name' => 'layout', 'type' => 'button_group', 'choices' => array( 'inline' => 'Inline', 'centered' => 'Centered' ), 'default_value' => 'inline', 'wrapper' => array( 'width' => 50 ) ),
	array( 'label' => __( 'Contained (card) style', 'mcrp' ), 'name' => 'contained', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1 ),
);
