<?php
/**
 * Title: International students page
 * Slug: mcrp-theme/international
 * Categories: mcrp-pages
 * Post Types: page
 * Description: Recruitment page for international applicants.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

echo mcrp_acf_block(
	'hero',
	array(
		'layout'       => 'form',
		'variant'      => 'coral',
		'height'       => 'md',
		'eyebrow'      => 'International students',
		'heading'      => 'Your global career starts in Northbridge',
		'text'         => 'Join students from 80+ countries in programs with co-op, post-graduation work pathways and dedicated international support.',
		'points'       => array(
			array( 'text' => 'Students from 80+ countries' ),
			array( 'text' => 'Dedicated international advisors' ),
		),
		'form_heading' => 'Talk to an international advisor',
		'campaign'     => 'international',
	)
);

echo mcrp_acf_block(
	'media-text',
	array(
		'eyebrow'    => 'Support from day one',
		'heading'    => 'We\'re with you from application to arrival',
		'content'    => '<p>Our international team helps with study permits, airport pickup, housing, orientation and finding part-time work - so you can focus on your studies.</p>',
		'checklist'  => array(
			array( 'text' => 'Study permit guidance' ),
			array( 'text' => 'Airport pickup & orientation' ),
			array( 'text' => 'Residence & homestay options' ),
			array( 'text' => 'Career and co-op support' ),
		),
		'position'   => 'left',
		'background' => 'white',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'faq-accordion',
	array(
		'heading'    => 'International student FAQs',
		'source'     => 'category',
		'category'   => 'international-students',
		'layout'     => 'split',
		'schema'     => 1,
		'background' => 'surface',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'testimonials',
	array(
		'heading'    => 'From around the world',
		'source'     => 'latest',
		'layout'     => 'slider',
		'background' => 'white',
		'spacing'    => 'lg',
	)
);
