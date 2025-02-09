<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

get_header();
?>

	<main id="primary" <?php post_class( 'site-main' ); ?>>

		<?php
		while ( have_posts() ) :
			the_post();

			if ( is_singular( array( 'page', 'post' ) ) ) {
				get_template_part( 'template-parts/content', 'singular' );
			} else {
				// Get template part for custom post types.
				get_template_part( 'template-parts/content', get_post_type() );
			}

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();
