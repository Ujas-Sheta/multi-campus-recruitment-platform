<?php
/**
 * Title: Campaign landing page
 * Slug: mcrp-theme/campaign-landing
 * Categories: mcrp-pages
 * Post Types: page
 * Description: Open house campaign page. Use it with the Campaign Landing Page template.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

echo mcrp_acf_block(
	'hero',
	array(
		'layout'       => 'form',
		'variant'      => 'navy',
		'height'       => 'md',
		'eyebrow'      => 'Fall Open House | All campuses',
		'heading'      => 'See yourself here.<br>Join us at Open House.',
		'text'         => 'Tour our labs, meet faculty and current students, and get your admissions and financial-aid questions answered on the spot. Attendees get their application fee waived.',
		'points'       => array(
			array( 'text' => 'Application fee waived' ),
			array( 'text' => 'Live program demos' ),
			array( 'text' => 'Scholarship info' ),
		),
		'form_heading' => 'Save your spot',
		'campaign'     => 'fall-open-house',
	)
);

echo mcrp_acf_block(
	'stats',
	array(
		'items'      => array(
			array( 'number' => 93, 'suffix' => '%', 'label' => 'Graduate employment' ),
			array( 'number' => 12, 'suffix' => '+', 'label' => 'Career-ready programs' ),
			array( 'number' => 4, 'suffix' => '', 'label' => 'Campuses incl. online' ),
			array( 'number' => 18, 'suffix' => ':1', 'label' => 'Student-to-faculty ratio' ),
		),
		'animate'    => 1,
		'background' => 'surface',
		'spacing'    => 'md',
	)
);

echo mcrp_acf_block(
	'program-finder',
	array(
		'eyebrow'      => 'Programs on show',
		'heading'      => 'Explore programs at the Open House',
		'mode'         => 'filter',
		'show_filters' => 0,
		'per_page'     => 6,
		'background'   => 'white',
		'spacing'      => 'lg',
	)
);

echo mcrp_acf_block(
	'admissions-steps',
	array(
		'eyebrow'    => 'Your visit',
		'heading'    => 'What to expect on the day',
		'steps'      => array(
			array( 'title' => 'Check in & welcome', 'text' => 'Grab your personalised agenda and a campus map in the main atrium.' ),
			array( 'title' => 'Program showcases', 'text' => 'Visit program booths, see live demos and talk to faculty.' ),
			array( 'title' => 'Campus tours', 'text' => 'Student ambassadors lead tours of labs, residence and student spaces.' ),
			array( 'title' => 'Apply on the spot', 'text' => 'Advisors help you apply - fee waived for attendees.' ),
		),
		'layout'     => 'cards',
		'background' => 'surface',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'testimonials',
	array(
		'heading'    => 'Why students choose Northbridge',
		'source'     => 'latest',
		'layout'     => 'grid',
		'limit'      => 3,
		'background' => 'white',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'faq-accordion',
	array(
		'heading'    => 'Open House FAQs',
		'source'     => 'category',
		'category'   => '',
		'limit'      => 5,
		'layout'     => 'stacked',
		'schema'     => 1,
		'background' => 'surface',
		'spacing'    => 'lg',
	)
);

echo mcrp_acf_block(
	'inquiry-form',
	array(
		'layout'       => 'split',
		'eyebrow'      => 'Can\'t make it?',
		'side_heading' => 'Get the Open House info pack',
		'side_text'    => 'We\'ll send you program guides, tuition details and a link to book a personal tour at a time that suits you.',
		'points'       => array(
			array( 'text' => 'Program & tuition guide' ),
			array( 'text' => 'Scholarship checklist' ),
			array( 'text' => 'Personal tour booking link' ),
		),
		'heading'      => 'Send me the info pack',
		'button'       => 'Send my info pack',
		'campaign'     => 'fall-open-house-infopack',
		'show_message' => 0,
		'background'   => 'primary',
		'spacing'      => 'lg',
	),
	array( 'anchor' => 'info-pack' )
);
