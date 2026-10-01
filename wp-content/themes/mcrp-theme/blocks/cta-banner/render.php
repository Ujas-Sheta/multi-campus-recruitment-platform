<?php
/**
 * CTA Banner block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$heading = mcrp_get( 'heading' );
if ( ! $heading ) {
	mcrp_block_placeholder( __( 'CTA Banner: add a heading.', 'mcrp' ), $is_preview );
	return;
}
$variant   = mcrp_get( 'variant', null, 'navy' );
$layout    = mcrp_get( 'layout', null, 'inline' );
$contained = (bool) mcrp_get( 'contained', null, true );

echo mcrp_block_open( $block, 'cta-banner', array( 'cta-banner--' . $variant, 'cta-banner--' . $layout, $contained ? 'cta-banner--contained' : 'cta-banner--bleed' ) );
?>
	<div class="container">
		<div class="cta-banner__box">
			<div class="cta-banner__content">
				<?php if ( $eyebrow = mcrp_get( 'eyebrow' ) ) : ?>
					<p class="eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
				<?php endif; ?>
				<h2 class="cta-banner__title"><?php echo esc_html( $heading ); ?></h2>
				<?php if ( $text = mcrp_get( 'text' ) ) : ?>
					<p class="cta-banner__text"><?php echo esc_html( $text ); ?></p>
				<?php endif; ?>
			</div>
			<?php echo mcrp_render_buttons( mcrp_get( 'buttons' ), 'btn-group cta-banner__actions' ); ?>
		</div>
	</div>
</section>
