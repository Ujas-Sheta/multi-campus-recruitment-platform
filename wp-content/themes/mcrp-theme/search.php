<?php
/**
 * Search results, grouped by content type.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

mcrp_page_hero(
	array(
		/* translators: %s: search query */
		'title' => sprintf( __( 'Results for "%s"', 'mcrp' ), esc_html( get_search_query() ) ),
		'intro' => sprintf( /* translators: %d: results */ _n( '%d result', '%d results', (int) $GLOBALS['wp_query']->found_posts, 'mcrp' ), (int) $GLOBALS['wp_query']->found_posts ),
	)
);

$groups = array();
while ( have_posts() ) {
	the_post();
	$groups[ get_post_type() ][] = get_post();
}
$cards = array( 'program' => 'program', 'campus' => 'campus', 'event' => 'event', 'instructor' => 'instructor' );
?>
<div class="section container">
	<?php if ( ! $groups ) : ?>
		<p><?php esc_html_e( 'No results. Try a different keyword, or browse all programs.', 'mcrp' ); ?></p>
		<p><a class="btn btn--primary" href="<?php echo esc_url( get_post_type_archive_link( 'program' ) ?: home_url( '/' ) ); ?>"><?php esc_html_e( 'Browse programs', 'mcrp' ); ?></a></p>
	<?php endif; ?>

	<?php foreach ( $groups as $type => $group_posts ) : ?>
		<section class="search-group">
			<h2><?php echo esc_html( get_post_type_object( $type )->labels->name ); ?></h2>
			<?php mcrp_card_grid( $group_posts, $cards[ $type ] ?? 'post', 'instructor' === $type ? 4 : 3 ); ?>
		</section>
	<?php endforeach; ?>

	<?php mcrp_pagination(); ?>
</div>
<?php
get_footer();
