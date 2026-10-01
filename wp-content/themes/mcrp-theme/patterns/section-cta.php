<?php
/**
 * Title: Apply CTA band
 * Slug: mcrp-theme/section-cta
 * Categories: mcrp-sections
 * Description: Coral call-to-action band with apply + tour buttons.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

echo mcrp_acf_block(
	'cta-banner',
	array(
		'heading'   => 'Ready to take the next step?',
		'text'      => 'Apply online in about 15 minutes, or come see us at an upcoming open house.',
		'buttons'   => array(
			array( 'link' => mcrp_link( 'Apply now', 'https://apply.example.edu' ), 'style' => 'white' ),
			array( 'link' => mcrp_link( 'Book a tour', get_post_type_archive_link( 'event' ) ?: home_url( '/events/' ) ), 'style' => 'outline-light' ),
		),
		'variant'   => 'coral',
		'layout'    => 'inline',
		'contained' => 1,
	)
);
