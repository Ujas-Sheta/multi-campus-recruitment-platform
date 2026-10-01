<?php
/**
 * Campus card.
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
$pid       = $post_obj->ID;
$type     = mcrp_get( 'campus_type', $pid );
$programs = count( mcrp_q( 'programs_at_campus', $pid ) );
$address  = trim( mcrp_get( 'address', $pid, '' ) . ( mcrp_get( 'city', $pid ) ? ', ' . mcrp_get( 'city', $pid ) : '' ), ', ' );
$color    = mcrp_get( 'accent_color', $pid, '#12355b' );
?>
<article class="card campus-card" style="--campus:<?php echo esc_attr( $color ); ?>">
	<a class="card__media-link" href="<?php echo esc_url( get_permalink( $pid ) ); ?>" tabindex="-1" aria-hidden="true">
		<?php echo mcrp_media( $post_obj, 'mcrp-card', 'card__media' ); ?>
	</a>
	<div class="card__body">
		<?php if ( 'online' === $type ) : ?>
			<span class="badge badge--secondary"><?php esc_html_e( 'Online', 'mcrp' ); ?></span>
		<?php elseif ( 'main' === $type ) : ?>
			<span class="badge badge--primary"><?php esc_html_e( 'Main campus', 'mcrp' ); ?></span>
		<?php endif; ?>
		<h3 class="card__title"><a href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( get_the_title( $pid ) ); ?></a></h3>
		<p class="card__excerpt"><?php echo esc_html( get_the_excerpt( $pid ) ); ?></p>
		<ul class="card__meta">
			<?php if ( $address && 'online' !== $type ) : ?>
				<li><?php echo mcrp_icon( 'map-pin' ); ?><?php echo esc_html( $address ); ?></li>
			<?php endif; ?>
			<?php if ( $programs ) : ?>
				<li><?php echo mcrp_icon( 'book' ); ?><?php echo esc_html( sprintf( /* translators: %d: count */ _n( '%d program', '%d programs', $programs, 'mcrp' ), $programs ) ); ?></li>
			<?php endif; ?>
		</ul>
		<a class="card__link" href="<?php echo esc_url( get_permalink( $pid ) ); ?>">
			<?php esc_html_e( 'Explore campus', 'mcrp' ); ?><?php echo mcrp_icon( 'arrow-right' ); ?>
			<span class="screen-reader-text"><?php echo esc_html( get_the_title( $pid ) ); ?></span>
		</a>
	</div>
</article>
