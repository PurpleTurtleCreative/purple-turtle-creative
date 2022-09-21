<?php
/**
 * Graphic asset markups and helpers.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

/**
 * Outputs an SVG asset from a file.
 *
 * @param string $asset_image_filename The SVG filename.
 *
 * @param string $directory Optional. The full path where the SVG file is
 * located. Default '' to use the theme's /assets/images/ folder.
 */
function svg( string $asset_image_filename, string $directory = '' ) {
	echo get_svg( $asset_image_filename, $directory );//phpcs:ignore
}

/**
 * Gets an SVG asset from a file.
 *
 * @param string $asset_image_filename The SVG filename.
 *
 * @param string $directory Optional. The full path where the SVG file is
 * located. Default '' to use the theme's /assets/images/ folder.
 */
function get_svg( string $asset_image_filename, string $directory = '' ) {

	if ( '' === $directory ) {
		$directory = THEME_PATH . '/assets/images/';
	}

	$filename = $directory . $asset_image_filename;

	if ( ! is_file( $filename ) ) {
		error_log( 'SVG asset does not exist: ' . $filename );
		return;
	}

	$svg = file_get_contents( $filename );

	if ( ! $svg ) {
		error_log( 'Failed to get SVG asset contents: ' . $filename );
		return;
	}

	// SUCCESS!
	return wp_kses_post( $svg );
}

/**
 * Outputs an SVG asset's URI.
 *
 * @param string $asset_image_filename The SVG filename in the theme's
 * /assets/images/ folder.
 */
function svg_uri( string $asset_image_filename ) {
	echo get_svg_uri( $asset_image_filename );//phpcs:ignore
}

/**
 * Gets an SVG asset's URI.
 *
 * @param string $asset_image_filename The SVG filename in the theme's
 * /assets/images/ folder.
 */
function get_svg_uri( string $asset_image_filename ) {
	return esc_url(
		IMAGES_URI . '/' . $asset_image_filename
	);
}

/**
 * Outputs a FontAwesome icon SVG.
 *
 * @param string $icon_name The name of the icon.
 *
 * @param string $family_dir Optional. The FontAwesome family to retrieve the
 * icon such as 'solid', 'regular', or 'brands'. Default 'solid'.
 */
function fa( string $icon_name, string $family_dir = 'solid' ) {
	echo get_fa( $icon_name, $family_dir );//phpcs:ignore
}

/**
 * Gets a FontAwesome icon SVG.
 *
 * @param string $icon_name The name of the icon.
 *
 * @param string $family_dir Optional. The FontAwesome family to retrieve the
 * icon such as 'solid', 'regular', or 'brands'. Default 'solid'.
 */
function get_fa( string $icon_name, string $family_dir = 'solid' ) {

	$full_family_dir = THEME_PATH . '/assets/icons/' . $family_dir . '/';

	if ( ! is_dir( $full_family_dir ) ) {
		error_log( 'FA icon family is invalid: ' . $full_family_dir );
		return;
	}

	return get_svg( $icon_name . '.svg', $full_family_dir );
}
