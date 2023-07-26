<?php
/**
 * System page to confirm email verification requests.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

require_once THEME_PATH . '/classes/public/class-mailing-lists.php';

// Process the request.

$is_success = false;

if (
	! empty( $_GET['subscriber'] ) &&
	! empty( $_GET['list_id'] ) &&
	! empty( $_GET['token'] )
) {
	$is_success = Mailing_Lists::process_email_verification(
		sanitize_text_field( wp_unslash( $_GET['subscriber'] ) ),
		sanitize_text_field( wp_unslash( $_GET['list_id'] ) ),
		sanitize_text_field( wp_unslash( $_GET['token'] ) )
	);
}

// Begin template output.
get_header();
?>

	<main id="primary" <?php post_class( 'site-main  has-primary-background-color has-background' ); ?>>
		<div class="content-width-slim">
			<?php if ( $is_success ) : ?>
				<p class="banner banner-success has-text-align-center"><strong>Success!</strong><br />You are now subscribed to receive the latest updates.</p>
			<?php else : ?>
				<p class="banner banner-danger has-text-align-center"><strong>Oops!</strong> Something went wrong, so you still aren't subscribed.<br />Please try again later or email <a href="mailto:michelle@purpleturtlecreative.com">michelle@purpleturtlecreative.com</a> for assistance.</p>
			<?php endif; ?>
		</div>
	</main><!-- #main -->

<?php
get_footer();
