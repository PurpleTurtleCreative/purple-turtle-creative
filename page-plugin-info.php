<?php
/**
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

require_once \PTC_Resources_Server\PLUGIN_PATH . 'src/public/servers/class-plugins-server.php';

$parent_post = get_post_field( 'post_parent', get_post() );
$parent_slug = get_post_field( 'post_name', $parent_post );

if ( $parent_slug && class_exists( '\PTC_Resources_Server\Plugins\Server' ) ) {
	$plugins_server = new \PTC_Resources_Server\Plugins\Server();
	$plugin_zip = $plugins_server->find_latest( $parent_slug );
	if ( $plugin_zip ) {
		$plugin_info = $plugins_server->get_plugin_info_from_zip( $parent_slug, $plugin_zip );
	} else {
		wp_die( 'Could not find plugin package.' );
	}
} else {
	wp_die( 'Plugin information is currently unavailable.' );
}

get_header();
?>

	<main id="primary" <?php post_class( 'site-main' ); ?>>

		<header class="wave-trim-bottom">
			<div class="content-width-slim">

				<div class="plugin-info">
					<h1><?php echo esc_html( ucfirst( str_replace( '-', ' ', $parent_slug ) ) ); ?></h1>
					<p class="plugin-info__description">
						<?php echo esc_html( $plugin_info->description ); ?>
					</p>
				</div>

				<div class="button-group center">
					<div class="button">
						<a class="icon-button has-primary-dark-background-color --is-icon-position-after" href="<?php echo esc_url( $plugin_info->homepage ); ?>"><?php fa( 'long-arrow-alt-right' ); ?>Go to Plugin Page</a>
					</div>
				</div>

			</div>
		</header>

		<div class="package-info content-width-slim">

			<div class="plugin-headers">

					<h2 id="latest">Latest Release Info</h2>

					<ul>
						<li>
							<h3>Version:</h3>
							<p><?php echo esc_html( $plugin_info->version ); ?></p>
						</li>
						<li>
							<h3>Released:</h3>
							<p title="<?php echo esc_attr( $plugin_info->last_updated ); ?>">
								<?php
								$last_updated_date = date_create_from_format( 'Y-m-d H:i:s', $plugin_info->last_updated );
								echo esc_html( human_time_diff( date_timestamp_get( $last_updated_date ) ) . ' ago' );
								?>
							</p>
						</li>
						<li>
							<h3>Requires WP:</h3>
							<p><?php echo esc_html( $plugin_info->requires ); ?></p>
						</li>
						<li>
							<h3>Tested Up To:</h3>
							<p><?php echo esc_html( $plugin_info->tested ); ?></p>
						</li>
						<li>
							<h3>Requires PHP:</h3>
							<p><?php echo esc_html( $plugin_info->requires_php ); ?></p>
						</li>
						<li>
							<h3>File Size:</h3>
							<p><?php echo esc_html( round( ( filesize( $plugin_zip ) / 1000000 ), 2 ) . ' MB' ); ?></p>
						</li>
					</ul>

			</div>

			<div class="plugin-changelog content-width-slim">

					<h2 id="changelog">Changelog</h2>

					<div class="plugin-changelog__timeline">
						<div class="content">
							<?php echo wp_kses_post( $plugin_info->sections->changelog ); ?>
						</div>
					</div>

			</div>

		</div><!-- .package-info -->

	</main><!-- #main -->

<?php
get_footer();
