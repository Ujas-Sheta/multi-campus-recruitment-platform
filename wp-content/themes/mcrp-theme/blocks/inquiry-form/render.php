<?php
/**
 * Inquiry Form block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$layout = mcrp_get( 'layout', null, 'split' );
$points = (array) mcrp_get( 'points', null, array() );

echo mcrp_block_open( $block, 'inquiry-block', array( 'inquiry-block--' . $layout ) );
?>
	<div class="container inquiry-block__grid">
		<?php if ( 'split' === $layout ) : ?>
			<div class="inquiry-block__side">
				<?php echo mcrp_section_heading( (string) mcrp_get( 'eyebrow', null, '' ), (string) mcrp_get( 'side_heading', null, '' ), (string) mcrp_get( 'side_text', null, '' ) ); ?>
				<?php if ( $points ) : ?>
					<ul class="checklist">
						<?php foreach ( $points as $point ) : ?>
							<li><?php echo mcrp_icon( 'check' ); ?><?php echo esc_html( $point['text'] ?? '' ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
		<?php endif; ?>
		<div class="inquiry-block__form">
			<?php
			get_template_part(
				'template-parts/components/inquiry-form',
				null,
				array(
					'heading'      => mcrp_get( 'heading', null, __( 'Request information', 'mcrp' ) ),
					'intro'        => mcrp_get( 'intro', null, '' ),
					'button'       => mcrp_get( 'button', null, __( 'Get program info', 'mcrp' ) ),
					'campaign'     => mcrp_get( 'campaign', null, '' ),
					'program'      => (int) mcrp_get( 'program', null, 0 ),
					'lock_program' => (bool) mcrp_get( 'lock_program' ),
					'campus'       => (int) mcrp_get( 'campus', null, 0 ),
					'show_message' => (bool) mcrp_get( 'show_message' ),
					'success'      => mcrp_get( 'success', null, '' ),
					'redirect'     => mcrp_get( 'redirect', null, '' ),
					'id'           => ! empty( $block['anchor'] ) ? 'form-' . $block['anchor'] : 'inquiry',
				)
			);
			?>
		</div>
	</div>
</section>
