<?php
/**
 * Demo content seeder for a fictional college ("Northbridge College").
 *
 * Run with `wp mcrp seed` or from Tools -> Recruitment Demo Content.
 * Every generated item is flagged with `_mcrp_demo` so it can be removed
 * cleanly with `wp mcrp seed --reset`.
 *
 * @package MCRP_Core
 */

namespace MCRP;

defined( 'ABSPATH' ) || exit;

/**
 * Seeder.
 */
class Seeder {

	/** @var array<string,int> slug => post ID */
	private static array $ids = array();

	/** @var string[] */
	public static array $log = array();

	public static function init(): void {
		add_action( 'admin_menu', array( __CLASS__, 'menu' ) );
		add_action( 'admin_post_mcrp_seed', array( __CLASS__, 'handle_admin' ) );
	}

	public static function menu(): void {
		add_management_page(
			__( 'Recruitment Demo Content', 'mcrp' ),
			__( 'Recruitment Demo', 'mcrp' ),
			'manage_options',
			'mcrp-demo',
			array( __CLASS__, 'page' )
		);
	}

	public static function page(): void {
		$seeded = get_option( 'mcrp_demo_seeded' );
		echo '<div class="wrap"><h1>' . esc_html__( 'Recruitment Demo Content', 'mcrp' ) . '</h1>';
		if ( isset( $_GET['done'] ) ) {
			echo '<div class="notice notice-success"><p>' . esc_html__( 'Done.', 'mcrp' ) . '</p></div>';
		}
		echo '<p>' . esc_html__( 'Creates sample campuses, programs, instructors, events, testimonials, FAQs, pages built with the marketing blocks, menus and settings for a fictional college.', 'mcrp' ) . '</p>';
		echo '<form method="post" action="' . esc_url( admin_url( 'admin-post.php' ) ) . '">';
		wp_nonce_field( 'mcrp_seed' );
		echo '<input type="hidden" name="action" value="mcrp_seed">';
		if ( $seeded ) {
			echo '<p><label><input type="checkbox" name="reset" value="1"> ' . esc_html__( 'Remove existing demo content first', 'mcrp' ) . '</label></p>';
		}
		submit_button( $seeded ? __( 'Re-import demo content', 'mcrp' ) : __( 'Import demo content', 'mcrp' ) );
		echo '</form></div>';
	}

	public static function handle_admin(): void {
		if ( ! current_user_can( 'manage_options' ) || ! check_admin_referer( 'mcrp_seed' ) ) {
			wp_die( esc_html__( 'Not allowed.', 'mcrp' ) );
		}
		if ( ! empty( $_POST['reset'] ) ) {
			self::reset();
		}
		self::run();
		wp_safe_redirect( admin_url( 'tools.php?page=mcrp-demo&done=1' ) );
		exit;
	}

	/**
	 * Delete all demo content.
	 */
	public static function reset(): int {
		$ids = get_posts(
			array(
				'post_type'      => 'any',
				'post_status'    => 'any',
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'meta_key'       => '_mcrp_demo',
			)
		);
		foreach ( $ids as $id ) {
			wp_delete_post( $id, true );
		}
		foreach ( array( 'Primary', 'Footer' ) as $menu ) {
			$obj = wp_get_nav_menu_object( 'Demo ' . $menu );
			if ( $obj ) {
				wp_delete_nav_menu( $obj->term_id );
			}
		}
		delete_option( 'mcrp_demo_seeded' );
		self::$log[] = sprintf( 'Removed %d demo items.', count( $ids ) );
		return count( $ids );
	}

	/**
	 * Set a field value through ACF when available (stores field key references), raw meta otherwise.
	 */
	private static function set( int $post_id, string $group, string $name, $value ): void {
		if ( is_array( $value ) && array_is_list( $value ) && $value && is_int( $value[0] ) ) {
			$value = array_map( 'strval', $value ); // Relationship format.
		}
		if ( function_exists( 'update_field' ) ) {
			update_field( ACF_Fields::key( $group, $name ), $value, $post_id );
		} else {
			update_post_meta( $post_id, $name, $value );
		}
	}

	private static function set_option( string $name, $value ): void {
		if ( function_exists( 'update_field' ) ) {
			update_field( ACF_Fields::key( 'options', $name ), $value, 'option' );
		} else {
			update_option( 'options_' . $name, $value );
		}
	}

	/**
	 * Create a post (or reuse one with the same slug).
	 */
	private static function post( string $type, string $title, array $args = array(), array $fields = array(), array $terms = array() ): int {
		$slug     = $args['post_name'] ?? sanitize_title( $title );
		$existing = get_page_by_path( $slug, OBJECT, $type );
		$data     = array_merge(
			array(
				'post_type'    => $type,
				'post_title'   => $title,
				'post_name'    => $slug,
				'post_status'  => 'publish',
				'post_content' => '',
			),
			$args
		);
		if ( $existing ) {
			$data['ID'] = $existing->ID;
		}
		$id = wp_insert_post( wp_slash( $data ) );
		update_post_meta( $id, '_mcrp_demo', 1 );

		foreach ( $fields as $name => $value ) {
			self::set( $id, 'page' === $type ? 'page' : $type, $name, $value );
		}
		foreach ( $terms as $taxonomy => $names ) {
			$term_ids = array();
			foreach ( (array) $names as $term_name ) {
				$term = term_exists( $term_name, $taxonomy );
				if ( ! $term ) {
					$term = wp_insert_term( $term_name, $taxonomy );
				}
				if ( ! is_wp_error( $term ) ) {
					$term_ids[] = (int) $term['term_id'];
				}
			}
			wp_set_object_terms( $id, $term_ids, $taxonomy );
		}

		self::$ids[ $type . ':' . $slug ] = $id;
		return $id;
	}

	private static function id( string $type, string $slug ): int {
		return self::$ids[ $type . ':' . $slug ] ?? 0;
	}

	/**
	 * Gutenberg paragraph helper.
	 */
	private static function p( string ...$paragraphs ): string {
		return implode(
			"\n\n",
			array_map( static fn( $t ) => "<!-- wp:paragraph -->\n<p>" . $t . "</p>\n<!-- /wp:paragraph -->", $paragraphs )
		);
	}

	/**
	 * Page content from a registered block pattern (provided by the theme).
	 */
	private static function pattern( string $name, string $fallback = '' ): string {
		$registry = \WP_Block_Patterns_Registry::get_instance();
		if ( $registry->is_registered( $name ) ) {
			return $registry->get_registered( $name )['content'];
		}
		return $fallback;
	}

	/**
	 * Run the full import.
	 */
	public static function run(): void {
		self::seed_terms();
		self::seed_campuses();
		self::seed_instructors();
		self::seed_programs();
		self::seed_testimonials();
		self::seed_faqs();
		self::seed_events();
		self::seed_options();
		self::seed_pages();
		self::seed_menus();

		update_option( 'mcrp_demo_seeded', time() );
		flush_rewrite_rules();
		self::$log[] = sprintf( 'Seeded %d items.', count( self::$ids ) );
	}

	private static function seed_terms(): void {
		$terms = array(
			'program_area'  => array( 'Business', 'Health Sciences', 'Technology', 'Community Services', 'Trades & Engineering', 'Hospitality & Culinary', 'Creative Arts' ),
			'credential'    => array( 'Certificate', 'Diploma', 'Advanced Diploma', 'Post-Graduate Certificate' ),
			'delivery_mode' => array( 'In-person', 'Hybrid', 'Online' ),
			'event_type'    => array( 'Open House', 'Info Session', 'Campus Tour', 'Webinar' ),
			'faq_category'  => array( 'Admissions', 'Tuition & Financial Aid', 'International Students', 'Student Life' ),
		);
		foreach ( $terms as $taxonomy => $names ) {
			foreach ( $names as $name ) {
				if ( ! term_exists( $name, $taxonomy ) ) {
					wp_insert_term( $name, $taxonomy );
				}
			}
		}
	}

	private static function seed_campuses(): void {
		$campuses = array(
			array( 'Downtown Campus', 'Our flagship campus in the heart of the city - steps from transit, start-ups and the arts district.', '200 King Street West', 'Northbridge', 'main', '#12355b', '(555) 010-2000', array( array( 'value' => '9,800', 'label' => 'Students' ), array( 'value' => '42', 'label' => 'Programs' ), array( 'value' => '18:1', 'label' => 'Student-to-faculty' ) ), array( 'Innovation Hub & Makerspace', 'Teaching Restaurant', 'Library & Learning Commons', 'Student Wellness Centre' ), '43.6487', '-79.3854' ),
			array( 'Lakeside Campus', 'A green, walkable campus on the waterfront with modern labs and on-site child care.', '45 Shoreline Drive', 'Lakeside', 'satellite', '#2a9d8f', '(555) 010-3000', array( array( 'value' => '4,200', 'label' => 'Students' ), array( 'value' => '21', 'label' => 'Programs' ), array( 'value' => '15:1', 'label' => 'Student-to-faculty' ) ), array( 'Early Learning Lab School', 'Athletics & Fitness Centre', 'Residence (380 beds)', 'Business Simulation Lab' ), '43.6205', '-79.4760' ),
			array( 'Riverside Campus', 'Home to our health sciences and skilled trades programs, with hospital-grade simulation suites.', '1 Millrace Road', 'Riverside', 'satellite', '#e4572e', '(555) 010-4000', array( array( 'value' => '3,600', 'label' => 'Students' ), array( 'value' => '16', 'label' => 'Programs' ), array( 'value' => '96%', 'label' => 'Grad employment' ) ), array( 'Clinical Simulation Centre', 'Trades & Apprenticeship Shops', 'Paramedic Training Ambulance Bay', 'Indigenous Student Centre' ), '43.7001', '-79.5163' ),
			array( 'Northbridge Online', 'Flexible, fully online programs with live virtual classes, dedicated success coaches and 24/7 tutoring.', 'Virtual', 'Anywhere', 'online', '#f4b942', '(555) 010-5000', array( array( 'value' => '6,100', 'label' => 'Online learners' ), array( 'value' => '28', 'label' => 'Programs' ), array( 'value' => '24/7', 'label' => 'Tutoring' ) ), array( 'Live virtual classrooms', 'Online success coaching', 'Digital library access', 'Virtual career fairs' ), '', '' ),
		);

		foreach ( $campuses as $i => list( $title, $excerpt, $address, $city, $type, $color, $phone, $stats, $amenities, $lat, $lng ) ) {
			$slug = sanitize_title( $title );
			self::post(
				'campus',
				$title,
				array(
					'post_excerpt' => $excerpt,
					'menu_order'   => $i,
					'post_content' => self::p( $excerpt, 'Visit us for a guided tour, meet faculty and current students, and see the spaces where you will learn. Our admissions advisors can help you choose a program, apply for financial aid and plan your move.' ),
				),
				array(
					'address'      => $address,
					'city'         => $city,
					'region'       => 'ON',
					'postal_code'  => 'online' === $type ? '' : 'N0B 1A' . $i,
					'campus_type'  => $type,
					'accent_color' => $color,
					'phone'        => $phone,
					'email'        => 'admissions.' . explode( '-', $slug )[0] . '@example.edu',
					'hours'        => "Mon-Fri: 8:30 am - 4:30 pm\nSat: 10:00 am - 2:00 pm (during intake season)",
					'lat'          => $lat,
					'lng'          => $lng,
					'stats'        => $stats,
					'amenities'    => array_map( static fn( $a ) => array( 'name' => $a, 'description' => '' ), $amenities ),
					'tour_url'     => 'https://example.edu/tour/' . $slug,
				)
			);
		}
	}

	private static function seed_instructors(): void {
		$people = array(
			array( 'Dr. Amara Okafor', 'Program Coordinator, Business', 'PhD, MBA', 'downtown-campus', 'Business', 'Digital marketing, Consumer behaviour, Brand strategy' ),
			array( 'Marcus Chen', 'Professor, Accounting', 'CPA, CA', 'lakeside-campus', 'Business', 'Financial reporting, Taxation, Audit' ),
			array( 'Priya Raman, RN', 'Professor, Practical Nursing', 'MScN, RN', 'riverside-campus', 'Health Sciences', 'Clinical practice, Gerontology, Simulation' ),
			array( 'Jordan Blake', 'Professor, Software Development', 'MSc Computer Science', 'downtown-campus', 'Technology', 'Web development, Cloud, DevOps' ),
			array( 'Sofia Martinez', 'Program Coordinator, Cybersecurity', 'CISSP, OSCP', 'northbridge-online', 'Technology', 'Network security, Ethical hacking, Incident response' ),
			array( 'Liam O\'Connor', 'Chef Professor, Culinary Management', 'Red Seal Chef', 'downtown-campus', 'Hospitality & Culinary', 'Classical French cuisine, Menu engineering' ),
			array( 'Hannah Kim', 'Professor, Early Childhood Education', 'RECE, MEd', 'lakeside-campus', 'Community Services', 'Play-based learning, Inclusion, Family studies' ),
			array( 'Daniel Mensah, P.Eng.', 'Professor, Electrical Engineering', 'P.Eng., MASc', 'riverside-campus', 'Trades & Engineering', 'Power systems, PLCs, Renewable energy' ),
		);
		foreach ( $people as list( $name, $position, $creds, $campus, $area, $expertise ) ) {
			$slug = sanitize_title( $name );
			self::post(
				'instructor',
				$name,
				array(
					'post_excerpt' => $position . ' with over a decade of industry and teaching experience.',
					'post_content' => self::p(
						$name . ' brings real-world industry experience into the classroom and has taught at Northbridge College for more than ten years.',
						'Outside the classroom they mentor student clubs, lead applied research projects with local employers and regularly speak at industry conferences.'
					),
				),
				array(
					'position'    => $position,
					'credentials' => $creds,
					'email'       => strtolower( preg_replace( '/[^a-z]/i', '', explode( ' ', str_replace( 'Dr. ', '', $name ) )[0] ) ) . '@example.edu',
					'campus'      => self::id( 'campus', $campus ),
					'expertise'   => $expertise,
					'linkedin'    => 'https://www.linkedin.com/in/example-' . $slug,
				),
				array( 'program_area' => $area )
			);
		}
	}

	private static function seed_programs(): void {
		$c = static fn( string ...$slugs ) => array_map( static fn( $s ) => self::id( 'campus', $s ), $slugs );
		$i = static fn( string ...$slugs ) => array_map( static fn( $s ) => self::id( 'instructor', sanitize_title( $s ) ), $slugs );

		$programs = array(
			array( 'Business Administration - Marketing', 'BMK', 'Business', 'Diploma', array( 'In-person', 'Hybrid' ), '2 years (4 semesters)', $c( 'downtown-campus', 'lakeside-campus' ), 'coop', array( 'Fall (September)', 'Winter (January)' ), 4850, 16900, $i( 'Dr. Amara Okafor' ), 'Build campaigns, analyse data and launch brands in a program designed with industry partners.', array( 'Marketing Coordinator', 'Digital Marketing Specialist', 'Brand Assistant', 'Social Media Manager' ), 91 ),
			array( 'Accounting', 'ACC', 'Business', 'Diploma', array( 'In-person', 'Online' ), '2 years (4 semesters)', $c( 'lakeside-campus', 'northbridge-online' ), 'coop', array( 'Fall (September)', 'Winter (January)' ), 4850, 16900, $i( 'Marcus Chen' ), 'Gain the technical accounting, tax and software skills employers need - with a pathway to the CPA designation.', array( 'Accounting Technician', 'Payroll Administrator', 'Junior Auditor', 'Tax Preparer' ), 89 ),
			array( 'Supply Chain & Logistics', 'SCL', 'Business', 'Certificate', array( 'Hybrid', 'Online' ), '1 year (2 semesters)', $c( 'lakeside-campus', 'northbridge-online' ), 'capstone', array( 'Fall (September)', 'Spring (May)' ), 4200, 15800, $i( 'Marcus Chen' ), 'Learn how goods move around the world - procurement, warehousing, transportation and analytics.', array( 'Logistics Coordinator', 'Purchasing Agent', 'Inventory Analyst' ), 87 ),
			array( 'Practical Nursing', 'PNR', 'Health Sciences', 'Diploma', array( 'In-person' ), '2 years (4 semesters)', $c( 'riverside-campus' ), 'placement', array( 'Fall (September)' ), 5200, 18500, $i( 'Priya Raman, RN' ), 'Prepare to write the national nursing exam with hands-on training in our clinical simulation centre and hospital placements.', array( 'Registered Practical Nurse', 'Long-term Care Nurse', 'Community Health Nurse' ), 97 ),
			array( 'Paramedic', 'PAR', 'Health Sciences', 'Diploma', array( 'In-person' ), '2 years (4 semesters)', $c( 'riverside-campus' ), 'placement', array( 'Fall (September)' ), 5400, 18900, $i( 'Priya Raman, RN' ), 'Train for life-saving careers with ambulance ride-alongs and realistic emergency simulations.', array( 'Primary Care Paramedic', 'Emergency Medical Responder', 'Industrial Paramedic' ), 94 ),
			array( 'Software Development', 'SWD', 'Technology', 'Advanced Diploma', array( 'In-person', 'Hybrid' ), '3 years (6 semesters)', $c( 'downtown-campus', 'northbridge-online' ), 'coop', array( 'Fall (September)', 'Winter (January)' ), 5100, 18200, $i( 'Jordan Blake' ), 'Design, build and ship full-stack web and mobile applications using modern tools and agile practices.', array( 'Full-stack Developer', 'Mobile App Developer', 'QA Automation Engineer', 'DevOps Engineer' ), 90 ),
			array( 'Cybersecurity', 'CYB', 'Technology', 'Post-Graduate Certificate', array( 'Online' ), '1 year (3 semesters)', $c( 'northbridge-online' ), 'capstone', array( 'Fall (September)', 'Winter (January)', 'Spring (May)' ), 6200, 19800, $i( 'Sofia Martinez' ), 'Defend networks and respond to threats in our virtual security operations centre.', array( 'Security Analyst', 'SOC Analyst', 'Penetration Tester' ), 92 ),
			array( 'Data Analytics', 'DAT', 'Technology', 'Post-Graduate Certificate', array( 'Hybrid', 'Online' ), '1 year (2 semesters)', $c( 'downtown-campus', 'northbridge-online' ), 'capstone', array( 'Fall (September)', 'Winter (January)' ), 6000, 19500, $i( 'Jordan Blake', 'Sofia Martinez' ), 'Turn raw data into decisions with Python, SQL, Power BI and machine-learning fundamentals.', array( 'Data Analyst', 'Business Intelligence Developer', 'Reporting Analyst' ), 88 ),
			array( 'Early Childhood Education', 'ECE', 'Community Services', 'Diploma', array( 'In-person', 'Hybrid' ), '2 years (4 semesters)', $c( 'lakeside-campus', 'northbridge-online' ), 'placement', array( 'Fall (September)', 'Winter (January)' ), 4700, 16500, $i( 'Hannah Kim' ), 'Learn to create inclusive, play-based learning environments - with placements in our on-site lab school.', array( 'Early Childhood Educator', 'Child Care Supervisor', 'Family Support Worker' ), 95 ),
			array( 'Electrical Engineering Technician', 'EET', 'Trades & Engineering', 'Diploma', array( 'In-person' ), '2 years (4 semesters)', $c( 'riverside-campus' ), 'coop', array( 'Fall (September)' ), 5000, 18000, $i( 'Daniel Mensah, P.Eng.' ), 'Install, test and troubleshoot electrical systems, PLCs and renewable energy installations.', array( 'Electrical Technician', 'Controls Technician', 'Field Service Technician' ), 93 ),
			array( 'Culinary Management', 'CUL', 'Hospitality & Culinary', 'Diploma', array( 'In-person' ), '2 years (4 semesters)', $c( 'downtown-campus' ), 'placement', array( 'Fall (September)', 'Winter (January)' ), 5600, 18700, $i( 'Liam O\'Connor' ), 'Cook, plan and lead in our public teaching restaurant while learning the business of food.', array( 'Line Cook', 'Sous Chef', 'Catering Manager', 'Food Entrepreneur' ), 90 ),
			array( 'Graphic Design', 'GRD', 'Creative Arts', 'Advanced Diploma', array( 'In-person', 'Hybrid' ), '3 years (6 semesters)', $c( 'downtown-campus' ), 'placement', array( 'Fall (September)' ), 5300, 18400, $i( 'Jordan Blake' ), 'Develop a professional portfolio in branding, UX/UI, motion and print design.', array( 'Graphic Designer', 'UX/UI Designer', 'Art Director', 'Motion Designer' ), 86 ),
		);

		foreach ( $programs as $n => list( $title, $code, $area, $credential, $delivery, $duration, $campuses, $wil, $intakes, $dom, $intl, $instructors, $summary, $careers, $rate ) ) {
			$courses = array();
			for ( $t = 1; $t <= 2; $t++ ) {
				foreach ( array( 'Foundations of ' . strtok( $title, ' -' ), 'Professional Communication', 'Applied Project ' . $t ) as $k => $course ) {
					$courses[] = array(
						'term'    => 'Term ' . $t,
						'code'    => $code . $t . '0' . ( $k + 1 ),
						'name'    => 1 === $t ? $course : str_replace( 'Foundations of', 'Advanced', $course ),
						'credits' => 3,
					);
				}
			}

			self::post(
				'program',
				$title,
				array(
					'post_excerpt' => $summary,
					'menu_order'   => $n,
					'post_content' => self::p(
						$summary,
						'You will learn from faculty who are active in the industry, work on real projects for real clients and graduate with a portfolio of experience that sets you apart. Small class sizes mean you get the support you need from day one.',
						'Graduates may be eligible for credit transfer into related degree programs at partner universities.'
					),
				),
				array(
					'program_code'               => $code,
					'duration'                   => $duration,
					'work_integrated'            => $wil,
					'intakes'                    => array_map( static fn( $l ) => strtolower( strtok( $l, ' ' ) ), $intakes ),
					'tuition_domestic'           => $dom,
					'tuition_international'      => $intl,
					'campuses'                   => $campuses,
					'instructors'                => $instructors,
					'highlights'                 => array(
						array( 'text' => 'Small classes taught by industry experts' ),
						array( 'text' => 'coop' === $wil ? 'Paid co-op work term' : 'Hands-on ' . ( 'placement' === $wil ? 'field placement' : 'industry capstone project' ) ),
						array( 'text' => $rate . '% of graduates employed within 6 months' ),
						array( 'text' => 'Pathways to university degrees' ),
					),
					'courses'                    => $courses,
					'admission_requirements'     => '<ul><li>Ontario Secondary School Diploma (OSSD) or equivalent, or mature student status</li><li>Grade 12 English (C or U)</li><li>Grade 11 or 12 Mathematics (C, M or U)</li></ul>',
					'international_requirements' => '<ul><li>Secondary school diploma equivalent</li><li>IELTS Academic 6.0 overall (no band below 5.5) or equivalent</li></ul>',
					'career_outcomes'            => array_map( static fn( $j ) => array( 'title' => $j, 'salary' => '' ), $careers ),
					'employment_rate'            => $rate,
				),
				array(
					'program_area'  => $area,
					'credential'    => $credential,
					'delivery_mode' => $delivery,
				)
			);
		}
	}

	private static function seed_testimonials(): void {
		$items = array(
			array( 'Aisha Rahman', 'The co-op term changed everything. I was hired by my co-op employer two weeks before graduation and I now lead their social campaigns.', 'Marketing Coordinator, Lumen Agency', 2024, 'domestic', 'business-administration-marketing', 'downtown-campus' ),
			array( 'Carlos Mendes', 'As an international student I was nervous, but the advisors helped me with everything from housing to my study permit. The simulation labs are incredible.', 'Practical Nurse, Riverside General', 2023, 'international', 'practical-nursing', 'riverside-campus' ),
			array( 'Emily Tran', 'I studied fully online while working part-time. Live classes kept me connected and my capstone became my first freelance client.', 'Junior Data Analyst, Northwind Retail', 2025, 'mature', 'data-analytics', 'northbridge-online' ),
			array( 'Noah Williams', 'Our instructors were developers, not just teachers. I graduated with three apps in my portfolio and a job offer.', 'Full-stack Developer, Brightpath', 2024, 'domestic', 'software-development', 'downtown-campus' ),
			array( 'Fatima Al-Sayed', 'The lab school placement gave me confidence from my very first semester. I knew this was the right career for me.', 'Early Childhood Educator', 2023, 'domestic', 'early-childhood-education', 'lakeside-campus' ),
			array( 'Ethan Park', 'Working the line in the teaching restaurant prepared me for the pace of a real kitchen. I became a sous chef within a year.', 'Sous Chef, Harbour Bistro', 2022, 'domestic', 'culinary-management', 'downtown-campus' ),
		);
		foreach ( $items as list( $name, $quote, $role, $year, $type, $program, $campus ) ) {
			self::post(
				'testimonial',
				$name,
				array(),
				array(
					'quote'        => $quote,
					'role'         => $role,
					'grad_year'    => $year,
					'student_type' => $type,
					'program'      => self::id( 'program', $program ),
					'campus'       => self::id( 'campus', $campus ),
				)
			);
		}
	}

	private static function seed_faqs(): void {
		$faqs = array(
			array( 'How do I apply?', 'Apply online through our application portal. Choose your program, campus and intake, upload your transcripts and pay the application fee. Most applicants receive a decision within 2-3 weeks.', 'Admissions' ),
			array( 'When are the application deadlines?', 'We accept applications on a rolling basis until programs are full. For the best chance of admission to high-demand programs such as Nursing and Paramedic, apply by February 1 for the Fall intake.', 'Admissions' ),
			array( 'Can I apply as a mature student?', 'Yes. If you are 19 or older and do not have a high school diploma, you may be admitted as a mature student by completing an academic skills assessment.', 'Admissions' ),
			array( 'How much does tuition cost?', 'Tuition varies by program. Each program page lists domestic and international tuition per year. Additional ancillary fees cover services such as transit passes, health plans and athletics.', 'Tuition & Financial Aid' ),
			array( 'What scholarships and financial aid are available?', 'Northbridge awards over $3 million in scholarships and bursaries every year. Students may also be eligible for government student loans and grants. Apply for awards through your student portal.', 'Tuition & Financial Aid' ),
			array( 'What English language test scores do I need?', 'Most diploma programs require IELTS Academic 6.0 overall with no band below 5.5, or an equivalent TOEFL, PTE or Duolingo score. Post-graduate programs require IELTS 6.5.', 'International Students' ),
			array( 'Can I work while I study?', 'Eligible international students can work off-campus during academic sessions and full-time during scheduled breaks, subject to current immigration regulations. Co-op terms are full-time.', 'International Students' ),
			array( 'Is housing available?', 'Our Lakeside residence offers 380 furnished rooms. Our housing office also maintains a list of off-campus rentals near every campus.', 'Student Life' ),
			array( 'Can I switch campuses?', 'Many programs are offered at more than one campus. Speak with your program coordinator about transferring between campuses or to an online section.', 'Student Life' ),
		);
		foreach ( $faqs as $n => list( $q, $a, $cat ) ) {
			self::post( 'faq', $q, array( 'post_content' => self::p( $a ), 'menu_order' => $n ), array(), array( 'faq_category' => $cat ) );
		}
	}

	private static function seed_events(): void {
		$at = static fn( int $days, string $time ) => wp_date( 'Y-m-d', strtotime( "+{$days} days" ) ) . ' ' . $time;

		$events = array(
			array( 'Fall Open House', 14, '10:00:00', '14:00:00', 'Open House', 'downtown-campus', false, true, array( 'business-administration-marketing', 'software-development', 'culinary-management', 'graphic-design' ) ),
			array( 'Health Sciences Info Session', 9, '18:00:00', '19:30:00', 'Info Session', 'riverside-campus', false, false, array( 'practical-nursing', 'paramedic' ) ),
			array( 'Lakeside Campus Tour', 6, '13:00:00', '14:00:00', 'Campus Tour', 'lakeside-campus', false, false, array( 'early-childhood-education', 'accounting' ) ),
			array( 'Studying Online: Live Q&A Webinar', 4, '12:00:00', '13:00:00', 'Webinar', '', true, false, array( 'cybersecurity', 'data-analytics' ) ),
			array( 'International Student Webinar', 21, '09:00:00', '10:00:00', 'Webinar', '', true, true, array() ),
			array( 'Riverside Open House', 28, '10:00:00', '14:00:00', 'Open House', 'riverside-campus', false, true, array( 'practical-nursing', 'paramedic', 'electrical-engineering-technician' ) ),
		);

		foreach ( $events as list( $title, $days, $start, $end, $type, $campus, $virtual, $featured, $programs ) ) {
			self::post(
				'event',
				$title,
				array(
					'post_excerpt' => 'Meet faculty, tour labs, talk to admissions and financial aid advisors, and get your questions answered.',
					'post_content' => self::p( 'Explore programs, meet faculty and current students, and get your admissions and financial aid questions answered. Attendees receive an application fee waiver.' ),
				),
				array(
					'start_datetime'   => $at( $days, $start ),
					'end_datetime'     => $at( $days, $end ),
					'is_virtual'       => $virtual ? 1 : 0,
					'campus'           => $campus ? self::id( 'campus', $campus ) : '',
					'location'         => $virtual ? 'Zoom - link sent after registration' : 'Main Atrium',
					'registration_url' => 'https://example.edu/register/' . sanitize_title( $title ),
					'capacity'         => $virtual ? 500 : 250,
					'featured'         => $featured ? 1 : 0,
					'programs'         => array_map( static fn( $p ) => self::id( 'program', $p ), $programs ),
				),
				array( 'event_type' => $type )
			);
		}
	}

	private static function seed_options(): void {
		$opts = array(
			'apply_url'       => 'https://apply.example.edu',
			'tour_url'        => home_url( '/campuses/' ),
			'phone'           => '1-800-555-0199',
			'email'           => 'admissions@example.edu',
			'currency_symbol' => '$',
			'lead_recipients' => get_option( 'admin_email' ),
			'consent_text'    => 'I agree to receive information about programs, events and admissions from Northbridge College. I can unsubscribe at any time.',
			'success_message' => 'Thanks! An admissions advisor will be in touch within one business day.',
			'announcement'    => 'Applications for Fall 2027 are open - apply by February 1 for priority consideration.',
			'footer_tagline'  => 'Career-focused education across three campuses and online.',
			'social'          => array(
				array( 'network' => 'instagram', 'url' => 'https://instagram.com/example' ),
				array( 'network' => 'facebook', 'url' => 'https://facebook.com/example' ),
				array( 'network' => 'linkedin', 'url' => 'https://linkedin.com/school/example' ),
				array( 'network' => 'youtube', 'url' => 'https://youtube.com/@example' ),
			),
		);
		foreach ( $opts as $name => $value ) {
			self::set_option( $name, $value );
		}
		update_option( 'blogname', 'Northbridge College' );
		update_option( 'blogdescription', 'Career-ready programs across three campuses and online' );
	}

	private static function seed_pages(): void {
		$home = self::post( 'page', 'Home', array( 'post_name' => 'home', 'post_content' => self::pattern( 'mcrp-theme/home', self::p( 'Welcome to Northbridge College.' ) ) ) );
		self::post( 'page', 'Admissions', array( 'post_content' => self::pattern( 'mcrp-theme/admissions', self::p( 'How to apply.' ) ) ) );
		self::post( 'page', 'International Students', array( 'post_content' => self::pattern( 'mcrp-theme/international', self::p( 'Study with us from anywhere in the world.' ) ) ) );
		$landing = self::post( 'page', 'Fall Open House', array( 'post_name' => 'open-house', 'post_content' => self::pattern( 'mcrp-theme/campaign-landing', self::p( 'Join us at our open house.' ) ) ) );
		update_post_meta( $landing, '_wp_page_template', 'page-templates/template-landing.php' );

		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home );
	}

	private static function seed_menus(): void {
		$menus = array(
			'primary' => array(
				'Demo Primary',
				array(
					array( 'Programs', get_post_type_archive_link( 'program' ) ),
					array( 'Campuses', get_post_type_archive_link( 'campus' ) ),
					array( 'Admissions', get_permalink( self::id( 'page', 'admissions' ) ) ),
					array( 'International', get_permalink( self::id( 'page', 'international-students' ) ) ),
					array( 'Events', get_post_type_archive_link( 'event' ) ),
					array( 'Faculty', get_post_type_archive_link( 'instructor' ) ),
				),
			),
			'footer'  => array(
				'Demo Footer',
				array(
					array( 'Program Finder', get_post_type_archive_link( 'program' ) ),
					array( 'Admissions', get_permalink( self::id( 'page', 'admissions' ) ) ),
					array( 'Open House', get_permalink( self::id( 'page', 'open-house' ) ) ),
					array( 'Faculty Directory', get_post_type_archive_link( 'instructor' ) ),
					array( 'Upcoming Events', get_post_type_archive_link( 'event' ) ),
				),
			),
		);

		$locations = get_theme_mod( 'nav_menu_locations', array() );
		foreach ( $menus as $location => list( $name, $items ) ) {
			$menu = wp_get_nav_menu_object( $name );
			if ( $menu ) {
				wp_delete_nav_menu( $menu->term_id );
			}
			$menu_id = wp_create_nav_menu( $name );
			foreach ( $items as list( $title, $url ) ) {
				wp_update_nav_menu_item(
					$menu_id,
					0,
					array(
						'menu-item-title'  => $title,
						'menu-item-url'    => $url,
						'menu-item-status' => 'publish',
						'menu-item-type'   => 'custom',
					)
				);
			}
			$locations[ $location ] = $menu_id;
		}
		set_theme_mod( 'nav_menu_locations', $locations );
	}
}
