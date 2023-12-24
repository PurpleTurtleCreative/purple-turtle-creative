<?php
/**
 * PTC Post Previews block rendering.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

// Get block data.
$selected_post_type  = get_field( 'ptc_post_previews_type' ) ?: 'post';
$display_limit_count = get_field( 'ptc_post_previews_limit' ) ?: 3;

// Get post objects.
$recent_posts_query = new WP_Query(
	array(
		'post_type'      => $selected_post_type,
		'posts_per_page' => $display_limit_count,
	)
);

// Display post previews.
if ( $recent_posts_query->have_posts() ) {
	echo '<div class="ptc-block ptc-block-post-previews"><div class="posts-loop type-' . esc_attr( $selected_post_type ) . '">';
	while ( $recent_posts_query->have_posts() ) {
		$recent_posts_query->the_post();
		get_template_part( 'template-parts/content', get_post_type() . '-preview' );
	}
	echo '</div></div>';
}

// Reset postdata to the main Loop query.
wp_reset_postdata();
