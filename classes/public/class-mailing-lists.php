<?php
/**
 * Mailing_List Class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

require_once THEME_PATH . '/classes/public/class-captcha.php';
require_once THEME_PATH . '/classes/public/class-html-routes.php';

/**
 * Static class for managing email mailing lists.
 *
 * @link https://documentation.mailgun.com/en/latest/api-mailinglists.html
 */
class Mailing_Lists {

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
	 * The list of valid mailing list IDs mapped to the actual
	 * mailing list which they represent.
	 *
	 * This is used for securing the frontend and obscuring the
	 * actual mailing lists, though that doesn't mean much since
	 * they are rendered to the frontend for form submissions.
	 * Still, a bot can't blatantly see what the actual mailing
	 * list is and it cannot be guessed.
	 *
	 * Keys are the actual mailing list addresses and the values
	 * are (random, hard-coded) obscure IDs.
	 *
	 * @var string[] MAILING_LIST_IDS
	 */
	private const MAILING_LIST_IDS = array(
		'completionist@purpleturtlecreative.com' => 'RN3epj3Y8VmA9iKj',
		'mail.test@purpleturtlecreative.com' => '4T3HmaE8JtUovVF7',
	);

	/**
	 * Hooks code into WordPress.
	 */
	public static function register() {
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::register_scripts' );
		add_action( 'html_routes_init', __CLASS__ . '::register_html_routes' );
		add_action( 'rest_api_init', __CLASS__ . '::register_rest_routes' );
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
		string $mailing_list,
		string $title_text,
		string $body_text,
		string $captcha_action,
		string $submit_label = 'Subscribe'
	) {
		// @TODO - Build the REST API endpoint to process the submission.
		// @TODO - Write the JavaScript for asynchronous submission.
		// @TODO - Define the ACF block for custom render placement.

		if ( empty( static::MAILING_LIST_IDS[ $mailing_list ] ) ) {
			trigger_error(
				'Refused to render mailing list subscription form for invalid mailing list address: ' . $mailing_list,
				\E_USER_WARNING
			);
			return;
		}

		$form_action_url = rest_url( REST_API_NAMESPACE_V1 . '/mailing-lists/subscribe' );

		?>
			<form method="POST" action="<?php echo esc_url( $form_action_url ); ?>">
				<?php
				if ( ! empty( $title_text ) ) {
					echo '<h3>' . esc_html( $title_text ) . '</h3>';
				}
				if ( ! empty( $body_text ) ) {
					echo wp_kses_post( $body_text );
				}
				?>
				<input type="email" name="email" placeholder="mail@example.com" required />
				<input type="hidden" name="list_id" value="<?php echo esc_attr( static::MAILING_LIST_IDS[ $mailing_list ] ); ?>" />
				<?php wp_nonce_field( 'wp_rest', '_wpnonce', true, true ); ?>
				<?php Captcha::render( $captcha_action ); ?>
				<button type="submit"><?php echo esc_html( $submit_label ); ?></button>
			</form>
		<?php
	}

	/**
	 * Gets the mailing list address by ID.
	 *
	 * @param string $list_id The list ID.
	 *
	 * @return string|false The mailing list address.
	 */
	private static function get_mailing_list( string $list_id ) : string|bool {
		return array_search( $list_id, static::MAILING_LIST_IDS, true );
	}

	/**
	 * Registers HTML route endpoints.
	 */
	public static function register_html_routes() {
		HTML_Routes::register_route(
			'/mailing-lists/email-verification',
			THEME_PATH . '/html-routes/email-verification.php'
		);
	}

	/**
	 * Registers REST API route endpoints.
	 */
	public static function register_rest_routes() {
		register_rest_route(
			REST_API_NAMESPACE_V1,
			'/mailing-lists/subscribe',
			array(
				array(
					'methods' => 'POST',
					'callback' => __CLASS__ . '::handle_post_subscribe',
					'permission_callback' => '__return_true',
					'args' => array(
						'email' => array(
							'type' => 'string',
							'required' => true,
							// WordPress discloses these functions may be
							// inaccurate, but I'd rather be safer and mayyybe
							// miss a few subscribers than welcome nonsense.
							'sanitize_callback' => 'sanitize_email',
							'validate_callback' => 'is_email',
						),
						'list_id' => array(
							'type' => 'string',
							'required' => true,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function( $value, $request, $param ) {
								return in_array( $value, static::MAILING_LIST_IDS, true );
							},
						),
						'cf-turnstile-action' => array(
							'type' => 'string',
							'required' => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'cf-turnstile-response' => array(
							'type' => 'string',
							'required' => true,
							// Hopefully this doesn't invalidate successful tokens.
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function( $value, $request, $param ) {
								return Captcha::verify(
									$request['cf-turnstile-action'],
									$request['cf-turnstile-response']
								);
							},
						),
					),
				),
			)
		);
	}

	/**
	 * Handles a requrest to subscribe to a mailing list.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response|\WP_Error The response.
	 */
	public static function handle_post_subscribe(
		\WP_REST_Request $request
	) : \WP_REST_Response|\WP_Error {

		$mailing_list = static::get_mailing_list( $request['list_id'] );
		if ( ! $mailing_list ) {
			return \WP_Error(
				'invalid_list',
				'The provided list_id is invalid.',
				array( 'status' => 400 )
			);
		}

		$response = array(
			'message' => "Email {$request['email']} is eligible to subscribe to the {$mailing_list} mailing list!",
		);

		return new \WP_REST_Response( $response, 200 );
	}
}
