<?php
/**
 * Theme script and stylesheet management.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\enqueue_scripts', 10 );
add_action( 'wp_print_styles', __NAMESPACE__ . '\dequeue_unused_scripts', 100 );
add_filter( 'print_scripts_array', __NAMESPACE__ . '\optimize_script_loading', \PHP_INT_MAX, 1 );
add_filter( 'block_type_metadata', __NAMESPACE__ . '\enqueue_block_scripts' );

function enqueue_block_scripts( $metadata ) {

	if ( 'core/details' === $metadata['name'] ) {

		if ( empty( $metadata['viewScript'] ) ) {
			$metadata['viewScript'] = array();
		} elseif ( ! is_array( $metadata['viewScript'] ) ) {
			$metadata['viewScript'] = array( $metadata['viewScript'] );
		}

		wp_register_script(
			'ptc-block-core-details-view-script',
			SCRIPTS_URI . '/block-core-details-view-script.min.js',
			array( 'ptc-theme-script' ),
			THEME_VERSION,
			true
		);
		$metadata['viewScript'][] = 'ptc-block-core-details-view-script';
	}

	return $metadata;
}

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
			wp_enqueue_script(
				'ptc-completionist-landing-page-script',
				SCRIPTS_URI . '/completionist-landing-page.min.js',
				array( 'ptc-theme-script' ),
				THEME_VERSION,
				true
			);
			break;

		case 'page-plugin-info.php':
			$theme_stylesheet = '/template_page-plugin-info.css';
			break;

		case 'email-verification.php':
		case 'manual-download.php':
			$theme_stylesheet = '/template_html-route-endpoint.css';
			break;

		default:
			$theme_stylesheet = '/style.css';
			break;
	}

	wp_enqueue_style(
		'ptc-theme-style',
		STYLES_URI . $theme_stylesheet,
		array(),
		THEME_VERSION
	);
	wp_enqueue_script(
		'ptc-theme-script',
		SCRIPTS_URI . '/frontend.min.js',
		array(),
		THEME_VERSION,
		true
	);
}

/**
 * Dequeue unused styles and scripts.
 */
function dequeue_unused_scripts() {

	$current_template = $GLOBALS['current_theme_template'] ?? '';

	if ( in_array( $current_template, array( 'index.php', '404.php', 'page-plugin-info.php' ) ) ) {
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
 * Scripts are only optimized on frontend screens that are
 * controlled by the theme. (ie. NOT wp-login.php)
 *
 * @param string[] $handles The enqueued script handles.
 */
function optimize_script_loading( $handles = array() ) {

	if ( is_admin() || current_user_can( 'edit_posts' ) ) {
		return $handles;
	}

	if ( is_singular() ) {

		foreach ( $handles as &$handle ) {

			if ( in_array( $handle, DEFER_SCRIPTS, true ) ) {
				wp_script_add_data( $handle, 'strategy', 'defer' );
			}

			if ( in_array( $handle, ASYNC_SCRIPTS, true ) ) {
				wp_script_add_data( $handle, 'strategy', 'async' );
			}
		}
	}

	return $handles;
}
