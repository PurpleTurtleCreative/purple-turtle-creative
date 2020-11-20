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

				<div class="left">
					<section class="col-identity">
						<div class="site-logo">
							<?php svg( 'decal-duo-opacity.svg' ); ?>
							<?php svg( 'type-duo-opacity.svg' ); ?>
						</div>
						<div class="site-description">
							<p>Welcome to my oasis! This is my little island in the great wide Web where I come to document and craft new things, especially WordPress things. As a full-stack web developer, there's always more to learn and create. So come relax a while. Maybe you'll learn a new skill, find a useful tool, or refresh your inspiration.</p>
						</div>
						<div class="site-social">
							<a href="https://www.linkedin.com/company/purple-turtle-creative" target="_blank"><span class="fa-linkedin-in"><?php fa( 'linkedin-in', 'brands' ); ?></span>Follow for Updates</a>
						</div>
					</section>
				</div>

				<div class="right">
					<section class="col-recent">
						<h6>Recent</h6>
						<?php the_recent_posts(); ?>
					</section>

					<section class="col-links">
						<h6>Plugins</h6>
						<?php the_sites(); ?>
						<h6>Legal</h6>
						<?php a_link_to( 'terms-conditions' ); ?>
						<?php a_link_to( 'privacy-policy' ); ?>
					</section>
				</div>

			</div>
		</div><!-- .footer-main -->

		<div class="site-info">
			<div class="content-width">

				<div class="left">
					<span>&copy; <a href="https://www.linkedin.com/in/michelle-blanchette/" target="_blank">Michelle Blanchette</a>. All rights reserved.</span>
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
