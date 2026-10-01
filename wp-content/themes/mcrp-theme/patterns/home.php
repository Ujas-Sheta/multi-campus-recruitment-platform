<?php
/**
 * Title: Homepage
 * Slug: mcrp-theme/home
 * Categories: mcrp-pages
 * Post Types: page
 * Description: Homepage layout.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

$programs_url = get_post_type_archive_link( 'program' ) ?: home_url( '/programs/' );
$events_url   = get_post_type_archive_link( 'event' ) ?: home_url( '/events/' );

echo mcrp_acf_block(
	'hero',
	array(
		'layout'  => 'left',
		'variant' => 'navy',
		'height'  => 'lg',
		'eyebrow' => 'Fall 2027 applications now open',
		'heading' => 'Learn it. Live it. <em>Launch your career.</em>',
		'text'    => 'Hands-on certificates, diplomas and post-graduate programs at three campuses and online - designed with employers, taught by industry experts.',
		'buttons' => array(
			array( 'link' => mcrp_link( 'Find your program', $programs_url ), 'style' => 'primary' ),
			array( 'link' => mcrp_link( 'Book a campus tour', $events_url ), 'style' => 'outline-light' ),
		),
		'points'  => array(
			array( 'text' => '93% graduate employment' ),
			array( 'text' => '12+ career-focused programs' ),
			array( 'text' => '$3M+ in scholarships yearly' ),
		),
	)
);

echo mcrp_acf_block(
	'program-finder',
	array(
		'eyebrow'      => 'Programs',
		'heading'      => 'Find the program that fits your future',
		'intro'        => 'Search by keyword or filter by area of study, credential, delivery and campus.',
		'mode'         => 'filter',
		'show_filters' => 1,
		'per_page'     => 6,
		'view_all'     => mcrp_link( 'View all programs', $programs_url ),
		'background'   => 'surface',
		'spacing'      => 'lg',
	)
);

echo mcrp_acf_block(
	'stats',
	array(
		'eyebrow'    => 'Why Northbridge',
		'heading'    => 'Results that speak for themselves',
		'items'      => array(
			array( 'prefix' => '', 'number' => 93, 'suffix' => '%', 'label' => 'Graduate employment within 6 months' ),
			array( 'prefix' => '', 'number' => 23700, 'suffix' => '+', 'label' => 'Students across all campuses' ),
			array( 'prefix' => '', 'number' => 1200, 'suffix' => '', 'label' => 'Employer & co-op partners' ),
			array( 'prefix' => '$', 'number' => 3, 'suffix' => 'M', 'label' => 'Awarded in scholarships each year' ),
		),
		'animate'    => 1,
		'footnote'   => 'Source: 2025 Graduate Outcomes Survey.',
		'background' => 'primary',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'campus-cards',
	array(
		'eyebrow'    => 'Campuses',
		'heading'    => 'Three campuses. One online. Your choice.',
		'intro'      => 'Every campus has its own personality - and the same commitment to hands-on learning.',
		'columns'    => '4',
		'background' => 'white',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'media-text',
	array(
		'eyebrow'     => 'Hands-on learning',
		'heading'     => 'Graduate with experience employers want',
		'content'     => '<p>Every program includes real-world learning - paid co-op terms, clinical placements, industry capstones or our public teaching restaurant. You will graduate with a portfolio, references and a professional network.</p>',
		'checklist'   => array(
			array( 'text' => 'Paid co-op and field placements' ),
			array( 'text' => 'Simulation labs and makerspaces' ),
			array( 'text' => 'Small classes taught by industry pros' ),
			array( 'text' => 'Career services for life' ),
		),
		'buttons'     => array( array( 'link' => mcrp_link( 'Explore programs', $programs_url ), 'style' => 'secondary' ) ),
		'position'    => 'right',
		'badge_value' => '1,200+',
		'badge_label' => 'employer partners',
		'background'  => 'surface',
		'spacing'     => 'lg',
	)
);

echo mcrp_acf_block(
	'testimonials',
	array(
		'eyebrow'    => 'Student stories',
		'heading'    => 'Real students. Real careers.',
		'source'     => 'latest',
		'layout'     => 'slider',
		'limit'      => 6,
		'background' => 'white',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'events',
	array(
		'eyebrow'    => 'Visit us',
		'heading'    => 'Open houses, tours & info sessions',
		'intro'      => 'The best way to choose a college is to see it for yourself - in person or online.',
		'limit'      => 3,
		'view_all'   => mcrp_link( 'All events', $events_url ),
		'background' => 'surface',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'faq-accordion',
	array(
		'eyebrow'    => 'Questions?',
		'heading'    => 'Answers to common questions',
		'intro'      => 'Can\'t find what you\'re looking for? Our admissions team is happy to help.',
		'source'     => 'category',
		'category'   => 'admissions',
		'limit'      => 5,
		'layout'     => 'split',
		'open_first' => 1,
		'schema'     => 1,
		'link'       => mcrp_link( 'Contact admissions', home_url( '/admissions/' ) ),
		'background' => 'white',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'cta-banner',
	array(
		'eyebrow'   => 'Your future starts here',
		'heading'   => 'Apply in 15 minutes. Start this fall.',
		'text'      => 'Rolling admissions, no application essays, and advisors ready to help with every step.',
		'buttons'   => array(
			array( 'link' => mcrp_link( 'Start your application', 'https://apply.example.edu' ), 'style' => 'white' ),
			array( 'link' => mcrp_link( 'Talk to an advisor', home_url( '/admissions/' ) ), 'style' => 'outline-light' ),
		),
		'variant'   => 'sunrise',
		'layout'    => 'inline',
		'contained' => 1,
	)
);
