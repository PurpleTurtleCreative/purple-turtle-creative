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

defined( 'ABSPATH' ) || die();

get_header();
?>

	<main id="primary" <?php post_class( 'site-main' ); ?>>

		<header class="page-header wave-trim-bottom">
			<div class="content-width">

				<?php
				$before_page_title = '<h1 class="page-title">';
				$after_page_title = '</h1>';

				if ( is_home() ) {
					echo wp_kses_post( $before_page_title . '<span>' . single_post_title( '', false ) . '</span>' . $after_page_title );
				} elseif ( is_search() ) {
					echo wp_kses_post( $before_page_title . 'Search Results for:<br /><span>' . get_search_query() . '</span>' . $after_page_title );
				} elseif ( is_post_type_archive( 'ptc-portfolio' ) ) {
					echo wp_kses_post( $before_page_title . '<span>' . post_type_archive_title( '', false ) . '</span>' . $after_page_title );
					the_archive_description( '<div class="archive-description">', '</div>' );
				} elseif ( is_404() ) {
					echo wp_kses_post( $before_page_title . 'Error <span>Not Found</span>' . $after_page_title );
				} else {
					the_archive_title( $before_page_title, $after_page_title );
					the_archive_description( '<div class="archive-description">', '</div>' );
				}

				if ( ! is_post_type_archive( 'ptc-portfolio' ) ) {
					get_search_form();
				}
				?>

				<div class="all-categories">
					<?php
					if ( is_post_type_archive( 'ptc-portfolio' ) ) {
						all_categories( 'skill' );
					} else {
						// Note the taxonomy is blank for regular post archive.
						all_categories( $GLOBALS['wp_query']->get( 'taxonomy' ) );
					}
					?>
				</div>

			</div><!-- .content-width -->
		</header><!-- .page-header -->

		<?php if ( have_posts() ) : ?>

			<div class="posts-loop content-width">
				<?php
				/* Start the Loop */
				while ( have_posts() ) :
					the_post();

					get_template_part( 'template-parts/content', get_post_type() . '-preview' );

				endwhile;
				?>
			</div>

			<nav class="pagination content-width">
				<?php
				echo paginate_links(
					array(
						'prev_text' => get_fa( 'angle-left' ) . ' Back',
						'next_text' => 'Next ' . get_fa( 'angle-right' ),
					)
				);
				?>
			</nav>

		<?php else : ?>

			<div class="no-results not-found content-width">
				<p><strong>No posts were found.</strong><br />Try using the search form or category buttons above!</p>
			</div>

		<?php endif; ?>

	</main><!-- #main -->

<?php
get_footer();
