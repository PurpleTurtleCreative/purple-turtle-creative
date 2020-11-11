<?php
/**
 * Purple Turtle Creative functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

/**
 * Remove screen reader text H2 element in the_post_navigation() template.
 */
add_filter( 'navigation_markup_template', function( $template, $class ) {
	return '
	<nav class="post-navigation %1$s" role="navigation" aria-label="%4$s">
		<div class="nav-links">%3$s</div>
	</nav>';
}, 10, 2 );
