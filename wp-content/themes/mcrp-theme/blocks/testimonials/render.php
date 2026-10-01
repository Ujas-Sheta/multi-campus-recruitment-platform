<?php
/**
 * Testimonials block (slider or grid).
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$source = mcrp_get( 'source', null, 'latest' );
$limit  = (int) mcrp_get( 'limit', null, 6 );
$query  = array( 'limit' => $limit );
if ( 'manual' === $source ) {
	$query['ids'] = mcrp_get( 'items' );
} elseif ( 'program' === $source ) {
	$query['program'] = mcrp_get( 'program' );
} elseif ( 'campus' === $source ) {
	$query['campus'] = mcrp_get( 'campus' );
}
$items  = mcrp_q( 'testimonials', $query );
$layout = mcrp_get( 'layout', null, 'slider' );

if ( ! $items ) {
	mcrp_block_placeholder( __( 'Testimonials: no testimonials match these settings.', 'mcrp' ), $is_preview );
	return;
}

echo mcrp_block_open( $block, 'testimonials', array( 'testimonials--' . $layout ) );
?>
	<div class="container">
		<div class="section-head-row">
			<?php echo mcrp_block_heading(); ?>
			<?php if ( 'slider' === $layout && count( $items ) > 1 ) : ?>
				<div class="slider-controls">
					<button type="button" class="slider-btn" data-slider-prev aria-label="<?php esc_attr_e( 'Previous testimonial', 'mcrp' ); ?>"><?php echo mcrp_icon( 'chevron-left' ); ?></button>
					<button type="button" class="slider-btn" data-slider-next aria-label="<?php esc_attr_e( 'Next testimonial', 'mcrp' ); ?>"><?php echo mcrp_icon( 'chevron-right' ); ?></button>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( 'slider' === $layout ) : ?>
			<div class="slider" data-slider tabindex="0" aria-roledescription="carousel" aria-label="<?php esc_attr_e( 'Student testimonials', 'mcrp' ); ?>">
				<?php foreach ( $items as $i => $item ) : ?>
					<div class="slider__slide" role="group" aria-roledescription="slide" aria-label="<?php echo esc_attr( sprintf( '%d / %d', $i + 1, count( $items ) ) ); ?>">
						<?php get_template_part( 'template-parts/cards/testimonial', null, array( 'post' => $item ) ); ?>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<?php mcrp_card_grid( $items, 'testimonial', 3 ); ?>
		<?php endif; ?>
	</div>
</section>
