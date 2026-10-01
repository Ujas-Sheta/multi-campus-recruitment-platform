<?php
/**
 * Program card.
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
$pid         = $post_obj->ID;
$credential = mcrp_term_names( $pid, 'credential' );
$area       = mcrp_term_names( $pid, 'program_area' );
$delivery   = mcrp_term_names( $pid, 'delivery_mode' );
$duration   = mcrp_get( 'duration', $pid );
$campuses   = mcrp_q( 'campuses_for_program', $pid );
?>
<article class="card program-card">
	<a class="card__media-link" href="<?php echo esc_url( get_permalink( $pid ) ); ?>" tabindex="-1" aria-hidden="true">
		<?php echo mcrp_media( $post_obj, 'mcrp-card', 'card__media' ); ?>
	</a>
	<div class="card__body">
		<div class="card__badges">
			<?php if ( $credential ) : ?>
				<span class="badge badge--primary"><?php echo esc_html( $credential ); ?></span>
			<?php endif; ?>
			<?php if ( $area ) : ?>
				<span class="badge"><?php echo esc_html( $area ); ?></span>
			<?php endif; ?>
		</div>
		<h3 class="card__title"><a href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( get_the_title( $pid ) ); ?></a></h3>
		<p class="card__excerpt"><?php echo esc_html( get_the_excerpt( $pid ) ); ?></p>
		<ul class="card__meta">
			<?php if ( $duration ) : ?>
				<li><?php echo mcrp_icon( 'clock' ); ?><?php echo esc_html( $duration ); ?></li>
			<?php endif; ?>
			<?php if ( $delivery ) : ?>
				<li><?php echo mcrp_icon( 'monitor' ); ?><?php echo esc_html( $delivery ); ?></li>
			<?php endif; ?>
			<?php if ( $campuses ) : ?>
				<li><?php echo mcrp_icon( 'map-pin' ); ?><?php echo esc_html( implode( ', ', wp_list_pluck( $campuses, 'post_title' ) ) ); ?></li>
			<?php endif; ?>
		</ul>
		<a class="card__link" href="<?php echo esc_url( get_permalink( $pid ) ); ?>">
			<?php esc_html_e( 'Explore program', 'mcrp' ); ?><?php echo mcrp_icon( 'arrow-right' ); ?>
			<span class="screen-reader-text"><?php echo esc_html( get_the_title( $pid ) ); ?></span>
		</a>
	</div>
</article>
