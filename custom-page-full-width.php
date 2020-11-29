<?php
/**
 * Template Name: Full Width
 *
 * @package Purple_Turtle_Creative
 */

get_header();
?>

	<main id="primary" <?php post_class( 'site-main' ); ?>>

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', 'page' );

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();
