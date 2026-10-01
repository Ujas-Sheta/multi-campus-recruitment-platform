<?php
/**
 * Campus directory.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

mcrp_page_hero(
	array(
		'title' => __( 'Our campuses', 'mcrp' ),
		'intro' => __( 'Three vibrant campuses and a fully online option - choose the learning environment that fits your life.', 'mcrp' ),
	)
);
?>
<section class="section section--tight-top">
	<div class="container">
		<?php
		$campuses = $GLOBALS['wp_query']->posts;
		mcrp_card_grid( $campuses, 'campus', count( $campuses ) % 3 === 0 ? 3 : 2 );
		?>
	</div>
</section>

<section class="section bg-surface">
	<div class="container">
		<?php echo mcrp_section_heading( __( 'Visit us', 'mcrp' ), __( 'Upcoming tours & open houses', 'mcrp' ) ); ?>
		<?php mcrp_card_grid( mcrp_q( 'upcoming_events', array( 'limit' => 3 ) ), 'event', 3 ); ?>
	</div>
</section>
<?php
get_footer();
