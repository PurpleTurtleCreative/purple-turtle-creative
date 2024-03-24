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

	private const HTTP_REQUEST_TIMEOUT = 10;

	private const CUSTOMERS_MAILING_LIST = 'customers@sandboxe9304e53e5994067aa8ce9e5897e4536.mailgun.org';

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
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'cf-turnstile-response' => array(
							'type'              => 'string',
							'required'          => true,
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
			'code'    => 500,
			'message' => 'An unknown error occurred.',
			'data'    => null,
		);

		try {

			// Check if customer has verified email.
			if (
				true !== Mailing_Lists::is_email_verified(
					$request['email'],
					static::CUSTOMERS_MAILING_LIST
				)
			) {
				// ALL CUSTOMERS MUST VERIFY PROOF OF EMAIL ADDRESS
				// OWNERSHIP BEFORE CREATING OR ACCESSING THEIR ACCOUNT.
				throw new \Exception( 'Email verification required.', 403 );
			}

			// Retrieve customer by email from Stripe.
			$customer = static::get_customer_by_email( $request['email'] );

			// Check customer account authentication action.
			if ( 'signup' === $request['action'] ) {

				// Ensure customer doesn't already exist.
				if ( ! empty( $customer ) ) {
					throw new \Exception( 'Customer account already exists.', 409 );
				}

				// Create new customer.
				$customer = static::create_customer(
					array(
						'email' => '',
						'metadata' => array(
							'referrer' => $request->get_header( 'Referer' ),
							'ptc_password' => wp_hash_password( $request['password'] ),
						),
					)
				);
			} elseif ( 'login' === $request['action'] ) {

				// Check existing customer's password.
				if (
					true !== wp_check_password(
						$request['password'],
						$customer['metadata']['ptc_password']
					)
				) {
					throw new \Exception( 'Incorrect password.', 401 );
				}
			} else {
				throw new \Exception( 'Unrecognized requested action.', 400 );
			}

			// @todo - Return static::create_customer_jwt( $customer ) in res.data on success.

		} catch ( \Exception $err ) {
			$res = array(
				'status'  => 'error',
				'code'    => $err->getCode(),
				'message' => $err->getMessage(),
				'data'    => null,
			);
		}

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

	/**
	 * Gets a customer by email.
	 *
	 * @param string $email The customer's email to search by.
	 *
	 * @return array The customer. Empty if none found.
	 */
	private static function get_customer_by_email( string $email ) : array {

		if ( empty( $email ) ) {
			trigger_error( 'Refused to search for customer by empty email value.', \E_USER_ERROR );
			return array();
		}

		$customers = static::get_customers( "email:'{$email}'", 1 );
		if ( empty( $customers[0] ) ) {
			return array();
		}

		return $customers[0];
	}

	/**
	 * Gets matching customers.
	 *
	 * @link https://docs.stripe.com/api/customers/search
	 * @link https://docs.stripe.com/search#query-fields-for-customers
	 *
	 * @param string $query The search query.
	 *
	 * @return array The matching customers. Empty if none found.
	 */
	private static function get_customers( string $query, int $limit = 1 ) : array {

		// Validate required parameters.

		if ( empty( $query ) ) {
			trigger_error( 'Failed to get customers with no [query] specified.', \E_USER_ERROR );
			return array();
		}

		if ( $limit < 1 || $limit > 100 ) {
			trigger_error( 'Failed to get customers with invalid [limit] value. Must be between 1 and 100.', \E_USER_ERROR );
		}

		// Prepare request args.
		$args = array(
			'query' => $query,
			'limit' => $limit,
			// 'expand' => array(
			// 	'invoice',
			// 	'subscription.plan.product',
			// ),
		);

		// Perform the request.
		$response = wp_remote_request(
			'https://api.stripe.com/v1/customers/search?' . http_build_query( $args ),
			array(
				'timeout' => static::HTTP_REQUEST_TIMEOUT,
				'method'  => 'GET',
				'headers' => array(
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( \PTC_STRIPE_API_SECRET . ':' ),
				),
			)
		);

		// Process the response.

		if ( is_wp_error( $response ) ) {

			$error_message = $response->get_error_message();
			$error_code    = $response->get_error_code();

			trigger_error(
				wp_kses_post( "Failed request to Stripe API: {$error_code} - {$error_message}" ),
				\E_USER_NOTICE
			);

			return array();
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );

		$body             = wp_remote_retrieve_body( $response );
		$decoded_response = json_decode( $body, true );

		if (
			200 !== $status_code ||
			! isset( $decoded_response['data'] ) ||
			! is_array( $decoded_response['data'] )
		) {

			trigger_error(
				wp_kses_post( "Stripe API {$status_code} response: " . print_r( $decoded_response, true ) ),
				\E_USER_NOTICE
			);

			return array();
		}

		// Successfully retrieved the customers.
		return $decoded_response['data'];
	}

	/**
	 * Creates a new customer.
	 *
	 * @link https://docs.stripe.com/api/customers/create
	 *
	 * @param array $args The request arguments.
	 *
	 * @return array The new customer. Empty on failure.
	 */
	private static function create_customer( array $args ) : array {

		// Validate parameters.
		if (
			empty( $args['email'] ) ||
			empty( $args['metadata']['ptc_password'] )
		) {
			trigger_error( 'Refused to create customer with missing required parameter(s) [email] or [metadata][ptc_password].', \E_USER_ERROR );
			return array();
		}

		// Recommended parameters.
		if ( empty( $args['metadata']['referrer'] ) ) {
			trigger_error( 'Creating customer without recommended [metadata][referrer] value.', \E_USER_WARNING );
		}

		// Perform the request.
		$response = wp_remote_request(
			'https://api.stripe.com/v1/customers',
			array(
				'timeout' => static::HTTP_REQUEST_TIMEOUT,
				'method'  => 'POST',
				'headers' => array(
					'Content-Type'  => 'application/x-www-form-urlencoded',
					'Authorization' => 'Basic ' . base64_encode( \PTC_STRIPE_API_SECRET . ':' ),
				),
				'body'    => http_build_query( $args ),
			)
		);

		// Process the response.

		if ( is_wp_error( $response ) ) {

			$error_message = $response->get_error_message();
			$error_code    = $response->get_error_code();

			trigger_error(
				wp_kses_post( "Failed request to Stripe API: {$error_code} - {$error_message}" ),
				\E_USER_NOTICE
			);

			return array();
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );

		$body             = wp_remote_retrieve_body( $response );
		$decoded_response = json_decode( $body, true );

		if (
			200 !== $status_code ||
			empty( $decoded_response['id'] ) ||
			empty( $decoded_response['email'] )
		) {

			trigger_error(
				wp_kses_post( "Stripe API {$status_code} response: " . print_r( $decoded_response, true ) ),
				\E_USER_NOTICE
			);

			return array();
		}

		// Successfully created new Checkout Session instance.
		return $decoded_response;
	}
}
