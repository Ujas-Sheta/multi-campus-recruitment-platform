<?php
/**
 * Event card (date tile layout).
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
$virtual = (bool) mcrp_get( 'is_virtual', $pid );
$campus  = mcrp_get( 'campus', $pid );
$type    = mcrp_term_names( $pid, 'event_type' );
$reg     = mcrp_get( 'registration_url', $pid );
?>
<article class="card event-card<?php echo mcrp_get( 'featured', $pid ) ? ' event-card--featured' : ''; ?>">
	<div class="event-card__date" aria-hidden="true">
		<span class="event-card__month"><?php echo esc_html( mcrp_event_date( $pid, 'month' ) ); ?></span>
		<span class="event-card__day"><?php echo esc_html( mcrp_event_date( $pid, 'day' ) ); ?></span>
	</div>
	<div class="card__body">
		<?php if ( $type ) : ?>
			<span class="badge<?php echo $virtual ? ' badge--secondary' : ''; ?>"><?php echo esc_html( $type ); ?></span>
		<?php endif; ?>
		<h3 class="card__title"><a href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php echo esc_html( get_the_title( $pid ) ); ?></a></h3>
		<ul class="card__meta">
			<li><?php echo mcrp_icon( 'calendar' ); ?><time datetime="<?php echo esc_attr( mcrp_get( 'start_datetime', $pid ) ); ?>"><?php echo esc_html( mcrp_event_date( $pid, 'date' ) ); ?></time></li>
			<li><?php echo mcrp_icon( 'clock' ); ?><?php echo esc_html( mcrp_event_date( $pid, 'time' ) ); ?></li>
			<li><?php echo mcrp_icon( $virtual ? 'monitor' : 'map-pin' ); ?><?php echo esc_html( $virtual ? __( 'Online', 'mcrp' ) : ( $campus ? get_the_title( $campus ) : '' ) ); ?></li>
		</ul>
		<div class="event-card__actions">
			<?php if ( $reg ) : ?>
				<a class="btn btn--primary btn--sm" href="<?php echo esc_url( $reg ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Register', 'mcrp' ); ?><span class="screen-reader-text"> - <?php echo esc_html( get_the_title( $pid ) ); ?></span></a>
			<?php endif; ?>
			<a class="card__link" href="<?php echo esc_url( get_permalink( $pid ) ); ?>"><?php esc_html_e( 'Details', 'mcrp' ); ?><?php echo mcrp_icon( 'arrow-right' ); ?><span class="screen-reader-text"> - <?php echo esc_html( get_the_title( $pid ) ); ?></span></a>
		</div>
	</div>
</article>
