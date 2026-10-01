<?php
/**
 * Instructor card.
 *
 * @package MCRP_Theme
 *
 * @var array $args post.
 */

defined( 'ABSPATH' ) || exit;

$post_obj = get_post( $args['post'] ?? null );
if ( ! $post_obj ) {
	return;
}
$pid     = $post_obj->ID;
$campus = mcrp_get( 'campus', $pid );
?>
<article class="card instructor-card">
	<a class="instructor-card__photo" href="<?php echo esc_url( get_permalink( $pid ) ); ?>" tabindex="-1" aria-hidden="true">
		<?php echo mcrp_media( $post_obj, 'mcrp-square', 'card__media card__media--round' ); ?>
	</a>
	<div class="card__body">
		<h3 class="card__title"><a href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( get_the_title( $pid ) ); ?></a></h3>
		<p class="instructor-card__position"><?php echo esc_html( mcrp_get( 'position', $pid, '' ) ); ?></p>
		<?php if ( $creds = mcrp_get( 'credentials', $pid ) ) : ?>
			<p class="instructor-card__creds"><?php echo esc_html( $creds ); ?></p>
		<?php endif; ?>
		<?php if ( $campus ) : ?>
			<p class="instructor-card__campus"><?php echo mcrp_icon( 'map-pin' ); ?><?php echo esc_html( get_the_title( $campus ) ); ?></p>
		<?php endif; ?>
	</div>
</article>
