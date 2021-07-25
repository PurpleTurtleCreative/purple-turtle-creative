<?php
/**
 * Theme definitions.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

define( __NAMESPACE__ . '\VERSION', '1.0.0' );
define( __NAMESPACE__ . '\STYLES_URI', get_template_directory_uri() . '/assets/styles' );
define( __NAMESPACE__ . '\SCRIPTS_URI', get_template_directory_uri() . '/assets/scripts' );

define( __NAMESPACE__ . '\DEFER_SCRIPTS', [ 'jquery-core', 'jquery-migrate', 'wp-embed', 'ptc-theme-script' ] );
define( __NAMESPACE__ . '\ASYNC_SCRIPTS', [ 'mkaz-code-syntax-prism-js' ] );

/**
 * Require all custom theme functions.
 */
foreach ( glob( get_template_directory() . '/functions/*.php' ) as $file ) {
	require_once $file;
}
