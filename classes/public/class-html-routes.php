<?php
/**
 * HTML_Routes Class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

class HTML_Routes {

	/**
	 * The HTML route for the current request. Default ''.
	 *
	 * This is set during the parse_query hook, if applicable.
	 *
	 * @var string $current_route
	 */
	private static $current_route = '';

	/**
	 * The path prefix for HTML route paths.
	 *
	 * @var string ROUTE_PREFIX
	 */
	private const ROUTE_PREFIX = 'system';

	/**
	 * The registered HTML route endpoints.
	 *
	 * Registered route paths keys to template file values.
	 *
	 * @var array $endpoints
	 */
	private static $endpoints = array();

	/**
	 * Registers an HTML endpoint.
	 *
	 * @param string $route The route path. Regex is not supported.
	 * @param string $template_file The absolute file path of the
	 * template file to load for the route.
	 */
	public static function register_route(
		string $route,
		string $template_file
	) {

		if ( ! file_exists( $template_file ) ) {
			trigger_error(
				'Refused to register HTML route for non-existent template file: ' . $template_file,
				\E_USER_WARNING
			);
			return;
		}

		static::$endpoints[ $route ] = $template_file;
	}

	/**
	 * Gets the full URL of the HTML route.
	 *
	 * @param string $route The route path.
	 *
	 * @return string
	 */
	public static function get_url( string $route ) {

		if ( empty( static::$endpoints[ $route ] ) ) {
			trigger_error(
				'Using the URL of an unregistered HTML route path may result in HTTP 404 pages. Getting URL for route: ' . $route,
				\E_USER_WARNING
			);
		}

		return home_url( static::ROUTE_PREFIX . $route );
	}

	/**
	 * Hooks code into WordPress execution.
	 */
	public static function register() {
		add_action( 'init', __CLASS__ . '::init' );
		add_action( 'parse_query', __CLASS__ . '::parse_query' );
	}

	/**
	 * Initialize the HTML route rewrites.
	 */
	public static function init() {

		// Register HTML API route parsing rewrites.

		add_rewrite_rule(
			'^' . static::ROUTE_PREFIX . '/(.*)?',
			'index.php?html_route=/$matches[1]',
			'top'
		);

		add_rewrite_rule(
			'^' . $GLOBALS['wp_rewrite']->index . '/' . static::ROUTE_PREFIX . '/(.*)?',
			'index.php?html_route=/$matches[1]',
			'top'
		);

		// Register associated query var.
		$GLOBALS['wp']->add_query_var( 'html_route' );

		/**
		 * Fires after the HTML routes API is registered.
		 *
		 * This is where integrations should register HTML routes.
		 */
		do_action( 'html_routes_init' );
	}

	/**
	 * Alters the query for HTML route support.
	 *
	 * @param \WP_Query $query The query.
	 */
	public static function parse_query( $query ) {
		if (
			$query->is_main_query() &&
			isset( $query->query['html_route'] )
		) {
			// The main query is for a potential HTML route.

			if ( static::is_endpoint_query( $query ) ) {

				// The main query has a valid HTML route endpoint.
				static::$current_route = $query->query['html_route'];

				/**
				 * Fires after the HTML route is determined.
				 *
				 * @param string $current_route The HTML route for the current request.
				 */
				do_action( 'html_route', static::$current_route );

				// Register modifications for HTML routes.
				add_filter( 'posts_request', __CLASS__ . '::modify_posts_request', PHP_INT_MAX, 2 );
				add_action( 'template_redirect', __CLASS__ . '::optimize_html_route', 10, 0 );
				add_filter( 'wp_header', __CLASS__ . '::modify_http_headers', PHP_INT_MAX, 1 );
				add_filter( 'body_class', __CLASS__ . '::get_body_classes', 9, 0 );
				add_filter( 'template_include', __CLASS__ . '::get_endpoint_template', 9, 0 );
			} else {
				// The HTML route doesn't have a valid endpoint.
				$query->set_404();
				status_header( 404 );
			}
		}
	}

	/**
	 * Alters the HTTP headers for HTML route responses.
	 *
	 * @param array $headers The array of HTTP header keys and values.
	 *
	 * @return array The resulting HTTP headers.
	 */
	public static function modify_http_headers( $headers ) {

		// Content is HTML.
		$headers['Content-Type'] = get_option( 'html_type' ) . '; charset=' . get_option( 'blog_charset' );
		// Content Type is deliberate.
		$headers['X-Content-Type-Options'] = 'nosniff';
		// Do not cache.
		$headers = array_merge( $headers, wp_get_nocache_headers() );
		// Deny search engines and crawler bots.
		$headers['X-Robots-Tag'] = 'noindex, nofollow, noarchive';

		return $headers;
	}

	/**
	 * Alters the posts SQL statement for the given query.
	 *
	 * @param string    $sql The SQL statement.
	 * @param \WP_Query $query The query.
	 *
	 * @return string The SQL statement.
	 */
	public static function modify_posts_request( $sql, $query ) {
		// Prevent querying posts unnecessarily for HTML endpoints.
		$query->set( 'no_found_rows', true );
		$query->set( 'cache_results', false );
		return false;
	}

	public static function optimize_html_route() {

		// Remove Yoast SEO.
		$front_end = \YoastSEO()->classes->get( \Yoast\WP\SEO\Integrations\Front_End_Integration::class );
		remove_action( 'wpseo_head', [ $front_end, 'present_head' ], -9999 );

		// Disable WP Rocket caching.
		add_filter( 'do_rocket_generate_caching_files', '__return_false' );

		/**
		 * Fires after HTML route optimizations.
		 *
		 * @param string $current_route The HTML route for the current request.
		 */
		do_action( 'html_route_optimizations', static::$current_route );
	}

	/**
	 * Gets the HTML route from the query.
	 *
	 * @param \WP_Query|null $query Optional. The query. Default
	 * null to use the global $wp_query.
	 *
	 * @return string The HTML route. Empty string if none.
	 */
	public static function get_queried_route( $query = null ) {

		$html_route = '';

		if ( null === $query ) {
			// Use global wp_query.
			$html_route = get_query_var( 'html_route', '' );
		} else {
			$html_route = $query->get( 'html_route', '' );
		}

		return $html_route;
	}

	/**
	 * Checks if the query is for a valid HTML route.
	 *
	 * @param \WP_Query|null $query Optional. The query. Default
	 * null to use the global $wp_query.
	 *
	 * @return bool
	 */
	public static function is_endpoint_query( $query = null ) {
		return ( ! empty( static::get_endpoint_template( $query ) ) );
	}

	/**
	 * Gets the located HTML route endpoint template file.
	 *
	 * @param \WP_Query|null $query Optional. The query. Default
	 * null to use the global $wp_query.
	 *
	 * @return string The located template file. Empty string on
	 * failure.
	 */
	public static function get_endpoint_template( $query = null ) {
		$html_route = static::get_queried_route( $query );
		return static::$endpoints[ $html_route ] ?? '';
	}

	/**
	 * Alters the array of HTML classes for the <body> tag.
	 *
	 * @param array $classes The HTML classes.
	 *
	 * @return array
	 */
	public static function get_body_classes() {
		// Clear existing classes and set content type main class.
		return array( 'html-route-endpoint' );
	}
}
