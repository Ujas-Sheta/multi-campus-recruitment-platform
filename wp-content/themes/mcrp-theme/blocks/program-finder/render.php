<?php
/**
 * Program Finder block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$mode = mcrp_get( 'mode', null, 'filter' );

echo mcrp_block_open( $block, 'program-finder-block' );
?>
	<div class="container">
		<div class="section-head-row">
			<?php echo mcrp_block_heading(); ?>
			<?php echo mcrp_button( mcrp_get( 'view_all' ), 'outline', 'arrow-right' ); ?>
		</div>

		<?php
		if ( 'featured' === $mode ) {
			$ids = mcrp_ids( mcrp_get( 'programs' ) );
			if ( ! $ids ) {
				mcrp_block_placeholder( __( 'Program Finder: choose programs to feature.', 'mcrp' ), $is_preview );
			} else {
				mcrp_card_grid( get_posts( array( 'post_type' => 'program', 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => count( $ids ) ) ), 'program', 3 );
			}
		} else {
			get_template_part(
				'template-parts/components/program-finder',
				null,
				array(
					'locked'       => array_filter(
						array(
							'area'       => mcrp_get( 'area' ),
							'credential' => mcrp_get( 'credential' ),
							'delivery'   => mcrp_get( 'delivery' ),
							'campus'     => (int) mcrp_get( 'campus' ),
						)
					),
					'show_filters' => (bool) mcrp_get( 'show_filters', null, true ),
					'per_page'     => (int) mcrp_get( 'per_page', null, 6 ),
					'id'           => 'finder-' . ( $block['id'] ?? 'block' ),
				)
			);
		}
		?>
	</div>
</section>
