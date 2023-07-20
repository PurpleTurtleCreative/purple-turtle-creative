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

	<main id="primary" <?php post_class( 'site-main' ); ?>>
		<div class="content-width">
			<h1>Handling Email Verification</h1>
			<pre style="margin:5rem 0;">
				<?php print_r( $GLOBALS['wp_query'] ); ?>
			</pre>
		</div>
	</main><!-- #main -->

<?php
get_footer();
