<?php
/**
 * WP-CLI commands.
 *
 * @package MCRP_Core
 */

namespace MCRP;

defined( 'ABSPATH' ) || exit;

/**
 * Manage the Multi-Campus Recruitment Platform.
 */
class CLI {

	/**
	 * Import demo content (campuses, programs, instructors, events, testimonials, FAQs, pages, menus).
	 *
	 * ## OPTIONS
	 *
	 * [--reset]
	 * : Remove previously imported demo content first.
	 *
	 * ## EXAMPLES
	 *
	 *     wp mcrp seed
	 *     wp mcrp seed --reset
	 *
	 * @param array $args       Positional args.
	 * @param array $assoc_args Flags.
	 */
	public function seed( $args, $assoc_args ): void {
		if ( ! empty( $assoc_args['reset'] ) ) {
			Seeder::reset();
		}
		Seeder::run();
		foreach ( Seeder::$log as $line ) {
			\WP_CLI::log( $line );
		}
		\WP_CLI::success( 'Demo content imported.' );
	}

	/**
	 * Remove all demo content.
	 */
	public function unseed(): void {
		$n = Seeder::reset();
		\WP_CLI::success( "Removed {$n} demo items." );
	}

	/**
	 * Show content counts.
	 *
	 * @subcommand stats
	 */
	public function stats(): void {
		$rows = array();
		foreach ( array_keys( Post_Types::definitions() ) as $type ) {
			$rows[] = array( 'post_type' => $type, 'published' => (int) wp_count_posts( $type )->publish );
		}
		\WP_CLI\Utils\format_items( 'table', $rows, array( 'post_type', 'published' ) );
	}
}

\WP_CLI::add_command( 'mcrp', CLI::class );
