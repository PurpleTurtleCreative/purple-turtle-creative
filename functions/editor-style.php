<?php
/**
 * Styles for the Gutenberg editor.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

add_action( 'after_setup_theme', __NAMESPACE__ . '\configure_gutenberg_support', 10 );
add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets', 999 );
add_filter( 'mkaz_code_syntax_language_list', __NAMESPACE__ . '\mkaz_code_syntax_language_list', 999, 1 );

/**
 * Configure the Gutenberg Editor.
 */
function configure_gutenberg_support() {
	// Disable theme overrides from being applied.
	add_theme_support( 'disable-custom-gradients' );
	add_theme_support( 'disable-custom-colors' );
	add_theme_support( 'disable-custom-font-sizes' );
	// Define color palette.
	add_theme_support( 'editor-color-palette', get_custom_colors() );
}

/**
 * Enqueue Gutenberg Editor styles.
 */
function enqueue_block_editor_assets() {

	$editor_stylesheet = get_template_directory() . '/assets/styles/style-editor.css';
	$editor_stylesheet_uri = get_template_directory_uri() . '/assets/styles/style-editor.css';

	if ( ! is_file( $editor_stylesheet ) ) {
		error_log( 'Gutenberg editor stylesheet does not exist: ' . $editor_stylesheet );
		return;
	}

	wp_enqueue_style(
		'ptc-gutenberg-css',
		$editor_stylesheet_uri,
		[],
		'1.0'
	);
}

/**
 * Get color values defined in _colors.scss
 *
 * @see /assets/styles/sass/abstracts/variables/_colors.scss
 */
function get_custom_colors() {

	$colors = [];

	try {

		$file_contents = file_get_contents( get_template_directory() . '/assets/styles/sass/abstracts/variables/_colors.scss' );

		$colors_map = [];
		if ( preg_match( '/\$colors: \([^;]*\);/', $file_contents, $colors_map ) ) {

			if ( isset( $colors_map[0] ) ) {
				$colors_map = $colors_map[0];
			} else {
				throw new \Exception( 'Could not get $colors list.' );
			}

			$color_matches = [];
			if ( preg_match_all( '/(?P<slug>[a-z\-]+)\:\s*(?P<color>#[0-9abcdef]{3,6})/i', $colors_map, $color_matches ) ) {

				if (
					isset( $color_matches['slug'] )
					&& $color_matches['slug']
					&& isset( $color_matches['color'] )
					&& $color_matches['color']
					&& count( $color_matches['slug'] ) === count( $color_matches['color'] )
				) {

					foreach ( $color_matches['slug'] as $i => $slug ) {
						$colors[] = [
							'name' => ucwords( str_replace( '-', ' ', $slug ) ),
							'slug' => $slug,
							'color' => $color_matches['color'][ $i ],
						];
					}
				} else {
					throw new \Exception( 'Something went wrong with $color_matches.' );
				}
			} else {
				throw new \Exception( 'Could not get $color_matches.' );
			}
		} else {
			throw new \Exception( 'Could not match $colors list variable.' );
		}
	} catch ( \Exception $e ) {
		error_log( 'Failed to get_custom_colors. ' . $e->getMessage() );
		return [];
	}

	return $colors;
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
	return [
		'bash'       => 'Bash/Shell',
		'css'        => 'CSS',
		'javascript' => 'JavaScript',
		'json'       => 'JSON',
		'php'        => 'PHP',
	];
}
