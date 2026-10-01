<?php
/**
 * Upcoming events (open houses, info sessions, tours, webinars).
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

$term = is_tax( 'event_type' ) ? get_queried_object() : null;
mcrp_page_hero(
	array(
		'title' => $term ? $term->name : __( 'Events & open houses', 'mcrp' ),
		'intro' => __( 'Tour a campus, meet faculty and get your admissions questions answered - in person or online.', 'mcrp' ),
	)
);
$types = get_terms( array( 'taxonomy' => 'event_type', 'hide_empty' => true ) );
?>
<section class="section section--tight-top">
	<div class="container">
		<?php if ( $types && ! is_wp_error( $types ) ) : ?>
			<nav class="filter-pills" aria-label="<?php esc_attr_e( 'Filter by event type', 'mcrp' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'event' ) ); ?>" <?php echo $term ? '' : 'aria-current="true"'; ?>><?php esc_html_e( 'All events', 'mcrp' ); ?></a>
				<?php foreach ( $types as $type ) : ?>
					<a href="<?php echo esc_url( get_term_link( $type ) ); ?>" <?php echo ( $term && $term->term_id === $type->term_id ) ? 'aria-current="true"' : ''; ?>><?php echo esc_html( $type->name ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<?php mcrp_card_grid( $GLOBALS['wp_query']->posts, 'event', 3 ); ?>
			<?php mcrp_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No upcoming events right now - check back soon or contact admissions to book a personal tour.', 'mcrp' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
