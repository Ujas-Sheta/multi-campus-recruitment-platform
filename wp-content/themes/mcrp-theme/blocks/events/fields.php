<?php
/**
 * Upcoming Events block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

return array_merge(
	array( array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ) ),
	mcrp_fields_heading( true, 'Upcoming events' ),
	array(
		array( 'label' => __( 'Event type', 'mcrp' ), 'name' => 'type', 'type' => 'select', 'mcrp_terms' => 'event_type', 'choices' => array(), 'allow_null' => 1, 'wrapper' => array( 'width' => 25 ) ),
		array( 'label' => __( 'Campus', 'mcrp' ), 'name' => 'campus', 'type' => 'post_object', 'post_type' => array( 'campus' ), 'return_format' => 'id', 'allow_null' => 1, 'wrapper' => array( 'width' => 25 ) ),
		array( 'label' => __( 'Featured only', 'mcrp' ), 'name' => 'featured', 'type' => 'true_false', 'ui' => 1, 'wrapper' => array( 'width' => 25 ) ),
		array( 'label' => __( 'Number of events', 'mcrp' ), 'name' => 'limit', 'type' => 'number', 'default_value' => 3, 'min' => 1, 'max' => 12, 'wrapper' => array( 'width' => 25 ) ),
		array( 'label' => __( '"All events" link', 'mcrp' ), 'name' => 'view_all', 'type' => 'link' ),
		array( 'label' => __( 'Empty state message', 'mcrp' ), 'name' => 'empty', 'type' => 'text', 'default_value' => 'New events are added regularly - check back soon or book a personal tour.' ),
	)
);
