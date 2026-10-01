<?php
/**
 * Upcoming Events block.
 *
 * @package MCRP_Theme
 *
 * @var array $block      Block settings.
 * @var bool  $is_preview Editor preview.
 */

defined( 'ABSPATH' ) || exit;

$events = mcrp_q(
	'upcoming_events',
	array(
		'type'     => mcrp_get( 'type' ),
		'campus'   => (int) mcrp_get( 'campus' ),
		'featured' => (bool) mcrp_get( 'featured' ),
		'limit'    => (int) mcrp_get( 'limit', null, 3 ),
	)
);

echo mcrp_block_open( $block, 'events-block' );
?>
	<div class="container">
		<div class="section-head-row">
			<?php echo mcrp_block_heading(); ?>
			<?php echo mcrp_button( mcrp_get( 'view_all' ), 'outline', 'arrow-right' ); ?>
		</div>
		<?php if ( $events ) : ?>
			<?php mcrp_card_grid( $events, 'event', min( 3, count( $events ) ) === 1 ? 2 : 3 ); ?>
		<?php else : ?>
			<p class="empty-state"><?php echo esc_html( mcrp_get( 'empty', null, __( 'No upcoming events right now.', 'mcrp' ) ) ); ?></p>
		<?php endif; ?>
	</div>
</section>
