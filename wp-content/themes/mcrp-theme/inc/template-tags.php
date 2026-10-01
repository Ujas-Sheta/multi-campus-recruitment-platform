<?php
/**
 * Template tags & UI helpers.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Inline SVG icons (feather style, 24x24).
 *
 * @param string $name  Icon name.
 * @param string $class Extra classes.
 */
function mcrp_icon( string $name, string $class = '' ): string {
	static $paths = array(
		'arrow-right'   => '<path d="M5 12h14M13 6l6 6-6 6"/>',
		'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
		'chevron-left'  => '<path d="m15 18-6-6 6-6"/>',
		'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
		'map-pin'       => '<path d="M12 21s-7-6.2-7-12a7 7 0 1 1 14 0c0 5.8-7 12-7 12Z"/><circle cx="12" cy="9" r="2.5"/>',
		'phone'         => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2Z"/>',
		'mail'          => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
		'calendar'      => '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>',
		'clock'         => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>',
		'search'        => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
		'menu'          => '<path d="M4 6h16M4 12h16M4 18h16"/>',
		'close'         => '<path d="M6 6l12 12M18 6 6 18"/>',
		'check'         => '<path d="m5 12 5 5L20 7"/>',
		'quote'         => '<path d="M7 7h4v4c0 3-1.5 5-4 6M15 7h4v4c0 3-1.5 5-4 6"/>',
		'users'         => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8"/>',
		'award'         => '<circle cx="12" cy="8" r="6"/><path d="M8.2 13.3 7 22l5-3 5 3-1.2-8.7"/>',
		'briefcase'     => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>',
		'globe'         => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10Z"/>',
		'building'      => '<path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1M14 9h1M9 13h1M14 13h1M9 17h1M14 17h1"/>',
		'book'          => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20V3H6.5A2.5 2.5 0 0 0 4 5.5v14Z"/><path d="M4 19.5A2.5 2.5 0 0 0 6.5 22H20v-5"/>',
		'monitor'       => '<rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/>',
		'download'      => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>',
		'play'          => '<path d="m6 4 14 8-14 8Z"/>',
		'star'          => '<path d="m12 2 3.1 6.3 6.9 1-5 4.9 1.2 6.8L12 17.8 5.8 21l1.2-6.8-5-4.9 6.9-1Z"/>',
		'instagram'     => '<rect x="2" y="2" width="20" height="20" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".5"/>',
		'facebook'      => '<path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3Z"/>',
		'linkedin'      => '<path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-4 0v7h-4v-7a6 6 0 0 1 6-6ZM2 9h4v12H2zM4 2a2 2 0 1 1 0 4 2 2 0 0 1 0-4Z"/>',
		'youtube'       => '<path d="M22.5 6.4a2.8 2.8 0 0 0-2-2C18.9 4 12 4 12 4s-6.9 0-8.5.4a2.8 2.8 0 0 0-2 2A29 29 0 0 0 1 12a29 29 0 0 0 .5 5.6 2.8 2.8 0 0 0 2 2c1.6.4 8.5.4 8.5.4s6.9 0 8.5-.4a2.8 2.8 0 0 0 2-2A29 29 0 0 0 23 12a29 29 0 0 0-.5-5.6Z"/><path d="m10 15 5-3-5-3Z"/>',
		'tiktok'        => '<path d="M9 12a4 4 0 1 0 4 4V2a5 5 0 0 0 5 5"/>',
		'x'             => '<path d="M4 4l16 16M20 4 4 20"/>',
	);
	$svg = $paths[ $name ] ?? $paths['arrow-right'];
	return '<svg class="icon icon-' . esc_attr( $name ) . ( $class ? ' ' . esc_attr( $class ) : '' ) . '" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $svg . '</svg>';
}

/**
 * Render an ACF link array as a button.
 *
 * @param array|null $link  ACF link (url, title, target).
 * @param string     $style primary | secondary | outline | ghost | white.
 * @param string     $icon  Optional trailing icon.
 */
function mcrp_button( $link, string $style = 'primary', string $icon = '' ): string {
	if ( empty( $link['url'] ) ) {
		return '';
	}
	$target = ! empty( $link['target'] ) ? ' target="' . esc_attr( $link['target'] ) . '" rel="noopener"' : '';
	return sprintf(
		'<a class="btn btn--%1$s" href="%2$s"%3$s><span>%4$s</span>%5$s</a>',
		esc_attr( $style ),
		esc_url( $link['url'] ),
		$target,
		esc_html( $link['title'] ?: __( 'Learn more', 'mcrp' ) ),
		$icon ? mcrp_icon( $icon ) : ''
	);
}

/**
 * Application URL for a program (falls back to the global setting).
 */
function mcrp_apply_url( int $program_id = 0 ): string {
	$url = $program_id ? mcrp_get( 'apply_url', $program_id ) : '';
	return (string) ( $url ?: mcrp_option( 'apply_url', home_url( '/admissions/' ) ) );
}

/**
 * Human-readable intake labels.
 *
 * @param mixed $intakes Values or labels.
 */
function mcrp_intake_labels( $intakes ): array {
	$map = array(
		'fall'   => __( 'Fall', 'mcrp' ),
		'winter' => __( 'Winter', 'mcrp' ),
		'spring' => __( 'Spring', 'mcrp' ),
	);
	return array_map(
		static fn( $v ) => is_array( $v ) ? $v['label'] : ( $map[ $v ] ?? $v ),
		array_filter( (array) $intakes )
	);
}

/**
 * Featured image, or a coloured placeholder with initials if there isn't one.
 *
 * @param int|WP_Post $post  Post.
 * @param string      $size  Image size.
 * @param string      $class Wrapper class.
 */
function mcrp_media( $post, string $size = 'mcrp-card', string $class = 'media' ): string {
	$post = get_post( $post );
	if ( ! $post ) {
		return '';
	}
	if ( has_post_thumbnail( $post ) ) {
		return '<div class="' . esc_attr( $class ) . '">' . get_the_post_thumbnail( $post, $size, array( 'loading' => 'lazy' ) ) . '</div>';
	}

	$variants = array( 'navy', 'teal', 'coral', 'sun' );
	$variant  = $variants[ $post->ID % count( $variants ) ];
	$color    = 'campus' === $post->post_type ? mcrp_get( 'accent_color', $post->ID ) : '';
	$style    = $color ? ' style="--placeholder:' . esc_attr( $color ) . '"' : '';

	$words    = preg_split( '/[\s-]+/u', wp_strip_all_tags( get_the_title( $post ) ) );
	$initials = '';
	foreach ( array_slice( array_filter( $words, static fn( $w ) => ctype_upper( mb_substr( $w, 0, 1 ) ) ), 0, 2 ) as $w ) {
		$initials .= mb_substr( $w, 0, 1 );
	}

	$icon = array(
		'program'    => 'book',
		'campus'     => 'building',
		'event'      => 'calendar',
		'instructor' => 'users',
	)[ $post->post_type ] ?? 'star';

	return sprintf(
		'<div class="%1$s media--placeholder media--%2$s"%3$s role="img" aria-label="%4$s"><span class="media__initials">%5$s</span>%6$s</div>',
		esc_attr( $class ),
		esc_attr( $variant ),
		$style,
		esc_attr( get_the_title( $post ) ),
		esc_html( $initials ),
		mcrp_icon( $icon, 'media__icon' )
	);
}

/**
 * Eyebrow + heading + intro.
 */
function mcrp_section_heading( string $eyebrow = '', string $heading = '', string $intro = '', string $align = 'left', string $tag = 'h2' ): string {
	if ( ! $eyebrow && ! $heading && ! $intro ) {
		return '';
	}
	$tag = in_array( $tag, array( 'h1', 'h2', 'h3' ), true ) ? $tag : 'h2';
	$out = '<header class="section-heading section-heading--' . esc_attr( $align ) . '">';
	if ( $eyebrow ) {
		$out .= '<p class="eyebrow">' . esc_html( $eyebrow ) . '</p>';
	}
	if ( $heading ) {
		$out .= '<' . $tag . ' class="section-heading__title">' . wp_kses_post( $heading ) . '</' . $tag . '>';
	}
	if ( $intro ) {
		$out .= '<p class="section-heading__intro">' . wp_kses_post( $intro ) . '</p>';
	}
	return $out . '</header>';
}

/**
 * Breadcrumbs.
 */
function mcrp_breadcrumbs(): void {
	if ( is_front_page() ) {
		return;
	}
	$crumbs = array( array( __( 'Home', 'mcrp' ), home_url( '/' ) ) );

	if ( is_singular() ) {
		$type = get_post_type();
		if ( ! in_array( $type, array( 'page', 'post' ), true ) && ( $archive = get_post_type_archive_link( $type ) ) ) {
			$crumbs[] = array( get_post_type_object( $type )->labels->name, $archive );
		}
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $ancestor ) {
			$crumbs[] = array( get_the_title( $ancestor ), get_permalink( $ancestor ) );
		}
		$crumbs[] = array( get_the_title(), '' );
	} elseif ( is_tax() ) {
		$term = get_queried_object();
		$tax  = get_taxonomy( $term->taxonomy );
		$type = $tax->object_type[0] ?? '';
		if ( $type && ( $archive = get_post_type_archive_link( $type ) ) ) {
			$crumbs[] = array( get_post_type_object( $type )->labels->name, $archive );
		}
		$crumbs[] = array( $term->name, '' );
	} elseif ( is_post_type_archive() ) {
		$crumbs[] = array( post_type_archive_title( '', false ), '' );
	} elseif ( is_search() ) {
		$crumbs[] = array( __( 'Search', 'mcrp' ), '' );
	} elseif ( is_archive() ) {
		$crumbs[] = array( wp_strip_all_tags( get_the_archive_title() ), '' );
	}

	echo '<nav class="breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'mcrp' ) . '"><ol itemscope itemtype="https://schema.org/BreadcrumbList">';
	foreach ( $crumbs as $i => list( $label, $url ) ) {
		echo '<li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">';
		if ( $url ) {
			printf( '<a itemprop="item" href="%s"><span itemprop="name">%s</span></a>', esc_url( $url ), esc_html( $label ) );
		} else {
			printf( '<span itemprop="name" aria-current="page">%s</span>', esc_html( $label ) );
		}
		printf( '<meta itemprop="position" content="%d"></li>', (int) $i + 1 );
	}
	echo '</ol></nav>';
}

/**
 * Standard inner-page hero.
 *
 * @param array $args title, intro, eyebrow, meta (html), actions (html), variant.
 */
function mcrp_page_hero( array $args = array() ): void {
	get_template_part( 'template-parts/components/page-hero', null, $args );
}

/**
 * Social profiles from the options page.
 */
function mcrp_social_links(): string {
	$profiles = (array) mcrp_option( 'social', array() );
	if ( ! $profiles ) {
		return '';
	}
	$out = '<ul class="social-links">';
	foreach ( $profiles as $profile ) {
		if ( empty( $profile['url'] ) ) {
			continue;
		}
		$network = is_array( $profile['network'] ) ? $profile['network']['value'] : $profile['network'];
		$out    .= sprintf( '<li><a href="%s" target="_blank" rel="noopener" aria-label="%s">%s</a></li>', esc_url( $profile['url'] ), esc_attr( ucfirst( $network ) ), mcrp_icon( $network ) );
	}
	return $out . '</ul>';
}

/**
 * Pagination wrapper.
 */
function mcrp_pagination( ?WP_Query $query = null ): void {
	$links = paginate_links(
		array(
			'total'     => $query ? $query->max_num_pages : $GLOBALS['wp_query']->max_num_pages,
			'current'   => max( 1, get_query_var( 'paged' ) ),
			'prev_text' => mcrp_icon( 'chevron-left' ) . '<span class="screen-reader-text">' . __( 'Previous', 'mcrp' ) . '</span>',
			'next_text' => '<span class="screen-reader-text">' . __( 'Next', 'mcrp' ) . '</span>' . mcrp_icon( 'chevron-right' ),
			'type'      => 'list',
		)
	);
	if ( $links ) {
		echo '<nav class="pagination" aria-label="' . esc_attr__( 'Pagination', 'mcrp' ) . '">' . $links . '</nav>';
	}
}

/**
 * Render a card grid for a list of posts.
 *
 * @param WP_Post[] $posts   Posts.
 * @param string    $card    Card template (program|campus|event|instructor|testimonial).
 * @param int       $columns Columns.
 * @param array     $args    Extra args passed to card.
 */
function mcrp_card_grid( array $posts, string $card, int $columns = 3, array $args = array() ): void {
	if ( ! $posts ) {
		return;
	}
	echo '<div class="card-grid card-grid--' . (int) $columns . '">';
	foreach ( $posts as $post ) {
		get_template_part( 'template-parts/cards/' . $card, null, array_merge( $args, array( 'post' => $post ) ) );
	}
	echo '</div>';
}
