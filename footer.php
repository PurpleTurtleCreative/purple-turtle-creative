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
						<div class="button-group left">
							<a class="referral-badge free-estimate" href="<?php echo esc_url( home_url( '#contact' ) ); ?>">
								<?php fa( 'file-invoice', 'solid' ); ?>
								<p>
									<small>Request an</small>
									Estimate
								</p>
							</a>
							<a class="referral-badge linkedin" href="https://www.linkedin.com/company/purple-turtle-creative" target="_blank">
								<?php fa( 'linkedin', 'brands' ); ?>
								<p>
									<small>Follow on</small>
									LinkedIn
								</p>
							</a>
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

				<div class="referrals">
					<p><small>Claim discounts and benefits by using my referral links:</small></p>
					<a class="referral-badge digitalocean" href="https://m.do.co/c/379a11ccae2a" target="_blank" rel="sponsored">
						<?php fa( 'digital-ocean', 'brands' ); ?>
						<p>
							<small>Hosting with</small>
							DigitalOcean
						</p>
					</a>
					<a class="referral-badge wp-rocket" href="https://wp-rocket.me/?ref=058407d8" target="_blank" rel="sponsored">
						<?php svg( 'wp-rocket-logo.svg' ); ?>
						<p>
							<small>Performance via</small>
							WP Rocket
						</p>
					</a>
					<a class="referral-badge dropbox" href="https://www.dropbox.com/referrals/AAC1FnrY_5WdLC4PUqUPV3HZy5IKCmD1w5o?src=global9" target="_blank" rel="sponsored">
						<?php fa( 'dropbox', 'brands' ); ?>
						<p>
							<small>Storage with</small>
							Dropbox
						</p>
					</a>
				</div>
			</div>
		</div><!-- .footer-main -->

		<div class="site-info">
			<div class="content-width">

				<div class="left">
					<small>&copy;&nbsp;<?php echo esc_html( gmdate( 'Y' ) ); ?>&nbsp;Purple&nbsp;Turtle&nbsp;Creative,&nbsp;LLC. All&nbsp;rights&nbsp;reserved.</small>
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
