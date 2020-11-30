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
 *
 * @param string $format Optional. Template to format the date.
 * Translators:
 * * %1$s - date label, 'Updated' or 'Published'
 * * %2$s - the date
 * * %3$s - info icon
 */
function the_published_or_modified_date( string $format = '%1$s <strong>%2$s</strong>%3$s' ) {

	$published_date = get_the_date();
	$modified_date = get_the_modified_date();
	[ $label, $date, $info ] = ( $modified_date !== $published_date ) ?
		[ 'Updated', $modified_date, " <span class='fa-info-circle' title='Originally published on {$published_date}'>" . get_fa( 'info-circle' ) . '</span>' ] :
		[ 'Published', $published_date, '' ];

	echo wp_kses_post( sprintf( $format, $label, $date, $info ) );

}

/**
 * Outputs all categories for the site.
 *
 * @param string $category_template Optional. Template to format each category.
 * Translators:
 * * %1$s - category archive url
 * * %2$s - category name
 * * %3$s - category post count
 * * %4$s - category active class
 */
function all_categories( string $category_template = '<a href="%1$s" class="%4$s badge-dark">%2$s (%3$s)</a>' ) {

	$categories = get_categories(
		[
			'fields' => 'all',
			'hide_empty' => true,
		]
	);

	if ( ! $categories || ! is_array( $categories ) ) {
		return;
	}

	/* Start with All Posts link */
	$category_links = sprintf(
		$category_template,
		get_post_type_archive_link( 'post' ),
		'All Posts',
		wp_count_posts( 'post' )->publish,
		( is_home() ) ? 'active' : ''
	);

	/*
	 * Apply an active class only when viewing a category archive.
	 * Otherwise, there is no 'active category' to bother testing against.
	 */
	if ( is_category() ) {
		global $cat;
		foreach ( $categories as $c ) {
			$category_links .= sprintf(
				$category_template,
				get_category_link( $c ),
				$c->name,
				$c->count,
				( $cat == $c->term_id ) ? 'active' : ''
			);
		}
	} else {
		foreach ( $categories as $c ) {
			$category_links .= sprintf(
				$category_template,
				get_category_link( $c ),
				$c->name,
				$c->count,
				''
			);
		}
	}

	if ( $category_links ) {
		echo wp_kses_post( $category_links );
	}
}

/**
 * Outputs the network sites.
 *
 * @param bool $include_current_site Optional. If the current site should
 * be included. Default false.
 *
 * @param string $format Optional. The format for each site. Translators:
 * * %1$s - site url
 * * %2$s - site name
 */
function the_sites( bool $include_current_site = false, string $format = '<a href="%1$s" class="network-site">%2$s</a>' ) {

	if (
		! function_exists( '\get_sites' ) ||
		! function_exists( '\get_current_site' ) ||
		! function_exists( '\get_site_url' ) ||
		! function_exists( '\get_blog_option' )
	) {
		error_log( 'Missing necessary multisite functions to display the network sites.' );
		return;
	}

	$site_args = [
		'fields' => '',
		'public' => 1,
		'orderby' => 'path',
		'order' => 'ASC',
	];

	if ( false === $include_current_site ) {
		$site_args['site__not_in'] = \get_current_site()->id;
	}

	$sites = \get_sites( $site_args );

	if ( ! $sites || ! is_array( $sites ) ) {
		return;
	}

	$sites_html = '';
	foreach ( $sites as $s ) {
		$sites_html .= sprintf(
			$format,
			esc_url( \get_site_url( $s->blog_id, '/' ) ),
			esc_html( \get_blog_option( $s->blog_id, 'blogname', '' ) )
		);
	}

	if ( ! $sites_html ) {
		return;
	}

	echo wp_kses_post( $sites_html );

}

/**
 * Outputs the recent posts.
 *
 * @param int $numberposts Optional. The number of most recent posts to display.
 * Default 3.
 *
 * @param string $format Optional. Template to format each post.
 * Translators:
 * * %1$s - post permalink
 * * %2$s - post title
 * * %3$s - post date
 */
function the_recent_posts( int $numberposts = 3, string $format = '<p class="recent-post"><a href="%1$s" class="recent-post_link">%2$s</a> <span class="recent-post_date">%3$s</span></p>' ) {

	$recent_posts = wp_get_recent_posts(
		[
			'numberposts' => $numberposts,
			'post_status' => 'publish',
			'post_type' => 'post',
		],
		OBJECT
	);

	if ( ! $recent_posts || ! is_array( $recent_posts ) ) {
		return;
	}

	$recent_posts_html = '';
	foreach ( $recent_posts as $p ) {
		$recent_posts_html .= sprintf(
			$format,
			esc_url( get_the_permalink( $p ) ),
			esc_html( get_the_title( $p ) ),
			esc_html( get_the_date( '', $p ) )
		);
	}

	if ( ! $recent_posts_html ) {
		return;
	}

	echo wp_kses_post( $recent_posts_html );

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

/**
 * Outputs a post link.
 *
 * @see https://developer.wordpress.org/reference/functions/get_page_by_path/
 * @see https://developer.wordpress.org/reference/functions/get_post/
 *
 * @param string|int|WP_Post|null $post_identity The post for which to link.
 * If type string, get_page_by_path() will be used to retrieve by slug. If type
 * int, \WP_Post, or null, then get_post() will be used.
 *
 * @param string[] $post_types Optional. Array of expected content type for
 * query filtering and validation. Default 'page'.
 */
function a_link_to( $post_identity, array $post_types = [ 'page' ] ) {

	$p = null;

	if ( is_string( $post_identity ) ) {
		$p = get_page_by_path( $post_identity, OBJECT, $post_types );
	} else {
		$p = get_post( $post_identity );
	}

	/* Validate type */
	if ( isset( $p->post_type ) ) {
		if ( ! in_array( $p->post_type, $post_types ) ) {
			$p = null;
		}
	} else {
		$p = null;
	}

	if ( ! $p || ! is_a( $p, '\WP_Post' ) ) {
		error_log( 'Failed to identify any ' . implode( ',', $post_types ) . ' with identity: ' . $post_identity );
		return;
	}

	printf(
		'<a href="%1$s" class="post-link">%2$s</a>',
		esc_url( get_permalink( $p ) ),
		esc_html( get_the_title( $p ) )
	);

}
