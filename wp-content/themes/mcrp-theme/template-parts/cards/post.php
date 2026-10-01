<?php
/**
 * Generic post card (news, pages in search results).
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
$post_obj = get_post( $args['post'] ?? null );
?>
<article <?php post_class( 'card post-card', $post_obj ); ?>>
	<a class="card__media-link" href="<?php echo esc_url( get_permalink( $post_obj ) ); ?>" tabindex="-1" aria-hidden="true">
		<?php echo mcrp_media( $post_obj, 'mcrp-card', 'card__media' ); ?>
	</a>
	<div class="card__body">
		<?php if ( 'post' === get_post_type( $post_obj ) ) : ?>
			<p class="card__date"><?php echo esc_html( get_the_date( '', $post_obj ) ); ?></p>
		<?php endif; ?>
		<h3 class="card__title"><a href="<?php echo esc_url( get_permalink( $post_obj ) ); ?>"><?php echo esc_html( get_the_title( $post_obj ) ); ?></a></h3>
		<p class="card__excerpt"><?php echo esc_html( get_the_excerpt( $post_obj ) ); ?></p>
	</div>
</article>
