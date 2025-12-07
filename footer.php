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

defined( 'ABSPATH' ) || die();

require_once THEME_PATH . '/classes/public/class-html-routes.php';

?>

	<footer id="footer" class="site-footer">

		<?php if ( ! HTML_Routes::is_html_endpoint() ) : ?>
		<div class="footer-main">
			<div class="content-width">

				<div class="left">
					<section class="col-identity">
						<div class="site-logo">
							<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
								<?php svg( 'decal-duo-opacity.svg' ); ?>
								<?php svg( 'type-duo-opacity.svg' ); ?>
							</a>
						</div>
						<div class="site-description">
							<p><?php echo get_bloginfo( 'description' ); ?></p>
							<p class="has-color has-primary-light-color"><small>Purple&nbsp;Turtle&nbsp;Creative,&nbsp;LLC is a limited liability company registered&nbsp;with&nbsp;New&nbsp;York&nbsp;State.</small></p>
						</div>
						<div class="button-group left">
							<a class="referral-badge linkedin" href="https://www.linkedin.com/company/purple-turtle-creative" target="_blank">
								<?php fa( 'linkedin', 'brands' ); ?>
								<p>
									<small>Follow on</small>
									LinkedIn
								</p>
							</a>
							<a class="referral-badge digitalocean" href="https://m.do.co/c/379a11ccae2a" target="_blank" rel="sponsored nofollow">
								<?php fa( 'digital-ocean', 'brands' ); ?>
								<p>
									<small>Get $200 with</small>
									DigitalOcean
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
						<a style="cursor:pointer;" href="<?php echo esc_url( site_url( '/completionist/' ) ); ?>">Completionist<br /><small style="color:#e4e6fd;font-weight:300;">&#9584; Asana for WordPress</small></a>
						<a style="cursor:pointer;" href="<?php echo esc_url( site_url( '/completionist-pro/' ) ); ?>">Completionist PRO<br /><small style="color:#e4e6fd;font-weight:300;">&#9584; Asana client portals</small></a>
						<h2 class="h6">Contact</h2>
						<a href="mailto:michelle@purpleturtlecreative.com">Michelle Blanchette</a>
					</section>
				</div>

				<div class="has-color has-primary-light-color has-text-align-center">
					<p><small>Purple&nbsp;Turtle&nbsp;Creative,&nbsp;LLC is not affiliated with, endorsed by, or in any way associated with any third-party brands or trademarks mentioned on this website. All trademarks, service marks, and brand names are the property of their respective owners.</small></p>
				</div>
			</div>
		</div><!-- .footer-main -->
		<?php endif; // is not html endpoint. ?>

		<div class="site-info">
			<div class="content-width">

				<div class="left">
					<small>&copy;&nbsp;<?php echo esc_html( gmdate( 'Y' ) ); ?>&nbsp;Purple&nbsp;Turtle&nbsp;Creative,&nbsp;LLC. All&nbsp;rights&nbsp;reserved.</small>
				</div>

				<div class="right">
					<small><?php a_link_to( 'terms-conditions' ); ?><span class="separator"></span><?php a_link_to( 'privacy-policy' ); ?></small>
				</div>

			</div>
		</div><!-- .site-info -->
	</footer><!-- #footer -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
