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
define( __NAMESPACE__ . '\STYLES_URI', get_template_directory_uri() . '/assets/styles' );
define( __NAMESPACE__ . '\SCRIPTS_URI', get_template_directory_uri() . '/assets/scripts' );

define( __NAMESPACE__ . '\DEFER_SCRIPTS', [ 'jquery-core', 'jquery-migrate', 'wp-embed', 'ptc-theme-script' ] );
define( __NAMESPACE__ . '\ASYNC_SCRIPTS', [ 'mkaz-code-syntax-prism-js' ] );

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
	add_theme_support( 'post-formats', [ 'status' ] );

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
 * Define current template file.
 *
 * Create a global variable with the name of the current
 * theme template file being used.
 *
 * @link https://www.kevinleary.net/get-current-theme-template-filename-wordpress/
 *
 * @param $template The full path to the current template.
 */
add_filter( 'template_include', function( $template ) {

	if ( is_search() ) {
		$template = locate_template( 'index.php' );
	}

	$GLOBALS['current_theme_template'] = basename( $template );
	return $template;

}, 1000 );

/**
 * Add template file class to body.
 */
add_filter( 'body_class', function( $classes ) {

	$current_template = $GLOBALS['current_theme_template'] ?? '';

	if ( $current_template ) {
		$classes[] = 'file-' . str_replace( '.', '-', $current_template );
	}

	return $classes;

}, 10 );

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

	switch ( $current_template ) {
		case 'index.php':
		case '404.php':
			$theme_stylesheet = '/template_index.css';
			break;

		case 'singular.php':
			$theme_stylesheet = '/template_singular.css';
			break;

		case 'custom-page-full-width.php':
			$theme_stylesheet = '/template_page-full-width.css';
			break;

		default:
			$theme_stylesheet = '/style.css';
			break;
	}

	wp_enqueue_style( 'ptc-theme-style', STYLES_URI . $theme_stylesheet, [], VERSION );
	wp_enqueue_script( 'ptc-theme-script', SCRIPTS_URI . '/frontend.min.js', [ 'jquery' ], VERSION, true );

}, 10 );

/**
 * Dequeue unused styles and scripts.
 */
add_action( 'wp_print_styles', function() {

	$current_template = $GLOBALS['current_theme_template'] ?? '';

	if ( in_array( $current_template, [ 'index.php', '404.php' ] ) ) {
		wp_dequeue_style( 'wp-block-library' );
	}

	if ( 'singular.php' !== $current_template ) {
		wp_dequeue_style( 'mkaz-code-syntax-prism-css' );
		wp_dequeue_style( 'mkaz-code-syntax-css' );
		wp_dequeue_script( 'mkaz-code-syntax-prism-js' );
	}

}, 100 );

/**
 * Optimize critical path latency for scripts.
 */
add_filter( 'script_loader_tag', function( $tag, $handle, $src ) {

	if ( is_admin() || current_user_can( 'edit_posts' ) ) {
		return $tag;
	}

	if ( in_array( $handle, DEFER_SCRIPTS ) ) {
		if ( false === stripos( $tag, 'defer' ) ) {
			$tag = str_replace( '<script ', '<script defer ', $tag );
		}
	}

	if ( in_array( $handle, ASYNC_SCRIPTS ) ) {
		if ( false === stripos( $tag, 'async' ) ) {
			$tag = str_replace( ' src', ' async="async" src', $tag );
		}
	}

	return $tag;

}, 10, 3 );

/**
 * Customize login screen.
 */
add_action( 'login_enqueue_scripts', function() {
	wp_enqueue_style( 'ptc-theme_login', STYLES_URI . '/login.css', [], VERSION );
}, 10 );

add_filter( 'login_headerurl', function() { return home_url(); } );
add_filter( 'login_headertitle', function() { return get_bloginfo( 'name' ); } );

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
