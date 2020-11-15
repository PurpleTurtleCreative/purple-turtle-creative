<?php
/**
 * Purple Turtle Creative functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

define( __NAMESPACE__ . '\VERSION', '1.0.0' );

/**
 * Require all custom theme functions.
 */
foreach ( glob( get_template_directory() . '/functions/*.php' ) as $filename ) {
	require_once $filename;
}

/**
 * Configure theme's supported features.
 */
add_action( 'after_setup_theme', function() {

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
	add_theme_support( 'post-formats', [ 'aside', 'status' ] );

}, 0, 1 );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
add_action( 'after_setup_theme', function() {
	$GLOBALS['content_width'] = 1024;
}, 0 );

/**
 * Define current template file
 *
 * Create a global variable with the name of the current
 * theme template file being used.
 *
 * @link https://www.kevinleary.net/get-current-theme-template-filename-wordpress/
 *
 * @param $template The full path to the current template
 */
add_filter( 'template_include', function( $template ) {

	if ( is_search() ) {
		$template = locate_template( 'index.php' );
	} elseif ( is_privacy_policy() || is_page( 'terms-conditions' ) ) {
		$template = locate_template( 'single.php' );
	}

	$GLOBALS['current_theme_template'] = basename( $template );
	return $template;
}, 1000 );

/**
 * Filter the site search query.
 */
add_action( 'pre_get_posts', function( $query ) {
	if ( $query->is_search ) {
		$query->set( 'post_type', [ 'post' ] );
	}
}, 10 );

/**
 * Enqueue scripts and styles.
 */
add_action( 'wp_enqueue_scripts', function() {

	$current_template = $GLOBALS['current_theme_template'] ?? '';
	$styles_uri = get_template_directory_uri() . '/assets/styles';

	switch ( $current_template ) {
		case 'index.php':
			wp_enqueue_style( 'purple-turtle-creative-style_index', $styles_uri . '/template_index.css', [], VERSION );
			break;

		case 'single.php':
			wp_enqueue_style( 'purple-turtle-creative-style_single', $styles_uri . '/template_single.css', [], VERSION );
			break;

		default:
			wp_enqueue_style( 'purple-turtle-creative-style', $styles_uri . '/style.css', [], VERSION );
			break;
	}

	// if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
	// 	wp_enqueue_script( 'comment-reply' );
	// }

}, 10 );

/**
 * Allow SVG tags in HTML content.
 */
add_filter( 'wp_kses_allowed_html', function( $tags ) {

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

	return $tags;

}, 10, 1 );

/**
 * Allow SVG style attributes in HTML content.
 */
add_filter( 'safe_style_css', function( $styles ) {

	$styles[] = 'fill';
	$styles[] = 'opacity';

	return $styles;

}, 10, 1 );
