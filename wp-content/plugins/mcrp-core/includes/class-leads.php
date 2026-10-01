<?php
/**
 * Inquiry (RFI) form submissions.
 *
 * Saved as a private post type with the UTM/campaign info, emailed to admissions
 * (plus the campus inbox), and optionally pushed to a CRM webhook.
 *
 * @package MCRP_Core
 */

namespace MCRP;

defined( 'ABSPATH' ) || exit;

/**
 * Lead capture.
 */
class Leads {

	const POST_TYPE = 'mcrp_lead';

	/**
	 * Lead meta fields => label.
	 */
	public static function fields(): array {
		return array(
			'first_name'   => __( 'First name', 'mcrp' ),
			'last_name'    => __( 'Last name', 'mcrp' ),
			'email'        => __( 'Email', 'mcrp' ),
			'phone'        => __( 'Phone', 'mcrp' ),
			'program_id'   => __( 'Program', 'mcrp' ),
			'campus_id'    => __( 'Campus', 'mcrp' ),
			'intake'       => __( 'Intended intake', 'mcrp' ),
			'student_type' => __( 'Student type', 'mcrp' ),
			'message'      => __( 'Message', 'mcrp' ),
			'consent'      => __( 'Marketing consent', 'mcrp' ),
			'campaign'     => __( 'Campaign', 'mcrp' ),
			'utm_source'   => 'utm_source',
			'utm_medium'   => 'utm_medium',
			'utm_campaign' => 'utm_campaign',
			'utm_term'     => 'utm_term',
			'utm_content'  => 'utm_content',
			'landing_page' => __( 'Landing page', 'mcrp' ),
			'referrer'     => __( 'Referrer', 'mcrp' ),
		);
	}

	public static function init(): void {
		add_action( 'admin_post_nopriv_mcrp_inquiry', array( __CLASS__, 'handle_post' ) );
		add_action( 'admin_post_mcrp_inquiry', array( __CLASS__, 'handle_post' ) );

		add_filter( 'manage_' . self::POST_TYPE . '_posts_columns', array( __CLASS__, 'columns' ) );
		add_action( 'manage_' . self::POST_TYPE . '_posts_custom_column', array( __CLASS__, 'column_content' ), 10, 2 );
		add_action( 'add_meta_boxes_' . self::POST_TYPE, array( __CLASS__, 'meta_box' ) );
		add_action( 'restrict_manage_posts', array( __CLASS__, 'admin_filters' ) );
		add_action( 'pre_get_posts', array( __CLASS__, 'admin_filter_query' ) );
		add_action( 'admin_post_mcrp_export_leads', array( __CLASS__, 'export_csv' ) );
		add_action( 'manage_posts_extra_tablenav', array( __CLASS__, 'export_button' ) );
	}

	/**
	 * Validate and store a lead.
	 *
	 * @param array $raw Raw submitted data.
	 * @return int|\WP_Error Lead ID.
	 */
	public static function create( array $raw ) {
		// basic spam checks - honeypot, time trap, rate limit per IP
		if ( ! empty( $raw['website'] ) ) {
			return new \WP_Error( 'mcrp_spam', __( 'Submission rejected.', 'mcrp' ), array( 'status' => 400 ) );
		}
		$started = (int) ( $raw['started'] ?? 0 );
		if ( $started && ( time() - (int) ( $started / 1000 ) ) < 3 ) {
			return new \WP_Error( 'mcrp_spam', __( 'Please take a moment to complete the form.', 'mcrp' ), array( 'status' => 400 ) );
		}
		$ip_key = 'mcrp_rl_' . md5( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		$hits   = (int) get_transient( $ip_key );
		if ( $hits >= (int) apply_filters( 'mcrp_lead_rate_limit', 5 ) ) {
			return new \WP_Error( 'mcrp_rate_limited', __( 'Too many submissions. Please try again later.', 'mcrp' ), array( 'status' => 429 ) );
		}

		$data = array(
			'first_name'   => sanitize_text_field( $raw['first_name'] ?? '' ),
			'last_name'    => sanitize_text_field( $raw['last_name'] ?? '' ),
			'email'        => sanitize_email( $raw['email'] ?? '' ),
			'phone'        => preg_replace( '/[^0-9+\-\s().]/', '', (string) ( $raw['phone'] ?? '' ) ),
			'program_id'   => absint( $raw['program_id'] ?? 0 ),
			'campus_id'    => absint( $raw['campus_id'] ?? 0 ),
			'intake'       => sanitize_text_field( $raw['intake'] ?? '' ),
			'student_type' => in_array( $raw['student_type'] ?? '', array( 'domestic', 'international' ), true ) ? $raw['student_type'] : '',
			'message'      => sanitize_textarea_field( $raw['message'] ?? '' ),
			'consent'      => empty( $raw['consent'] ) ? 'no' : 'yes',
			'campaign'     => sanitize_text_field( $raw['campaign'] ?? '' ),
			'landing_page' => esc_url_raw( $raw['landing_page'] ?? '' ),
			'referrer'     => esc_url_raw( $raw['referrer'] ?? '' ),
		);

		// posted utm fields first, then fall back to the first-touch cookie
		$cookie = array();
		if ( ! empty( $_COOKIE['mcrp_utm'] ) ) {
			$cookie = json_decode( wp_unslash( $_COOKIE['mcrp_utm'] ), true ) ?: array();
		}
		foreach ( array( 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content' ) as $utm ) {
			$data[ $utm ] = sanitize_text_field( ! empty( $raw[ $utm ] ) ? $raw[ $utm ] : ( $cookie[ $utm ] ?? '' ) );
		}

		$errors = new \WP_Error();
		if ( '' === $data['first_name'] ) {
			$errors->add( 'first_name', __( 'Please enter your first name.', 'mcrp' ) );
		}
		if ( ! is_email( $data['email'] ) ) {
			$errors->add( 'email', __( 'Please enter a valid email address.', 'mcrp' ) );
		}
		if ( $data['program_id'] && 'program' !== get_post_type( $data['program_id'] ) ) {
			$data['program_id'] = 0;
		}
		if ( $data['campus_id'] && 'campus' !== get_post_type( $data['campus_id'] ) ) {
			$data['campus_id'] = 0;
		}

		$errors = apply_filters( 'mcrp_validate_lead', $errors, $data );
		if ( $errors->has_errors() ) {
			$errors->add_data( array( 'status' => 422 ) );
			return $errors;
		}

		$lead_id = wp_insert_post(
			array(
				'post_type'   => self::POST_TYPE,
				'post_status' => 'publish',
				'post_title'  => trim( $data['first_name'] . ' ' . $data['last_name'] ) . ' - ' . ( $data['program_id'] ? get_the_title( $data['program_id'] ) : __( 'General inquiry', 'mcrp' ) ),
			),
			true
		);
		if ( is_wp_error( $lead_id ) ) {
			return $lead_id;
		}

		foreach ( $data as $key => $value ) {
			update_post_meta( $lead_id, $key, $value );
		}

		set_transient( $ip_key, $hits + 1, HOUR_IN_SECONDS );

		self::notify( $lead_id, $data );
		self::send_webhook( $lead_id, $data );

		/**
		 * Fires after a lead is stored. Use for CRM integrations.
		 *
		 * @param int   $lead_id Lead post ID.
		 * @param array $data    Sanitised lead data.
		 */
		do_action( 'mcrp_lead_created', $lead_id, $data );

		return $lead_id;
	}

	/**
	 * Email the admissions team(s).
	 */
	private static function notify( int $lead_id, array $data ): void {
		$recipients = array_filter( array_map( 'trim', explode( ',', (string) mcrp_option( 'lead_recipients', '' ) ) ) );
		if ( $data['campus_id'] && ( $campus_email = mcrp_get( 'email', $data['campus_id'] ) ) ) {
			$recipients[] = $campus_email;
		}
		if ( ! $recipients ) {
			$recipients[] = get_option( 'admin_email' );
		}
		$recipients = apply_filters( 'mcrp_lead_recipients', array_unique( $recipients ), $data );

		// TODO: move this into a template so admissions can change the wording.
		$lines = array();
		foreach ( self::fields() as $key => $label ) {
			$lines[] = $label . ': ' . self::display_value( $key, $data[ $key ] ?? '' );
		}
		$lines[] = '';
		$lines[] = admin_url( 'post.php?action=edit&post=' . $lead_id );

		/* translators: %s: program title */
		$subject = sprintf( __( 'New inquiry: %s', 'mcrp' ), get_the_title( $lead_id ) );
		wp_mail( $recipients, $subject, implode( "\n", $lines ), array( 'Reply-To: ' . $data['email'] ) );
	}

	/**
	 * Forward to a CRM webhook (non-blocking).
	 */
	private static function send_webhook( int $lead_id, array $data ): void {
		$url = mcrp_option( 'lead_webhook' );
		if ( ! $url ) {
			return;
		}
		$payload = array_merge(
			$data,
			array(
				'lead_id'      => $lead_id,
				'program_name' => $data['program_id'] ? get_the_title( $data['program_id'] ) : '',
				'campus_name'  => $data['campus_id'] ? get_the_title( $data['campus_id'] ) : '',
				'submitted_at' => current_time( 'c' ),
			)
		);
		wp_remote_post(
			$url,
			array(
				'blocking' => false,
				'timeout'  => 5,
				'headers'  => array( 'Content-Type' => 'application/json' ),
				'body'     => wp_json_encode( apply_filters( 'mcrp_lead_webhook_payload', $payload, $lead_id ) ),
			)
		);
	}

	/**
	 * Human-readable value for a lead field.
	 */
	public static function display_value( string $key, $value ): string {
		if ( in_array( $key, array( 'program_id', 'campus_id' ), true ) ) {
			return $value ? get_the_title( (int) $value ) : '-';
		}
		return '' === (string) $value ? '-' : (string) $value;
	}

	/**
	 * No-JS fallback: classic form POST to admin-post.php.
	 */
	public static function handle_post(): void {
		$result   = self::create( wp_unslash( $_POST ) );
		$redirect = wp_validate_redirect( wp_unslash( $_POST['redirect'] ?? '' ), wp_get_referer() ?: home_url( '/' ) );
		$redirect = add_query_arg( 'inquiry', is_wp_error( $result ) ? 'error' : 'success', $redirect );
		wp_safe_redirect( $redirect . '#inquiry' );
		exit;
	}

	// Admin UI

	public static function columns( array $columns ): array {
		return array(
			'cb'       => $columns['cb'],
			'title'    => __( 'Inquiry', 'mcrp' ),
			'email'    => __( 'Email', 'mcrp' ),
			'campus'   => __( 'Campus', 'mcrp' ),
			'intake'   => __( 'Intake', 'mcrp' ),
			'source'   => __( 'Source / Campaign', 'mcrp' ),
			'date'     => __( 'Received', 'mcrp' ),
		);
	}

	public static function column_content( string $column, int $post_id ): void {
		switch ( $column ) {
			case 'email':
				$email = get_post_meta( $post_id, 'email', true );
				printf( '<a href="mailto:%1$s">%1$s</a>', esc_html( $email ) );
				break;
			case 'campus':
				echo esc_html( self::display_value( 'campus_id', get_post_meta( $post_id, 'campus_id', true ) ) );
				break;
			case 'intake':
				echo esc_html( get_post_meta( $post_id, 'intake', true ) ?: '-' );
				break;
			case 'source':
				$parts = array_filter(
					array(
						get_post_meta( $post_id, 'utm_source', true ),
						get_post_meta( $post_id, 'utm_medium', true ),
					)
				);
				echo esc_html( $parts ? implode( ' / ', $parts ) : __( 'direct', 'mcrp' ) );
				$campaign = get_post_meta( $post_id, 'campaign', true ) ?: get_post_meta( $post_id, 'utm_campaign', true );
				if ( $campaign ) {
					echo '<br><small>' . esc_html( $campaign ) . '</small>';
				}
				break;
		}
	}

	public static function meta_box(): void {
		add_meta_box(
			'mcrp-lead-details',
			__( 'Inquiry details', 'mcrp' ),
			static function ( \WP_Post $post ) {
				echo '<table class="widefat striped"><tbody>';
				foreach ( self::fields() as $key => $label ) {
					printf(
						'<tr><th style="width:200px">%s</th><td>%s</td></tr>',
						esc_html( $label ),
						nl2br( esc_html( self::display_value( $key, get_post_meta( $post->ID, $key, true ) ) ) )
					);
				}
				echo '</tbody></table>';
			},
			self::POST_TYPE,
			'normal',
			'high'
		);
	}

	public static function admin_filters( string $post_type ): void {
		if ( self::POST_TYPE !== $post_type ) {
			return;
		}
		$current = absint( $_GET['mcrp_campus'] ?? 0 );
		echo '<select name="mcrp_campus"><option value="">' . esc_html__( 'All campuses', 'mcrp' ) . '</option>';
		foreach ( get_posts( array( 'post_type' => 'campus', 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC' ) ) as $campus ) {
			printf( '<option value="%d" %s>%s</option>', (int) $campus->ID, selected( $current, $campus->ID, false ), esc_html( $campus->post_title ) );
		}
		echo '</select>';
	}

	public static function admin_filter_query( \WP_Query $q ): void {
		if ( ! is_admin() || ! $q->is_main_query() || self::POST_TYPE !== $q->get( 'post_type' ) || empty( $_GET['mcrp_campus'] ) ) {
			return;
		}
		$q->set( 'meta_query', array( array( 'key' => 'campus_id', 'value' => absint( $_GET['mcrp_campus'] ) ) ) );
	}

	public static function export_button( string $which ): void {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( 'top' !== $which || ! $screen || 'edit-' . self::POST_TYPE !== $screen->id ) {
			return;
		}
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=mcrp_export_leads' ), 'mcrp_export_leads' );
		printf( '<div class="alignleft actions"><a class="button" href="%s">%s</a></div>', esc_url( $url ), esc_html__( 'Export CSV', 'mcrp' ) );
	}

	public static function export_csv(): void {
		if ( ! current_user_can( 'edit_posts' ) || ! check_admin_referer( 'mcrp_export_leads' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'mcrp' ) );
		}
		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=inquiries-' . gmdate( 'Y-m-d' ) . '.csv' );

		$out    = fopen( 'php://output', 'w' );
		$fields = self::fields();
		fputcsv( $out, array_merge( array( 'ID', 'Date' ), array_values( $fields ) ) );

		$ids = get_posts( array( 'post_type' => self::POST_TYPE, 'posts_per_page' => -1, 'fields' => 'ids' ) );
		foreach ( $ids as $id ) {
			$row = array( $id, get_the_date( 'Y-m-d H:i', $id ) );
			foreach ( array_keys( $fields ) as $key ) {
				$row[] = self::display_value( $key, get_post_meta( $id, $key, true ) );
			}
			fputcsv( $out, $row );
		}
		fclose( $out );
		exit;
	}
}
