<?php
/**
 * Single instructor profile.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

while ( have_posts() ) :
	the_post();
	$pid       = get_the_ID();
	$campus   = mcrp_get( 'campus', $pid );
	$programs = mcrp_q( 'programs_for_instructor', $pid );
	$tags     = array_filter( array_map( 'trim', explode( ',', (string) mcrp_get( 'expertise', $pid, '' ) ) ) );

	$meta = '';
	if ( $creds = mcrp_get( 'credentials', $pid ) ) {
		$meta .= '<span class="badge badge--sun">' . esc_html( $creds ) . '</span>';
	}
	if ( $campus ) {
		$meta .= '<a class="badge badge--light" href="' . esc_url( get_permalink( $campus ) ) . '">' . esc_html( get_the_title( $campus ) ) . '</a>';
	}
	$actions = '';
	if ( $email = mcrp_get( 'email', $pid ) ) {
		$actions .= mcrp_button( array( 'title' => __( 'Email', 'mcrp' ), 'url' => 'mailto:' . $email ), 'primary', 'mail' );
	}
	if ( $linkedin = mcrp_get( 'linkedin', $pid ) ) {
		$actions .= mcrp_button( array( 'title' => 'LinkedIn', 'url' => $linkedin, 'target' => '_blank' ), 'white', 'linkedin' );
	}

	mcrp_page_hero(
		array(
			'title'   => get_the_title(),
			'intro'   => mcrp_get( 'position', $pid, '' ),
			'meta'    => $meta,
			'actions' => $actions,
			'media'   => mcrp_media( get_post(), 'mcrp-square', 'page-hero__image page-hero__image--portrait' ),
		)
	);
	?>
	<div class="container layout-sidebar section">
		<div class="layout-sidebar__main">
			<h2><?php esc_html_e( 'Biography', 'mcrp' ); ?></h2>
			<div class="entry-content entry-content--flush"><?php the_content(); ?></div>
			<?php if ( $tags ) : ?>
				<h3><?php esc_html_e( 'Areas of expertise', 'mcrp' ); ?></h3>
				<ul class="pill-list">
					<?php foreach ( $tags as $tag ) : ?>
						<li><?php echo esc_html( $tag ); ?></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<aside class="layout-sidebar__aside">
			<?php if ( $programs ) : ?>
				<div class="contact-card">
					<h2 class="contact-card__title"><?php esc_html_e( 'Teaches in', 'mcrp' ); ?></h2>
					<ul class="link-list">
						<?php foreach ( $programs as $program ) : ?>
							<li><a href="<?php echo esc_url( get_permalink( $program ) ); ?>"><?php echo esc_html( $program->post_title ); ?><?php echo mcrp_icon( 'arrow-right' ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>
		</aside>
	</div>
	<?php
endwhile;

get_footer();
