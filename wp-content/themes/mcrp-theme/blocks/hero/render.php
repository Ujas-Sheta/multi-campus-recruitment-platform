<?php
/**
 * Hero block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$layout  = mcrp_get( 'layout', null, 'left' );
$variant = mcrp_get( 'variant', null, 'navy' );
$image   = mcrp_get( 'image' );
$overlay = (int) mcrp_get( 'overlay', null, 60 );
$height  = mcrp_get( 'height', null, 'md' );
$points  = (array) mcrp_get( 'points', null, array() );
$heading = mcrp_get( 'heading', null, '' );

if ( ! $heading ) {
	mcrp_block_placeholder( __( 'Hero: add a heading in the block settings.', 'mcrp' ), $is_preview );
	return;
}

$classes = array( 'hero--' . $layout, 'hero--' . $variant, 'hero--h-' . $height, $image ? 'hero--has-image' : '' );
echo mcrp_block_open( $block, 'hero', $classes );
?>
	<?php if ( $image ) : ?>
		<div class="hero__bg" aria-hidden="true">
			<?php echo wp_get_attachment_image( $image, 'mcrp-hero', false, array( 'loading' => 'eager', 'fetchpriority' => 'high', 'alt' => '' ) ); ?>
			<span class="hero__overlay" style="opacity:<?php echo esc_attr( $overlay / 100 ); ?>"></span>
		</div>
	<?php else : ?>
		<div class="hero__shapes" aria-hidden="true"><span></span><span></span><span></span></div>
	<?php endif; ?>

	<div class="container hero__inner">
		<div class="hero__content">
			<?php if ( $eyebrow = mcrp_get( 'eyebrow' ) ) : ?>
				<p class="eyebrow hero__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
			<?php endif; ?>
			<h1 class="hero__title"><?php echo wp_kses( $heading, array( 'br' => array(), 'em' => array(), 'strong' => array() ) ); ?></h1>
			<?php if ( $text = mcrp_get( 'text' ) ) : ?>
				<p class="hero__text"><?php echo esc_html( $text ); ?></p>
			<?php endif; ?>
			<?php echo mcrp_render_buttons( mcrp_get( 'buttons' ), 'btn-group hero__actions' ); ?>
			<?php if ( $points ) : ?>
				<ul class="hero__points">
					<?php foreach ( $points as $point ) : ?>
						<li><?php echo mcrp_icon( 'check' ); ?><?php echo esc_html( $point['text'] ?? '' ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<?php if ( 'form' === $layout ) : ?>
			<div class="hero__form">
				<?php
				get_template_part(
					'template-parts/components/inquiry-form',
					null,
					array(
						'heading'  => mcrp_get( 'form_heading', null, __( 'Get program info', 'mcrp' ) ),
						'campaign' => mcrp_get( 'campaign', null, '' ),
						'program'  => (int) mcrp_get( 'program', null, 0 ),
						'id'       => 'hero-' . ( $block['id'] ?? 'form' ),
					)
				);
				?>
			</div>
		<?php endif; ?>
	</div>
</section>
