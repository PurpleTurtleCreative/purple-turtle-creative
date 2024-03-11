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
		add_action( 'rest_api_init', __CLASS__ . '::register_rest_routes' );
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

	public static function register_rest_routes() {
		register_rest_route(
			REST_API_NAMESPACE_V1,
			'/customer/authenticate',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => __CLASS__ . '::handle_post_customer_authenticate',
					'permission_callback' => '__return_true',
					'args'                => array(
						'action'                => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $value ) {
								return in_array(
									$value,
									array( 'signup', 'login' ),
									true
								);
							},
						),
						'email'                 => array(
							'type'              => 'string',
							'required'          => true,
							// WordPress discloses these functions may be
							// inaccurate, but I'd rather be safer and mayyybe
							// miss a few subscribers than permit bad data.
							'sanitize_callback' => 'sanitize_email',
							'validate_callback' => 'is_email',
						),
						'password'              => array(
							'type'              => 'string',
							'required'          => true,
							'validate_callback' => function ( $value ) {
								// DO NOT sanitize a password and change its value.
								// The user then won't know what their password is,
								// so just validate its cleanliness instead.
								return ( sanitize_text_field( $value ) === (string) $value );
							},
						),
						'nonce'                 => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $value ) {
								return in_array(
									wp_verify_nonce( $value, THEME_BASENAME ),
									array( 1, 2 ),
									true
								);
							},
						),
						'cf-turnstile-action'   => array(
							'type'              => 'string',
							'required'          => false, // @TODO - USE CLOUDFLARE TURNSTILE VERIFICATION IN PRODUCTION.
							'sanitize_callback' => 'sanitize_text_field',
						),
						'cf-turnstile-response' => array(
							'type'              => 'string',
							'required'          => false, // @TODO - USE CLOUDFLARE TURNSTILE VERIFICATION IN PRODUCTION.
							// Hopefully this doesn't invalidate successful tokens.
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $value, $request ) {
								return Captcha::verify(
									$request['cf-turnstile-action'],
									$request['cf-turnstile-response']
								);
							},
						),
					),
				),
			)
		);
	}

	/**
	 * Handles a request to authenticate a customer's session.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response The response.
	 */
	public static function handle_post_customer_authenticate(
		\WP_REST_Request $request
	) : \WP_REST_Response {

		$res = array(
			'status'  => 'error',
			'code'    => 403,
			'message' => 'TESTING - Email verification required.',
			'data'    => null,
		);

		// @todo - Retrieve customer by email from Stripe.

		// @todo - Check request.action to create or login customer.

		// @todo - Handle create new customer. Email must have been verified, otherwise return 403 Unauthorized response.

		// @todo - Handle log in existing customer by confirming hashed password value from Stripe customer.meta.ptc_password value.

		// @todo - Return static::create_customer_jwt( $customer ) in res.data on success.

		sleep( 5 ); // @TODO - JUST QUICK TESTING FOR FRONTEND.

		return new \WP_REST_Response( $res, $res['code'] );
	}

	private static function create_customer_jwt( array $customer ) : string {}
	public static function is_customer_jwt_valid( string $jwt ) : bool {}

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
