<?php
/**
 * Single campus.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$pid        = get_the_ID();
	$type      = mcrp_get( 'campus_type', $pid );
	$online    = 'online' === $type;
	$color     = mcrp_get( 'accent_color', $pid, '#12355b' );
	$address   = mcrp_get( 'address', $pid );
	$city_line = trim( implode( ', ', array_filter( array( mcrp_get( 'city', $pid ), mcrp_get( 'region', $pid ) ) ) ) . ' ' . mcrp_get( 'postal_code', $pid, '' ) );
	$phone     = mcrp_get( 'phone', $pid );
	$email     = mcrp_get( 'email', $pid );
	$stats     = (array) mcrp_get( 'stats', $pid, array() );
	$amenities = (array) mcrp_get( 'amenities', $pid, array() );
	$programs  = mcrp_q( 'programs_at_campus', $pid );
	$events    = mcrp_q( 'upcoming_events', array( 'campus' => $pid, 'limit' => 3 ) );
	$faculty   = mcrp_q( 'instructors_at_campus', $pid, 4 );
	$stories   = mcrp_q( 'testimonials', array( 'campus' => $pid, 'limit' => 3 ) );
	$tour      = mcrp_get( 'tour_url', $pid );

	mcrp_page_hero(
		array(
			'title'   => get_the_title(),
			'intro'   => get_the_excerpt(),
			'eyebrow' => $online ? __( 'Online campus', 'mcrp' ) : ( 'main' === $type ? __( 'Main campus', 'mcrp' ) : __( 'Campus', 'mcrp' ) ),
			'actions' => ( $tour ? mcrp_button( array( 'title' => __( 'Take a virtual tour', 'mcrp' ), 'url' => $tour, 'target' => '_blank' ), 'primary', 'play' ) : '' )
				. mcrp_button( array( 'title' => __( 'Programs at this campus', 'mcrp' ), 'url' => '#programs' ), 'white' ),
			'media'   => mcrp_media( get_post(), 'mcrp-card', 'page-hero__image' ),
			'style'   => '--campus:' . $color,
			'variant' => 'campus',
		)
	);
	?>

	<?php if ( $stats ) : ?>
		<section class="key-facts key-facts--stats">
			<div class="container">
				<dl class="key-facts__list">
					<?php foreach ( $stats as $stat ) : ?>
						<div class="key-facts__item">
							<dd class="key-facts__value"><?php echo esc_html( $stat['value'] ?? '' ); ?></dd>
							<dt><?php echo esc_html( $stat['label'] ?? '' ); ?></dt>
						</div>
					<?php endforeach; ?>
				</dl>
			</div>
		</section>
	<?php endif; ?>

	<div class="container layout-sidebar section">
		<div class="layout-sidebar__main">
			<h2><?php esc_html_e( 'About this campus', 'mcrp' ); ?></h2>
			<div class="entry-content entry-content--flush"><?php the_content(); ?></div>

			<?php if ( $amenities ) : ?>
				<h3><?php esc_html_e( 'Facilities & services', 'mcrp' ); ?></h3>
				<ul class="checklist checklist--grid">
					<?php foreach ( $amenities as $amenity ) : ?>
						<li><?php echo mcrp_icon( 'check' ); ?><span><strong><?php echo esc_html( $amenity['name'] ?? '' ); ?></strong><?php echo ! empty( $amenity['description'] ) ? '<br><small>' . esc_html( $amenity['description'] ) . '</small>' : ''; ?></span></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>

		<aside class="layout-sidebar__aside">
			<div class="contact-card">
				<h2 class="contact-card__title"><?php esc_html_e( 'Contact & location', 'mcrp' ); ?></h2>
				<ul class="contact-card__list">
					<?php if ( $address && ! $online ) : ?>
						<li><?php echo mcrp_icon( 'map-pin' ); ?><address><?php echo esc_html( $address ); ?><br><?php echo esc_html( $city_line ); ?></address></li>
					<?php endif; ?>
					<?php if ( $phone ) : ?>
						<li><?php echo mcrp_icon( 'phone' ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
					<?php endif; ?>
					<?php if ( $email ) : ?>
						<li><?php echo mcrp_icon( 'mail' ); ?><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
					<?php endif; ?>
					<?php if ( $hours = mcrp_get( 'hours', $pid ) ) : ?>
						<li><?php echo mcrp_icon( 'clock' ); ?><span><?php echo wp_kses( nl2br( $hours ), array( 'br' => array() ) ); ?></span></li>
					<?php endif; ?>
				</ul>
				<?php if ( ! $online && $address ) : ?>
					<?php // TODO: replace the iframe with a proper map once we have a Maps API key. ?>
					<div class="contact-card__map">
						<iframe title="<?php echo esc_attr( sprintf( /* translators: %s campus */ __( 'Map of %s', 'mcrp' ), get_the_title() ) ); ?>" loading="lazy" referrerpolicy="no-referrer-when-downgrade" src="<?php echo esc_url( 'https://www.google.com/maps?output=embed&q=' . rawurlencode( mcrp_get( 'lat', $pid ) ? mcrp_get( 'lat', $pid ) . ',' . mcrp_get( 'lng', $pid ) : $address . ' ' . $city_line ) ); ?>"></iframe>
					</div>
					<a class="btn btn--outline btn--block" href="<?php echo esc_url( 'https://www.google.com/maps/dir/?api=1&destination=' . rawurlencode( $address . ' ' . $city_line ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'Get directions', 'mcrp' ); ?></a>
				<?php endif; ?>
			</div>
		</aside>
	</div>

	<?php if ( $programs ) : ?>
		<section id="programs" class="section bg-surface">
			<div class="container">
				<?php
				echo mcrp_section_heading(
					__( 'Study here', 'mcrp' ),
					/* translators: %s: campus */
					sprintf( __( 'Programs at %s', 'mcrp' ), get_the_title() )
				);
				get_template_part( 'template-parts/components/program-finder', null, array( 'locked' => array( 'campus' => $pid ), 'per_page' => 6, 'id' => 'campus-programs' ) );
				?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $events ) : ?>
		<section class="section">
			<div class="container">
				<?php echo mcrp_section_heading( __( 'Visit', 'mcrp' ), __( 'Upcoming events at this campus', 'mcrp' ) ); ?>
				<?php mcrp_card_grid( $events, 'event', 3 ); ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $faculty ) : ?>
		<section class="section bg-surface">
			<div class="container">
				<?php echo mcrp_section_heading( __( 'People', 'mcrp' ), __( 'Faculty at this campus', 'mcrp' ) ); ?>
				<?php mcrp_card_grid( $faculty, 'instructor', 4 ); ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $stories ) : ?>
		<section class="section">
			<div class="container">
				<?php echo mcrp_section_heading( __( 'Student life', 'mcrp' ), __( 'Stories from this campus', 'mcrp' ) ); ?>
				<?php mcrp_card_grid( $stories, 'testimonial', 3 ); ?>
			</div>
		</section>
	<?php endif; ?>
	<?php
endwhile;

get_footer();
