<?php
/**
 * Stats block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$items   = (array) mcrp_get( 'items', null, array() );
$animate = (bool) mcrp_get( 'animate', null, true ) && ! $is_preview;
if ( ! $items ) {
	mcrp_block_placeholder( __( 'Stats: add at least one stat.', 'mcrp' ), $is_preview );
	return;
}

echo mcrp_block_open( $block, 'stats' );
?>
	<div class="container">
		<?php echo mcrp_block_heading( 'center' ); ?>
		<dl class="stats__grid stats__grid--<?php echo (int) min( 4, count( $items ) ); ?>">
			<?php foreach ( $items as $item ) : ?>
				<?php $number = (float) ( $item['number'] ?? 0 ); ?>
				<div class="stats__item">
					<dd class="stats__value">
						<?php echo esc_html( $item['prefix'] ?? '' ); ?><span <?php echo $animate ? 'data-count-to="' . esc_attr( $number ) . '"' : ''; ?>><?php echo esc_html( number_format_i18n( $number, floor( $number ) == $number ? 0 : 1 ) ); ?></span><?php echo esc_html( $item['suffix'] ?? '' ); ?>
					</dd>
					<dt class="stats__label"><?php echo esc_html( $item['label'] ?? '' ); ?></dt>
				</div>
			<?php endforeach; ?>
		</dl>
		<?php if ( $note = mcrp_get( 'footnote' ) ) : ?>
			<p class="stats__footnote"><?php echo esc_html( $note ); ?></p>
		<?php endif; ?>
	</div>
</section>
