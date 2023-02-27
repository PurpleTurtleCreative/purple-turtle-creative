<?php
/**
 * Styles for the Gutenberg editor.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

add_action( 'enqueue_block_editor_assets', __NAMESPACE__ . '\enqueue_block_editor_assets', 999 );
add_filter( 'mkaz_code_syntax_language_list', __NAMESPACE__ . '\mkaz_code_syntax_language_list', 999, 1 );

// Remove SVG definitions for duotones.
remove_action( 'wp_body_open', 'wp_global_styles_render_svg_filters' );

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
