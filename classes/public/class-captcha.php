<?php
/**
 * Captcha Class
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
class Captcha {

	/**
	 * The server-side endpoint used for verifying client-side tokens.
	 *
	 * @var string SITE_VERIFY_ENDPOINT
	 */
	private const SITE_VERIFY_ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

	/**
	 * Hooks code into WordPress.
	 */
	public static function register() {
		add_action(
			'wp_enqueue_scripts',
			__CLASS__ . '::register_scripts'
		);
	}

	/**
	 * Checks if the required configurations exist.
	 *
	 * @return bool If the required configurations exist.
	 */
	public static function is_enabled() : bool {
		return (
			defined( '\PTC_CF_TURNSTILE_SITE_KEY' ) &&
			! empty( \PTC_CF_TURNSTILE_SITE_KEY ) &&
			defined( '\PTC_CF_TURNSTILE_SECRET_KEY' ) &&
			! empty( \PTC_CF_TURNSTILE_SECRET_KEY )
		);
	}

	/**
	 * Registers the related script and style handles.
	 */
	public static function register_scripts() {
		wp_register_script(
			'cf-turnstile',
			'https://challenges.cloudflare.com/turnstile/v0/api.js',
			array(),
			'v0',
			true
		);
	}

	/**
	 * Prints the captcha HTML target container.
	 *
	 * Note that you should not render captchas with the same
	 * action value within the same page load! This will cause
	 * the unique nonce to be overridden on each render, breaking
	 * verification for earlier renders of the captcha with the
	 * same action value.
	 *
	 * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/#configurations
	 *
	 * @param string $action A unique identifier for this widget
	 * instance.
	 */
	public static function render( string $action ) {

		// Ensure required globals are configured.
		if ( ! static::is_enabled() ) {
			trigger_error(
				'Failed to render client-side CAPTCHA widget. This site may be unprotected against bots. Missing required constants.',
				\E_USER_WARNING
			);
			return;
		}

		// Render DOM node.
		printf(
			'
			<input type="hidden" name="cf-turnstile-action" value="%2$s" />
			<div class="cf-turnstile" data-language="en-US" data-theme="light" data-size="normal" data-appearance="always" data-sitekey="%1$s" data-action="%2$s"></div>
			',
			esc_attr( \PTC_CF_TURNSTILE_SITE_KEY ),
			esc_attr( $action )
		);

		// Enqueue script dependency.
		wp_enqueue_script( 'cf-turnstile' );
	}

	/**
	 * Checks the captcha client-side token.
	 *
	 * @param string $action The action associated with the token.
	 * @param string $token The client-side token to be verified.
	 *
	 * @return bool If the token is verified as human. (ie success)
	 */
	public static function verify( string $action, string $token ) : bool {

		// Ensure required globals are configured.
		if ( ! static::is_enabled() ) {
			trigger_error(
				'Failed to process server-side CAPTCHA verification. Frontend user requests may be permanently blocked. Missing required constants.',
				\E_USER_WARNING
			);
			return false;
		}

		// Send the verification request.
		$response = wp_remote_post(
			static::SITE_VERIFY_ENDPOINT,
			array(
				'body' => array(
					'secret'   => \PTC_CF_TURNSTILE_SECRET_KEY,
					'response' => $token,
				),
			)
		);

		// Validate the response.
		$http_code = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $http_code ) {
			return false;
		}

		// Get the response body.
		$body = wp_remote_retrieve_body( $response );
		if ( empty( $body ) ) {
			return false;
		}

		// Decode the JSON data.
		$response = json_decode( $body, true );// associative array.
		if ( empty( $response ) || ! is_array( $response ) ) {
			return false;
		}

		// Check if successful verification.
		if ( empty( $response['success'] ) || ! $response['success'] ) {
			return false;
		}

		// Final check whether this is a legitimate success.
		return (
			! empty( $response['action'] ) &&
			$action === $response['action']
		);
	}
}
