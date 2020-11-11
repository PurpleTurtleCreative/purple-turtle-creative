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
			<div class="wave-trim" style="background-image: url(<?php echo get_svg_uri( 'wave-transparent.svg' ); ?>)"></div>
		</div>

		<?php
		while ( have_posts() ) :
			the_post();

			get_template_part( 'template-parts/content', get_post_type() );

			the_post_navigation(
				[
					'prev_text' => '<span class="nav-subtitle">Previous:</span> <span class="nav-title">%title</span>',
					'next_text' => '<span class="nav-subtitle">Next:</span> <span class="nav-title">%title</span>',
					'class' => 'content-width',
				]
			);

			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();
