<?php
/**
 * Program archive & taxonomy archives - the Program Finder.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
get_header();

$locked = array();
$title  = __( 'Find your program', 'mcrp' );
$intro  = __( 'Explore career-focused certificates, diplomas and post-graduate programs across all of our campuses and online.', 'mcrp' );

$map = array( 'program_area' => 'area', 'credential' => 'credential', 'delivery_mode' => 'delivery' );
foreach ( $map as $tax => $key ) {
	if ( is_tax( $tax ) ) {
		$term           = get_queried_object();
		$locked[ $key ] = $term->slug;
		$title          = $term->name;
		$intro          = $term->description ?: sprintf( /* translators: %s: term */ __( 'Explore our %s programs.', 'mcrp' ), $term->name );
	}
}

mcrp_page_hero( array( 'title' => $title, 'intro' => $intro ) );
?>
<section class="section section--tight-top">
	<div class="container">
		<?php get_template_part( 'template-parts/components/program-finder', null, array( 'locked' => $locked, 'id' => 'program-finder' ) ); ?>
	</div>
</section>
<?php
get_footer();
