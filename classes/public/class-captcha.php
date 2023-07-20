<?php
/**
 * Captcha Class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

/**
 * Static class for managing client-side and server-side
 * bot challenges (aka captchas) for trustworthy form handling.
 *
 * @link https://developers.cloudflare.com/turnstile/get-started/
 */
class Captcha {

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
	 * @link https://developers.cloudflare.com/turnstile/get-started/client-side-rendering/#configurations
	 */
	public static function render( string $action ) {
		if ( static::is_enabled() ) {

			// Generate a random nonce value for extra verification.
			$_SESSION["ptc_captcha_{$action}_cdata"] = wp_generate_password( 32, false, false );

			// Render DOM node.
			printf(
				'<div class="cf-turnstile" data-language="en-US" data-theme="light" data-sitekey="%s" data-action="%s" data-cdata="%s"></div>',
				esc_attr( \PTC_CF_TURNSTILE_SITE_KEY ),
				esc_attr( $action ),
				esc_attr( $_SESSION["ptc_captcha_{$action}_cdata"] ),
			);

			// Enqueue script dependency.
			wp_enqueue_script( 'cf-turnstile' );
		}
	}

	/**
	 * Checks the captcha client-side token.
	 *
	 * @return bool If the token is verified as human. (ie success)
	 */
	public static function verify() {}
}
