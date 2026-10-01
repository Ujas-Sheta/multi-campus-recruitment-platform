<?php
/**
 * Fallback template (blog index, generic archives).
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

mcrp_page_hero(
	array(
		'title' => is_home() && get_option( 'page_for_posts' ) ? get_the_title( get_option( 'page_for_posts' ) ) : ( is_archive() ? wp_strip_all_tags( get_the_archive_title() ) : __( 'News & Stories', 'mcrp' ) ),
		'intro' => is_archive() ? wp_strip_all_tags( get_the_archive_description() ) : '',
	)
);
?>
<div class="section container">
	<?php if ( have_posts() ) : ?>
		<div class="card-grid card-grid--3">
			<?php
			while ( have_posts() ) :
				the_post();
				get_template_part( 'template-parts/cards/post' );
			endwhile;
			?>
		</div>
		<?php mcrp_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'mcrp' ); ?></p>
	<?php endif; ?>
</div>
<?php
get_footer();
