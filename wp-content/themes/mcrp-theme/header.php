<?php
/**
 * Site header.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

$phone        = mcrp_option( 'phone' );
$announcement = mcrp_option( 'announcement' );
$ann_link     = mcrp_option( 'announcement_link' );
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

<?php if ( $announcement ) : ?>
	<div class="announcement">
		<div class="container">
			<p><?php echo esc_html( $announcement ); ?>
			<?php if ( ! empty( $ann_link['url'] ) ) : ?>
				<a href="<?php echo esc_url( $ann_link['url'] ); ?>"><?php echo esc_html( $ann_link['title'] ?: __( 'Learn more', 'mcrp' ) ); ?></a>
			<?php endif; ?>
			</p>
		</div>
	</div>
<?php endif; ?>

<header class="site-header" data-header>
	<div class="utility-bar">
		<div class="container utility-bar__inner">
			<ul class="utility-bar__links">
				<?php if ( $phone ) : ?>
					<li><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo mcrp_icon( 'phone' ); ?><?php echo esc_html( $phone ); ?></a></li>
				<?php endif; ?>
				<li><a href="<?php echo esc_url( get_post_type_archive_link( 'campus' ) ?: home_url( '/' ) ); ?>"><?php echo mcrp_icon( 'map-pin' ); ?><?php esc_html_e( 'Find a campus', 'mcrp' ); ?></a></li>
				<li><a href="<?php echo esc_url( get_post_type_archive_link( 'event' ) ?: home_url( '/' ) ); ?>"><?php echo mcrp_icon( 'calendar' ); ?><?php esc_html_e( 'Visit us', 'mcrp' ); ?></a></li>
			</ul>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'utility',
					'container'      => false,
					'menu_class'     => 'utility-bar__menu',
					'depth'          => 1,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
	</div>

	<div class="container site-header__inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : ?>
				<?php the_custom_logo(); ?>
			<?php else : ?>
				<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
					<span class="site-logo__mark" aria-hidden="true"><?php echo esc_html( mb_substr( get_bloginfo( 'name' ), 0, 1 ) ); ?></span>
					<span class="site-logo__text"><?php bloginfo( 'name' ); ?></span>
				</a>
			<?php endif; ?>
		</div>

		<button class="nav-toggle" type="button" aria-expanded="false" aria-controls="site-nav" data-nav-toggle>
			<span class="nav-toggle__open"><?php echo mcrp_icon( 'menu' ); ?></span>
			<span class="nav-toggle__close"><?php echo mcrp_icon( 'close' ); ?></span>
			<span class="screen-reader-text"><?php esc_html_e( 'Menu', 'mcrp' ); ?></span>
		</button>

		<nav id="site-nav" class="site-nav" aria-label="<?php esc_attr_e( 'Primary', 'mcrp' ); ?>" data-nav>
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-nav__menu',
					'depth'          => 2,
					'fallback_cb'    => static function () {
						echo '<ul class="site-nav__menu">';
						foreach ( array( 'program', 'campus', 'event', 'instructor' ) as $type ) {
							$link = get_post_type_archive_link( $type );
							if ( $link ) {
								printf( '<li class="menu-item"><a href="%s">%s</a></li>', esc_url( $link ), esc_html( get_post_type_object( $type )->labels->name ) );
							}
						}
						echo '</ul>';
					},
				)
			);
			?>
			<div class="site-nav__actions">
				<form class="header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
					<label class="screen-reader-text" for="header-search-input"><?php esc_html_e( 'Search', 'mcrp' ); ?></label>
					<input id="header-search-input" type="search" name="s" placeholder="<?php esc_attr_e( 'Search programs...', 'mcrp' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>">
					<button type="submit" aria-label="<?php esc_attr_e( 'Submit search', 'mcrp' ); ?>"><?php echo mcrp_icon( 'search' ); ?></button>
				</form>
				<a class="btn btn--primary btn--sm" href="<?php echo esc_url( mcrp_apply_url() ); ?>"><?php esc_html_e( 'Apply now', 'mcrp' ); ?></a>
			</div>
		</nav>
	</div>
</header>

<main id="main" class="site-main" tabindex="-1">
