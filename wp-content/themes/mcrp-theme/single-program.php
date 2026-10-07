<?php
/**
 * Single program.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$pid          = get_the_ID();
	$credential  = mcrp_term_names( $pid, 'credential' );
	$area_terms  = get_the_terms( $pid, 'program_area' );
	$delivery    = mcrp_term_names( $pid, 'delivery_mode' );
	$campuses    = mcrp_q( 'campuses_for_program', $pid );
	$intakes     = mcrp_intake_labels( mcrp_get( 'intakes', $pid, array() ) );
	$courses     = array_filter( (array) mcrp_get( 'courses', $pid, array() ), static fn( $row ) => ! empty( $row['name'] ) );
	// skip empty repeater rows (e.g. a row added in the admin but left blank)
	$careers     = array_filter( (array) mcrp_get( 'career_outcomes', $pid, array() ), static fn( $row ) => ! empty( $row['title'] ) );
	$highlights  = array_filter( (array) mcrp_get( 'highlights', $pid, array() ), static fn( $row ) => ! empty( $row['text'] ) );
	$instructors = mcrp_q( 'instructors_for_program', $pid );
	$events      = mcrp_q( 'upcoming_events', array( 'program' => $pid, 'limit' => 3 ) );
	$t_ids       = mcrp_get( 'testimonials', $pid );
	$testimonials = mcrp_q( 'testimonials', $t_ids ? array( 'ids' => $t_ids ) : array( 'program' => $pid, 'limit' => 3 ) );
	$faq_ids     = mcrp_get( 'faqs', $pid );
	$faqs        = mcrp_q( 'faqs', $faq_ids ? array( 'ids' => $faq_ids ) : array( 'category' => 'admissions', 'limit' => 5 ) );
	$wil_labels  = array( 'coop' => __( 'Co-op', 'mcrp' ), 'practicum' => __( 'Practicum', 'mcrp' ), 'placement' => __( 'Field placement', 'mcrp' ), 'capstone' => __( 'Industry capstone', 'mcrp' ) );
	$wil         = mcrp_get( 'work_integrated', $pid );
	$wil         = is_array( $wil ) ? $wil['label'] : ( $wil_labels[ $wil ] ?? '' );
	$apply       = mcrp_apply_url( $pid );

	$badges = '';
	if ( $credential ) {
		$badges .= '<span class="badge badge--sun">' . esc_html( $credential ) . '</span>';
	}
	if ( $area_terms && ! is_wp_error( $area_terms ) ) {
		$badges .= '<a class="badge badge--light" href="' . esc_url( get_term_link( $area_terms[0] ) ) . '">' . esc_html( $area_terms[0]->name ) . '</a>';
	}
	if ( $code = mcrp_get( 'program_code', $pid ) ) {
		$badges .= '<span class="badge badge--light">' . esc_html__( 'Code', 'mcrp' ) . ' ' . esc_html( $code ) . '</span>';
	}

	mcrp_page_hero(
		array(
			'title'   => get_the_title(),
			'intro'   => get_the_excerpt(),
			'eyebrow' => '',
			'meta'    => $badges,
			'actions' => mcrp_button( array( 'title' => __( 'Apply now', 'mcrp' ), 'url' => $apply ), 'primary', 'arrow-right' )
				. mcrp_button( array( 'title' => __( 'Request info', 'mcrp' ), 'url' => '#inquiry' ), 'white' ),
			'media'   => mcrp_media( get_post(), 'mcrp-card', 'page-hero__image' ),
		)
	);

	$facts = array_filter(
		array(
			array( 'clock', __( 'Duration', 'mcrp' ), mcrp_get( 'duration', $pid ) ),
			array( 'award', __( 'Credential', 'mcrp' ), $credential ),
			array( 'monitor', __( 'Delivery', 'mcrp' ), $delivery ),
			array( 'calendar', __( 'Start dates', 'mcrp' ), implode( ', ', $intakes ) ),
			array( 'map-pin', __( 'Campuses', 'mcrp' ), implode( ', ', wp_list_pluck( $campuses, 'post_title' ) ) ),
			array( 'briefcase', __( 'Work experience', 'mcrp' ), $wil ),
		),
		static fn( $f ) => ! empty( $f[2] )
	);

	$tabs = array_filter(
		array(
			'overview'     => __( 'Overview', 'mcrp' ),
			'courses'      => $courses ? __( 'Courses', 'mcrp' ) : '',
			'admissions'   => __( 'Admissions', 'mcrp' ),
			'tuition'      => mcrp_get( 'tuition_domestic', $pid ) ? __( 'Tuition', 'mcrp' ) : '',
			'careers'      => $careers ? __( 'Careers', 'mcrp' ) : '',
			'faculty'      => $instructors ? __( 'Faculty', 'mcrp' ) : '',
			'faqs'         => $faqs ? __( 'FAQs', 'mcrp' ) : '',
		)
	);
	?>

	<section class="key-facts" aria-label="<?php esc_attr_e( 'Key facts', 'mcrp' ); ?>">
		<div class="container">
			<dl class="key-facts__list">
				<?php foreach ( $facts as list( $icon, $label, $value ) ) : ?>
					<div class="key-facts__item">
						<?php echo mcrp_icon( $icon ); ?>
						<dt><?php echo esc_html( $label ); ?></dt>
						<dd><?php echo esc_html( $value ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>
	</section>

	<nav class="subnav" aria-label="<?php esc_attr_e( 'On this page', 'mcrp' ); ?>" data-subnav>
		<div class="container">
			<ul>
				<?php foreach ( $tabs as $anchor => $label ) : ?>
					<li><a href="#<?php echo esc_attr( $anchor ); ?>"><?php echo esc_html( $label ); ?></a></li>
				<?php endforeach; ?>
			</ul>
		</div>
	</nav>

	<div class="container layout-sidebar">
		<div class="layout-sidebar__main">

			<section id="overview" class="program-section">
				<h2><?php esc_html_e( 'Program overview', 'mcrp' ); ?></h2>
				<div class="entry-content entry-content--flush"><?php the_content(); ?></div>

				<?php if ( $highlights ) : ?>
					<ul class="checklist checklist--grid">
						<?php foreach ( $highlights as $row ) : ?>
							<li><?php echo mcrp_icon( 'check' ); ?><?php echo esc_html( $row['text'] ?? '' ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</section>

			<?php if ( $courses ) : ?>
				<section id="courses" class="program-section">
					<h2><?php esc_html_e( 'Courses', 'mcrp' ); ?></h2>
					<div class="table-wrap">
						<table class="table">
							<thead><tr><th><?php esc_html_e( 'Term', 'mcrp' ); ?></th><th><?php esc_html_e( 'Code', 'mcrp' ); ?></th><th><?php esc_html_e( 'Course', 'mcrp' ); ?></th><th><?php esc_html_e( 'Credits', 'mcrp' ); ?></th></tr></thead>
							<tbody>
								<?php foreach ( $courses as $course ) : ?>
									<tr>
										<td><?php echo esc_html( $course['term'] ?? '' ); ?></td>
										<td><code><?php echo esc_html( $course['code'] ?? '' ); ?></code></td>
										<td><?php echo esc_html( $course['name'] ?? '' ); ?></td>
										<td><?php echo esc_html( (string) ( $course['credits'] ?? '' ) ); ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</section>
			<?php endif; ?>

			<section id="admissions" class="program-section">
				<h2><?php esc_html_e( 'Admission requirements', 'mcrp' ); ?></h2>
				<div class="tabs" data-tabs>
					<div class="tabs__list" role="tablist">
						<button role="tab" id="tab-domestic" aria-controls="panel-domestic" aria-selected="true"><?php esc_html_e( 'Domestic applicants', 'mcrp' ); ?></button>
						<button role="tab" id="tab-international" aria-controls="panel-international" aria-selected="false" tabindex="-1"><?php esc_html_e( 'International applicants', 'mcrp' ); ?></button>
					</div>
					<div class="tabs__panel entry-content entry-content--flush" role="tabpanel" id="panel-domestic" aria-labelledby="tab-domestic">
						<?php echo wp_kses_post( mcrp_get( 'admission_requirements', $pid, '<p>' . __( 'Contact admissions for requirements.', 'mcrp' ) . '</p>' ) ); ?>
					</div>
					<div class="tabs__panel entry-content entry-content--flush" role="tabpanel" id="panel-international" aria-labelledby="tab-international" hidden>
						<?php echo wp_kses_post( mcrp_get( 'international_requirements', $pid, '<p>' . __( 'Contact our international office for requirements.', 'mcrp' ) . '</p>' ) ); ?>
					</div>
				</div>
			</section>

			<?php if ( mcrp_get( 'tuition_domestic', $pid ) ) : ?>
				<section id="tuition" class="program-section">
					<h2><?php esc_html_e( 'Tuition & fees', 'mcrp' ); ?></h2>
					<div class="tuition">
						<div class="tuition__item">
							<span><?php esc_html_e( 'Domestic', 'mcrp' ); ?></span>
							<strong><?php echo esc_html( mcrp_money( mcrp_get( 'tuition_domestic', $pid ) ) ); ?></strong>
							<small><?php esc_html_e( 'per year (approx.)', 'mcrp' ); ?></small>
						</div>
						<?php if ( $intl = mcrp_get( 'tuition_international', $pid ) ) : ?>
							<div class="tuition__item">
								<span><?php esc_html_e( 'International', 'mcrp' ); ?></span>
								<strong><?php echo esc_html( mcrp_money( $intl ) ); ?></strong>
								<small><?php esc_html_e( 'per year (approx.)', 'mcrp' ); ?></small>
							</div>
						<?php endif; ?>
					</div>
					<p class="small-print"><?php esc_html_e( 'Fees are estimates and subject to change. Ancillary fees and books are additional. Scholarships and financial aid are available.', 'mcrp' ); ?></p>
				</section>
			<?php endif; ?>

			<?php if ( $careers ) : ?>
				<section id="careers" class="program-section">
					<h2><?php esc_html_e( 'Career outcomes', 'mcrp' ); ?></h2>
					<?php if ( $rate = mcrp_get( 'employment_rate', $pid ) ) : ?>
						<p class="stat-callout"><strong><?php echo esc_html( $rate ); ?>%</strong> <?php esc_html_e( 'of graduates employed within six months of graduation', 'mcrp' ); ?></p>
					<?php endif; ?>
					<ul class="pill-list">
						<?php foreach ( $careers as $career ) : ?>
							<li><?php echo mcrp_icon( 'briefcase' ); ?><?php echo esc_html( $career['title'] ?? '' ); ?><?php echo ! empty( $career['salary'] ) ? ' <small>' . esc_html( $career['salary'] ) . '</small>' : ''; ?></li>
						<?php endforeach; ?>
					</ul>
				</section>
			<?php endif; ?>

			<?php if ( $instructors ) : ?>
				<section id="faculty" class="program-section">
					<h2><?php esc_html_e( 'Meet your faculty', 'mcrp' ); ?></h2>
					<?php mcrp_card_grid( $instructors, 'instructor', 3 ); ?>
				</section>
			<?php endif; ?>

			<?php if ( $faqs ) : ?>
				<section id="faqs" class="program-section">
					<h2><?php esc_html_e( 'Frequently asked questions', 'mcrp' ); ?></h2>
					<?php get_template_part( 'template-parts/components/faq-list', null, array( 'posts' => $faqs, 'id' => 'program-faq' ) ); ?>
				</section>
			<?php endif; ?>
		</div>

		<aside class="layout-sidebar__aside">
			<div class="sticky">
				<?php
				get_template_part(
					'template-parts/components/inquiry-form',
					null,
					array(
						'heading'      => __( 'Get program details', 'mcrp' ),
						'intro'        => __( 'Tuition, start dates and next steps - sent to your inbox.', 'mcrp' ),
						'program'      => $pid,
						'lock_program' => true,
						'campaign'     => 'program-page',
						'id'           => 'inquiry',
					)
				);
				?>
			</div>
		</aside>
	</div>

	<?php if ( $campuses ) : ?>
		<section class="section bg-surface">
			<div class="container">
				<?php echo mcrp_section_heading( __( 'Where you\'ll study', 'mcrp' ), __( 'Campuses offering this program', 'mcrp' ) ); ?>
				<?php mcrp_card_grid( $campuses, 'campus', 3 ); ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $testimonials ) : ?>
		<section class="section">
			<div class="container">
				<?php echo mcrp_section_heading( __( 'Student stories', 'mcrp' ), __( 'Hear from our graduates', 'mcrp' ) ); ?>
				<?php mcrp_card_grid( $testimonials, 'testimonial', 3 ); ?>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $events ) : ?>
		<section class="section bg-surface">
			<div class="container">
				<?php echo mcrp_section_heading( __( 'Upcoming events', 'mcrp' ), __( 'Info sessions & open houses', 'mcrp' ) ); ?>
				<?php mcrp_card_grid( $events, 'event', 3 ); ?>
			</div>
		</section>
	<?php endif; ?>

	<section class="cta-strip">
		<div class="container cta-strip__inner">
			<div>
				<h2><?php esc_html_e( 'Ready to get started?', 'mcrp' ); ?></h2>
				<p><?php esc_html_e( 'Applications take about 15 minutes. Our advisors are here to help every step of the way.', 'mcrp' ); ?></p>
			</div>
			<div class="cta-strip__actions">
				<?php echo mcrp_button( array( 'title' => __( 'Apply now', 'mcrp' ), 'url' => $apply ), 'white', 'arrow-right' ); ?>
			</div>
		</div>
	</section>

	<div class="mobile-cta" data-mobile-cta>
		<a class="btn btn--outline-light btn--sm" href="#inquiry"><?php esc_html_e( 'Request info', 'mcrp' ); ?></a>
		<a class="btn btn--primary btn--sm" href="<?php echo esc_url( $apply ); ?>"><?php esc_html_e( 'Apply now', 'mcrp' ); ?></a>
	</div>
	<?php
endwhile;

get_footer();
