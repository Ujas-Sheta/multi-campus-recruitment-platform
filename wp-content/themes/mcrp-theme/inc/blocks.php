<?php
/**
 * ACF blocks.
 *
 * Each folder in /blocks has a block.json, fields.php and render.php.
 * They get picked up automatically, so a new block is just a new folder.
 *
 * @package MCRP_Theme
 */

defined( 'ABSPATH' ) || exit;

/**
 * Block directories.
 *
 * @return string[] slug => absolute dir.
 */
function mcrp_block_dirs(): array {
	$dirs = array();
	foreach ( glob( MCRP_THEME_DIR . '/blocks/*/block.json' ) ?: array() as $json ) {
		$dirs[ basename( dirname( $json ) ) ] = dirname( $json );
	}
	return $dirs;
}

add_action(
	'init',
	static function () {
		foreach ( mcrp_block_dirs() as $dir ) {
			register_block_type( $dir );
		}
	}
);

/**
 * Fallback for when ACF Pro isn't active: still render the saved blocks on the
 * front end straight from the block data, so pages don't go blank.
 * (Editing them still needs ACF Pro.)
 */
add_filter(
	'block_type_metadata_settings',
	static function ( $settings, $metadata ) {
		if ( function_exists( 'acf_register_block_type' ) || empty( $metadata['acf']['renderTemplate'] ) ) {
			return $settings;
		}
		$template = dirname( $metadata['file'] ) . '/' . $metadata['acf']['renderTemplate'];

		$settings['render_callback'] = static function ( $attributes, $content ) use ( $template ) {
			$block       = $attributes;
			$block['id'] = 'block_' . substr( md5( wp_json_encode( $attributes ) ), 0, 8 );
			$is_preview  = false;
			$post_id     = get_the_ID();

			$previous                  = $GLOBALS['mcrp_block_data'] ?? null;
			$GLOBALS['mcrp_block_data'] = (array) ( $attributes['data'] ?? array() );

			ob_start();
			include $template;
			$html = (string) ob_get_clean();

			if ( null === $previous ) {
				unset( $GLOBALS['mcrp_block_data'] );
			} else {
				$GLOBALS['mcrp_block_data'] = $previous;
			}

			return preg_replace( '#<InnerBlocks[^>]*/>#', $content, $html );
		};
		return $settings;
	},
	10,
	2
);

/**
 * Reads a value from the current block's saved data (used by mcrp_get() in fallback mode).
 * Repeaters are stored flattened (name = count, name_0_sub = value), so rebuild the rows.
 */
function mcrp_block_fallback_value( string $name ) {
	$data = $GLOBALS['mcrp_block_data'] ?? array();
	if ( ! preg_grep( '/^' . preg_quote( $name, '/' ) . '_0_/', array_keys( $data ) ) ) {
		return $data[ $name ] ?? null;
	}
	$rows = array();
	for ( $i = 0; $i < (int) $data[ $name ]; $i++ ) {
		$prefix = $name . '_' . $i . '_';
		$row    = array();
		foreach ( $data as $key => $value ) {
			if ( 0 === strpos( $key, $prefix ) ) {
				$row[ substr( $key, strlen( $prefix ) ) ] = $value;
			}
		}
		$rows[] = $row;
	}
	return $rows;
}

/**
 * Register each block's field group.
 */
add_action(
	'acf/include_fields',
	static function () {
		if ( ! function_exists( 'acf_add_local_field_group' ) || ! function_exists( 'mcrp_acf_keys' ) ) {
			return;
		}
		foreach ( mcrp_block_dirs() as $slug => $dir ) {
			if ( ! file_exists( $dir . '/fields.php' ) ) {
				continue;
			}
			$fields   = include $dir . '/fields.php';
			$meta     = wp_json_file_decode( $dir . '/block.json', array( 'associative' => true ) );
			$has_style = ! empty( $meta['acf']['sectionStyle'] );
			if ( $has_style ) {
				$fields = array_merge( $fields, mcrp_block_style_fields() );
			}
			acf_add_local_field_group(
				array(
					'key'      => 'group_mcrp_block_' . $slug,
					'title'    => sprintf( /* translators: %s: block title */ __( 'Block: %s', 'mcrp' ), $meta['title'] ?? $slug ),
					'fields'   => mcrp_acf_keys( $fields, 'mcrp_block_' . str_replace( '-', '_', $slug ) ),
					'location' => array( array( array( 'param' => 'block', 'operator' => '==', 'value' => 'mcrp/' . $slug ) ) ),
					'active'   => true,
				)
			);
		}
	}
);

/**
 * Shared "Style" tab for section-level blocks.
 */
function mcrp_block_style_fields(): array {
	return array(
		array( 'label' => __( 'Style', 'mcrp' ), 'name' => 'style_tab', 'type' => 'tab' ),
		array(
			'label'         => __( 'Background', 'mcrp' ),
			'name'          => 'background',
			'type'          => 'button_group',
			'choices'       => array( 'white' => 'White', 'surface' => 'Light', 'primary' => 'Navy', 'accent' => 'Coral' ),
			'default_value' => 'white',
		),
		array(
			'label'         => __( 'Vertical spacing', 'mcrp' ),
			'name'          => 'spacing',
			'type'          => 'button_group',
			'choices'       => array( 'lg' => 'Large', 'md' => 'Medium', 'sm' => 'Small', 'none' => 'None' ),
			'default_value' => 'lg',
		),
	);
}

/**
 * Block categories.
 */
add_filter(
	'block_categories_all',
	static function ( array $categories ) {
		array_unshift(
			$categories,
			array( 'slug' => 'mcrp', 'title' => __( 'Campus - Recruitment Blocks', 'mcrp' ), 'icon' => 'welcome-learn-more' ),
			array( 'slug' => 'mcrp-content', 'title' => __( 'Campus - Content Blocks', 'mcrp' ), 'icon' => 'database' )
		);
		return $categories;
	}
);

/**
 * Opening <section> for a block (id, align, custom class, background, spacing).
 *
 * @param array  $block ACF block array.
 * @param string $base  Base CSS class (BEM block name).
 * @param array  $extra Extra classes.
 * @param string $tag   Wrapper tag.
 */
function mcrp_block_open( array $block, string $base, array $extra = array(), string $tag = 'section' ): string {
	$classes = array( 'mcrp-block', $base );
	if ( ! empty( $block['className'] ) ) {
		$classes[] = $block['className'];
	}
	if ( ! empty( $block['align'] ) ) {
		$classes[] = 'align' . $block['align'];
	}
	$bg = mcrp_get( 'background' );
	if ( $bg ) {
		$classes[] = 'bg-' . $bg;
	}
	$spacing   = mcrp_get( 'spacing' );
	$classes[] = 'space-' . ( $spacing ?: 'lg' );
	$classes   = array_merge( $classes, array_filter( $extra ) );

	$id = ! empty( $block['anchor'] ) ? $block['anchor'] : ( $block['id'] ?? '' );
	return sprintf( '<%1$s id="%2$s" class="%3$s">', tag_escape( $tag ), esc_attr( $id ), esc_attr( implode( ' ', array_unique( $classes ) ) ) );
}

/**
 * Placeholder shown in the editor when a block has no content yet.
 */
function mcrp_block_placeholder( string $message, bool $is_preview ): void {
	if ( $is_preview ) {
		echo '<div class="mcrp-block-placeholder">' . esc_html( $message ) . '</div>';
	}
}

/**
 * Select fields with a custom 'mcrp_terms' key get their choices from that taxonomy.
 * Using slugs instead of IDs so the patterns work on any environment.
 */
add_filter(
	'acf/load_field/type=select',
	static function ( array $field ) {
		if ( empty( $field['mcrp_terms'] ) || ! taxonomy_exists( $field['mcrp_terms'] ) ) {
			return $field;
		}
		$terms = get_terms( array( 'taxonomy' => $field['mcrp_terms'], 'hide_empty' => false ) );
		$field['choices'] = array( '' => __( 'Any', 'mcrp' ) );
		if ( ! is_wp_error( $terms ) ) {
			foreach ( $terms as $term ) {
				$field['choices'][ $term->slug ] = $term->name;
			}
		}
		return $field;
	}
);

// Shared field builders for block fields.php files

/**
 * Eyebrow / heading / intro fields.
 */
function mcrp_fields_heading( bool $intro = true, string $default = '' ): array {
	$fields = array(
		array( 'label' => __( 'Eyebrow', 'mcrp' ), 'name' => 'eyebrow', 'type' => 'text', 'wrapper' => array( 'width' => 30 ) ),
		array( 'label' => __( 'Heading', 'mcrp' ), 'name' => 'heading', 'type' => 'text', 'default_value' => $default, 'wrapper' => array( 'width' => 70 ) ),
	);
	if ( $intro ) {
		$fields[] = array( 'label' => __( 'Intro text', 'mcrp' ), 'name' => 'intro', 'type' => 'textarea', 'rows' => 2, 'new_lines' => '' );
	}
	return $fields;
}

/**
 * Repeatable buttons (link + style).
 */
function mcrp_fields_buttons( int $max = 2 ): array {
	return array(
		'label'        => __( 'Buttons', 'mcrp' ),
		'name'         => 'buttons',
		'type'         => 'repeater',
		'layout'       => 'table',
		'max'          => $max,
		'button_label' => __( 'Add button', 'mcrp' ),
		'sub_fields'   => array(
			array( 'label' => __( 'Link', 'mcrp' ), 'name' => 'link', 'type' => 'link', 'return_format' => 'array' ),
			array( 'label' => __( 'Style', 'mcrp' ), 'name' => 'style', 'type' => 'select', 'choices' => array( 'primary' => 'Primary (coral)', 'secondary' => 'Navy', 'white' => 'White', 'outline' => 'Outline', 'outline-light' => 'Outline (light)' ), 'default_value' => 'primary' ),
		),
	);
}

/**
 * Render a buttons repeater.
 *
 * @param mixed $rows Repeater rows.
 */
function mcrp_render_buttons( $rows, string $class = 'btn-group' ): string {
	if ( empty( $rows ) || ! is_array( $rows ) ) {
		return '';
	}
	$out = '';
	foreach ( $rows as $i => $row ) {
		$style = is_array( $row['style'] ?? '' ) ? $row['style']['value'] : ( $row['style'] ?? 'primary' );
		$out  .= mcrp_button( $row['link'] ?? array(), $style ?: 'primary', 0 === $i ? 'arrow-right' : '' );
	}
	return $out ? '<div class="' . esc_attr( $class ) . '">' . $out . '</div>' : '';
}

/**
 * Heading markup from the standard heading fields of the current block.
 */
function mcrp_block_heading( string $align = 'left' ): string {
	return mcrp_section_heading( (string) mcrp_get( 'eyebrow', null, '' ), (string) mcrp_get( 'heading', null, '' ), (string) mcrp_get( 'intro', null, '' ), $align );
}
