<?php
/**
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

require_once \PTC_Resources_Server\PLUGIN_PATH . 'src/public/servers/class-plugins-server.php';

$free_download_url = '#';
$free_download_tag = '{{Error}}';
if ( class_exists( '\PTC_Resources_Server\Plugins\Server' ) ) {
	$plugins_server = new \PTC_Resources_Server\Plugins\Server();
	$free_download_url = $plugins_server->generate_free_download_url( 'completionist', 'latest' );
	if ( ! empty( $plugins_server->plugin_tag ) ) {
		$free_download_tag = $plugins_server->plugin_tag;
	}
}

get_header();
?>

	<main id="primary" <?php post_class( 'site-main' ); ?>>

		<header class="wave-trim-bottom">
			<div class="content-width-slim">

				<div class="heading-info">
					<h1>Completionist</h1>
					<h2>Asana for WordPress</h2>
					<p>Establish a complete project management workflow between your Asana workspace and WordPress dashboard.</p>
				</div>

				<div class="button-group center">
					<div class="button">
						<a class="icon-button has-primary-dark-background-color --is-icon-position-after ptc-completionist-free-download" href="<?php echo esc_url( $free_download_url ); ?>" target="_blank" rel="nofollow"><?php fa( 'download' ); ?>Download Now</a>
						<span>
							<a href="<?php the_permalink(); ?>plugin-info/#latest">
								Current Release <strong>v<?php echo esc_html( $free_download_tag ); ?></strong>
							</a>
						</span>
					</div>
					<div class="button">
						<a class="icon-button has-primary-dark-background-color --is-icon-position-after ptc-completionist-see-docs" href="https://docs.purpleturtlecreative.com/completionist/" target="_blank"><?php fa( 'long-arrow-alt-right' ); ?>Documentation</a>
						<span><a href="<?php the_permalink(); ?>plugin-info/#changelog">View Changelog</a></span>
					</div>
				</div>

			</div>
		</header>

		<div class="content-width-slim">
			<?php
			Mailing_Lists::render_subscription_form(
				'mail.test',
				'Join the Mailing List',
				'Effortlessly keep updated on the latest features and announcements.',
				'ptc-local-test',
				'Submit'
			);
			?>
		</div>

		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'page' );
		endwhile; // End of the loop.
		?>

		<footer class="wave-trim-top">
			<div class="content-width-slim">

				<h2 id="try-completionist">Ready to try?</h2>
				<p>Download the Completionist WordPress plugin now to integrate your Asana tasks with your WordPress admin area.</p>

				<div class="button-group center">
					<div class="button">
						<a class="icon-button has-primary-dark-background-color --is-icon-position-after ptc-completionist-free-download" href="<?php echo esc_url( $free_download_url ); ?>" target="_blank" rel="nofollow"><?php fa( 'download' ); ?>Download Now</a>
						<span>
							<a href="<?php the_permalink(); ?>plugin-info/#latest">
								Current Release <strong>v<?php echo esc_html( $free_download_tag ); ?></strong>
							</a>
						</span>
					</div>
					<div class="button">
						<a class="icon-button has-primary-dark-background-color --is-icon-position-after ptc-completionist-see-docs" href="https://docs.purpleturtlecreative.com/completionist/" target="_blank"><?php fa( 'long-arrow-alt-right' ); ?>Documentation</a>
						<span><a href="<?php the_permalink(); ?>plugin-info/#changelog">View Changelog</a></span>
					</div>
				</div>

				<p class="asana-disclaimer">**Completionist by Purple Turtle Creative is not associated with Asana. Asana is a trademark and service mark of Asana, Inc., registered in the U.S. and in other countries. <a href="https://asana.com/">Learn more.</a></p>

			</div>
		</footer>

	</main><!-- #main -->

<?php
get_footer();
