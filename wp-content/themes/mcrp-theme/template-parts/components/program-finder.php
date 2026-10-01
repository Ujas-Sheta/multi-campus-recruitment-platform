<?php
/**
 * Program finder.
 * Normal GET form so it works without JS. program-finder.js loads results
 * from /wp-json/mcrp/v1/programs and updates the URL.
 *
 * @package MCRP_Theme
 *
 * @var array $args locked (filters that can't be changed), show_filters, per_page, id.
 */

defined( 'ABSPATH' ) || exit;

$args = wp_parse_args(
	$args,
	array(
		'locked'       => array(),
		'show_filters' => true,
		'per_page'     => 12,
		'id'           => 'program-finder',
	)
);

$locked  = array_filter( (array) $args['locked'] );
$request = class_exists( '\MCRP\Queries' ) ? \MCRP\Queries::filters_from_request() : array();
$current = array_merge(
	array( 'search' => '', 'area' => '', 'credential' => '', 'delivery' => '', 'campus' => 0 ),
	$args['show_filters'] ? array_filter( $request ) : array(),
	$locked
);
$page    = max( 1, absint( $_GET['pg'] ?? 1 ) );

$query = class_exists( '\MCRP\Queries' )
	? new WP_Query( \MCRP\Queries::program_args( array_merge( $current, array( 'per_page' => $args['per_page'], 'page' => $page ) ) ) )
	: new WP_Query( array( 'post_type' => 'program', 'posts_per_page' => $args['per_page'] ) );

$selects = array(
	'area'       => array( __( 'Area of study', 'mcrp' ), 'program_area' ),
	'credential' => array( __( 'Credential', 'mcrp' ), 'credential' ),
	'delivery'   => array( __( 'Delivery', 'mcrp' ), 'delivery_mode' ),
);
$campuses = get_posts( array( 'post_type' => 'campus', 'posts_per_page' => -1, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
$total    = (int) $query->found_posts;
?>
<div class="program-finder" id="<?php echo esc_attr( $args['id'] ); ?>" data-program-finder data-per-page="<?php echo (int) $args['per_page']; ?>">
	<?php if ( $args['show_filters'] ) : ?>
		<form class="program-finder__filters" method="get" action="<?php echo esc_url( get_post_type_archive_link( 'program' ) ?: '' ); ?>" role="search" data-finder-form>
			<div class="field field--search">
				<label for="<?php echo esc_attr( $args['id'] ); ?>-q"><?php esc_html_e( 'Keyword', 'mcrp' ); ?></label>
				<div class="field__control">
					<?php echo mcrp_icon( 'search' ); ?>
					<input id="<?php echo esc_attr( $args['id'] ); ?>-q" type="search" name="q" value="<?php echo esc_attr( $current['search'] ); ?>" placeholder="<?php esc_attr_e( 'e.g. nursing, marketing, code...', 'mcrp' ); ?>" autocomplete="off">
				</div>
			</div>

			<?php foreach ( $selects as $param => list( $label, $taxonomy ) ) : ?>
				<?php if ( isset( $locked[ $param ] ) ) : ?>
					<input type="hidden" name="<?php echo esc_attr( $param ); ?>" value="<?php echo esc_attr( $locked[ $param ] ); ?>">
					<?php continue; ?>
				<?php endif; ?>
				<?php $terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => true ) ); ?>
				<div class="field">
					<label for="<?php echo esc_attr( $args['id'] . '-' . $param ); ?>"><?php echo esc_html( $label ); ?></label>
					<select id="<?php echo esc_attr( $args['id'] . '-' . $param ); ?>" name="<?php echo esc_attr( $param ); ?>">
						<option value=""><?php esc_html_e( 'All', 'mcrp' ); ?></option>
						<?php foreach ( is_wp_error( $terms ) ? array() : $terms as $term ) : ?>
							<option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $current[ $param ], $term->slug ); ?>><?php echo esc_html( $term->name ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endforeach; ?>

			<?php if ( isset( $locked['campus'] ) ) : ?>
				<input type="hidden" name="campus" value="<?php echo (int) $locked['campus']; ?>">
			<?php elseif ( $campuses ) : ?>
				<div class="field">
					<label for="<?php echo esc_attr( $args['id'] ); ?>-campus"><?php esc_html_e( 'Campus', 'mcrp' ); ?></label>
					<select id="<?php echo esc_attr( $args['id'] ); ?>-campus" name="campus">
						<option value=""><?php esc_html_e( 'All campuses', 'mcrp' ); ?></option>
						<?php foreach ( $campuses as $campus ) : ?>
							<option value="<?php echo (int) $campus->ID; ?>" <?php selected( (int) $current['campus'], $campus->ID ); ?>><?php echo esc_html( $campus->post_title ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			<?php endif; ?>

			<div class="program-finder__submit">
				<button class="btn btn--primary" type="submit"><?php esc_html_e( 'Search', 'mcrp' ); ?></button>
				<button class="btn btn--ghost" type="reset" data-finder-reset><?php esc_html_e( 'Clear', 'mcrp' ); ?></button>
			</div>
		</form>
	<?php endif; ?>

	<p class="program-finder__count" aria-live="polite" data-finder-count>
		<?php
		echo esc_html(
			0 === $total ? __( 'No programs match your filters. Try removing a filter.', 'mcrp' ) :
			/* translators: %d: number of programs */
			sprintf( _n( '%d program found', '%d programs found', $total, 'mcrp' ), $total )
		);
		?>
	</p>

	<div class="card-grid card-grid--3 program-finder__results" data-finder-results>
		<?php
		foreach ( $query->posts as $program ) {
			get_template_part( 'template-parts/cards/program', null, array( 'post' => $program ) );
		}
		?>
	</div>

	<?php if ( $query->max_num_pages > 1 ) : ?>
		<nav class="program-finder__pager" data-finder-pager data-pages="<?php echo (int) $query->max_num_pages; ?>" data-page="<?php echo (int) $page; ?>">
			<?php if ( $page < $query->max_num_pages ) : ?>
				<a class="btn btn--outline" href="<?php echo esc_url( add_query_arg( 'pg', $page + 1 ) ); ?>" data-finder-more><?php esc_html_e( 'Load more programs', 'mcrp' ); ?></a>
			<?php endif; ?>
		</nav>
	<?php endif; ?>
</div>
