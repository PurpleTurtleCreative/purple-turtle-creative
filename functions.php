<?php
/**
 * Theme definitions.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

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

// Scripts with "defer" always execute when the DOM is ready (but before DOMContentLoaded event).
define(
	__NAMESPACE__ . '\DEFER_SCRIPTS',
	[
		'jquery-core',
		'jquery-migrate',
		'wp-embed',
		'formidable',
		'ptc-theme-script',
		'ptc-completionist-landing-page-script',
		'cf-turnstile',
	]
);
// Scripts with "async" load in the background and run when ready.
define(
	__NAMESPACE__ . '\ASYNC_SCRIPTS',
	[
		'mkaz-code-syntax-prism-js',
		'cf-turnstile',
	]
);

// Require all custom theme functions.
foreach ( glob( THEME_PATH . '/functions/*.php' ) as $file ) {
	require_once $file;
}

// Require all public classes.
foreach ( glob( THEME_PATH . '/classes/public/class-*.php' ) as $file ) {
	require_once $file;
}

Captcha::register();
HTML_Routes::register();
Mailing_Lists::register();
