<?php
/**
 * Request info form.
 * JS posts it to the REST endpoint, without JS it goes to admin-post.php.
 * The hidden utm fields get filled in by inquiry-form.js.
 *
 * @package MCRP_Theme
 *
 * @var array $args heading, intro, campaign, program, campus, lock_program, show_message, button, success, redirect, variant, id.
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'heading'      => __( 'Request information', 'mcrp' ),
		'intro'        => '',
		'campaign'     => '',
		'program'      => 0,
		'campus'       => 0,
		'lock_program' => false,
		'show_message' => false,
		'button'       => __( 'Get program info', 'mcrp' ),
		'success'      => '',
		'redirect'     => '',
		'variant'      => 'card',
		'id'           => 'inquiry',
	)
);

$uid      = sanitize_html_class( $args['id'] );
$programs = get_posts( array( 'post_type' => 'program', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) );
$campuses = $args['program'] ? mcrp_q( 'campuses_for_program', (int) $args['program'] ) : get_posts( array( 'post_type' => 'campus', 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
$intakes  = array(
	wp_date( 'Y', strtotime( '+1 year' ) ),
	wp_date( 'Y', strtotime( '+2 years' ) ),
);
$consent  = mcrp_option( 'consent_text', __( 'I agree to receive information about programs, events and admissions. I can unsubscribe at any time.', 'mcrp' ) );
$success  = $args['success'] ?: mcrp_option( 'success_message', __( 'Thanks! An admissions advisor will be in touch within one business day.', 'mcrp' ) );
$status   = sanitize_key( $_GET['inquiry'] ?? '' );
?>
<div class="inquiry inquiry--<?php echo esc_attr( $args['variant'] ); ?>" id="<?php echo esc_attr( $uid ); ?>">
	<?php if ( $args['heading'] ) : ?>
		<h2 class="inquiry__title"><?php echo esc_html( $args['heading'] ); ?></h2>
	<?php endif; ?>
	<?php if ( $args['intro'] ) : ?>
		<p class="inquiry__intro"><?php echo esc_html( $args['intro'] ); ?></p>
	<?php endif; ?>

	<div class="inquiry__success" role="status" data-inquiry-success <?php echo 'success' === $status ? '' : 'hidden'; ?>>
		<?php echo mcrp_icon( 'check' ); ?>
		<p><?php echo esc_html( $success ); ?></p>
	</div>

	<form class="inquiry__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-inquiry-form data-redirect="<?php echo esc_url( $args['redirect'] ); ?>" <?php echo 'success' === $status ? 'hidden' : ''; ?>>
		<input type="hidden" name="action" value="mcrp_inquiry">
		<input type="hidden" name="campaign" value="<?php echo esc_attr( $args['campaign'] ); ?>">
		<input type="hidden" name="redirect" value="<?php echo esc_url( $args['redirect'] ?: get_permalink() ); ?>">
		<input type="hidden" name="landing_page" value="" data-landing>
		<input type="hidden" name="referrer" value="" data-referrer>
		<input type="hidden" name="started" value="" data-started>
		<?php foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content' ) as $utm ) : ?>
			<input type="hidden" name="<?php echo esc_attr( $utm ); ?>" value="" data-utm="<?php echo esc_attr( $utm ); ?>">
		<?php endforeach; ?>
		<div class="hp" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

		<?php if ( 'error' === $status ) : ?>
			<p class="inquiry__error" role="alert"><?php esc_html_e( 'Sorry, we could not send your request. Please check the form and try again.', 'mcrp' ); ?></p>
		<?php endif; ?>
		<p class="inquiry__error" role="alert" data-inquiry-error hidden></p>

		<div class="form-grid">
			<div class="field">
				<label for="<?php echo esc_attr( $uid ); ?>-first"><?php esc_html_e( 'First name', 'mcrp' ); ?> <span aria-hidden="true">*</span></label>
				<input id="<?php echo esc_attr( $uid ); ?>-first" name="first_name" type="text" autocomplete="given-name" required>
			</div>
			<div class="field">
				<label for="<?php echo esc_attr( $uid ); ?>-last"><?php esc_html_e( 'Last name', 'mcrp' ); ?></label>
				<input id="<?php echo esc_attr( $uid ); ?>-last" name="last_name" type="text" autocomplete="family-name">
			</div>
			<div class="field">
				<label for="<?php echo esc_attr( $uid ); ?>-email"><?php esc_html_e( 'Email', 'mcrp' ); ?> <span aria-hidden="true">*</span></label>
				<input id="<?php echo esc_attr( $uid ); ?>-email" name="email" type="email" autocomplete="email" required>
			</div>
			<div class="field">
				<label for="<?php echo esc_attr( $uid ); ?>-phone"><?php esc_html_e( 'Phone', 'mcrp' ); ?></label>
				<input id="<?php echo esc_attr( $uid ); ?>-phone" name="phone" type="tel" autocomplete="tel">
			</div>

			<?php if ( $args['lock_program'] && $args['program'] ) : ?>
				<input type="hidden" name="program_id" value="<?php echo (int) $args['program']; ?>">
			<?php else : ?>
				<div class="field field--full">
					<label for="<?php echo esc_attr( $uid ); ?>-program"><?php esc_html_e( 'Program of interest', 'mcrp' ); ?></label>
					<select id="<?php echo esc_attr( $uid ); ?>-program" name="program_id">
						<option value=""><?php esc_html_e( 'I\'m not sure yet', 'mcrp' ); ?></option>
						<?php foreach ( $programs as $program ) : ?>
							<option value="<?php echo (int) $program->ID; ?>" <?php selected( (int) $args['program'], $program->ID ); ?>><?php echo esc_html( $program->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<?php if ( count( $campuses ) === 1 ) : ?>
				<input type="hidden" name="campus_id" value="<?php echo (int) $campuses[0]->ID; ?>">
			<?php elseif ( $campuses ) : ?>
				<div class="field">
					<label for="<?php echo esc_attr( $uid ); ?>-campus"><?php esc_html_e( 'Preferred campus', 'mcrp' ); ?></label>
					<select id="<?php echo esc_attr( $uid ); ?>-campus" name="campus_id">
						<option value=""><?php esc_html_e( 'No preference', 'mcrp' ); ?></option>
						<?php foreach ( $campuses as $campus ) : ?>
							<option value="<?php echo (int) $campus->ID; ?>" <?php selected( (int) $args['campus'], $campus->ID ); ?>><?php echo esc_html( $campus->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<div class="field">
				<label for="<?php echo esc_attr( $uid ); ?>-intake"><?php esc_html_e( 'When do you want to start?', 'mcrp' ); ?></label>
				<select id="<?php echo esc_attr( $uid ); ?>-intake" name="intake">
					<option value=""><?php esc_html_e( 'Select', 'mcrp' ); ?></option>
					<?php foreach ( $intakes as $year ) : ?>
						<?php foreach ( array( __( 'Fall', 'mcrp' ), __( 'Winter', 'mcrp' ), __( 'Spring', 'mcrp' ) ) as $term ) : ?>
							<option><?php echo esc_html( $term . ' ' . $year ); ?></option>
						<?php endforeach; ?>
					<?php endforeach; ?>
				</select>
			</div>

			<fieldset class="field field--full field--radios">
				<legend><?php esc_html_e( 'I am a...', 'mcrp' ); ?></legend>
				<label><input type="radio" name="student_type" value="domestic" checked> <?php esc_html_e( 'Domestic student', 'mcrp' ); ?></label>
				<label><input type="radio" name="student_type" value="international"> <?php esc_html_e( 'International student', 'mcrp' ); ?></label>
			</fieldset>

			<?php if ( $args['show_message'] ) : ?>
				<div class="field field--full">
					<label for="<?php echo esc_attr( $uid ); ?>-message"><?php esc_html_e( 'Questions for our advisors', 'mcrp' ); ?></label>
					<textarea id="<?php echo esc_attr( $uid ); ?>-message" name="message" rows="3"></textarea>
				</div>
			<?php endif; ?>

			<label class="field--full field--consent">
				<input type="checkbox" name="consent" value="1">
				<span><?php echo esc_html( $consent ); ?></span>
			</label>
		</div>

		<button class="btn btn--primary btn--block" type="submit" data-inquiry-submit><?php echo esc_html( $args['button'] ); ?><?php echo mcrp_icon( 'arrow-right' ); ?></button>
	</form>
</div>
