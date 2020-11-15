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

/**
 * Outputs the published or modified date.
 */
function the_published_or_modified_date() {

	$published_date = get_the_date();
	$modified_date = get_the_modified_date();
	$entry_date = ( $modified_date !== $published_date ) ?
		"Updated <strong>{$modified_date}</strong>" : "Published <strong>{$published_date}</strong>";

	echo wp_kses( $entry_date, 'strong' );

}

/**
 * Get the Yoast SEO primary category or first category \WP_Term object.
 *
 * @return \WP_Term|bool The category. Default FALSE.
 */
function get_the_primary_category() {

	$primary_category = false;

	$post_id = get_the_ID();
	$term = 'category';

	if ( class_exists( '\WPSEO_Primary_Term' ) ) {
		$wpseo_primary_term = new \WPSEO_Primary_Term( $term, $post_id );
		$primary_term = get_term( $wpseo_primary_term->get_primary_term() );

		if ( ! is_wp_error( $primary_term ) && $primary_term ) {
			$primary_category = $primary_term;
		}
	}

	if ( ! is_a( $primary_category, '\WP_Term' ) ) {
		/* Get first category if primary unavailable */
		$categories_list = get_the_category( $post_id );
		if ( isset( $categories_list[0] ) && $categories_list[0] ) {
			$primary_category = $categories_list[0];
		}
	}

	if ( is_a( $primary_category, '\WP_Term' ) ) {
		return $primary_category;
	}

	return false;
}

/**
 * Outputs the primary category.
 *
 * @param string $before The HTML before the category anchor tag.
 * @param string $after The HTML after the category anchor tag.
 */
function the_primary_category( string $before = '<p class="entry-primary-category">', string $after = '</p>' ) {

	$primary_category = get_the_primary_category();

	if (
		is_a( $primary_category, '\WP_Term' )
		&& isset( $primary_category->name )
		&& $primary_category->name
	) {

		$category_link = esc_url( get_category_link( $primary_category ) );

		if ( $category_link ) {
			echo wp_kses_post( $before . "<a href='{$category_link}'>{$primary_category->name}</a>" . $after );
		} else {
			echo wp_kses_post( $before . $primary_category->name . $after );
		}
	}

}

/**
 * Outputs the Yoast SEO meta description or post excerpt.
 *
 * @param string $before The HTML before the description.
 * @param string $after The HTML after the description.
 */
function the_short_description( string $before = '<p class="entry-metadesc">', string $after = '</p>' ) {

	$the_short_description = '';

	/* WPSEO_Meta defined in plugins/wordpress-seo/inc/class-wpseo-meta.php */
	if ( class_exists( '\WPSEO_Meta' ) ) {
		$the_short_description = \WPSEO_Meta::get_value( 'metadesc', get_the_ID() );
	}

	if ( ! $the_short_description ) {
		/* Use the excerpt if Yoast SEO metadesc unavailable */
		$the_short_description = get_the_excerpt();
	}

	/* Give up if we still don't have a description */
	if ( ! $the_short_description ) {
		return;
	}

	echo wp_kses_post( $before . $the_short_description . $after );

}
