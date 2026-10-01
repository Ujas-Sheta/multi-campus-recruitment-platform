<?php
/**
 * Inner page hero.
 *
 * @package MCRP_Theme
 *
 * @var array $args title, intro, eyebrow, meta (html), actions (html), variant, media (html).
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'title'   => '',
		'intro'   => '',
		'eyebrow' => '',
		'meta'    => '',
		'actions' => '',
		'media'   => '',
		'variant' => 'navy',
		'style'   => '',
	)
);
?>
<section class="page-hero page-hero--<?php echo esc_attr( $args['variant'] ); ?><?php echo $args['media'] ? ' page-hero--has-media' : ''; ?>"<?php echo $args['style'] ? ' style="' . esc_attr( $args['style'] ) . '"' : ''; ?>>
	<div class="container page-hero__inner">
		<div class="page-hero__content">
			<?php mcrp_breadcrumbs(); ?>
			<?php if ( $args['eyebrow'] ) : ?>
				<p class="eyebrow"><?php echo wp_kses_post( $args['eyebrow'] ); ?></p>
			<?php endif; ?>
			<h1 class="page-hero__title"><?php echo wp_kses_post( $args['title'] ); ?></h1>
			<?php if ( $args['intro'] ) : ?>
				<p class="page-hero__intro"><?php echo wp_kses_post( $args['intro'] ); ?></p>
			<?php endif; ?>
			<?php if ( $args['meta'] ) : ?>
				<div class="page-hero__meta"><?php echo $args['meta']; ?></div>
			<?php endif; ?>
			<?php if ( $args['actions'] ) : ?>
				<div class="page-hero__actions"><?php echo $args['actions']; ?></div>
			<?php endif; ?>
		</div>
		<?php if ( $args['media'] ) : ?>
			<div class="page-hero__media"><?php echo $args['media']; ?></div>
		<?php endif; ?>
	</div>
</section>
