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

				<div class="button-group">
					<div class="button-dark">
						<a class="ptc-completionist-free-download" href="<?php echo esc_url( $free_download_url ); ?>" target="_blank">Download Now <?php fa( 'download' ); ?></a>
						<span>Current Release <strong>v<?php echo esc_html( $free_download_tag ); ?></strong></span>
					</div>
					<div class="button-dark">
						<a class="ptc-completionist-see-docs" href="#" target="_blank">Documentation <?php fa( 'long-arrow-alt-right' ); ?></a>
						<span><em>Coming Soon!</em></span>
					</div>
				</div>

			</div>
		</header>

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

				<div class="button-group">
					<div class="button-dark">
						<a class="ptc-completionist-free-download" href="<?php echo esc_url( $free_download_url ); ?>" target="_blank">Download Now <?php fa( 'download' ); ?></a>
						<span>Current Release <strong>v<?php echo esc_html( $free_download_tag ); ?></strong></span>
					</div>
					<div class="button-dark">
						<a class="ptc-completionist-see-docs" href="#" target="_blank">Documentation <?php fa( 'long-arrow-alt-right' ); ?></a>
						<span><em>Coming Soon!</em></span>
					</div>
				</div>

				<p class="asana-disclaimer">**Completionist by Purple Turtle Creative is not associated with Asana. Asana is a trademark and service mark of Asana, Inc., registered in the U.S. and in other countries. <a href="https://asana.com/">Learn more.</a></p>

			</div>
		</footer>

	</main><!-- #main -->

<?php
get_footer();
