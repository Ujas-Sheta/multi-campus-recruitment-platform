<?php
/**
 * Title: Admissions page
 * Slug: mcrp-theme/admissions
 * Categories: mcrp-pages
 * Post Types: page
 * Description: How-to-apply page with steps, FAQs by category and an advisor inquiry form.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

echo mcrp_acf_block(
	'hero',
	array(
		'layout'  => 'centered',
		'variant' => 'navy',
		'height'  => 'sm',
		'eyebrow' => 'Admissions',
		'heading' => 'Applying is easier than you think',
		'text'    => 'Rolling admissions, no essays and real people to help you every step of the way.',
		'buttons' => array(
			array( 'link' => mcrp_link( 'Start your application', 'https://apply.example.edu' ), 'style' => 'primary' ),
			array( 'link' => mcrp_link( 'Talk to an advisor', '#inquiry' ), 'style' => 'outline-light' ),
		),
	)
);

echo mcrp_acf_block(
	'admissions-steps',
	array(
		'eyebrow'    => 'How to apply',
		'heading'    => 'Four steps to your first day',
		'steps'      => array(
			array( 'title' => 'Choose your program', 'text' => 'Use the Program Finder to compare programs, campuses and start dates.', 'link' => mcrp_link( 'Find a program', get_post_type_archive_link( 'program' ) ?: home_url( '/programs/' ) ) ),
			array( 'title' => 'Apply online', 'text' => 'Complete the 15-minute online application and pay the application fee.' ),
			array( 'title' => 'Submit documents', 'text' => 'Upload transcripts and any program-specific requirements.' ),
			array( 'title' => 'Accept your offer', 'text' => 'Confirm your seat, apply for financial aid and register for orientation.' ),
		),
		'layout'     => 'timeline',
		'background' => 'white',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'faq-accordion',
	array(
		'eyebrow'    => 'Tuition & financial aid',
		'heading'    => 'Paying for college',
		'source'     => 'category',
		'category'   => 'tuition-financial-aid',
		'limit'      => 6,
		'layout'     => 'split',
		'schema'     => 1,
		'background' => 'surface',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'events',
	array(
		'eyebrow'    => 'Visit',
		'heading'    => 'Meet us before you apply',
		'type'       => 'info-session',
		'limit'      => 3,
		'background' => 'white',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'inquiry-form',
	array(
		'layout'       => 'split',
		'eyebrow'      => 'Questions?',
		'side_heading' => 'Talk to an admissions advisor',
		'side_text'    => 'Tell us a little about yourself and an advisor will reach out within one business day.',
		'points'       => array(
			array( 'text' => 'Personalised program recommendations' ),
			array( 'text' => 'Help with transcripts and requirements' ),
			array( 'text' => 'Scholarship and financial aid guidance' ),
		),
		'heading'      => 'Request a call back',
		'button'       => 'Contact me',
		'campaign'     => 'admissions-page',
		'show_message' => 1,
		'background'   => 'surface',
		'spacing'      => 'lg',
	)
);
