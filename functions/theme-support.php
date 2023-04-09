<?php
/**
 * Theme script and stylesheet management.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

add_filter( 'admin_email_check_interval', '\__return_false' );// Don't ask about admin email.
add_action( 'after_setup_theme', __NAMESPACE__ . '\configure_theme_support', 0, 1 );
add_action( 'after_setup_theme', __NAMESPACE__ . '\define_content_width', 0 );
add_filter( 'wp_kses_allowed_html', __NAMESPACE__ . '\allow_svg_markup', 10, 1 );
add_filter( 'safe_style_css', __NAMESPACE__ . '\allow_svg_styles', 10, 1 );
add_filter( 'acf/settings/save_json', __NAMESPACE__ . '\acf_json_save_point' );

/**
 * Configure theme's supported features.
 */
function configure_theme_support() {
	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	add_theme_support( 'post-thumbnails' );

	// This theme uses wp_nav_menu() in one location.
	register_nav_menus(
		[
			'menu-header' => 'Primary',
			'menu-footer' => 'Footer',
		]
	);

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		[
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		]
	);

	/*
	 * Support various formatting for Posts.
	 *
	 * @link https://developer.wordpress.org/reference/functions/add_theme_support/#post-formats
	 */
	add_theme_support( 'post-formats', [ 'status' ] );
}

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @see https://codex.wordpress.org/Content_Width
 *
 * @global int $content_width
 */
function define_content_width() {
	$GLOBALS['content_width'] = 1024;
}

/**
 * Allow SVG tags in HTML content.
 *
 * @param array $tags The allowed HTML tags and their respective attributes.
 */
function allow_svg_markup( $tags ) {

	$tags['svg'] = [
		'class' => [],
		'aria-hidden' => [],
		'aria-labelledby' => [],
		'role' => [],
		'style' => [],
		'xmlns' => [],
		'width' => [],
		'height' => [],
		'preserveAspectRatio' => [],
		'viewbox' => [], // <= Must be lower case!
	];

	$tags['g'] = [
		'fill' => [],
		'style' => [],
	];

	$tags['path'] = [
		'd' => [],
		'fill' => [],
		'style' => [],
	];

	$tags['polygon'] = [
		'd' => [],
		'fill' => [],
		'style' => [],
		'points' => [],
	];

	return $tags;
}

/**
 * Allow SVG style attributes in HTML content.
 *
 * @param string[] $styles The allowed CSS attributes.
 */
function allow_svg_styles( $styles ) {
	$styles[] = 'fill';
	$styles[] = 'opacity';
	return $styles;
}

/**
 * Specifies the save point for ACF.
 *
 * @link https://www.advancedcustomfields.com/resources/local-json/#syncing-changes
 *
 * @param string $path The directory in which ACF field groups will be saved.
 */
function acf_json_save_point( $path ) {
	$path = THEME_PATH . '/acf-json';
	return $path;
}
