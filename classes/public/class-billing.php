<?php
/**
 * Billing Class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

require_once THEME_PATH . '/classes/public/class-html-routes.php';

/**
 * Static class for managing client-side and server-side
 * bot challenges (aka captchas) for trustworthy form handling.
 *
 * @link https://developers.cloudflare.com/turnstile/get-started/
 */
class Billing {

	private const THANK_YOU_PAGE_ROUTE = '/billing/thank-you';

	/**
	 * Hooks code into WordPress.
	 */
	public static function register() {
		add_action( 'html_routes_init', __CLASS__ . '::register_html_routes' );
		add_filter( 'ptc_resource_plugin_get_metadata', __CLASS__ . '::filter_plugin_resource_metadata', 10, 2 );
	}

	/**
	 * Registers HTML route endpoints.
	 */
	public static function register_html_routes() {
		HTML_Routes::register_route(
			static::THANK_YOU_PAGE_ROUTE,
			THEME_PATH . '/html-routes/billing-thank-you.php'
		);
	}

	/**
	 * Adds billing pages to plugin checkout plan metadata.
	 *
	 * @param array $plugin_metadata The metadata. Empty on failure.
	 * @return array The filtered metadata.
	 */
	public static function filter_plugin_resource_metadata( array $plugin_metadata ) : array {

		if ( ! empty( $plugin_metadata['checkout_plans'] ) ) {
			$plugin_metadata['checkout_plans'] = array_map(
				function ( $plan ) {

					if ( empty( $plan['success_url'] ) ) {
						// Set default checkout success URL.
						$plan['success_url'] = add_query_arg(
							'session_id',
							'{CHECKOUT_SESSION_ID}',
							HTML_Routes::get_url( static::THANK_YOU_PAGE_ROUTE )
						);
					}

					if ( empty( $plan['cancel_url'] ) ) {
						// Set default checkout cancel URL.
						$plan['cancel_url'] = wp_get_referer();
						if ( empty( $plan['cancel_url'] ) ) {
							$plan['cancel_url'] = home_url();
						}
					}

					return $plan;
				},
				$plugin_metadata['checkout_plans']
			);
		}

		return $plugin_metadata;
	}
}
