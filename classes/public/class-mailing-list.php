<?php
/**
 * Mailing_List Class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

require_once THEME_PATH . '/classes/public/class-captcha.php';

/**
 * Static class for managing email mailing lists.
 *
 * @link https://documentation.mailgun.com/en/latest/api-mailinglists.html
 */
class Mailing_List {

	/**
	 * The number of API requests permitted per limit period.
	 *
	 * @var int REQUESTS_LIMIT_COUNT
	 */
	private const REQUESTS_LIMIT_COUNT = 100;

	/**
	 * The period for which the subscriber limit is tracked and
	 * eventually reset (in seconds).
	 *
	 * @var int SUBSCRIBER_LIMIT_DAYS
	 */
	private const REQUESTS_LIMIT_PERIOD = 14 * \DAY_IN_SECONDS;

	/**
	 * Hooks code into WordPress.
	 */
	public static function register() {
		add_action(
			'wp_enqueue_scripts',
			__CLASS__ . '::register_scripts'
		);
	}

	/**
	 * Registers the related script and style handles.
	 */
	public static function register_scripts() {}

	/**
	 * Gets the mailing list subscription form HTML.
	 *
	 * @param string $list_id The mailing list ID.
	 * @param string $title_text The form title text.
	 * @param string $body_text The form body text.
	 * @param string $captcha_action The captcha widget ID.
	 * @param string $submit_label Optional. The submit button
	 * label text. Default 'Subscribe'.
	 */
	public static function render_subscription_form(
		string $list_id,
		string $title_text,
		string $body_text,
		string $captcha_action,
		string $submit_label = 'Subscribe'
	) {
		// @TODO - Build the REST API endpoint to process the submission.
		// @TODO - Write the JavaScript for asynchronous submission.
		// @TODO - Define the ACF block for custom render placement.
		?>
			<form method="POST" action="">
				<?php
				if ( ! empty( $title_text ) ) {
					echo '<h3>' . esc_html( $title_text ) . '</h3>';
				}
				if ( ! empty( $body_text ) ) {
					echo wp_kses_post( $body_text );
				}
				?>
				<input type="email" name="ptc_subscribe_email" placeholder="mail@example.com" />
				<input type="hidden" name="ptc_subscribe_list" value="<?php echo esc_attr( $list_id ); ?>" />
				<?php Captcha::render( $captcha_action ); ?>
				<button type="submit"><?php echo esc_html( $submit_label ); ?></button>
			</form>
		<?php
	}
}
