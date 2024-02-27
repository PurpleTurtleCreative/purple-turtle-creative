<?php
/**
 * Billing Class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

/**
 * Static class for managing client-side and server-side
 * bot challenges (aka captchas) for trustworthy form handling.
 *
 * @link https://developers.cloudflare.com/turnstile/get-started/
 */
class Billing {

	/**
	 * Hooks code into WordPress.
	 */
	public static function register() {
		add_action( 'html_routes_init', __CLASS__ . '::register_html_routes' );
	}

	/**
	 * Registers HTML route endpoints.
	 */
	public static function register_html_routes() {
		HTML_Routes::register_route(
			'/billing/thank-you',
			THEME_PATH . '/html-routes/billing-thank-you.php'
		);
	}
}
