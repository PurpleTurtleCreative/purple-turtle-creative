<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

?>

	<footer id="footer" class="site-footer">
		<div class="site-info">
			<div class="content-width">
				<div class="left">
					<span>&copy; <a href="https://www.linkedin.com/in/michelle-blanchette/">Michelle Blanchette</a>. All rights reserved.</span>
				</div>
				<div class="right">
					<span title="Made with love in New York, USA">Made with <?php fa( 'heart' ); ?> in New York, USA</span>
				</div>
			</div>
		</div><!-- .site-info -->
	</footer><!-- #footer -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
