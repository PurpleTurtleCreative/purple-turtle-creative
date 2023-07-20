<?php
/**
 * Custom HTML routes handled by the system.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

define( __NAMESPACE__ . '\HTML_API_ROUTE_PREFIX', 'system' );

define(
	__NAMESPACE__ . '\HTML_API_ENDPOINTS',
	array(
		'/email-verification/v1/verify' => 'handle-email-verification.php',
	)
);

add_action(
	'parse_query',
	function( $query ) {
		if ( ! is_valid_html_api_endpoint() ) {
			$query->set_404();
			status_header( 404 );
		}
	}
);

add_filter(
	'posts_request',
	function( $sql, $query ) {

		if ( ! empty( $query->get( 'html_endpoint' ) ) ) {
			// Prevent querying posts unnecessarily for HTML endpoints.
			$query->set( 'no_found_rows', true );
			$query->set( 'cache_results', false );
			$sql = false;
		}

		return $sql;
	},
	PHP_INT_MAX,
	2
);

add_action( 'init', __NAMESPACE__ . '\html_endpoints_init' );
add_filter( 'template_include', __NAMESPACE__ . '\filter_html_api_template_include', 9, 1 );
add_filter( 'body_class', __NAMESPACE__ . '\set_html_api_body_class', 9 );
add_filter( 'wp_headers', __NAMESPACE__ . '\set_html_api_http_headers', PHP_INT_MAX );

function html_endpoints_init() {

	// Register HTML API route parsing rewrites.

	add_rewrite_rule(
		'^' . HTML_API_ROUTE_PREFIX . '/(.*)?',
		'index.php?html_endpoint=/$matches[1]',
		'top'
	);

	add_rewrite_rule(
		'^' . $GLOBALS['wp_rewrite']->index . '/' . HTML_API_ROUTE_PREFIX . '/(.*)?',
		'index.php?html_endpoint=/$matches[1]',
		'top'
	);

	// Register associated query var.
	$GLOBALS['wp']->add_query_var( 'html_endpoint' );
}

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
function filter_html_api_template_include( $template ) {

	if ( is_html_api_endpoint() ) {
		// NEVER cache system-generated HTML pages.
		add_filter( 'do_rocket_generate_caching_files', '__return_false' );
		// Get associated template file.
		$html_api_endpoint_template = get_html_api_endpoint_template();
		if ( ! empty( $html_api_endpoint_template ) ) {
			$template = $html_api_endpoint_template;
		}
	}

	return $template;
}

function get_html_api_endpoint_template( $query = null ) {

	$html_endpoint_route = '';

	if ( null === $query ) {
		// Use global wp_query.
		$html_endpoint_route = get_query_var( 'html_endpoint' );
	} else {
		$html_endpoint_route = $query->get( 'html_endpoint' );
	}

	return locate_template( HTML_API_ENDPOINTS[ $html_endpoint_route ] ?? '' );
}

function is_valid_html_api_endpoint() {
	return ( ! empty( get_html_api_endpoint_template() ) );
}

function is_html_api_endpoint() {
	return ( ! empty( get_query_var( 'html_endpoint' ) ) );
}

function set_html_api_body_class( $classes ) {
	return array( 'html-api-endpoint' );
}

function set_html_api_http_headers( $headers ) {

	if ( is_html_api_endpoint() ) {
		// Content is HTML.
		$headers['Content-Type'] = get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' );
		// Content Type is deliberate.
		$headers['X-Content-Type-Options'] = 'nosniff';
		// Do not cache.
		$headers = array_merge( $headers, wp_get_nocache_headers() );
		// Deny search engines and crawler bots.
		$headers['X-Robots-Tag'] = 'noindex, nofollow, noarchive';
	}

	return $headers;
}
