<?php
/**
 * Testimonial card.
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
$pid      = $post_obj->ID;
$program = mcrp_get( 'program', $pid );
$year    = mcrp_get( 'grad_year', $pid );
?>
<figure class="card testimonial-card">
	<?php echo mcrp_icon( 'quote', 'testimonial-card__icon' ); ?>
	<blockquote class="testimonial-card__quote"><p><?php echo esc_html( mcrp_get( 'quote', $pid, '' ) ); ?></p></blockquote>
	<figcaption class="testimonial-card__author">
		<?php echo mcrp_media( $post_obj, 'thumbnail', 'testimonial-card__avatar' ); ?>
		<span>
			<strong><?php echo esc_html( get_the_title( $pid ) ); ?></strong>
			<?php if ( $role = mcrp_get( 'role', $pid ) ) : ?>
				<span><?php echo esc_html( $role ); ?></span>
			<?php endif; ?>
			<?php if ( $program ) : ?>
				<a href="<?php echo esc_url( get_permalink( $program ) ); ?>"><?php echo esc_html( get_the_title( $program ) . ( $year ? " '" . substr( (string) $year, -2 ) : '' ) ); ?></a>
			<?php endif; ?>
		</span>
	</figcaption>
</figure>
