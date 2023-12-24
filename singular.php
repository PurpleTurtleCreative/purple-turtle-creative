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

			if ( is_singular( [ 'page', 'post' ] ) ) {
				get_template_part( 'template-parts/content', 'singular' );
			} else {
				// Get template part for custom post types.
				get_template_part( 'template-parts/content', get_post_type() );
			}

			if ( ! is_page( [ 'privacy-policy', 'terms-conditions' ] ) ) {
				echo '<div class="content-width" style="margin-top: var(--wp--preset--spacing--40);margin-bottom: var(--wp--preset--spacing--40);">';
				echo do_shortcode( '[ptc-bio-card]' );
				echo '</div>';
			}

			if ( ! is_singular( 'page' ) ) {
				the_post_navigation(
					[
						'prev_text' => '<div class="nav-icon">' . get_fa( 'angle-left' ) . '</div><div class="nav-label"><span class="nav-subtitle">Back</span><br/>%title</div>',
						'next_text' => '<div class="nav-label"><span class="nav-subtitle">Next</span><br/>%title</div><div class="nav-icon">' . get_fa( 'angle-right' ) . '</div>',
						'class' => 'content-width',
					]
				);
			}

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();
