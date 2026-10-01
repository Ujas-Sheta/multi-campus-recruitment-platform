<?php
/**
 * Media & Text block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

return array(
	array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ),
	array( 'label' => __( 'Eyebrow', 'mcrp' ), 'name' => 'eyebrow', 'type' => 'text', 'wrapper' => array( 'width' => 30 ) ),
	array( 'label' => __( 'Heading', 'mcrp' ), 'name' => 'heading', 'type' => 'text', 'wrapper' => array( 'width' => 70 ) ),
	array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content', 'type' => 'wysiwyg', 'toolbar' => 'basic', 'media_upload' => 0 ),
	array( 'label' => __( 'Checklist', 'mcrp' ), 'name' => 'checklist', 'type' => 'repeater', 'layout' => 'table', 'max' => 8, 'button_label' => __( 'Add item', 'mcrp' ), 'sub_fields' => array(
		array( 'label' => __( 'Item', 'mcrp' ), 'name' => 'text', 'type' => 'text' ),
	) ),
	mcrp_fields_buttons( 2 ),
	array( 'label' => __( 'Media', 'mcrp' ), 'name' => 'media_tab', 'type' => 'tab' ),
	array( 'label' => __( 'Image', 'mcrp' ), 'name' => 'image', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'medium' ),
	array( 'label' => __( 'Video URL (YouTube/Vimeo)', 'mcrp' ), 'name' => 'video', 'type' => 'oembed', 'instructions' => __( 'Optional - replaces the image.', 'mcrp' ) ),
	array( 'label' => __( 'Media position', 'mcrp' ), 'name' => 'position', 'type' => 'button_group', 'choices' => array( 'right' => 'Right', 'left' => 'Left' ), 'default_value' => 'right' ),
	array( 'label' => __( 'Floating stat', 'mcrp' ), 'name' => 'badge_value', 'type' => 'text', 'placeholder' => '96%', 'wrapper' => array( 'width' => 30 ) ),
	array( 'label' => __( 'Floating stat label', 'mcrp' ), 'name' => 'badge_label', 'type' => 'text', 'placeholder' => 'graduate employment', 'wrapper' => array( 'width' => 70 ) ),
);
