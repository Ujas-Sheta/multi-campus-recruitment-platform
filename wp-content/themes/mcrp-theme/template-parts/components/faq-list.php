<?php
/**
 * Accessible FAQ accordion with FAQPage structured data.
 *
 * @package MCRP_Theme
 *
 * @var array $args posts (WP_Post[]), schema (bool), id, open_first (bool).
 */

defined( 'ABSPATH' ) || exit;

$args  = wp_parse_args( $args, array( 'posts' => array(), 'schema' => true, 'id' => 'faq', 'open_first' => false ) );
$faq_items = (array) $args['posts'];
if ( ! $faq_items ) {
	return;
}
$schema = array();
?>
<div class="accordion" data-accordion>
	<?php
	foreach ( $faq_items as $i => $faq ) :
		$answer   = apply_filters( 'the_content', $faq->post_content );
		$panel_id = $args['id'] . '-panel-' . $faq->ID;
		$open     = $args['open_first'] && 0 === $i;
		$schema[] = array(
			'@type'          => 'Question',
			'name'           => get_the_title( $faq ),
			'acceptedAnswer' => array( '@type' => 'Answer', 'text' => wp_strip_all_tags( $answer ) ),
		);
		?>
		<div class="accordion__item">
			<h3 class="accordion__heading">
				<button class="accordion__trigger" type="button" aria-expanded="<?php echo $open ? 'true' : 'false'; ?>" aria-controls="<?php echo esc_attr( $panel_id ); ?>" id="<?php echo esc_attr( $panel_id ); ?>-btn">
					<span><?php echo esc_html( get_the_title( $faq ) ); ?></span>
					<?php echo mcrp_icon( 'chevron-down', 'accordion__icon' ); ?>
				</button>
			</h3>
			<div class="accordion__panel" id="<?php echo esc_attr( $panel_id ); ?>" role="region" aria-labelledby="<?php echo esc_attr( $panel_id ); ?>-btn" <?php echo $open ? '' : 'hidden'; ?>>
				<div class="accordion__content"><?php echo wp_kses_post( $answer ); ?></div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
<?php
if ( $args['schema'] && ! is_admin() && ! wp_is_json_request() ) {
	mcrp_print_schema( array( '@type' => 'FAQPage', 'mainEntity' => $schema ) );
}
