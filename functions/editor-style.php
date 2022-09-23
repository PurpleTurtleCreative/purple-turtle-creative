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

register_block_style(
	'core/columns',
	[
		'name'         => 'cards',
		'label'        => 'Cards',
		'inline_style' => '
			.wp-block-columns.is-style-cards {
				gap: 1em;
				margin-bottom: 1em;
			}
			.wp-block-columns.is-style-cards .wp-block-column {
				border: 2px solid #e4e6fd;
				border-radius: .625rem;
				padding: .625rem 1.625rem;
			}
	',
	]
);

register_block_style(
	'core/group',
	[
		'name'         => 'fading-curve-top',
		'label'        => 'Fading Curve Top',
		'inline_style' => '
			.wp-block-group.is-style-fading-curve-top {
				background-image: linear-gradient(#f6f7fd, rgba(255,255,255,0));
				border-radius: 50%/4%;
			}
			@media (min-width: 768px) {
				.wp-block-group.is-style-fading-curve-top {
					border-radius: 50%/15%;
				}
			}
	',
	]
);

register_block_style(
	'core/list',
	[
		'name'         => 'clean',
		'label'        => 'Clean',
		'inline_style' => '
			ul.is-style-clean {
				margin: 0;
				padding: 0 0 0 20px;
				list-style-type: none;
			}
			p + ul.is-style-clean {
				margin-top: -0.5em;
			}
			ul.is-style-clean li {
				position: relative;
				margin: 1em 0;
			}
			ul.is-style-clean li::before {
				content: "";
				display: block;
				background: currentColor;
				height: 6px;
				width: 6px;
				border-radius: 999px;
				position: absolute;
				left: -18px;
				top: calc(0.5em + 3px);
			}
	',
	]
);

register_block_style(
	'core/gallery',
	[
		'name'  => 'popover-alt-text',
		'label' => 'Popover Alt Text',
		'inline_style' => '
			.wp-block-gallery.is-style-popover-alt-text {
				--wp--style--gallery-gap-default: 1rem;
				padding: 0.5rem 2rem;
			}
			.wp-block-gallery.is-style-popover-alt-text figure.wp-block-image {
				position: relative;
			}
			.wp-block-gallery.is-style-popover-alt-text figure.wp-block-image p.popover-alt-text {
				display: none;
			}
			.wp-block-gallery.is-style-popover-alt-text figure.wp-block-image:hover p.popover-alt-text {
				display: block;
				position: absolute;
				top: -10px;
				left: 50%;
				transform: translate(-50%,-100%);
				max-width: calc(100% + 20px);
				text-align: center;
				line-height: 1.3;
				background: #08082b;
				color: white;
				padding: 7px 10px;
				margin: 0;
				border-radius: 5px;
				user-select: none;
				pointer-events: none;
				z-index: 1;
			}
			@media (min-width: 768px) {
				.wp-block-gallery.is-style-popover-alt-text figure.wp-block-image:hover p.popover-alt-text {
					white-space: nowrap;
					max-width: 100vw;
				}
			}
			.wp-block-gallery.is-style-popover-alt-text figure.wp-block-image p.popover-alt-text::after {
				content: "";
				border-top: 7px solid #08082b;
				border-left: 10px solid transparent;
				border-right: 10px solid transparent;
				border-bottom: 10px solid transparent;
				position: absolute;
				bottom: 1px;
				transform: translate(-50%,100%);
				left: 50%;
				width: 0;
				height: 0;
			}
		',
	]
);

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
