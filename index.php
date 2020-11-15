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
					$before_page_title = '<h1 class="page-title">';
					$after_page_title = '</h1>';

					if ( is_home() ) {
						echo wp_kses_post( $before_page_title . '<span>' . single_post_title( '', false ) . '</span>' . $after_page_title );
					} elseif ( is_search() ) {
						echo wp_kses_post( $before_page_title . 'Search Results for:<br /><span>' . get_search_query() . '</span>' . $after_page_title );
					} else {
						the_archive_title( $before_page_title, $after_page_title );
						the_archive_description( '<div class="archive-description">', '</div>' );
					}
					?>

					<div class="all-categories">
						<?php all_categories(); ?>
					</div>

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
