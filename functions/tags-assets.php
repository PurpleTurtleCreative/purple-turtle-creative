<?php
/**
 * Purple Turtle Creative functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

/**
 * Outputs an SVG asset from a file.
 *
 * @param string $asset_image_filename The SVG filename in the theme's
 * /assets/images/ folder.
 */
function svg( string $asset_image_filename ) {
	echo wp_kses_post(
		file_get_contents(
			get_template_directory() . '/assets/images/' . $asset_image_filename
		)
	);
}

/**
 * Gets an SVG asset's URI.
 *
 * @param string $asset_image_filename The SVG filename in the theme's
 * /assets/images/ folder.
 */
function get_svg_uri( string $asset_image_filename ) {
	return esc_url(
		get_template_directory_uri() . '/assets/images/' . $asset_image_filename
	);
}
