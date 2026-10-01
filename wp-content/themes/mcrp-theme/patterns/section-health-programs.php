<?php
/**
 * Title: Pre-filtered program list (Health Sciences)
 * Slug: mcrp-theme/section-health-programs
 * Categories: mcrp-sections
 * Description: Example of a locked Program Finder - only Health Sciences programs, no filter bar.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

echo mcrp_acf_block(
	'program-finder',
	array(
		'eyebrow'      => 'Health Sciences',
		'heading'      => 'Start a career in healthcare',
		'mode'         => 'filter',
		'show_filters' => 0,
		'area'         => 'health-sciences',
		'per_page'     => 6,
		'background'   => 'surface',
	)
);
