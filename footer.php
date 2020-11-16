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

		<div class="footer-main">
			<div class="content-width">

				<section class="col-identity">
					<div class="site-logo">
						<?php svg( 'decal-duo-opacity.svg' ); ?>
						<?php svg( 'type-duo-opacity.svg' ); ?>
					</div>
					<div class="site-description">
						<p></p>
					</div>
					<div class="site-social">
						<p><span class="fa-linkedin-in"><?php fa( 'linkedin-in', 'brands' ); ?></span>Follow for Updates</p>
					</div>
				</section>

				<section class="col-recent">
					<h5>Recent</h5>
					<?php the_recent_posts(); ?>
				</section>

				<section class="col-links">
					<?php the_sites(); ?>
				</section>

			</div>
		</div><!-- .footer-main -->

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
