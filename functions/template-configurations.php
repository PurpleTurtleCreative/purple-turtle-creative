<?php
/**
 * Theme script and stylesheet management.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

add_filter( 'template_include', __NAMESPACE__ . '\set_current_template', 1000 );
add_filter( 'body_class', __NAMESPACE__ . '\set_body_class', 10 );
add_action( 'pre_get_posts', __NAMESPACE__ . '\customize_wp_query', 10 );
add_filter( 'excerpt_length', __NAMESPACE__ . '\set_excerpt_length', 10 );
add_filter( 'excerpt_more', __NAMESPACE__ . '\set_excerpt_suffix', 10 );

add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\enqueue_login_scripts', 10 );
add_filter( 'login_headerurl', __NAMESPACE__ . '\set_login_headerurl', 10 );
add_filter( 'login_headertext', __NAMESPACE__ . '\set_login_headertext', 10 );

/**
 * Defines the current template file.
 *
 * Creates a global variable with the name of the current
 * theme template file being used.
 *
 * @link https://www.kevinleary.net/get-current-theme-template-filename-wordpress/
 *
 * @param string $template The full path to the current template.
 * @return string The full path to the current template.
 */
function set_current_template( $template ) {

	if ( is_search() ) {
		$template = locate_template( 'index.php' );
	}

	$GLOBALS['current_theme_template'] = basename( $template );
	return $template;
}

/**
 * Adds the template file class to the body.
 *
 * @param string[] $classes The <body> class names.
 * @return string[]
 */
function set_body_class( $classes ) {

	$current_template = $GLOBALS['current_theme_template'] ?? '';

	if ( $current_template ) {
		$classes[] = 'file-' . str_replace( '.', '-', $current_template );
	}

	return $classes;
}

/**
 * Filters the site search query.
 *
 * @param \WP_Query $query The WP_Query instance (passed by reference).
 */
function customize_wp_query( $query ) {
	if ( $query->is_search ) {
		// Search should only return blog posts.
		$query->set( 'post_type', [ 'post' ] );
	}
	if ( $query->is_post_type_archive( 'ptc-portfolio' ) ) {
		// Sort portfolio posts by project start date.
		$query->set( 'order', 'DESC' );
		$query->set( 'orderby', 'meta_value_num' );
		// ACF date field value is stored like 20220728 (YYYYMMDD).
		$query->set( 'meta_key', 'ptc_project_dates_ptc_project_to' );
	}
}

/**
 * Filters the maximum number of words in a post excerpt.
 *
 * @link https://developer.wordpress.org/reference/hooks/excerpt_length/
 *
 * @param int $word_count The maximum number of words. Default 55.
 * @return int
 */
function set_excerpt_length( $word_count ) {
	return 20;
}

/**
 * Filters the excerpt ending to indicate content was trimmed.
 *
 * @link https://developer.wordpress.org/reference/hooks/excerpt_more/
 *
 * @param string $more_string The suffix string.
 * @return string
 */
function set_excerpt_suffix( $more_string ) {
	return '...';
}

/**
 * Enqueues login screen styles.
 */
function enqueue_login_scripts() {
	wp_enqueue_style( 'ptc-theme_login', STYLES_URI . '/login.css', [], THEME_VERSION );
}

/**
 * Filters link URL of the header logo above login form.
 *
 * @link https://developer.wordpress.org/reference/hooks/login_headerurl/
 *
 * @param string $login_header_url The login header logo URL.
 * @return string
 */
function set_login_headerurl( $login_header_url ) {
	return home_url();
}

/**
 * Filters the link text of the header logo above the login form.
 *
 * @link https://developer.wordpress.org/reference/hooks/login_headertext/
 *
 * @param string $login_header_text The login header logo link text.
 * @return string
 */
function set_login_headertext( $login_header_text ) {
	return get_bloginfo( 'name' );
}
