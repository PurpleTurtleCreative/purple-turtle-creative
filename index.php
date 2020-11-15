<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

get_header();
?>

	<main id="primary" <?php post_class( 'site-main template-index' ); ?>>

		<?php if ( have_posts() ) : ?>

			<header class="page-header wave-trim-bottom">
				<div class="content-width">

					<?php
					if ( is_home() ) {
						echo '<h1 class="page-title">' . single_post_title( '', false ) . '</h1>';
					} else {
						the_archive_title( '<h1 class="page-title">', '</h1>' );
						the_archive_description( '<div class="archive-description">', '</div>' );
					}
					?>

					<?php all_categories(); ?>

				</div>
			</header><!-- .page-header -->

			<div class="posts-loop content-width">
				<?php
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();

					get_template_part( 'template-parts/content', 'post-preview' );

				endwhile;
				?>
			</div>

		<?php
		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

	</main><!-- #main -->

<?php
get_footer();
