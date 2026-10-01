<?php
/**
 * Plugin Name:       MCRP Core - Multi-Campus Recruitment Platform
 * Description:       Content model for the multi-campus college recruitment platform: programs, campuses, instructors, events, testimonials, FAQs, ACF Pro field groups, inquiry/lead capture, program search REST API and demo content seeder.
 * Version:           1.0.0
 * Requires at least: 6.4
 * Requires PHP:      8.1
 * Author:            Ujas
 * License:           GPL-2.0-or-later
 * Text Domain:       mcrp
 *
 * @package MCRP_Core
 */

defined( 'ABSPATH' ) || exit;

define( 'MCRP_CORE_VERSION', '1.0.0' );
define( 'MCRP_CORE_FILE', __FILE__ );
define( 'MCRP_CORE_DIR', plugin_dir_path( __FILE__ ) );
define( 'MCRP_CORE_URL', plugin_dir_url( __FILE__ ) );

require_once MCRP_CORE_DIR . 'includes/helpers.php';
require_once MCRP_CORE_DIR . 'includes/class-post-types.php';
require_once MCRP_CORE_DIR . 'includes/class-taxonomies.php';
require_once MCRP_CORE_DIR . 'includes/class-acf-fields.php';
require_once MCRP_CORE_DIR . 'includes/class-queries.php';
require_once MCRP_CORE_DIR . 'includes/class-leads.php';
require_once MCRP_CORE_DIR . 'includes/class-rest.php';
require_once MCRP_CORE_DIR . 'includes/class-events.php';
require_once MCRP_CORE_DIR . 'includes/class-admin.php';
require_once MCRP_CORE_DIR . 'includes/class-seeder.php';

if ( defined( 'WP_CLI' ) && WP_CLI ) {
	require_once MCRP_CORE_DIR . 'includes/class-cli.php';
}

add_action(
	'plugins_loaded',
	static function () {
		load_plugin_textdomain( 'mcrp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

		MCRP\Post_Types::init();
		MCRP\Taxonomies::init();
		MCRP\ACF_Fields::init();
		MCRP\Queries::init();
		MCRP\Leads::init();
		MCRP\REST::init();
		MCRP\Events::init();
		MCRP\Admin::init();
		MCRP\Seeder::init();
	}
);

register_activation_hook(
	__FILE__,
	static function () {
		MCRP\Post_Types::register();
		MCRP\Taxonomies::register();
		flush_rewrite_rules();
	}
);

register_deactivation_hook( __FILE__, 'flush_rewrite_rules' );
