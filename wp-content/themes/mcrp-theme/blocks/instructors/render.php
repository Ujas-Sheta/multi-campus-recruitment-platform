<?php
/**
 * Faculty Grid block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$source = mcrp_get( 'source', null, 'manual' );
$limit  = (int) mcrp_get( 'limit', null, 4 );

switch ( $source ) {
	case 'program':
		$people = array_slice( mcrp_q( 'instructors_for_program', (int) mcrp_get( 'program' ) ), 0, $limit );
		break;
	case 'campus':
		$people = mcrp_q( 'instructors_at_campus', (int) mcrp_get( 'campus' ), $limit );
		break;
	case 'area':
		$people = get_posts( array( 'post_type' => 'instructor', 'posts_per_page' => $limit, 'tax_query' => array( array( 'taxonomy' => 'program_area', 'field' => 'slug', 'terms' => (string) mcrp_get( 'area' ) ) ) ) );
		break;
	default:
		$ids    = mcrp_ids( mcrp_get( 'items' ) );
		$people = $ids ? get_posts( array( 'post_type' => 'instructor', 'post__in' => $ids, 'orderby' => 'post__in', 'posts_per_page' => count( $ids ) ) ) : get_posts( array( 'post_type' => 'instructor', 'posts_per_page' => $limit ) );
}

if ( ! $people ) {
	mcrp_block_placeholder( __( 'Faculty Grid: no instructors found.', 'mcrp' ), $is_preview );
	return;
}

echo mcrp_block_open( $block, 'instructors-block' );
?>
	<div class="container">
		<div class="section-head-row">
			<?php echo mcrp_block_heading(); ?>
			<?php echo mcrp_button( mcrp_get( 'view_all' ), 'outline', 'arrow-right' ); ?>
		</div>
		<?php mcrp_card_grid( $people, 'instructor', 4 ); ?>
	</div>
</section>
