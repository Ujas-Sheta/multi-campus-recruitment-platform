<?php
/**
 * Admin experience: dependency notice, dashboard widget, list-table columns.
 *
 * @package MCRP_Core
 */

namespace MCRP;

defined( 'ABSPATH' ) || exit;

/**
 * Admin.
 */
class Admin {

	public static function init(): void {
		add_action( 'admin_notices', array( __CLASS__, 'acf_notice' ) );
		add_action( 'wp_dashboard_setup', array( __CLASS__, 'dashboard_widget' ) );
		add_filter( 'manage_program_posts_columns', array( __CLASS__, 'program_columns' ) );
		add_action( 'manage_program_posts_custom_column', array( __CLASS__, 'program_column_content' ), 10, 2 );
		add_filter( 'manage_testimonial_posts_columns', array( __CLASS__, 'testimonial_columns' ) );
		add_action( 'manage_testimonial_posts_custom_column', array( __CLASS__, 'testimonial_column_content' ), 10, 2 );
	}

	public static function acf_notice(): void {
		if ( mcrp_acf_pro_active() || ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'Multi-Campus Recruitment Platform:', 'mcrp' ) . '</strong> ' .
			esc_html__( 'ACF Pro is required for custom fields, the options page and the marketing blocks. Install and activate Advanced Custom Fields PRO.', 'mcrp' ) . '</p></div>';
	}

	public static function dashboard_widget(): void {
		wp_add_dashboard_widget(
			'mcrp_glance',
			__( 'Recruitment at a glance', 'mcrp' ),
			array( __CLASS__, 'render_dashboard_widget' )
		);
	}

	public static function render_dashboard_widget(): void {
		$counts = array(
			__( 'Programs', 'mcrp' )        => array( (int) wp_count_posts( 'program' )->publish, 'program' ),
			__( 'Campuses', 'mcrp' )        => array( (int) wp_count_posts( 'campus' )->publish, 'campus' ),
			__( 'Instructors', 'mcrp' )     => array( (int) wp_count_posts( 'instructor' )->publish, 'instructor' ),
			__( 'Upcoming events', 'mcrp' ) => array( count( Queries::upcoming_events( array( 'limit' => 100 ) ) ), 'event' ),
		);

		$recent_leads = get_posts(
			array(
				'post_type'      => Leads::POST_TYPE,
				'posts_per_page' => -1,
				'fields'         => 'ids',
				'date_query'     => array( array( 'after' => '30 days ago' ) ),
			)
		);
		$sources = array();
		foreach ( $recent_leads as $id ) {
			$src             = get_post_meta( $id, 'utm_source', true ) ?: __( 'direct', 'mcrp' );
			$sources[ $src ] = ( $sources[ $src ] ?? 0 ) + 1;
		}
		arsort( $sources );

		echo '<ul style="display:grid;grid-template-columns:repeat(2,1fr);gap:8px;margin:0 0 12px">';
		foreach ( $counts as $label => list( $n, $type ) ) {
			printf(
				'<li style="background:#f6f7f7;padding:10px;border-radius:6px;margin:0"><a href="%s" style="text-decoration:none"><strong style="font-size:20px;display:block">%d</strong>%s</a></li>',
				esc_url( admin_url( 'edit.php?post_type=' . $type ) ),
				(int) $n,
				esc_html( $label )
			);
		}
		echo '</ul>';

		/* translators: %d: number of inquiries */
		echo '<h3>' . esc_html( sprintf( __( '%d inquiries in the last 30 days', 'mcrp' ), count( $recent_leads ) ) ) . '</h3>';
		if ( $sources ) {
			echo '<table class="widefat striped"><tbody>';
			foreach ( array_slice( $sources, 0, 6, true ) as $src => $n ) {
				printf( '<tr><td>%s</td><td style="text-align:right">%d</td></tr>', esc_html( $src ), (int) $n );
			}
			echo '</tbody></table>';
		}
	}

	public static function program_columns( array $columns ): array {
		$date = $columns['date'];
		unset( $columns['date'] );
		$columns['campuses'] = __( 'Campuses', 'mcrp' );
		$columns['duration'] = __( 'Duration', 'mcrp' );
		$columns['date']     = $date;
		return $columns;
	}

	public static function program_column_content( string $column, int $post_id ): void {
		if ( 'campuses' === $column ) {
			echo esc_html( implode( ', ', wp_list_pluck( Queries::campuses_for_program( $post_id ), 'post_title' ) ) ?: '-' );
		}
		if ( 'duration' === $column ) {
			echo esc_html( mcrp_get( 'duration', $post_id, '-' ) );
		}
	}

	public static function testimonial_columns( array $columns ): array {
		$date = $columns['date'];
		unset( $columns['date'] );
		$columns['program'] = __( 'Program', 'mcrp' );
		$columns['campus']  = __( 'Campus', 'mcrp' );
		$columns['date']    = $date;
		return $columns;
	}

	public static function testimonial_column_content( string $column, int $post_id ): void {
		if ( in_array( $column, array( 'program', 'campus' ), true ) ) {
			$id = mcrp_get( $column, $post_id );
			echo esc_html( $id ? get_the_title( $id ) : '-' );
		}
	}
}
