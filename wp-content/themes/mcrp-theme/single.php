<?php
/**
 * Single post (news / stories).
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	mcrp_page_hero(
		array(
			'title' => get_the_title(),
			'meta'  => '<span>' . esc_html( get_the_date() ) . '</span>',
		)
	);
	?>
	<article class="section">
		<div class="entry-content">
			<?php the_content(); ?>
		</div>
	</article>
	<?php
endwhile;

get_footer();
