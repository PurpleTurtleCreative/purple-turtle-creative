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
					<h1><?php echo esc_html( $plugin_info->name ); ?></h1>
					<p class="plugin-info__description">
						<?php echo esc_html( $plugin_info->description ); ?>
					</p>
				</div>

				<h2>Current Release Package Information</h2>
				<div class="plugin-header-info">
					<div class="plugin-header-info__entry">
						<h3>Version:</h3>
						<p><?php echo esc_html( $plugin_info->version ); ?></p>
					</div>
					<div class="plugin-header-info__entry">
						<h3>Released:</h3>
						<p title="<?php echo esc_attr( $plugin_info->last_updated ); ?>">
							<?php
							$last_updated_date = date_create_from_format( 'Y-m-d H:i:s', $plugin_info->last_updated );
							echo esc_html( human_time_diff( date_timestamp_get( $last_updated_date ) ) . ' ago' );
							?>
						</p>
					</div>
					<div class="plugin-header-info__entry">
						<h3>Requires WordPress:</h3>
						<p><?php echo esc_html( $plugin_info->requires ); ?></p>
					</div>
					<div class="plugin-header-info__entry">
						<h3>Tested Up To:</h3>
						<p><?php echo esc_html( $plugin_info->tested ); ?></p>
					</div>
					<div class="plugin-header-info__entry">
						<h3>Requires PHP:</h3>
						<p><?php echo esc_html( $plugin_info->requires_php ); ?></p>
					</div>
				</div>

				<div class="button-dark">
					<a href="<?php echo esc_url( $plugin_info->homepage ); ?>">
						Go to Plugin Homepage <?php fa( 'long-arrow-alt-right' ); ?>
					</a>
				</div>

			</div>
		</header>

		<div class="plugin-changelog">
			<div class="content-width">

				<h2>Changelog</h2>

				<div class="plugin-changelog__content">
					<?php echo wp_kses_post( $plugin_info->sections->changelog ); ?>
				</div>

			</div>
		</div>

	</main><!-- #main -->

<?php
get_footer();
