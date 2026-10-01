<?php
/**
 * Single event.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$pid       = get_the_ID();
	$virtual  = (bool) mcrp_get( 'is_virtual', $pid );
	$campus   = mcrp_get( 'campus', $pid );
	$reg      = mcrp_get( 'registration_url', $pid );
	$programs = get_posts( array( 'post_type' => 'program', 'post__in' => mcrp_ids( mcrp_get( 'programs', $pid ) ) ?: array( 0 ), 'orderby' => 'title', 'order' => 'ASC', 'posts_per_page' => -1 ) );
	$ics      = class_exists( '\MCRP\Events' ) ? \MCRP\Events::ics_url( $pid ) : '';
	$past     = (string) mcrp_get( 'start_datetime', $pid ) < current_time( 'mysql' );

	mcrp_page_hero(
		array(
			'title'   => get_the_title(),
			'intro'   => get_the_excerpt(),
			'eyebrow' => mcrp_term_names( $pid, 'event_type' ),
			'meta'    => '<span class="hero-meta">' . mcrp_icon( 'calendar' ) . esc_html( mcrp_event_date( $pid ) ) . '</span><span class="hero-meta">' . mcrp_icon( $virtual ? 'monitor' : 'map-pin' ) . esc_html( $virtual ? __( 'Online event', 'mcrp' ) : ( $campus ? get_the_title( $campus ) : '' ) ) . '</span>',
			'actions' => ( ! $past && $reg ? mcrp_button( array( 'title' => __( 'Register now', 'mcrp' ), 'url' => $reg, 'target' => '_blank' ), 'primary', 'arrow-right' ) : '' )
				. ( ! $past && $ics ? mcrp_button( array( 'title' => __( 'Add to calendar', 'mcrp' ), 'url' => $ics ), 'white', 'download' ) : '' ),
		)
	);
	?>
	<div class="container layout-sidebar section">
		<div class="layout-sidebar__main">
			<?php if ( $past ) : ?>
				<p class="notice"><?php esc_html_e( 'This event has already taken place.', 'mcrp' ); ?> <a href="<?php echo esc_url( get_post_type_archive_link( 'event' ) ); ?>"><?php esc_html_e( 'See upcoming events', 'mcrp' ); ?></a></p>
			<?php endif; ?>
			<div class="entry-content entry-content--flush"><?php the_content(); ?></div>

			<?php if ( $programs ) : ?>
				<h2><?php esc_html_e( 'Featured programs', 'mcrp' ); ?></h2>
				<?php mcrp_card_grid( $programs, 'program', 2 ); ?>
			<?php endif; ?>
		</div>
		<aside class="layout-sidebar__aside">
			<div class="contact-card">
				<h2 class="contact-card__title"><?php esc_html_e( 'Event details', 'mcrp' ); ?></h2>
				<ul class="contact-card__list">
					<li><?php echo mcrp_icon( 'calendar' ); ?><span><?php echo esc_html( mcrp_event_date( $pid, 'date' ) ); ?></span></li>
					<li><?php echo mcrp_icon( 'clock' ); ?><span><?php echo esc_html( mcrp_event_date( $pid, 'time' ) ); ?></span></li>
					<li><?php echo mcrp_icon( $virtual ? 'monitor' : 'map-pin' ); ?><span>
						<?php if ( $virtual ) : ?>
							<?php esc_html_e( 'Online', 'mcrp' ); ?>
						<?php elseif ( $campus ) : ?>
							<a href="<?php echo esc_url( get_permalink( $campus ) ); ?>"><?php echo esc_html( get_the_title( $campus ) ); ?></a><br><?php echo esc_html( mcrp_get( 'address', $campus, '' ) ); ?>
						<?php endif; ?>
						<?php if ( $loc = mcrp_get( 'location', $pid ) ) : ?>
							<br><small><?php echo esc_html( $loc ); ?></small>
						<?php endif; ?>
					</span></li>
					<?php if ( $cap = mcrp_get( 'capacity', $pid ) ) : ?>
						<li><?php echo mcrp_icon( 'users' ); ?><span><?php echo esc_html( sprintf( /* translators: %d: capacity */ __( 'Limited to %d guests', 'mcrp' ), $cap ) ); ?></span></li>
					<?php endif; ?>
				</ul>
				<?php if ( ! $past && $reg ) : ?>
					<a class="btn btn--primary btn--block" href="<?php echo esc_url( $reg ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Reserve your spot', 'mcrp' ); ?></a>
				<?php endif; ?>
			</div>
		</aside>
	</div>
	<?php
endwhile;

get_footer();
