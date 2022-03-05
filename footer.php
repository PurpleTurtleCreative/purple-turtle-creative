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
							<a href="<?php echo esc_url( home_url() ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
								<?php svg( 'decal-duo-opacity.svg' ); ?>
								<?php svg( 'type-duo-opacity.svg' ); ?>
							</a>
						</div>
						<div class="site-description">
							<p><?php echo get_bloginfo( 'description' ); ?></p>
							<p class="llc-notice"><small>Purple Turtle Creative, LLC is a limited liability company registered with New York State.</small></p>
						</div>
						<div class="site-social">
							<a href="https://www.linkedin.com/company/purple-turtle-creative" target="_blank" rel="noopener"><span class="fa-linkedin-in"><?php fa( 'linkedin-in', 'brands' ); ?></span>Follow for Updates</a>
						</div>
					</section>
				</div>

				<div class="right">
					<section class="col-recent">
						<h2 class="h6">Recent</h2>
						<?php the_recent_posts(); ?>
					</section>

					<section class="col-links">
						<h2 class="h6">Plugins</h2>
						<a href="<?php echo esc_url( site_url( '/completionist/' ) ); ?>">Completionist</a>
						<a href="<?php echo esc_url( 'https://wordpress.org/plugins/grouped-content/' ); ?>" target="_blank">Grouped Content</a>
						<h2 class="h6">Legal</h2>
						<?php a_link_to( 'terms-conditions' ); ?>
						<?php a_link_to( 'privacy-policy' ); ?>
						<h2 class="h6">Contact</h2>
						<a href="mailto:michelle@purpleturtlecreative.com">Michelle Blanchette</a>
					</section>
				</div>

			</div>
		</div><!-- .footer-main -->

		<div class="site-info">
			<div class="content-width">

				<div class="left">
					<small>&copy;&nbsp;<?php echo date( 'Y' ); ?>&nbsp;Purple&nbsp;Turtle&nbsp;Creative,&nbsp;LLC. All&nbsp;rights&nbsp;reserved.</small>
				</div>

				<div class="right">
					<small>Website designed and developed by <a href="https://www.linkedin.com/in/michelle-blanchette/" target="_blank" rel="noopener">Michelle&nbsp;Blanchette</a>.</small>
				</div>

			</div>
		</div><!-- .site-info -->
	</footer><!-- #footer -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
