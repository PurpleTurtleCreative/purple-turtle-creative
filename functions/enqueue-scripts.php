<?php
/**
 * Theme script and stylesheet management.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts', 10 );
add_action( 'wp_print_styles', __NAMESPACE__ . '\dequeue_unused_scripts', 100 );
add_filter( 'script_loader_tag', __NAMESPACE__ . '\optimize_script_loading', 10, 3 );

/**
 * Enqueue scripts and styles.
 */
function enqueue_scripts() {

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

		case 'page-completionist.php':
			$theme_stylesheet = '/template_page-completionist.css';
			break;

		case 'page-plugin-info.php':
			$theme_stylesheet = '/template_page-plugin-info.css';
			break;

		default:
			$theme_stylesheet = '/style.css';
			break;
	}

	wp_enqueue_style( 'ptc-theme-style', STYLES_URI . $theme_stylesheet, [], THEME_VERSION );
	wp_enqueue_script( 'ptc-theme-script', SCRIPTS_URI . '/frontend.min.js', [], THEME_VERSION, true );
}

/**
 * Dequeue unused styles and scripts.
 */
function dequeue_unused_scripts() {

	$current_template = $GLOBALS['current_theme_template'] ?? '';

	if ( in_array( $current_template, [ 'index.php', '404.php', 'page-plugin-info.php' ] ) ) {
		wp_dequeue_style( 'wp-block-library' );
	}

	if ( 'singular.php' !== $current_template ) {
		wp_dequeue_style( 'mkaz-code-syntax-prism-css' );
		wp_dequeue_style( 'mkaz-code-syntax-css' );
		wp_dequeue_script( 'mkaz-code-syntax-prism-js' );
	}
}

/**
 * Optimize critical path latency for scripts.
 *
 * Scripts are only optimized on frontend screens that are controlled by the
 * theme. (ie. NOT wp-login.php)
 *
 * @param string $tag The <script> tag for the enqueued script.
 * @param string $handle The script's registered handle.
 * @param string $src The script's source URL.
 */
function optimize_script_loading( $tag, $handle, $src ) {

	if ( is_admin() || current_user_can( 'edit_posts' ) ) {
		return $tag;
	}

	if ( is_singular() ) {

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
	}

	return $tag;
}
