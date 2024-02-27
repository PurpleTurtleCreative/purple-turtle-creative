<?php
/**
 * Completionist plugin landing page.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

require_once THEME_PATH . '/classes/public/class-html-routes.php';
require_once \PTC_Resources_Server\PLUGIN_PATH . 'src/public/servers/class-plugins-server.php';
require_once \PTC_Resources_Server\PLUGIN_PATH . 'src/includes/class-billing.php';

$free_download_url = 'https://downloads.wordpress.org/plugin/completionist.zip';
$latest_tag        = '{{Error}}';
if ( class_exists( '\PTC_Resources_Server\Plugins\Server' ) ) {
	$plugins_server = new \PTC_Resources_Server\Plugins\Server();
	$plugins_server->generate_free_download_url( 'completionist', 'latest' );
	if ( ! empty( $plugins_server->plugin_tag ) ) {
		$latest_tag        = $plugins_server->plugin_tag;
		$free_download_url = "https://downloads.wordpress.org/plugin/completionist.{$latest_tag}.zip";
	}
}

get_header();
?>

	<main id="primary" <?php post_class( 'site-main' ); ?>>

		<header class="wave-trim-bottom">
			<div class="content-width-slim">

				<div class="heading-info">
					<div class="app-icon app-icon-asana">
						<img class="animate-floating" src="<?php echo esc_url( IMAGES_URI . '/tool_icons_asana.jpg' ); ?>" alt="Asana" width="120" height="120" draggable="false" />
					</div>
					<div class="app-icon app-icon-wordpress">
						<img class="animate-floating" src="<?php echo esc_url( IMAGES_URI . '/tool_icons_wordpress_grey.jpg' ); ?>" alt="WordPress" width="120" height="120" draggable="false" />
					</div>
					<h1>Enhance Your Asana + WordPress Workflow</h1>
					<p><strong>Completionist</strong> is the leading WordPress plugin to connect your favorite task and content management systems.</p>
				</div>

				<div class="button-group center">
					<div class="button">
						<a class="icon-button has-primary-dark-background-color --is-icon-position-after ptc-completionist-free-download" href="<?php echo esc_url( $free_download_url ); ?>" target="_blank" rel="nofollow"><?php fa( 'download' ); ?>Download Now</a>
						<span>
							<a href="<?php the_permalink(); ?>plugin-info/#latest">
								Current Release <strong>v<?php echo esc_html( $latest_tag ); ?></strong>
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

		<div class="ptc-resources-server-pricing-table">
			<?php
			$plugin_metadata = $plugins_server->get_metadata( 'completionist-pro' );
			foreach ( $plugin_metadata['checkout_plans'] as $plan ) {
				$plan['cancel_url'] = get_permalink();
				$plan['success_url'] = add_query_arg(
					'session_id',
					'{CHECKOUT_SESSION_ID}',
					HTML_Routes::get_url( '/billing/thank-you' )
				);
				$checkout_session = \PTC_Resources_Server\Billing::create_subscription_checkout_session( $plan );
				printf(
					'<a href="%s">Buy %d Sites at %s</a><pre>%s</pre>',
					esc_url( $checkout_session['url'] ),
					esc_html( $plan['line_items'][0]['quantity'] ),
					esc_html( '$' . number_format( $checkout_session['amount_total'] / 100, 2 ) ),
					esc_html( print_r( $checkout_session, true ) )
				);
			}
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

				<h2 id="try-completionist">Ready to boost your productivity?</h2>
				<p>Download the Completionist WordPress plugin now to integrate your Asana projects and tasks with your WordPress content.</p>

				<div class="button-group center">
					<div class="button">
						<a class="icon-button has-primary-dark-background-color --is-icon-position-after ptc-completionist-free-download" href="<?php echo esc_url( $free_download_url ); ?>" target="_blank" rel="nofollow"><?php fa( 'download' ); ?>Download Now</a>
						<span>
							<a href="<?php the_permalink(); ?>plugin-info/#latest">
								Current Release <strong>v<?php echo esc_html( $latest_tag ); ?></strong>
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
