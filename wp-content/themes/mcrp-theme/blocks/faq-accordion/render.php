<?php
/**
 * FAQ Accordion block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$source = mcrp_get( 'source', null, 'category' );
$faqs   = 'manual' === $source
	? mcrp_q( 'faqs', array( 'ids' => mcrp_get( 'items' ) ) )
	: mcrp_q( 'faqs', array( 'category' => mcrp_get( 'category' ), 'limit' => (int) mcrp_get( 'limit', null, 6 ) ) );
$layout = mcrp_get( 'layout', null, 'split' );

if ( ! $faqs ) {
	mcrp_block_placeholder( __( 'FAQ Accordion: no FAQs found.', 'mcrp' ), $is_preview );
	return;
}

echo mcrp_block_open( $block, 'faq-block', array( 'faq-block--' . $layout ) );
?>
	<div class="container faq-block__inner">
		<div class="faq-block__head">
			<?php echo mcrp_block_heading(); ?>
			<?php echo mcrp_button( mcrp_get( 'link' ), 'outline', 'arrow-right' ); ?>
		</div>
		<div class="faq-block__list">
			<?php
			get_template_part(
				'template-parts/components/faq-list',
				null,
				array(
					'posts'      => $faqs,
					'schema'     => (bool) mcrp_get( 'schema', null, true ) && ! $is_preview,
					'open_first' => (bool) mcrp_get( 'open_first' ),
					'id'         => 'faq-' . ( $block['id'] ?? 'b' ),
				)
			);
			?>
		</div>
	</div>
</section>
