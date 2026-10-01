<?php
/**
 * FAQ Accordion block fields.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

return array_merge(
	array( array( 'label' => __( 'Content', 'mcrp' ), 'name' => 'content_tab', 'type' => 'tab' ) ),
	mcrp_fields_heading( true, 'Frequently asked questions' ),
	array(
		array( 'label' => __( 'Source', 'mcrp' ), 'name' => 'source', 'type' => 'button_group', 'choices' => array( 'category' => 'By category', 'manual' => 'Hand-picked' ), 'default_value' => 'category' ),
		array( 'label' => __( 'FAQ category', 'mcrp' ), 'name' => 'category', 'type' => 'select', 'mcrp_terms' => 'faq_category', 'choices' => array(), 'allow_null' => 1, 'instructions' => __( 'Leave on "Any" to show all FAQs.', 'mcrp' ), 'conditional_logic' => array( array( array( 'field' => 'field_mcrp_block_faq_accordion_source', 'operator' => '==', 'value' => 'category' ) ) ), 'wrapper' => array( 'width' => 50 ) ),
		array( 'label' => __( 'Maximum', 'mcrp' ), 'name' => 'limit', 'type' => 'number', 'default_value' => 6, 'conditional_logic' => array( array( array( 'field' => 'field_mcrp_block_faq_accordion_source', 'operator' => '==', 'value' => 'category' ) ) ), 'wrapper' => array( 'width' => 50 ) ),
		array( 'label' => __( 'FAQs', 'mcrp' ), 'name' => 'items', 'type' => 'relationship', 'post_type' => array( 'faq' ), 'filters' => array( 'search', 'taxonomy' ), 'return_format' => 'id', 'conditional_logic' => array( array( array( 'field' => 'field_mcrp_block_faq_accordion_source', 'operator' => '==', 'value' => 'manual' ) ) ) ),
		array( 'label' => __( 'Layout', 'mcrp' ), 'name' => 'layout', 'type' => 'button_group', 'choices' => array( 'stacked' => 'Stacked', 'split' => 'Heading left' ), 'default_value' => 'split', 'wrapper' => array( 'width' => 34 ) ),
		array( 'label' => __( 'Open first item', 'mcrp' ), 'name' => 'open_first', 'type' => 'true_false', 'ui' => 1, 'wrapper' => array( 'width' => 33 ) ),
		array( 'label' => __( 'Output FAQ schema', 'mcrp' ), 'name' => 'schema', 'type' => 'true_false', 'ui' => 1, 'default_value' => 1, 'instructions' => __( 'Use once per page.', 'mcrp' ), 'wrapper' => array( 'width' => 33 ) ),
		array( 'label' => __( 'Link below', 'mcrp' ), 'name' => 'link', 'type' => 'link' ),
	)
);
