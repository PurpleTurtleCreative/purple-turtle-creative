<?php
/**
 * Theme script and stylesheet management.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

add_filter( 'template_include', __NAMESPACE__ . '\set_current_template', 1000 );
add_filter( 'body_class', __NAMESPACE__ . '\set_body_class', 10 );
add_action( 'pre_get_posts', __NAMESPACE__ . '\filter_site_search_results', 10 );

add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\enqueue_login_scripts', 10 );
add_filter( 'login_headerurl', function() { return home_url(); } );
add_filter( 'login_headertext', function() { return get_bloginfo( 'name' ); } );

/**
 * Define current template file.
 *
 * Create a global variable with the name of the current
 * theme template file being used.
 *
 * @link https://www.kevinleary.net/get-current-theme-template-filename-wordpress/
 *
 * @param string $template The full path to the current template.
 */
function set_current_template( $template ) {

	if ( is_search() ) {
		$template = locate_template( 'index.php' );
	}

	$GLOBALS['current_theme_template'] = basename( $template );
	return $template;
}

/**
 * Add template file class to body.
 *
 * @param string[] $classes The <body> class names.
 */
function set_body_class( $classes ) {

	$current_template = $GLOBALS['current_theme_template'] ?? '';

	if ( $current_template ) {
		$classes[] = 'file-' . str_replace( '.', '-', $current_template );
	}

	return $classes;
}

/**
 * Filter the site search query.
 *
 * @param \WP_Query $query The WP_Query instance (passed by reference).
 */
function filter_site_search_results( $query ) {
	if ( $query->is_search ) {
		$query->set( 'post_type', [ 'post' ] );
	}
}

/**
 * Customize login screen.
 */
function enqueue_login_scripts() {
	wp_enqueue_style( 'ptc-theme_login', STYLES_URI . '/login.css', [], THEME_VERSION );
}
