<?php
/**
 * ACF field groups. Kept in PHP so they live in git and deploy with the plugin.
 * Keys are generated as field_mcrp_{group}_{name}.
 *
 * @package MCRP_Core
 */

namespace MCRP;

defined( 'ABSPATH' ) || exit;

/**
 * Field groups for every content type plus the global "Recruitment Settings" options page.
 */
class ACF_Fields {

	public static function init(): void {
		add_action( 'acf/init', array( __CLASS__, 'options_page' ) );
		add_action( 'acf/include_fields', array( __CLASS__, 'register' ) );
		add_filter( 'acf/fields/relationship/query', array( __CLASS__, 'relationship_query' ), 10, 3 );
	}

	/**
	 * Field key for a given group + name (used by the seeder).
	 */
	public static function key( string $group, string $name ): string {
		return 'field_mcrp_' . $group . '_' . $name;
	}

	public static function options_page(): void {
		if ( ! function_exists( 'acf_add_options_page' ) ) {
			return;
		}
		acf_add_options_page(
			array(
				'page_title' => __( 'Recruitment Settings', 'mcrp' ),
				'menu_title' => __( 'Recruitment', 'mcrp' ),
				'menu_slug'  => 'mcrp-settings',
				'capability' => 'manage_options',
				'icon_url'   => 'dashicons-megaphone',
				'position'   => 27,
				'redirect'   => false,
			)
		);
	}

	/**
	 * Location rule helper.
	 */
	private static function for_post_type( string $type ): array {
		return array( array( array( 'param' => 'post_type', 'operator' => '==', 'value' => $type ) ) );
	}

	/**
	 * Register a group with auto-generated keys.
	 */
	private static function group( string $slug, string $title, array $fields, array $location, array $extra = array() ): void {
		acf_add_local_field_group(
			array_merge(
				array(
					'key'                   => 'group_mcrp_' . $slug,
					'title'                 => $title,
					'fields'                => mcrp_acf_keys( $fields, 'mcrp_' . $slug ),
					'location'              => $location,
					'position'              => 'normal',
					'style'                 => 'default',
					'label_placement'       => 'top',
					'show_in_rest'          => 1,
					'active'                => true,
				),
				$extra
			)
		);
	}

	public static function register(): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			return;
		}

		$tab = static fn( string $label ) => array( 'label' => $label, 'type' => 'tab', 'name' => sanitize_title( $label ) . '_tab', 'placement' => 'top' );

		// Program
		self::group(
			'program',
			__( 'Program Details', 'mcrp' ),
			array(
				$tab( __( 'Key Facts', 'mcrp' ) ),
				array( 'label' => __( 'Program code', 'mcrp' ), 'name' => 'program_code', 'type' => 'text', 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Duration', 'mcrp' ), 'name' => 'duration', 'type' => 'text', 'placeholder' => '2 years (4 semesters)', 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Work-integrated learning', 'mcrp' ), 'name' => 'work_integrated', 'type' => 'select', 'choices' => array( '' => '-', 'coop' => 'Co-op', 'practicum' => 'Practicum', 'placement' => 'Field placement', 'capstone' => 'Industry capstone' ), 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Intakes', 'mcrp' ), 'name' => 'intakes', 'type' => 'checkbox', 'choices' => array( 'fall' => 'Fall (September)', 'winter' => 'Winter (January)', 'spring' => 'Spring (May)' ), 'layout' => 'horizontal', 'return_format' => 'value', 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Domestic tuition (per year)', 'mcrp' ), 'name' => 'tuition_domestic', 'type' => 'number', 'prepend' => '$', 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'International tuition (per year)', 'mcrp' ), 'name' => 'tuition_international', 'type' => 'number', 'prepend' => '$', 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Apply URL', 'mcrp' ), 'name' => 'apply_url', 'type' => 'url', 'instructions' => __( 'Leave empty to use the global application URL.', 'mcrp' ), 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Offered at campuses', 'mcrp' ), 'name' => 'campuses', 'type' => 'relationship', 'post_type' => array( 'campus' ), 'filters' => array( 'search' ), 'return_format' => 'id', 'required' => 1 ),
				array( 'label' => __( 'Program highlights', 'mcrp' ), 'name' => 'highlights', 'type' => 'repeater', 'layout' => 'table', 'button_label' => __( 'Add highlight', 'mcrp' ), 'max' => 6, 'sub_fields' => array(
					array( 'label' => __( 'Highlight', 'mcrp' ), 'name' => 'text', 'type' => 'text' ),
				) ),

				$tab( __( 'Curriculum', 'mcrp' ) ),
				array( 'label' => __( 'Courses', 'mcrp' ), 'name' => 'courses', 'type' => 'repeater', 'layout' => 'table', 'button_label' => __( 'Add course', 'mcrp' ), 'sub_fields' => array(
					array( 'label' => __( 'Term', 'mcrp' ), 'name' => 'term', 'type' => 'text', 'wrapper' => array( 'width' => 15 ) ),
					array( 'label' => __( 'Code', 'mcrp' ), 'name' => 'code', 'type' => 'text', 'wrapper' => array( 'width' => 15 ) ),
					array( 'label' => __( 'Course name', 'mcrp' ), 'name' => 'name', 'type' => 'text' ),
					array( 'label' => __( 'Credits', 'mcrp' ), 'name' => 'credits', 'type' => 'number', 'wrapper' => array( 'width' => 12 ) ),
				) ),

				$tab( __( 'Admissions', 'mcrp' ) ),
				array( 'label' => __( 'Admission requirements', 'mcrp' ), 'name' => 'admission_requirements', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0 ),
				array( 'label' => __( 'International requirements', 'mcrp' ), 'name' => 'international_requirements', 'type' => 'wysiwyg', 'tabs' => 'all', 'toolbar' => 'basic', 'media_upload' => 0 ),

				$tab( __( 'Careers', 'mcrp' ) ),
				array( 'label' => __( 'Career outcomes', 'mcrp' ), 'name' => 'career_outcomes', 'type' => 'repeater', 'layout' => 'table', 'button_label' => __( 'Add career', 'mcrp' ), 'sub_fields' => array(
					array( 'label' => __( 'Job title', 'mcrp' ), 'name' => 'title', 'type' => 'text' ),
					array( 'label' => __( 'Typical salary', 'mcrp' ), 'name' => 'salary', 'type' => 'text', 'placeholder' => '$55,000 - $70,000' ),
				) ),
				array( 'label' => __( 'Graduate employment rate (%)', 'mcrp' ), 'name' => 'employment_rate', 'type' => 'number', 'min' => 0, 'max' => 100, 'append' => '%' ),

				$tab( __( 'Related', 'mcrp' ) ),
				array( 'label' => __( 'Instructors', 'mcrp' ), 'name' => 'instructors', 'type' => 'relationship', 'post_type' => array( 'instructor' ), 'filters' => array( 'search' ), 'return_format' => 'id' ),
				array( 'label' => __( 'FAQs', 'mcrp' ), 'name' => 'faqs', 'type' => 'relationship', 'post_type' => array( 'faq' ), 'filters' => array( 'search', 'taxonomy' ), 'return_format' => 'id' ),
				array( 'label' => __( 'Testimonials', 'mcrp' ), 'name' => 'testimonials', 'type' => 'relationship', 'post_type' => array( 'testimonial' ), 'filters' => array( 'search' ), 'return_format' => 'id', 'instructions' => __( 'Leave empty to automatically show testimonials linked to this program.', 'mcrp' ) ),
			),
			self::for_post_type( 'program' ),
			array( 'hide_on_screen' => array( 'custom_fields' ) )
		);

		// Campus
		self::group(
			'campus',
			__( 'Campus Details', 'mcrp' ),
			array(
				$tab( __( 'Location', 'mcrp' ) ),
				array( 'label' => __( 'Street address', 'mcrp' ), 'name' => 'address', 'type' => 'text', 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'City', 'mcrp' ), 'name' => 'city', 'type' => 'text', 'wrapper' => array( 'width' => 20 ) ),
				array( 'label' => __( 'Province / State', 'mcrp' ), 'name' => 'region', 'type' => 'text', 'wrapper' => array( 'width' => 15 ) ),
				array( 'label' => __( 'Postal code', 'mcrp' ), 'name' => 'postal_code', 'type' => 'text', 'wrapper' => array( 'width' => 15 ) ),
				array( 'label' => __( 'Latitude', 'mcrp' ), 'name' => 'lat', 'type' => 'text', 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Longitude', 'mcrp' ), 'name' => 'lng', 'type' => 'text', 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Campus type', 'mcrp' ), 'name' => 'campus_type', 'type' => 'select', 'choices' => array( 'main' => 'Main campus', 'satellite' => 'Satellite campus', 'online' => 'Online / virtual' ), 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Accent colour', 'mcrp' ), 'name' => 'accent_color', 'type' => 'color_picker', 'default_value' => '#12355b', 'wrapper' => array( 'width' => 25 ) ),

				$tab( __( 'Contact', 'mcrp' ) ),
				array( 'label' => __( 'Phone', 'mcrp' ), 'name' => 'phone', 'type' => 'text', 'wrapper' => array( 'width' => 33 ) ),
				array( 'label' => __( 'Admissions email', 'mcrp' ), 'name' => 'email', 'type' => 'email', 'instructions' => __( 'Inquiries for this campus are routed here.', 'mcrp' ), 'wrapper' => array( 'width' => 33 ) ),
				array( 'label' => __( 'Virtual tour URL', 'mcrp' ), 'name' => 'tour_url', 'type' => 'url', 'wrapper' => array( 'width' => 34 ) ),
				array( 'label' => __( 'Office hours', 'mcrp' ), 'name' => 'hours', 'type' => 'textarea', 'rows' => 3, 'new_lines' => 'br' ),

				$tab( __( 'Campus Life', 'mcrp' ) ),
				array( 'label' => __( 'Quick stats', 'mcrp' ), 'name' => 'stats', 'type' => 'repeater', 'layout' => 'table', 'max' => 4, 'button_label' => __( 'Add stat', 'mcrp' ), 'sub_fields' => array(
					array( 'label' => __( 'Value', 'mcrp' ), 'name' => 'value', 'type' => 'text' ),
					array( 'label' => __( 'Label', 'mcrp' ), 'name' => 'label', 'type' => 'text' ),
				) ),
				array( 'label' => __( 'Amenities', 'mcrp' ), 'name' => 'amenities', 'type' => 'repeater', 'layout' => 'table', 'button_label' => __( 'Add amenity', 'mcrp' ), 'sub_fields' => array(
					array( 'label' => __( 'Amenity', 'mcrp' ), 'name' => 'name', 'type' => 'text' ),
					array( 'label' => __( 'Description', 'mcrp' ), 'name' => 'description', 'type' => 'text' ),
				) ),
				array( 'label' => __( 'Gallery', 'mcrp' ), 'name' => 'gallery', 'type' => 'gallery', 'return_format' => 'id', 'preview_size' => 'medium' ),
			),
			self::for_post_type( 'campus' ),
			array( 'hide_on_screen' => array( 'custom_fields' ) )
		);

		// Instructor
		self::group(
			'instructor',
			__( 'Instructor Profile', 'mcrp' ),
			array(
				array( 'label' => __( 'Position / title', 'mcrp' ), 'name' => 'position', 'type' => 'text', 'placeholder' => 'Program Coordinator', 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Credentials', 'mcrp' ), 'name' => 'credentials', 'type' => 'text', 'placeholder' => 'MBA, CPA', 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Email', 'mcrp' ), 'name' => 'email', 'type' => 'email', 'wrapper' => array( 'width' => 33 ) ),
				array( 'label' => __( 'LinkedIn URL', 'mcrp' ), 'name' => 'linkedin', 'type' => 'url', 'wrapper' => array( 'width' => 33 ) ),
				array( 'label' => __( 'Home campus', 'mcrp' ), 'name' => 'campus', 'type' => 'post_object', 'post_type' => array( 'campus' ), 'return_format' => 'id', 'allow_null' => 1, 'wrapper' => array( 'width' => 34 ) ),
				array( 'label' => __( 'Areas of expertise', 'mcrp' ), 'name' => 'expertise', 'type' => 'text', 'instructions' => __( 'Comma separated.', 'mcrp' ) ),
			),
			self::for_post_type( 'instructor' ),
			array( 'position' => 'acf_after_title' )
		);

		// Event
		self::group(
			'event',
			__( 'Event Details', 'mcrp' ),
			array(
				array( 'label' => __( 'Starts', 'mcrp' ), 'name' => 'start_datetime', 'type' => 'date_time_picker', 'display_format' => 'F j, Y g:i a', 'return_format' => 'Y-m-d H:i:s', 'required' => 1, 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Ends', 'mcrp' ), 'name' => 'end_datetime', 'type' => 'date_time_picker', 'display_format' => 'F j, Y g:i a', 'return_format' => 'Y-m-d H:i:s', 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Virtual event', 'mcrp' ), 'name' => 'is_virtual', 'type' => 'true_false', 'ui' => 1, 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Campus', 'mcrp' ), 'name' => 'campus', 'type' => 'post_object', 'post_type' => array( 'campus' ), 'return_format' => 'id', 'allow_null' => 1, 'wrapper' => array( 'width' => 35 ), 'conditional_logic' => array( array( array( 'field' => self::key( 'event', 'is_virtual' ), 'operator' => '!=', 'value' => '1' ) ) ) ),
				array( 'label' => __( 'Room / location details', 'mcrp' ), 'name' => 'location', 'type' => 'text', 'wrapper' => array( 'width' => 40 ) ),
				array( 'label' => __( 'Registration URL', 'mcrp' ), 'name' => 'registration_url', 'type' => 'url', 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Capacity', 'mcrp' ), 'name' => 'capacity', 'type' => 'number', 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Featured', 'mcrp' ), 'name' => 'featured', 'type' => 'true_false', 'ui' => 1, 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Related programs', 'mcrp' ), 'name' => 'programs', 'type' => 'relationship', 'post_type' => array( 'program' ), 'filters' => array( 'search', 'taxonomy' ), 'return_format' => 'id' ),
			),
			self::for_post_type( 'event' ),
			array( 'position' => 'acf_after_title' )
		);

		// Testimonial
		self::group(
			'testimonial',
			__( 'Testimonial', 'mcrp' ),
			array(
				array( 'label' => __( 'Quote', 'mcrp' ), 'name' => 'quote', 'type' => 'textarea', 'rows' => 4, 'required' => 1, 'maxlength' => 480 ),
				array( 'label' => __( 'Role / employer', 'mcrp' ), 'name' => 'role', 'type' => 'text', 'placeholder' => 'Marketing Coordinator at Acme Co.', 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Graduation year', 'mcrp' ), 'name' => 'grad_year', 'type' => 'number', 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Student type', 'mcrp' ), 'name' => 'student_type', 'type' => 'select', 'choices' => array( 'domestic' => 'Domestic', 'international' => 'International', 'mature' => 'Mature / career changer' ), 'wrapper' => array( 'width' => 25 ) ),
				array( 'label' => __( 'Program', 'mcrp' ), 'name' => 'program', 'type' => 'post_object', 'post_type' => array( 'program' ), 'return_format' => 'id', 'allow_null' => 1, 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Campus', 'mcrp' ), 'name' => 'campus', 'type' => 'post_object', 'post_type' => array( 'campus' ), 'return_format' => 'id', 'allow_null' => 1, 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Video URL', 'mcrp' ), 'name' => 'video_url', 'type' => 'url' ),
			),
			self::for_post_type( 'testimonial' ),
			array( 'position' => 'acf_after_title' )
		);

		// FAQ
		self::group(
			'faq',
			__( 'FAQ Settings', 'mcrp' ),
			array(
				array( 'label' => __( 'Show on campuses', 'mcrp' ), 'name' => 'campuses', 'type' => 'relationship', 'post_type' => array( 'campus' ), 'return_format' => 'id', 'instructions' => __( 'Leave empty for FAQs that apply to all campuses.', 'mcrp' ) ),
			),
			self::for_post_type( 'faq' ),
			array( 'position' => 'side' )
		);

		// Global options
		self::group(
			'options',
			__( 'Recruitment Settings', 'mcrp' ),
			array(
				$tab( __( 'Admissions', 'mcrp' ) ),
				array( 'label' => __( 'Application URL', 'mcrp' ), 'name' => 'apply_url', 'type' => 'url', 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Book a tour URL', 'mcrp' ), 'name' => 'tour_url', 'type' => 'url', 'wrapper' => array( 'width' => 50 ) ),
				array( 'label' => __( 'Admissions phone', 'mcrp' ), 'name' => 'phone', 'type' => 'text', 'wrapper' => array( 'width' => 33 ) ),
				array( 'label' => __( 'Admissions email', 'mcrp' ), 'name' => 'email', 'type' => 'email', 'wrapper' => array( 'width' => 33 ) ),
				array( 'label' => __( 'Currency symbol', 'mcrp' ), 'name' => 'currency_symbol', 'type' => 'text', 'default_value' => '$', 'wrapper' => array( 'width' => 34 ) ),

				$tab( __( 'Lead Capture', 'mcrp' ) ),
				array( 'label' => __( 'Notification recipients', 'mcrp' ), 'name' => 'lead_recipients', 'type' => 'text', 'instructions' => __( 'Comma-separated. Campus admissions emails are added automatically when a campus is selected.', 'mcrp' ) ),
				array( 'label' => __( 'CRM webhook URL', 'mcrp' ), 'name' => 'lead_webhook', 'type' => 'url', 'instructions' => __( 'Optional. Each inquiry is POSTed here as JSON (Slate, Salesforce, HubSpot, Zapier...).', 'mcrp' ) ),
				array( 'label' => __( 'Consent text', 'mcrp' ), 'name' => 'consent_text', 'type' => 'textarea', 'rows' => 2, 'default_value' => 'I agree to receive information about programs, events and admissions. I can unsubscribe at any time.' ),
				array( 'label' => __( 'Default success message', 'mcrp' ), 'name' => 'success_message', 'type' => 'text', 'default_value' => 'Thanks! An admissions advisor will be in touch within one business day.' ),

				$tab( __( 'Site Chrome', 'mcrp' ) ),
				array( 'label' => __( 'Announcement bar', 'mcrp' ), 'name' => 'announcement', 'type' => 'text', 'instructions' => __( 'Shown above the header on every page. Leave empty to hide.', 'mcrp' ), 'wrapper' => array( 'width' => 60 ) ),
				array( 'label' => __( 'Announcement link', 'mcrp' ), 'name' => 'announcement_link', 'type' => 'link', 'wrapper' => array( 'width' => 40 ) ),
				array( 'label' => __( 'Footer tagline', 'mcrp' ), 'name' => 'footer_tagline', 'type' => 'textarea', 'rows' => 2 ),
				array( 'label' => __( 'Social profiles', 'mcrp' ), 'name' => 'social', 'type' => 'repeater', 'layout' => 'table', 'button_label' => __( 'Add profile', 'mcrp' ), 'sub_fields' => array(
					array( 'label' => __( 'Network', 'mcrp' ), 'name' => 'network', 'type' => 'select', 'choices' => array( 'instagram' => 'Instagram', 'facebook' => 'Facebook', 'linkedin' => 'LinkedIn', 'youtube' => 'YouTube', 'tiktok' => 'TikTok', 'x' => 'X' ) ),
					array( 'label' => __( 'URL', 'mcrp' ), 'name' => 'url', 'type' => 'url' ),
				) ),
			),
			array( array( array( 'param' => 'options_page', 'operator' => '==', 'value' => 'mcrp-settings' ) ) )
		);
	}

	/**
	 * Only show published items in relationship pickers, alphabetically.
	 *
	 * @param array $args  WP_Query args.
	 * @param array $field Field.
	 * @param int   $post_id Post ID.
	 */
	public static function relationship_query( $args, $field, $post_id ) {
		$args['post_status'] = 'publish';
		$args['orderby']     = 'title';
		$args['order']       = 'ASC';
		return $args;
	}
}
