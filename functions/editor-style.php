<?php
/**
 * Styles for the Gutenberg editor.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

add_action( 'init', __NAMESPACE__ . '\register_block_customizations', 10 );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets', 999 );
add_filter( 'mkaz_code_syntax_language_list', __NAMESPACE__ . '\mkaz_code_syntax_language_list', 999, 1 );

// Remove SVG definitions for duotones.
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

/**
 * Registers custom block types and styles.
 */
function register_block_customizations() {

	// Register custom block types.

	// foreach ( glob( THEME_PATH . '/blocks/*/*-block.json' ) as $file ) {
	// register_block_type( $file );
	// }

	// Register block styles.

	$block_style_headers = array(
		'Block Type'  => 'Block Type',
		'Style Name'  => 'Style Name',
		'Style Label' => 'Style Label',
	);

	// Find block styles.
	foreach ( glob( THEME_PATH . '/assets/styles/block-style_*.css' ) as $stylesheet ) {
		// Get block style file metadata.
		$metadata = get_file_data( $stylesheet, $block_style_headers );
		if ( ! empty( $metadata ) ) {
			// Register the block style.
			$style_properties = array(
				'name'         => $metadata['Style Name'],
				'label'        => $metadata['Style Label'],
				'inline_style' => file_get_contents( $stylesheet ),
			);
			if ( 'default' === $metadata['Style Name'] ) {
				$style_properties['is_default'] = true;
			}
			register_block_style( $metadata['Block Type'], $style_properties );
		}
	}
}

/**
 * Enqueue Gutenberg Editor styles.
 */
function enqueue_block_editor_assets() {

	$editor_stylesheet     = get_template_directory() . '/assets/styles/style-editor.css';
	$editor_stylesheet_uri = get_template_directory_uri() . '/assets/styles/style-editor.css';

	if ( ! is_file( $editor_stylesheet ) ) {
		error_log( 'Gutenberg editor stylesheet does not exist: ' . $editor_stylesheet );
		return;
	}

	wp_enqueue_style(
		'ptc-gutenberg-css',
		$editor_stylesheet_uri,
		array(),
		'1.0'
	);
}

/**
 * Only allow supported code languages to be used.
 *
 * @see wp-content/plugins/code-syntax-block/prism-languages.php
 * @see wp-content/themes/purple-turtle-creative/assets/styles/sass/base/elements/_code.scss
 *
 * @param string[] $languages The array of prism languages.
 */
function mkaz_code_syntax_language_list( $languages ) {
	return array(
		'bash'       => 'Bash/Shell',
		'css'        => 'CSS',
		'javascript' => 'JavaScript',
		'json'       => 'JSON',
		'php'        => 'PHP',
	);
}
