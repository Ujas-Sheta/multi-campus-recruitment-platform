<?php
/**
 * Admissions Steps block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$steps  = (array) mcrp_get( 'steps', null, array() );
$layout = mcrp_get( 'layout', null, 'cards' );
if ( ! $steps ) {
	mcrp_block_placeholder( __( 'Admissions Steps: add at least one step.', 'mcrp' ), $is_preview );
	return;
}

echo mcrp_block_open( $block, 'steps', array( 'steps--' . $layout ) );
?>
	<div class="container">
		<?php echo mcrp_block_heading( 'cards' === $layout ? 'center' : 'left' ); ?>
		<ol class="steps__list steps__list--<?php echo (int) min( 4, count( $steps ) ); ?>">
			<?php foreach ( $steps as $i => $step ) : ?>
				<li class="steps__item">
					<span class="steps__number" aria-hidden="true"><?php echo (int) $i + 1; ?></span>
					<div class="steps__body">
						<h3 class="steps__title"><?php echo esc_html( $step['title'] ?? '' ); ?></h3>
						<?php if ( ! empty( $step['text'] ) ) : ?>
							<p><?php echo esc_html( $step['text'] ); ?></p>
						<?php endif; ?>
						<?php if ( ! empty( $step['link']['url'] ) ) : ?>
							<a class="card__link" href="<?php echo esc_url( $step['link']['url'] ); ?>"<?php echo ! empty( $step['link']['target'] ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $step['link']['title'] ?: __( 'Learn more', 'mcrp' ) ); ?><?php echo mcrp_icon( 'arrow-right' ); ?></a>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>
		<?php echo mcrp_render_buttons( mcrp_get( 'buttons' ), 'btn-group btn-group--center' ); ?>
	</div>
</section>
