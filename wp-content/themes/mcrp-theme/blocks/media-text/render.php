<?php
/**
 * Media & Text block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$image     = mcrp_get( 'image' );
$video     = mcrp_get( 'video' );
$position  = mcrp_get( 'position', null, 'right' );
$checklist = (array) mcrp_get( 'checklist', null, array() );

echo mcrp_block_open( $block, 'media-text', array( 'media-text--media-' . $position ) );
?>
	<div class="container media-text__grid">
		<div class="media-text__content">
			<?php echo mcrp_section_heading( (string) mcrp_get( 'eyebrow', null, '' ), (string) mcrp_get( 'heading', null, '' ) ); ?>
			<div class="media-text__body"><?php echo wp_kses_post( (string) mcrp_get( 'content', null, '' ) ); ?></div>
			<?php if ( $checklist ) : ?>
				<ul class="checklist">
					<?php foreach ( $checklist as $row ) : ?>
						<li><?php echo mcrp_icon( 'check' ); ?><?php echo esc_html( $row['text'] ?? '' ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<?php echo mcrp_render_buttons( mcrp_get( 'buttons' ) ); ?>
		</div>
		<div class="media-text__media">
			<?php if ( $video ) : ?>
				<div class="media-text__video"><?php echo $video; ?></div>
			<?php elseif ( $image ) : ?>
				<?php echo wp_get_attachment_image( $image, 'large', false, array( 'class' => 'media-text__image', 'loading' => 'lazy' ) ); ?>
			<?php else : ?>
				<div class="media-text__image media--placeholder media--teal" aria-hidden="true"><?php echo mcrp_icon( 'users', 'media__icon' ); ?></div>
			<?php endif; ?>
			<?php if ( $badge = mcrp_get( 'badge_value' ) ) : ?>
				<div class="media-text__badge"><strong><?php echo esc_html( $badge ); ?></strong><span><?php echo esc_html( mcrp_get( 'badge_label', null, '' ) ); ?></span></div>
			<?php endif; ?>
		</div>
	</div>
</section>
