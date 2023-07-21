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
	private static $current_route;

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

			// Posts are never needed for an HTML route,
			// whether HTTP 404 or 200.
			add_filter( 'posts_request', __CLASS__ . '::remove_posts_request', PHP_INT_MAX, 2 );

			if ( static::is_valid_route( $query->query['html_route'] ) ) {

				// The main query has a valid HTML route endpoint.
				static::$current_route = $query->query['html_route'];

				/**
				 * Fires after the HTML route is determined.
				 *
				 * @param string $current_route The HTML route for the current request.
				 */
				do_action( 'html_route', static::$current_route );

				// Register modifications for HTML routes.
				add_action( 'template_redirect', __CLASS__ . '::optimize_html_route', 10, 0 );
				add_filter( 'wp_header', __CLASS__ . '::modify_http_headers', PHP_INT_MAX, 1 );
				add_filter( 'body_class', __CLASS__ . '::get_body_classes', 9, 0 );
				add_filter( 'template_include', __CLASS__ . '::get_current_route_template', 9, 0 );
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
	 * Alters the posts SQL statement to return no results.
	 *
	 * @param string    $sql The SQL statement.
	 * @param \WP_Query $query The query.
	 *
	 * @return bool Always false.
	 */
	public static function remove_posts_request( $sql, $query ) {
		// Prevent querying posts unnecessarily for HTML endpoints.
		$query->set( 'no_found_rows', true );
		$query->set( 'cache_results', false );
		return false;
	}

	/**
	 * Optimizes the request for HTML route endpoints.
	 */
	public static function optimize_html_route() {

		// Remove Yoast SEO.
		if ( function_exists( '\YoastSEO' ) ) {
			$front_end = \YoastSEO()->classes->get( \Yoast\WP\SEO\Integrations\Front_End_Integration::class );
			remove_action( 'wpseo_head', array( $front_end, 'present_head' ), -9999 );
		}

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
	 * Gets the body classes for all HTML route endpoint pages.
	 *
	 * @return string[] The HTML class strings.
	 */
	public static function get_body_classes() : array {
		return array( 'html-route-endpoint' );
	}

	/**
	 * Checks if the given HTML route is valid.
	 *
	 * @param string $route_path The HTML route.
	 *
	 * @return bool True if valid.
	 */
	public static function is_valid_route( string $route_path ) : bool {
		return ( ! empty( static::get_route_template( $route_path ) ) );
	}

	/**
	 * Gets the registered template file for the given HTML route.
	 *
	 * @param string $route_path The HTML route.
	 *
	 * @return string The HTML route template file. Default ''.
	 */
	public static function get_route_template( string $route_path ) : string {
		return static::$endpoints[ $route_path ] ?? '';
	}

	/**
	 * Gets the current HTML route.
	 *
	 * @return string The current HTML route. Default ''.
	 */
	public static function get_current_route() {
		return (
			empty( static::$current_route ) ?
			'' :
			static::$current_route
		);
	}

	/**
	 * Gets the registered template file for the current HTML route.
	 *
	 * @return string The current HTML route template file. Default ''.
	 */
	public static function get_current_route_template() : string {
		return static::get_route_template( static::get_current_route() );
	}
}
