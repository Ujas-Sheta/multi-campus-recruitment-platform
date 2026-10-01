<?php
/**
 * Campus Cards block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

return array_merge(
	array( array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ) ),
	mcrp_fields_heading( true, 'Our campuses' ),
	array(
		array( 'label' => __( 'Campuses', 'mcrp' ), 'name' => 'campuses', 'type' => 'relationship', 'post_type' => array( 'campus' ), 'return_format' => 'id', 'instructions' => __( 'Leave empty to show all campuses.', 'mcrp' ) ),
		array( 'label' => __( 'Columns', 'mcrp' ), 'name' => 'columns', 'type' => 'button_group', 'choices' => array( '2' => '2', '3' => '3', '4' => '4' ), 'default_value' => '4' ),
	)
);
