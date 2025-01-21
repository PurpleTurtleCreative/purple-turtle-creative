<?php
/**
 * Theme definitions.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

$wp_theme = wp_get_theme();
define( 'PTC_Theme\THEME_VERSION', $wp_theme->get( 'Version' ) );
define( 'PTC_Theme\THEME_NAME', $wp_theme->get( 'Name' ) );
define( 'PTC_Theme\THEME_BASENAME', basename( __DIR__ ) );
define( 'PTC_Theme\THEME_PATH', __DIR__ );
define( 'PTC_Theme\REST_API_NAMESPACE_V1', 'ptc-theme/v1' );

$theme_directory_uri = get_template_directory_uri();
define( 'PTC_Theme\IMAGES_URI', $theme_directory_uri . '/assets/images' );
define( 'PTC_Theme\STYLES_URI', $theme_directory_uri . '/assets/styles' );
define( 'PTC_Theme\SCRIPTS_URI', $theme_directory_uri . '/assets/scripts' );

// Scripts with "defer" always execute when the DOM is ready (but before DOMContentLoaded event).
define(
	'PTC_Theme\DEFER_SCRIPTS',
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
	'PTC_Theme\ASYNC_SCRIPTS',
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

Captcha::register();
HTML_Routes::register();
Mailing_Lists::register();
Manual_Downloads::register();

// WP Rocket - https://docs.wp-rocket.me/article/1835-automatic-lazy-rendering
add_filter(
	'rocket_lrc_exclusions',
	function ( $exclusions ) {
		$exclusions[] = 'class="wave-trim-bottom"';
		$exclusions[] = 'class="wave-trim-top"';
		return $exclusions;
	}
);
