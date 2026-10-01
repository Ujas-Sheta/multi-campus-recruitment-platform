<?php
/**
 * REST API: program search (powers the Program Finder) and inquiry submission.
 *
 * GET  /wp-json/mcrp/v1/programs?q=&area=&credential=&delivery=&campus=&page=&per_page=
 * POST /wp-json/mcrp/v1/inquiries
 *
 * @package MCRP_Core
 */

namespace MCRP;

defined( 'ABSPATH' ) || exit;

/**
 * REST routes.
 */
class REST {

	const NS = 'mcrp/v1';

	public static function init(): void {
		add_action( 'rest_api_init', array( __CLASS__, 'routes' ) );
	}

	public static function routes(): void {
		register_rest_route(
			self::NS,
			'/programs',
			array(
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => array( __CLASS__, 'programs' ),
				'permission_callback' => '__return_true',
				'args'                => array(
					'q'          => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_text_field' ),
					'area'       => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_title' ),
					'credential' => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_title' ),
					'delivery'   => array( 'type' => 'string', 'sanitize_callback' => 'sanitize_title' ),
					'campus'     => array( 'type' => 'integer', 'sanitize_callback' => 'absint' ),
					'page'       => array( 'type' => 'integer', 'default' => 1, 'minimum' => 1 ),
					'per_page'   => array( 'type' => 'integer', 'default' => 12, 'minimum' => 1, 'maximum' => 48 ),
				),
			)
		);

		register_rest_route(
			self::NS,
			'/inquiries',
			array(
				'methods'             => \WP_REST_Server::CREATABLE,
				'callback'            => array( __CLASS__, 'inquiry' ),
				'permission_callback' => '__return_true',
			)
		);
	}

	/**
	 * Program search.
	 */
	public static function programs( \WP_REST_Request $request ): \WP_REST_Response {
		$query = new \WP_Query(
			Queries::program_args(
				array(
					'search'     => $request['q'],
					'area'       => $request['area'],
					'credential' => $request['credential'],
					'delivery'   => $request['delivery'],
					'campus'     => $request['campus'],
					'page'       => $request['page'],
					'per_page'   => $request['per_page'],
				)
			)
		);

		$items = array();
		$html  = '';
		foreach ( $query->posts as $post ) {
			$items[] = array(
				'id'         => $post->ID,
				'title'      => get_the_title( $post ),
				'url'        => get_permalink( $post ),
				'excerpt'    => get_the_excerpt( $post ),
				'credential' => mcrp_term_names( $post->ID, 'credential' ),
				'area'       => mcrp_term_names( $post->ID, 'program_area' ),
				'delivery'   => mcrp_term_names( $post->ID, 'delivery_mode' ),
				'duration'   => mcrp_get( 'duration', $post->ID, '' ),
				'campuses'   => wp_list_pluck( Queries::campuses_for_program( $post->ID ), 'post_title' ),
			);
			$html .= self::render_card( $post );
		}

		$response = new \WP_REST_Response(
			array(
				'total' => (int) $query->found_posts,
				'pages' => (int) $query->max_num_pages,
				'page'  => (int) $request['page'],
				'items' => $items,
				'html'  => $html,
			)
		);
		$response->header( 'Cache-Control', 'public, max-age=300' );
		return $response;
	}

	/**
	 * Render a program card using the theme's template part when available.
	 */
	public static function render_card( \WP_Post $post ): string {
		ob_start();
		if ( locate_template( 'template-parts/cards/program.php' ) ) {
			get_template_part( 'template-parts/cards/program', null, array( 'post' => $post ) );
		} else {
			printf(
				'<article class="program-card"><h3><a href="%s">%s</a></h3><p>%s</p></article>',
				esc_url( get_permalink( $post ) ),
				esc_html( get_the_title( $post ) ),
				esc_html( get_the_excerpt( $post ) )
			);
		}
		return (string) ob_get_clean();
	}

	/**
	 * Inquiry submission.
	 *
	 * @return \WP_REST_Response|\WP_Error
	 */
	public static function inquiry( \WP_REST_Request $request ) {
		$params = $request->get_json_params() ?: $request->get_body_params();
		$result = Leads::create( (array) $params );
		if ( is_wp_error( $result ) ) {
			return $result;
		}
		return new \WP_REST_Response(
			array(
				'success' => true,
				'message' => mcrp_option( 'success_message', __( 'Thanks! An admissions advisor will be in touch within one business day.', 'mcrp' ) ),
			),
			201
		);
	}
}
