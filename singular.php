<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

get_header();
?>

	<main id="primary" <?php post_class( 'site-main' ); ?>>

		<div class="wave-bg-third">
			<div class="wave-trim"></div>
		</div>

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'post' );

			if ( 'page' != get_post_type() ) {
				the_post_navigation(
					[
						'prev_text' => '<div class="nav-icon">&lsaquo;</div><div class="nav-label" title="%title"><span class="nav-subtitle">Prev:</span> %title</div>',
						'next_text' => '<div class="nav-label" title="%title"><span class="nav-subtitle">Next:</span> %title</div><div class="nav-icon">&rsaquo;</div>',
						'class' => 'content-width',
					]
				);
			}

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();
