<?php
/**
 * Section block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

return array_merge(
	array( array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ) ),
	mcrp_fields_heading( true ),
	array(
		array( 'label' => __( 'Heading alignment', 'mcrp' ), 'name' => 'align_heading', 'type' => 'button_group', 'choices' => array( 'left' => 'Left', 'center' => 'Center' ), 'default_value' => 'left', 'wrapper' => array( 'width' => 50 ) ),
		array( 'label' => __( 'Content width', 'mcrp' ), 'name' => 'width', 'type' => 'button_group', 'choices' => array( 'wide' => 'Wide', 'narrow' => 'Narrow (reading)' ), 'default_value' => 'wide', 'wrapper' => array( 'width' => 50 ) ),
	)
);
