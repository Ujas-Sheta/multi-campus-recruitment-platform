<?php
/**
 * Faculty directory.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

mcrp_page_hero(
	array(
		'title' => __( 'Faculty directory', 'mcrp' ),
		'intro' => __( 'Learn from instructors who bring years of real industry experience into every class.', 'mcrp' ),
	)
);

$campuses = get_posts( array( 'post_type' => 'campus', 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
$current  = absint( $_GET['campus'] ?? 0 );
?>
<section class="section section--tight-top">
	<div class="container">
		<?php if ( $campuses ) : ?>
			<nav class="filter-pills" aria-label="<?php esc_attr_e( 'Filter by campus', 'mcrp' ); ?>">
				<a href="<?php echo esc_url( get_post_type_archive_link( 'instructor' ) ); ?>" <?php echo $current ? '' : 'aria-current="true"'; ?>><?php esc_html_e( 'All campuses', 'mcrp' ); ?></a>
				<?php foreach ( $campuses as $campus ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'campus', $campus->ID, get_post_type_archive_link( 'instructor' ) ) ); ?>" <?php echo $current === $campus->ID ? 'aria-current="true"' : ''; ?>><?php echo esc_html( $campus->post_title ); ?></a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>
			<?php mcrp_card_grid( $GLOBALS['wp_query']->posts, 'instructor', 4 ); ?>
			<?php mcrp_pagination(); ?>
		<?php else : ?>
			<p><?php esc_html_e( 'No faculty found.', 'mcrp' ); ?></p>
		<?php endif; ?>
	</div>
</section>
<?php
get_footer();
