<?php
/**
 * Campus Cards block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$ids      = mcrp_ids( mcrp_get( 'campuses' ) );
$campuses = get_posts(
	array(
		'post_type'      => 'campus',
		'posts_per_page' => $ids ? count( $ids ) : -1,
		'post__in'       => $ids,
		'orderby'        => $ids ? 'post__in' : 'menu_order title',
		'order'          => 'ASC',
	)
);
if ( ! $campuses ) {
	mcrp_block_placeholder( __( 'Campus Cards: no campuses published yet.', 'mcrp' ), $is_preview );
	return;
}

echo mcrp_block_open( $block, 'campus-cards' );
?>
	<div class="container">
		<?php echo mcrp_block_heading(); ?>
		<?php mcrp_card_grid( $campuses, 'campus', (int) mcrp_get( 'columns', null, 4 ) ); ?>
	</div>
</section>
