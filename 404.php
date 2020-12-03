<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

get_header();
?>

	<main id="primary" <?php post_class( 'site-main' ); ?>>

		<header class="page-header wave-trim-bottom">
			<div class="content-width">

				<h1 class="page-title"><span>Error 404</span> Not Found</h1>

				<div class="archive-description">
					<p>Dang it! I couldn't find what you were looking for, but maybe browsing the categories below with help you find it!</p>
				</div>

				<div class="all-categories">
					<?php all_categories(); ?>
				</div>

			</div><!-- .content-width -->
		</header><!-- .page-header -->

		<?php if ( have_posts() ) : ?>

			<div class="posts-loop content-width">
				<?php
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();

					get_template_part( 'template-parts/content', 'post-preview' );

				endwhile;
				?>
			</div>

		<?php else : ?>

			<div class="no-results not-found content-width">

			<?php if ( is_search() ) : ?>
				<p>No posts were found to include your search terms. Try again with some different keywords!</p>
			<?php else : ?>
				<p>No posts were found. Try using the search form below or the category buttons above!</p>
			<?php endif; ?>

			<?php get_search_form(); ?>

			</div><!-- .page-content -->

		<?php endif; ?>

	</main><!-- #main -->

<?php
get_footer();
