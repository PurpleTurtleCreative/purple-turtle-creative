<?php
/**
 * Theme definitions.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

$wp_theme = wp_get_theme();
define( __NAMESPACE__ . '\THEME_VERSION', $wp_theme->get( 'Version' ) );
define( __NAMESPACE__ . '\THEME_NAME', $wp_theme->get( 'Name' ) );
define( __NAMESPACE__ . '\THEME_BASENAME', basename( __DIR__ ) );
define( __NAMESPACE__ . '\THEME_PATH', __DIR__ );
define( __NAMESPACE__ . '\REST_API_NAMESPACE_V1', 'ptc-theme/v1' );

$theme_directory_uri = get_template_directory_uri();
define( __NAMESPACE__ . '\IMAGES_URI', $theme_directory_uri . '/assets/images' );
define( __NAMESPACE__ . '\STYLES_URI', $theme_directory_uri . '/assets/styles' );
define( __NAMESPACE__ . '\SCRIPTS_URI', $theme_directory_uri . '/assets/scripts' );
define( __NAMESPACE__ . '\REACT_BUILD_URI', $theme_directory_uri . '/react/build' );

// Scripts with "defer" always execute when the DOM is ready (but before DOMContentLoaded event).
define(
	__NAMESPACE__ . '\DEFER_SCRIPTS',
	array(
		'jquery-core',
		'jquery-migrate',
		'wp-embed',
		'ptc-theme-script',
		'ptc-completionist-landing-page-script',
		'block-acf-ptc-block-mailing-list-subscribe',
		'ptc-block-core-details-view-script',
	)
);
// Scripts with "async" load in the background and run when ready.
define(
	__NAMESPACE__ . '\ASYNC_SCRIPTS',
	array(
		'mkaz-code-syntax-prism-js',
		'cf-turnstile',
	)
);

// Require all custom theme functions.
foreach ( glob( THEME_PATH . '/functions/*.php' ) as $file ) {
	require_once $file;
}

// Require all public classes.
foreach ( glob( THEME_PATH . '/classes/public/class-*.php' ) as $file ) {
	require_once $file;
}

Billing::register();
Captcha::register();
HTML_Routes::register();
Mailing_Lists::register();


///// TEMPORARY DRAFTING - BETA FEATURES ///////////////

$ptc_theme_enqueued_frontend_namespace_var = false;
function enqueue_frontend_namespace_var( $handle, $before_or_after ) {
	global $ptc_theme_enqueued_frontend_namespace_var;

	if ( true === $ptc_theme_enqueued_frontend_namespace_var ) {
		trigger_error( "Refused to enqueue the frontend namespace variable more than once. Ignoring request for handle '{$handle}' with position '{$before_or_after}'.", \E_USER_WARNING );
		return;
	}

	$frontend_data = array(
		'api'     => array(
			'auth_nonce' => wp_create_nonce( 'wp_rest' ),
			'nonce'      => wp_create_nonce( THEME_BASENAME ),
			'url'        => rest_url(),
			'v1'         => rest_url( REST_API_NAMESPACE_V1 ),
		),
		'billing' => array(
			'customers_list_id' => Mailing_Lists::MAILING_LIST_IDS[ Billing::CUSTOMERS_MAILING_LIST ],
		),
	);

	if ( Captcha::is_enabled() ) {
		$frontend_data['cf_turnstile'] = array(
			'site_key' => \PTC_CF_TURNSTILE_SITE_KEY,
		);
	}

	$frontend_namespace_json = wp_json_encode( $frontend_data );

	wp_add_inline_script(
		$handle,
		"var ptcTheme = {$frontend_namespace_json}",
		$before_or_after
	);

	$ptc_theme_enqueued_frontend_namespace_var = true;
}

add_filter(
	'authenticate',
	function ( $user ) {
		// Only allow administrator users to attempt login.
		// User records are only for programmatic usage.

		if ( is_a( $user, '\WP_User' ) ) {
			if ( ! in_array( 'administrator', (array) $user->roles, true ) ) {
				$user = new \WP_Error( 401, 'Login not permitted.' );
			}
		}

		return $user;
	},
	\PHP_INT_MAX,
	1
);
