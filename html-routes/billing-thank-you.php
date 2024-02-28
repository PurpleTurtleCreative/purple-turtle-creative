<?php
/**
 * System page to thank customer for successful purchase.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

require_once \PTC_Resources_Server\PLUGIN_PATH . 'src/includes/class-billing.php';
require_once THEME_PATH . '/classes/includes/class-event-tracker.php';

// Process the request.

$checkout_session = null;

if ( ! empty( $_GET['session_id'] ) ) {
	$checkout_session = \PTC_Resources_Server\Billing::get_checkout_session(
		sanitize_text_field( wp_unslash( $_GET['session_id'] ) )
	);
}

// Record GA4 event.
// See https://developers.google.com/analytics/devguides/collection/protocol/ga4/reference/events#purchase
// Event_Tracker::record_ga4_event(
// 	'email_verify',
// 	array(
// 		'event_category' => 'mailing_lists',
// 		'event_label'    => ( ( $is_success ) ? 'email_verification_success' : 'email_verification_error' ),
// 	)
// );

// Begin template output.
get_header();
?>

	<main id="primary" <?php post_class( 'site-main  has-primary-background-color has-background' ); ?>>
		<div class="content-width-slim">
			<?php if ( $checkout_session ) : ?>
				<pre class="banner banner-success"><?php esc_html( print_r( $checkout_session ) ); ?></pre>
			<?php else : ?>
				<pre class="banner banner-danger"><?php esc_html( print_r( $checkout_session ) ); ?></pre>
			<?php endif; ?>
			<p id="backtoblog">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>">&larr; Back to <?php bloginfo( 'name' ); ?></a>
			</p>
		</div>
	</main><!-- #main -->
<?php
get_footer();
