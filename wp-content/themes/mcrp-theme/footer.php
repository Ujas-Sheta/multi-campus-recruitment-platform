<?php
/**
 * Site footer.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

$campuses = get_posts( array( 'post_type' => 'campus', 'posts_per_page' => 6, 'orderby' => 'menu_order title', 'order' => 'ASC' ) );
$phone    = mcrp_option( 'phone' );
$email    = mcrp_option( 'email' );
?>
</main>

<footer class="site-footer">
	<div class="container site-footer__grid">
		<div class="site-footer__brand">
			<a class="site-logo site-logo--light" href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span class="site-logo__mark" aria-hidden="true"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span>
				<span class="site-logo__text"><?php bloginfo( 'name' ); ?></span>
			</a>
			<p><?php echo esc_html( mcrp_option( 'footer_tagline', get_bloginfo( 'description' ) ) ); ?></p>
			<?php echo mcrp_social_links(); ?>
		</div>

		<?php if ( $campuses ) : ?>
			<div>
				<h2 class="site-footer__heading"><?php esc_html_e( 'Our campuses', 'mcrp' ); ?></h2>
				<ul class="site-footer__list">
					<?php foreach ( $campuses as $campus ) : ?>
						<li>
							<a href="<?php echo esc_url( get_permalink( $campus ) ); ?>"><?php echo esc_html( $campus->post_title ); ?></a>
							<?php if ( $city = mcrp_get( 'city', $campus->ID ) ) : ?>
								<small><?php echo esc_html( $city ); ?></small>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<div>
			<h2 class="site-footer__heading"><?php esc_html_e( 'Quick links', 'mcrp' ); ?></h2>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'footer',
					'container'      => false,
					'menu_class'     => 'site-footer__list',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>

		<div>
			<h2 class="site-footer__heading"><?php esc_html_e( 'Talk to admissions', 'mcrp' ); ?></h2>
			<ul class="site-footer__list site-footer__contact">
				<?php if ( $phone ) : ?>
					<li><?php echo mcrp_icon( 'phone' ); ?><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></li>
				<?php endif; ?>
				<?php if ( $email ) : ?>
					<li><?php echo mcrp_icon( 'mail' ); ?><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></li>
				<?php endif; ?>
			</ul>
			<a class="btn btn--primary btn--sm" href="<?php echo esc_url( mcrp_apply_url() ); ?>"><?php esc_html_e( 'Start your application', 'mcrp' ); ?></a>
		</div>
	</div>

	<div class="site-footer__bottom">
		<div class="container">
			<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'mcrp' ); ?></p>
			<?php if ( function_exists( 'the_privacy_policy_link' ) ) { the_privacy_policy_link(); } ?>
		</div>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
