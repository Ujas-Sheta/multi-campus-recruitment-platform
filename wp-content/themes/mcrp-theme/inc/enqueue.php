<?php
/**
 * Front-end and editor assets.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Asset version based on file modification time (cache-busting).
 */
function mcrp_asset_version( string $rel ): string {
	$path = MCRP_THEME_DIR . '/' . $rel;
	return file_exists( $path ) ? (string) filemtime( $path ) : MCRP_THEME_VERSION;
}

function mcrp_fonts_url(): string {
	return 'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap';
}

add_action(
	'wp_enqueue_scripts',
	static function () {
		wp_enqueue_style( 'mcrp-fonts', mcrp_fonts_url(), array(), null );
		wp_enqueue_style( 'mcrp-main', MCRP_THEME_URI . '/assets/dist/main.css', array(), mcrp_asset_version( 'assets/dist/main.css' ) );

		wp_enqueue_script( 'mcrp-main', MCRP_THEME_URI . '/assets/dist/main.js', array(), mcrp_asset_version( 'assets/dist/main.js' ), array( 'strategy' => 'defer', 'in_footer' => true ) );
		wp_localize_script(
			'mcrp-main',
			'mcrpData',
			array(
				'programsEndpoint' => esc_url_raw( rest_url( 'mcrp/v1/programs' ) ),
				'inquiryEndpoint'  => esc_url_raw( rest_url( 'mcrp/v1/inquiries' ) ),
				'i18n'             => array(
					/* translators: %d: number of programs */
					'results'   => __( '%d programs found', 'mcrp' ),
					'oneResult' => __( '1 program found', 'mcrp' ),
					'noResults' => __( 'No programs match your filters. Try removing a filter.', 'mcrp' ),
					'error'     => __( 'Something went wrong. Please try again.', 'mcrp' ),
					'sending'   => __( 'Sending...', 'mcrp' ),
				),
			)
		);
	}
);

add_action(
	'wp_head',
	static function () {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	},
	1
);

/**
 * Editor: fonts + block editor helpers.
 */
add_action(
	'enqueue_block_editor_assets',
	static function () {
		wp_enqueue_style( 'mcrp-fonts', mcrp_fonts_url(), array(), null );
	}
);
add_action(
	'after_setup_theme',
	static function () {
		add_editor_style( mcrp_fonts_url() );
	},
	20
);
