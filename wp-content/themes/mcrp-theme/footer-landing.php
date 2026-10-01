<?php
/**
 * Minimal footer for campaign landing pages.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
?>
</main>
<footer class="site-footer site-footer--landing">
	<div class="site-footer__bottom">
		<div class="container">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?></p>
			<?php if ( function_exists( 'the_privacy_policy_link' ) ) { the_privacy_policy_link(); } ?>
		</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
