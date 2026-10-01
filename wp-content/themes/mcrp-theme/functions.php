<?php
/**
 * MCRP Campus theme bootstrap.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

define( 'MCRP_THEME_VERSION', '1.0.0' );
define( 'MCRP_THEME_DIR', get_template_directory() );
define( 'MCRP_THEME_URI', get_template_directory_uri() );

require_once MCRP_THEME_DIR . '/inc/compat.php';
require_once MCRP_THEME_DIR . '/inc/setup.php';
require_once MCRP_THEME_DIR . '/inc/enqueue.php';
require_once MCRP_THEME_DIR . '/inc/template-tags.php';
require_once MCRP_THEME_DIR . '/inc/blocks.php';
require_once MCRP_THEME_DIR . '/inc/patterns.php';
require_once MCRP_THEME_DIR . '/inc/schema.php';
