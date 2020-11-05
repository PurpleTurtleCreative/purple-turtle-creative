<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Purple_Turtle_Creative
 */

get_header();
?>

	<main id="primary" <?php post_class( 'site-main' ); ?>>

		<section class="error-404 not-found">
			<header class="page-header">
				<h1 class="page-title">Oops! That page can't be found.</h1>
			</header><!-- .page-header -->

			<div class="page-content">
				<p>It looks like nothing was found at this location. Maybe try one of the links below or a search?</p>

					<?php
					get_search_form();

					the_widget( 'WP_Widget_Recent_Posts' );
					?>

					<div class="widget widget_categories">
						<h2 class="widget-title">Most Used Categories</h2>
						<ul>
							<?php
							wp_list_categories(
								array(
									'orderby'    => 'count',
									'order'      => 'DESC',
									'show_count' => 1,
									'title_li'   => '',
									'number'     => 10,
								)
							);
							?>
						</ul>
					</div><!-- .widget -->

					<?php
					/* translators: %1$s: smiley */
					$purple_turtle_creative_archive_content = '<p>Try looking in the monthly archives.</p>';
					the_widget( 'WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$purple_turtle_creative_archive_content" );

					the_widget( 'WP_Widget_Tag_Cloud' );
					?>

			</div><!-- .page-content -->
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
