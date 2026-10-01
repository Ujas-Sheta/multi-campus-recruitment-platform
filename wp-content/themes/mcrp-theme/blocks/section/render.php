<?php
/**
 * Section block - a styled wrapper around inner blocks.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$template = array(
	array( 'core/paragraph', array( 'placeholder' => __( 'Add any blocks here - paragraphs, columns, images, buttons...', 'mcrp' ) ) ),
);

echo mcrp_block_open( $block, 'section-block', array( 'section-block--' . mcrp_get( 'width', null, 'wide' ) ) );
?>
	<div class="container">
		<?php echo mcrp_block_heading( (string) mcrp_get( 'align_heading', null, 'left' ) ); ?>
		<div class="section-block__content entry-content entry-content--flush">
			<InnerBlocks template="<?php echo esc_attr( wp_json_encode( $template ) ); ?>" />
		</div>
	</div>
</section>
