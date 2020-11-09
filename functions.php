<?php
/**
 * Purple Turtle Creative functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Purple_Turtle_Creative
 */

if ( ! function_exists( 'purple_turtle_creative_setup' ) ) {
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function purple_turtle_creative_setup() {

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

	}
}
add_action( 'after_setup_theme', 'purple_turtle_creative_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function purple_turtle_creative_content_width() {
	$GLOBALS['content_width'] = 1024;
}
add_action( 'after_setup_theme', 'purple_turtle_creative_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function purple_turtle_creative_scripts() {

	$styles_uri = get_template_directory_uri() . '/assets/styles';

	wp_enqueue_style( 'purple-turtle-creative-style', $styles_uri . '/style.css', [], '1.0.0' );

	// if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
	// 	wp_enqueue_script( 'comment-reply' );
	// }

}
add_action( 'wp_enqueue_scripts', 'purple_turtle_creative_scripts' );
