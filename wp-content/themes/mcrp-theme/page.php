<?php
/**
 * Default page template.
 * If the page starts with a Hero block we don't output the normal page hero.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$blocks     = parse_blocks( get_the_content() );
	$first      = $blocks[0]['blockName'] ?? '';
	$block_page = 'mcrp/hero' === $first;

	if ( ! $block_page ) {
		mcrp_page_hero( array( 'title' => get_the_title(), 'intro' => has_excerpt() ? get_the_excerpt() : '' ) );
	}
	?>
	<div class="entry-content<?php echo $block_page ? ' entry-content--blocks' : ' section'; ?>">
		<?php the_content(); ?>
	</div>
	<?php
endwhile;

get_footer();
