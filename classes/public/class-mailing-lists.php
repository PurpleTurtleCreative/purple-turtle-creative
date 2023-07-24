<?php
/**
 * Mailing_List Class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

require_once THEME_PATH . '/classes/public/class-captcha.php';
require_once THEME_PATH . '/classes/public/class-html-routes.php';
require_once THEME_PATH . '/classes/includes/class-util.php';

/**
 * Static class for managing email mailing lists.
 *
 * @link https://documentation.mailgun.com/en/latest/api-mailinglists.html
 */
class Mailing_Lists {

	/**
	 * The number of API requests permitted per limit period.
	 *
	 * This helps reduce API usage costs with our mailing provider
	 * in case of spam abuse or otherwise unexpected traffic.
	 *
	 * @var int API_REQUESTS_LIMIT_COUNT
	 */
	private const API_REQUESTS_LIMIT_COUNT = 200;

	/**
	 * The period (in seconds) for which the total API requests
	 * limit is tracked and eventually reset.
	 *
	 * @var int API_REQUESTS_LIMIT_PERIOD
	 */
	private const API_REQUESTS_LIMIT_PERIOD = 7 * \DAY_IN_SECONDS;

	/**
	 * The option name for storing the number of API requests
	 * executed within the current period.
	 *
	 * @var string API_REQUESTS_TRACKER_COUNT_OPTION
	 */
	private const API_REQUESTS_TRACKER_COUNT_OPTION = '_ptc_mailing_lists_api_requests_tracker_count';

	/**
	 * The option name for storing the start of the current API
	 * requests tracker period as Unix seconds.
	 *
	 * @var string API_REQUESTS_TRACKER_START_OPTION
	 */
	private const API_REQUESTS_TRACKER_START_OPTION = '_ptc_mailing_lists_api_requests_tracker_start';

	/**
	 * The total number of allowed verification requests
	 * per subscriber.
	 *
	 * @var int SUBSCRIBER_REQUEST_LIMIT_COUNT
	 */
	private const SUBSCRIBER_REQUEST_LIMIT_COUNT = 2;

	/**
	 * The cooldown period (in seconds) between each verification
	 * request of each subscriber.
	 *
	 * This prevents accidental resubmissions, bot attacks, or
	 * otherwise impatient requests.
	 *
	 * @var int SUBSCRIBER_REQUEST_COOLDOWN
	 */
	private const SUBSCRIBER_REQUEST_COOLDOWN = \HOUR_IN_SECONDS;

	/**
	 * The code for an unexpected error.
	 *
	 * @var int ERROR_UNEXPECTED
	 */
	private const ERROR_UNEXPECTED = 1;

	/**
	 * The error code when a short-term limit is exceeded.
	 *
	 * @var int ERROR_NEEDS_COOLDOWN
	 */
	private const ERROR_NEEDS_COOLDOWN = 2;

	/**
	 * The error code when a long-term limit is exceeded.
	 *
	 * @var int ERROR_LIMIT_EXCEEDED
	 */
	private const ERROR_LIMIT_EXCEEDED = 3;

	/**
	 * The latest database version used by this class.
	 *
	 * @var int DATABASE_VERSION
	 */
	private const DATABASE_VERSION = 1;

	/**
	 * The option name of the currently installed database version.
	 *
	 * @var string DATABASE_VERSION_OPTION
	 */
	private const DATABASE_VERSION_OPTION = '_ptc_mailing_lists_db_version';

	/**
	 * The database table name for storing email verification
	 * requests for mailing list subscribers.
	 *
	 * Note that this table DOES NOT USE THE WPDB PREFIX. That is
	 * because this is a domain-level (global) table.
	 *
	 * @var string DATABASE_EMAIL_VERIFICATION_TABLE
	 */
	private const DATABASE_EMAIL_VERIFICATION_TABLE = 'ptc_mailing_lists_email_verification';

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
		'mail.test@purpleturtlecreative.com'     => '4T3HmaE8JtUovVF7',
	);

	/**
	 * Hooks code into WordPress.
	 */
	public static function register() {
		add_action( 'after_setup_theme', __CLASS__ . '::maybe_install_database_tables' );
		add_action( 'html_routes_init', __CLASS__ . '::register_html_routes' );
		add_action( 'rest_api_init', __CLASS__ . '::register_rest_routes' );
		add_action( 'wp_enqueue_scripts', __CLASS__ . '::register_scripts' );
	}

	/**
	 * Installs the database tables if not already up-to-date.
	 */
	public static function maybe_install_database_tables() {

		$installed_version = (int) get_option( static::DATABASE_VERSION_OPTION, 0 );

		if ( static::DATABASE_VERSION === $installed_version ) {
			// Already installed.
			return;
		}

		global $wpdb;
		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$email_verification_table = static::DATABASE_EMAIL_VERIFICATION_TABLE;
		dbDelta(
			"CREATE TABLE {$email_verification_table} (
				ID bigint(20) unsigned NOT NULL AUTO_INCREMENT UNIQUE,
				email varchar(100) NOT NULL,
				mailing_list varchar(100) NOT NULL,
				token char(32) NOT NULL,
				status varchar(20) NOT NULL,
				request_count smallint unsigned DEFAULT 1 NOT NULL,
				first_seen datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
				last_seen datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
				PRIMARY KEY  (ID)
			) {$charset_collate};"
		);

		update_option(
			static::DATABASE_VERSION_OPTION,
			static::DATABASE_VERSION,
			true // autoload.
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
		string $mailing_list,
		string $title_text,
		string $body_text,
		string $captcha_action,
		string $submit_label = 'Subscribe'
	) {

		// @TODO - Check API request balance, render error.

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
			<form class="ptc-mailing-list-subscribe" method="POST" action="<?php echo esc_url( $form_action_url ); ?>">
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
	private static function get_mailing_list_by_id( string $list_id ) : string|bool {
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
							// miss a few subscribers than permit bad data.
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
	 * @return \WP_REST_Response The response.
	 */
	public static function handle_post_subscribe(
		\WP_REST_Request $request
	) : \WP_REST_Response {

		// @TODO - Record GA4 event generic 'hit'.

		// Gather variables from request.
		$email        = $request['email'];
		$mailing_list = static::get_mailing_list_by_id( $request['list_id'] );
		if ( ! $mailing_list ) {
			return new \WP_REST_Response( 'Sorry, but your request could not be processed. This mailing list is no longer used.', 400 );
		}

		// Note time of request.
		$now_unix = time();
		$now_sql  = Util::unix_as_sql_timestamp( $now_unix );

		// Prepare default response.
		$code    = 500;
		$message = 'Sorry, but your request could not be processed. An unexpected error occurred.';

		// Check if subscriber verification already exists.
		$email_verification = static::get_email_verification_record(
			$email,
			$mailing_list
		);

		if ( null === $email_verification ) {
			// New subscriber request.

			// !! LENGTH MUST MATCH DATABASE TABLE SCHEMA !!
			$token = wp_generate_password( 32, false, false );

			// Insert new subscriber request.
			global $wpdb;
			$res = $wpdb->insert(
				static::DATABASE_EMAIL_VERIFICATION_TABLE,
				array(
					'email'         => $email,
					'mailing_list'  => $mailing_list,
					'token'         => $token,
					'status'        => 'pending',
					'request_count' => 1,
					'first_seen'    => $now_sql,
					'last_seen'     => $now_sql,
				),
				array(
					'%s', // email.
					'%s', // mailing_list.
					'%s', // token.
					'%s', // status.
					'%d', // request_count.
					'%s', // first_seen.
					'%s', // last_seen.
				)
			);

			if ( false === $res ) {
				$code    = 500;
				$message = 'Sorry, but your request could not be processed. An unexpected error occurred.';
			} else {

				// Send verification email template with link.
				$res = static::send_email_verification_request( $email, $token );

				// Check response code.
				switch ( $res ) {

					case 0:
						$code    = 201;
						$message = 'Thank you for your interest! Please check your inbox or spam folder to confirm your subscription.';
						break;

					case static::ERROR_LIMIT_EXCEEDED:
						$code    = 503;
						$message = 'Sorry, but your request could not be processed. We are currently experiencing a high number of requests.';
						break;

					case static::ERROR_UNEXPECTED:
					default:
						$code    = 500;
						$message = 'Sorry, but your request could not be processed. An unexpected error occurred.';
						break;
				}
			}
		} elseif (
			! empty( $email_verification['email'] ) &&
			$email === $email_verification['email']
		) {
			// Existing subscriber request.

			// @TODO - Update request_count and last_seen.

			// Check if already verified.
			if ( 'verified' === $email_verification['status'] ) {
				$code    = 400;
				$message = 'Hello, again! You previously confirmed your subscription to this mailing list. Please send us an email if you wish to resubscribe.';
			} else {

				// Check if permitted retry.
				$res = can_retry_email_verification( $email_verification );
				if ( $res > 0 ) {
					switch ( $res ) {

						case static::ERROR_NEEDS_COOLDOWN:
							$code    = 429;
							$message = 'Hello, again! You have recently tried to subscribe to this mailing list. Please be patient and check your inbox or spam folder to confirm your subscription.';
							break;

						case static::ERROR_LIMIT_EXCEEDED:
							$code    = 403;
							$message = 'Sorry, but your request could not be processed. You have sent too many requests.';
							break;
					}
				} else {

					// Send verification email template with link.
					$res = static::send_email_verification_request(
						$email_verification['email'],
						$email_verification['token']
					);

					// Check response code.
					switch ( $res ) {

						case 0:
							$code    = 200;
							$message = 'Thank you for your interest! Please check your inbox or spam folder to confirm your subscription.';
							break;

						case static::ERROR_LIMIT_EXCEEDED:
							$code    = 503;
							$message = 'Sorry, but your request could not be processed. We are currently experiencing a high number of requests.';
							break;

						case static::ERROR_UNEXPECTED:
						default:
							$code    = 500;
							$message = 'Sorry, but your request could not be processed. An unexpected error occurred.';
							break;
					}
				}
			}
		}

		// @TODO - Record GA4 event.

		return new \WP_REST_Response( $message, $code );
	}

	/**
	 * Gets the email verification record.
	 *
	 * @param string $email The email.
	 * @param string $mailing_list The mailing list.
	 *
	 * @return array|null An associative array or null.
	 */
	private static function get_email_verification_record(
		string $email,
		string $mailing_list
	) : ?array {

		// @TODO - Use database transactions and SELECT...FOR UPDATE
		// since this record is always expected to be updated
		// and should avoid race conditions since it tracks
		// repeat requests for rate limiting.

		global $wpdb;
		$res = $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT *
				FROM %i
				WHERE email = %s
				  AND mailing_list = %s
				LIMIT 1;
				",
				static::DATABASE_EMAIL_VERIFICATION_TABLE,
				$email,
				$mailing_list
			),
			\ARRAY_A
		);
	}

	/**
	 * Checks if the email verification is permitted to retry.
	 *
	 * @param array $email_verification The email verification record.
	 *
	 * @return int The error code if not permitted. 0 if permitted.
	 */
	private static function can_retry_email_verification(
		array $email_verification
	) : int {

		// Check subscriber's total request limit.
		if ( $email_verification['request_count'] >= static::SUBSCRIBER_REQUEST_LIMIT_COUNT ) {
			return ERROR_LIMIT_EXCEEDED;
		}

		// Check subscriber's request cooldown.
		if (
			false === Util::is_sql_timestamp_expired(
				$email_verification['last_seen'],
				static::SUBSCRIBER_REQUEST_COOLDOWN
			)
		) {
			return ERROR_NEEDS_COOLDOWN;
		}

		// All checks pass.
		return 0;
	}

	/**
	 * Prepares for tracking expectant API requests.
	 *
	 * You MUST use the accompanying end_api_request() function
	 * to conclude the transaction and unlock the API request
	 * tracker rows within the database.
	 *
	 * @see static::end_api_request()
	 *
	 * @return int The permitted number of requests.
	 */
	private static function start_api_request() : int {
		global $wpdb;
		$wpdb->query( 'START TRANSACTION;' );

		// Lock API requests tracker table rows for update.
		$tracker = $wpdb->get_row(
			$wpdb->prepare(
				"
				SELECT
					o1.option_value AS 'count',
					o2.option_value AS 'start_unix'
				FROM {$wpdb->options} o1
				JOIN {$wpdb->options} o2
				WHERE o1.option_name = %i
				  AND o2.option_name = %i
				FOR UPDATE;
				",
				static::API_REQUESTS_TRACKER_COUNT_OPTION,
				static::API_REQUESTS_TRACKER_START_OPTION,
			),
			\ARRAY_A
		);

		// Check result.
		if (
			! isset( $tracker['count'] ) ||
			! isset( $tracker['start_unix'] ) ||
			Util::is_sql_timestamp_expired(
				$tracker['start_unix'],
				static::API_REQUESTS_LIMIT_PERIOD
			)
		) {
			// API requests tracker needs to be initialized or reset.
			update_option(
				static::API_REQUESTS_TRACKER_COUNT_OPTION,
				0,
				false // Do not autoload.
			);
			update_option(
				static::API_REQUESTS_TRACKER_START_OPTION,
				time(),
				false // Do not autoload.
			);
			// Conclude and try again.
			$wpdb->query( 'COMMIT;' );
			return static::start_api_request();
		}

		// Return the remaining balance of requests permitted.
		return static::API_REQUESTS_LIMIT_COUNT - $tracker['count'];
	}

	/**
	 * Records and concludes usage of API request tracking.
	 *
	 * @param int $sent_requests_count The number of API requests
	 * to count toward the usage limit.
	 */
	private static function end_api_request( int $sent_requests_count ) {
		global $wpdb;

		if ( 0 === $sent_requests_count ) {
			// Nothing was counted, so end transaction with rollback.
			$wpdb->query( 'ROLLBACK;' );
			return;
		}

		// Record the counted API requests.
		$wpdb->update(
			$wpdb->prepare(
				"
				UPDATE {$wpdb->options}
				SET option_value = option_value + %d
				WHERE option_name = %i;
				",
				static::API_REQUESTS_TRACKER_COUNT_OPTION,
				$sent_requests_count
			)
		);

		// Conclude the transaction.
		$wpdb->query( 'COMMIT;' );
	}

	/**
	 * Sends an email verification request to confirm a subscriber.
	 *
	 * @link https://documentation.mailgun.com/en/latest/api-sending.html#sending
	 *
	 * @param string $email The subscriber's email address.
	 * @param string $token The subscriber's email verification
	 * token.
	 *
	 * @return int The error code or 0 on success.
	 */
	private static function send_email_verification_request(
		string $email,
		string $token
	) : int {

		// Check API request balance, return error code.
		if ( static::start_api_request() < 1 ) {
			static::end_api_request( 0 );
			return static::ERROR_LIMIT_EXCEEDED;
		}

		// Prepare email verification link.
		$email_verification_url = add_query_arg(
			array(
				'subscriber' => $email,
				'token'      => $token,
			),
			HTML_Routes::get_url( '/mailing-lists/email-verification' )
		);

		// Send confirmation email.
		$response = wp_remote_post(
			sprintf(
				'https://api.mailgun.net/v3/%s/messages',
				\PTC_MAILGUN_DOMAIN
			),
			array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( 'api:' . \PTC_MAILGUN_API_KEY ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
					'Content-Type'  => 'application/json',
				),
				'body'    => wp_json_encode(
					array(
						'from'                     => 'Purple Turtle Creative <noreply@purpleturtlecreative.com>',
						'to'                       => $email,
						'subject'                  => 'Please confirm your subscription',
						'template'                 => 'email verification',
						't:version'                => 'initial',
						'v:subscriber_email'       => $email,
						'v:email_verification_url' => $email_verification_url,
						'o:tracking'               => 'yes',
						'o:tag'                    => 'email-verification',
						'o:tag'                    => 'confirm-subscription',
					)
				),
			)
		);

		// @TODO - Log GA4 event.

		// Check HTTP response to handle error.
		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			static::end_api_request( 0 ); // Don't count failure toward usage limits.
			trigger_error(
				'Failed to send email verification template to potential subscriber: ' . print_r( $response, true ),
				\E_USER_WARNING
			);
			return static::ERROR_UNEXPECTED;
		}

		// Success!
		static::end_api_request( 1 );
		return 0;
	}
}
