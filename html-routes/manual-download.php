<?php
/**
 * System page to confirm email verification requests.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

require_once THEME_PATH . '/classes/public/class-manual-downloads.php';
require_once THEME_PATH . '/classes/includes/class-event-tracker.php';

// Process the request.

$resource_path = sanitize_text_field( wp_unslash( $_GET['resource_path'] ?? '' ) );
$email         = sanitize_text_field( wp_unslash( $_GET['email'] ?? '' ) );
$license_key   = sanitize_text_field( wp_unslash( $_GET['license_key'] ?? '' ) );

$event_params = array(
	'event_category' => 'manual_download',
	'event_label'    => $resource_path,
);

if ( $email && $license_key ) {
	$event_params['user_id'] = wp_hash( "{$email}_{$license_key}" );
}

// Record GA4 event.
Event_Tracker::record_ga4_event( 'visit_manual_download', $event_params );

// Begin template output.
get_header();
?>

	<main id="primary" <?php post_class( 'site-main has-primary-background-color has-background' ); ?>>
		<div class="content-width-slim">
			<h1 class="wp-block-heading has-text-align-center has-off-white-color has-color">Product Download</h1>
			<p class="has-text-align-center has-off-white-color has-color">Thank you for your support! Please complete the form below to download your purchase, or <a class="badge-dark" href="https://store.purpleturtlecreative.com/billing" target="_blank">sign in</a> to manage your license keys and billing information.</p>
			<?php
			Manual_Downloads::render_manual_download_form(
				$resource_path,
				$email,
				$license_key
			);
			?>
			<p id="backtoblog">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">&larr; Back to <?php bloginfo( 'name' ); ?></a>
			</p>
		</div>
	</main><!-- #main -->

<?php
get_footer();
