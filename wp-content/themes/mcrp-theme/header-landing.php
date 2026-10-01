<?php
/**
 * Stripped-down header for campaign landing pages (no main nav).
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;
$phone = mcrp_option( 'phone' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'mcrp' ); ?></a>

<header class="site-header site-header--landing">
	<div class="container site-header__inner">
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<span class="site-logo__mark" aria-hidden="true"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span>
			<span class="site-logo__text"><?php bloginfo( 'name' ); ?></span>
		</a>
		<div class="site-header__landing-actions">
			<?php if ( $phone ) : ?>
				<a class="landing-phone" href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo mcrp_icon( 'phone' ); ?><span><?php echo esc_html( $phone ); ?></span></a>
			<?php endif; ?>
			<a class="btn btn--primary btn--sm" href="#inquiry"><?php esc_html_e( 'Request info', 'mcrp' ); ?></a>
		</div>
	</div>
</header>

<main id="main" class="site-main" tabindex="-1">
