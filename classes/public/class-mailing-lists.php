<?php
/**
 * Mailing_List Class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

require_once THEME_PATH . '/classes/public/class-captcha.php';
require_once THEME_PATH . '/classes/public/class-html-routes.php';
require_once THEME_PATH . '/classes/includes/class-util.php';
require_once THEME_PATH . '/classes/includes/class-event-tracker.php';

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
	public const MAILING_LIST_IDS = array(
		'completionist@purpleturtlecreative.com' => 'RN3epj3Y8VmA9iKj',
		'stage@purpleturtlecreative.com'         => 'rV2rtR8ch8Kh6Dgg',
		'dev@sandboxe9304e53e5994067aa8ce9e5897e4536.mailgun.org' => 'GtujGDN2bj23QsU8',
		'customers@sandboxe9304e53e5994067aa8ce9e5897e4536.mailgun.org' => 'a2tBf2KrKnxBF66s',
	);

	/**
	 * Hooks code into WordPress.
	 */
	public static function register() {
		add_action( 'after_setup_theme', __CLASS__ . '::maybe_install_database_tables' );
		add_action( 'html_routes_init', __CLASS__ . '::register_html_routes' );
		add_action( 'rest_api_init', __CLASS__ . '::register_rest_routes' );
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
				status varchar(20) NOT NULL,
				request_count smallint unsigned NOT NULL,
				first_seen datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
				last_seen datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
				PRIMARY KEY  (ID, email, mailing_list)
			) {$charset_collate};"
		);

		update_option(
			static::DATABASE_VERSION_OPTION,
			static::DATABASE_VERSION,
			true // autoload.
		);
	}

	/**
	 * Gets the mailing list subscription form HTML.
	 *
	 * @param string $mailing_list The mailing list address.
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

		if ( empty( static::MAILING_LIST_IDS[ $mailing_list ] ) ) {
			trigger_error(
				'Refused to render mailing list subscription form for invalid mailing list address: ' . $mailing_list,
				\E_USER_WARNING
			);
			return;
		}

		$form_action_url = rest_url( REST_API_NAMESPACE_V1 . '/mailing-lists/subscribe' );

		?>
		<div class="ptc-mailing-list-subscribe">
			<?php
			if ( ! empty( $title_text ) ) {
				echo '<h2>' . esc_html( $title_text ) . '</h2>';
			}
			if ( ! empty( $body_text ) ) {
				echo wp_kses_post( wpautop( $body_text ) );
			}
			?>
			<form method="POST" action="<?php echo esc_url( $form_action_url ); ?>" data-label="<?php echo esc_attr( $captcha_action ); ?>">
				<div class="form-input-button-row">
					<input type="email" name="email" placeholder="mail@example.com" required />
					<button type="submit"><?php echo esc_html( $submit_label ); ?></button>
				</div>
				<div class="form-extra-details">
					<?php Captcha::render( $captcha_action ); ?>
					<div class="legal-text">
						<p><small>By submitting, you agree to our <?php a_link_to( 'privacy-policy' ); ?> and to receiving email messages from Purple&nbsp;Turtle&nbsp;Creative. A verification email will be sent to confirm your subscription request.</small></p>
					</div>
				</div>
				<input type="hidden" name="list_id" value="<?php echo esc_attr( static::MAILING_LIST_IDS[ $mailing_list ] ); ?>" />
			</form>
		</div>
		<?php
	}

	/**
	 * Renders the mailing list subscribe ACF block.
	 *
	 * This ensures the necessary scripts and styles are enqueued.
	 *
	 * @link https://dbushell.com/2020/10/05/wordpress-gutenberg-and-tips-for-acf-blocks/
	 * @see wp-content/plugins/advanced-custom-fields-pro/pro/blocks.php
	 *
	 * @param string $mailing_list The mailing list address.
	 * @param string $title_text The form title text.
	 * @param string $body_text The form body text.
	 * @param string $captcha_action The captcha widget ID.
	 * @param string $submit_label Optional. The submit button
	 * label text. Default 'Subscribe'.
	 */
	public static function render_subscription_form_block(
		string $mailing_list,
		string $title_text,
		string $body_text,
		string $captcha_action,
		string $submit_label = 'Subscribe'
	) {
		acf_render_block(
			array(
				'id'   => '',
				'name' => 'acf/ptc-block-mailing-list-subscribe',
				'data' => array(
					'ptc_mailing_list_title'             => $title_text,
					'ptc_mailing_list_body'              => $body_text,
					'ptc_mailing_list'                   => $mailing_list,
					'ptc_mailing_list_captcha_action_id' => $captcha_action,
					'ptc_mailing_list_submit_label'      => $submit_label,
				),
			)
		);
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
					'methods'             => 'POST',
					'callback'            => __CLASS__ . '::handle_post_subscribe',
					'permission_callback' => '__return_true',
					'args'                => array(
						'email'                 => array(
							'type'              => 'string',
							'required'          => true,
							// WordPress discloses these functions may be
							// inaccurate, but I'd rather be safer and mayyybe
							// miss a few subscribers than permit bad data.
							'sanitize_callback' => 'sanitize_email',
							'validate_callback' => 'is_email',
						),
						'list_id'               => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $value, $request, $param ) {
								return in_array( $value, static::MAILING_LIST_IDS, true );
							},
						),
						'verification_type'     => array(
							'type'              => 'string',
							'required'          => false,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $value, $request, $param ) {
								return in_array( $value, array( 'token', 'code' ), true );
							},
						),
						'cf-turnstile-action'   => array(
							'type'              => 'string',
							'required'          => false, // @todo - ADD TO REACT FormStepVerificationCode SUBMISSIONS !!
							'sanitize_callback' => 'sanitize_text_field',
						),
						'cf-turnstile-response' => array(
							'type'              => 'string',
							'required'          => false, // @todo - ADD TO REACT FormStepVerificationCode SUBMISSIONS !!
							// Hopefully this doesn't invalidate successful tokens.
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $value, $request, $param ) {
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

		register_rest_route(
			REST_API_NAMESPACE_V1,
			'/mailing-lists/verify',
			array(
				array(
					'methods'             => 'POST',
					'callback'            => __CLASS__ . '::handle_post_verify',
					'permission_callback' => '__return_true',
					'args'                => array(
						'email'             => array(
							'type'              => 'string',
							'required'          => true,
							// WordPress discloses these functions may be
							// inaccurate, but I'd rather be safer and mayyybe
							// miss a few subscribers than permit bad data.
							'sanitize_callback' => 'sanitize_email',
							'validate_callback' => 'is_email',
						),
						'list_id'           => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
							'validate_callback' => function ( $value, $request, $param ) {
								return in_array( $value, static::MAILING_LIST_IDS, true );
							},
						),
						'verification_code' => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
					),
				),
			)
		);
	}

	/**
	 * Handles a request to subscribe to a mailing list.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response The response.
	 */
	public static function handle_post_subscribe(
		\WP_REST_Request $request
	) : \WP_REST_Response {
		global $wpdb;

		// Gather variables from request.
		$email             = $request['email'];
		$mailing_list      = static::get_mailing_list_by_id( $request['list_id'] );
		$verification_type = $request['verification_type'] ?? 'token';

		// Default response that should always be overridden.
		$status  = 500;
		$message = 'Sorry, but your request could not be processed. Something went wrong on our end.';

		// Ensure mailing list is actually valid.
		if ( ! $mailing_list ) {
			$status  = 400;
			$message = 'Sorry, but your request could not be processed. This mailing list is no longer used.';
		} else {
			// Okay, this request is relevant.

			// Note time of request.
			$now_unix = time();
			$now_sql  = Util::unix_as_sql_timestamp( $now_unix );

			// Prepare default response.
			$status  = 500;
			$code    = 'unset';
			$message = 'Sorry, but your request could not be processed. An unexpected error occurred.';

			// Check if subscriber verification already exists.
			$email_verification = static::get_and_lock_email_verification_record(
				$email,
				$mailing_list
			);

			if ( null === $email_verification ) {
				// New subscriber request.

				// Send email verification request to user.
				$res = static::send_email_verification_request(
					$email,
					$mailing_list,
					$verification_type
				);

				// Check response code.
				switch ( $res ) {

					case 0:
						// Record new subscriber request.
						$res = $wpdb->insert(
							static::DATABASE_EMAIL_VERIFICATION_TABLE,
							array(
								'email'         => $email,
								'mailing_list'  => $mailing_list,
								'status'        => 'pending',
								'request_count' => 1,
								'first_seen'    => $now_sql,
								'last_seen'     => $now_sql,
							),
							array(
								'%s', // email.
								'%s', // mailing_list.
								'%s', // status.
								'%d', // request_count.
								'%s', // first_seen.
								'%s', // last_seen.
							)
						);

						if ( false === $res ) {
							// This could probably happen if the new subscriber
							// request was sent multiple times before it was
							// able to be written to the database. There's a
							// race condition in first checking if the subscriber
							// exists versus actually adding it to the databse
							// for the first time. When the record doesn't yet
							// exist, multiple requests could slip through and
							// cause an insertion error when the Primary Key
							// is checked. This is why a WAF is important.
							$status  = 500;
							$code    = 'new_insert_error';
							$message = 'Sorry, but your request could not be processed. An unexpected error occurred.';
						} else {
							$status  = 201;
							$code    = 'new_subscribe';
							$message = 'Thank you for your interest! Please check your inbox or spam folder to confirm your subscription.';
						}

						break;

					case static::ERROR_LIMIT_EXCEEDED:
						$status  = 503;
						$code    = 'new_high_traffic_error';
						$message = 'Sorry, but your request could not be processed. We are currently experiencing a high number of requests.';
						break;

					case static::ERROR_UNEXPECTED:
					default:
						$status  = 500;
						$code    = 'new_unexpected_error';
						$message = 'Sorry, but your request could not be processed. An unexpected error occurred.';
						break;
				}
			} elseif (
				! empty( $email_verification['email'] ) &&
				$email === $email_verification['email']
			) {
				// Existing subscriber request.

				$update_status = $email_verification['status'];

				// Check if already verified.
				if ( 'verified' === $email_verification['status'] ) {
					$status  = 400;
					$code    = 'retry_already_verified';
					$message = 'Hello, again! You previously confirmed your subscription to this mailing list. Please contact us if you wish to resubscribe.';
				} else {

					// Check if permitted retry.
					$res = static::can_retry_email_verification( $email_verification );
					if ( $res > 0 ) {
						switch ( $res ) {

							case static::ERROR_NEEDS_COOLDOWN:
								$status  = 429;
								$code    = 'retry_cooldown_error';
								$message = 'Hello, again! You have recently tried to subscribe to this mailing list. Please be patient and check your inbox or spam folder to confirm your subscription. If you still haven\'t received the confirmation email, please wait ' . human_time_diff( $now_unix, $now_unix + static::SUBSCRIBER_REQUEST_COOLDOWN ) . ' before trying to subscribe again.';
								break;

							case static::ERROR_LIMIT_EXCEEDED:
								$status  = 403;
								$code    = 'retry_lockout_error';
								$message = 'Sorry, but your request could not be processed. You have sent too many requests.';
								break;
						}
					} else {

						// Send verification email template with code.
						$res = static::send_email_verification_request(
							$email_verification['email'],
							$email_verification['mailing_list'],
							$verification_type
						);

						// Check response code.
						switch ( $res ) {

							case 0:
								$status        = 200;
								$code          = 'retry_subscribe';
								$message       = 'Hello, again! Sorry that the last verification request didn\'t work out. Please check your inbox or spam folder again now to confirm your subscription.';
								$update_status = 'pending';
								break;

							case static::ERROR_LIMIT_EXCEEDED:
								$status  = 503;
								$code    = 'retry_high_traffic_error';
								$message = 'Sorry, but your request could not be processed. We are currently experiencing a high number of requests.';
								break;

							case static::ERROR_UNEXPECTED:
							default:
								$status  = 500;
								$code    = 'retry_unexpected_error';
								$message = 'Sorry, but your request could not be processed. An unexpected error occurred.';
								break;
						}
					}

					$res = $wpdb->update(
						static::DATABASE_EMAIL_VERIFICATION_TABLE,
						array(
							'status'        => $update_status,
							'last_seen'     => $now_sql,
						),
						array(
							'ID'           => $email_verification['ID'],
							'email'        => $email_verification['email'],
							'mailing_list' => $email_verification['mailing_list'],
						),
						array(
							'%s', // status.
							'%s', // last_seen.
						),
						array(
							'%d', // ID.
							'%s', // email.
							'%s', // mailing_list.
						)
					);

					if ( false === $res ) {
						trigger_error(
							"Failed to update email verification record ID {$email_verification['ID']}.",
							\E_USER_NOTICE
						);
					}
				}
			}
		}

		// Record GA4 event.
		Event_Tracker::record_ga4_event(
			'request_subscribe',
			array(
				'event_category'     => 'mailing_lists',
				'event_label'        => $code,
				'http_response_code' => $status,
			)
		);

		// Format response.
		// @todo - Update to standard format of [code,status,message,data]
		// and ensure all code which uses this endpoint is updated
		// to support this breaking change. Particularly, email subscribe
		// forms on the frontend.
		return new \WP_REST_Response(
			array(
				'status'  => $status,
				'message' => $message,
			),
			$status
		);
	}

	/**
	 * Handles a request to verify subscription to a mailing list.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response The response.
	 */
	public static function handle_post_verify(
		\WP_REST_Request $request
	) : \WP_REST_Response {

		$res = array(
			'status'  => 'error',
			'code'    => 400,
			'message' => 'Email verification failed. Please check your email inbox or spam folder and try again.',
			'data'    => null,
		);

		if (
			true === static::process_email_verification(
				$request['email'],
				$request['list_id'],
				$request['verification_code']
			)
		) {
			$res = array(
				'status'  => 'success',
				'code'    => 200,
				'message' => 'Your email has been verified. Thank you!',
				'data'    => null,
			);
		}

		return new \WP_REST_Response( $res, $res['code'] );
	}

	/**
	 * Checks if an email address has been verified for the
	 * specified mailing list.
	 *
	 * @param string $email The email address.
	 * @param string $mailing_list The mailing list.
	 *
	 * @return bool If the email has been verified.
	 */
	public static function is_email_verified(
		string $email,
		string $mailing_list
	) : bool {

		global $wpdb;

		$maybe_status = $wpdb->get_var(
			$wpdb->prepare(
				'
				SELECT status
				FROM %i
				WHERE email = %s
					AND mailing_list = %s
				LIMIT 1;
				',
				static::DATABASE_EMAIL_VERIFICATION_TABLE,
				$email,
				$mailing_list
			)
		);

		return ( 'verified' === $maybe_status );
	}

	/**
	 * Gets the current request count for an email address's
	 * subscription request for a particular mailing list.
	 *
	 * @param string $email The email address.
	 * @param string $mailing_list The mailing list.
	 *
	 * @return int The request count. Default 0 on error.
	 */
	public static function get_request_count(
		string $email,
		string $mailing_list
	) : int {

		global $wpdb;

		$maybe_request_count = $wpdb->get_var(
			$wpdb->prepare(
				'
				SELECT request_count
				FROM %i
				WHERE email = %s
					AND mailing_list = %s
				LIMIT 1;
				',
				static::DATABASE_EMAIL_VERIFICATION_TABLE,
				$email,
				$mailing_list
			)
		);

		return intval( $maybe_request_count ?? 0 );
	}

	/**
	 * Gets the email verification record.
	 *
	 * @param string $email The email.
	 * @param string $mailing_list The mailing list.
	 *
	 * @return array|null An associative array or null.
	 */
	private static function get_and_lock_email_verification_record(
		string $email,
		string $mailing_list
	) : ?array {
		global $wpdb;
		return $wpdb->get_row(
			$wpdb->prepare(
				'
				SELECT *
				FROM %i
				WHERE email = %s
					AND mailing_list = %s
				LIMIT 1
				FOR UPDATE;
				',
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
			return static::ERROR_LIMIT_EXCEEDED;
		}

		// Check subscriber's request cooldown.
		if (
			false === Util::is_sql_timestamp_expired(
				$email_verification['last_seen'],
				static::SUBSCRIBER_REQUEST_COOLDOWN
			)
		) {
			// Note that this is based on when the user last submitted
			// a request to subscribe, which is refreshed even on
			// failed requests. This may seem counterintuitive, but
			// this behavior makes this a true cooldown as "spammer"
			// and bot activity remains "heated".
			return static::ERROR_NEEDS_COOLDOWN;
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
				WHERE o1.option_name = %s
					AND o2.option_name = %s
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
			Util::is_unix_expired(
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
		$wpdb->query(
			$wpdb->prepare(
				"
				UPDATE {$wpdb->options}
				SET option_value = option_value + %d
				WHERE option_name = %s;
				",
				$sent_requests_count,
				static::API_REQUESTS_TRACKER_COUNT_OPTION
			)
		);

		// Conclude the transaction.
		$wpdb->query( 'COMMIT;' );
	}

	/**
	 * Gets the email verification token.
	 *
	 * @link https://documentation.mailgun.com/en/latest/api-sending.html#sending
	 *
	 * @param string $email The subscriber's email address.
	 * @param string $mailing_list The desired mailing list.
	 *
	 * @return string
	 */
	private static function get_email_verification_token(
		string $email,
		string $mailing_list
	) {
		return wp_hash( $email . $mailing_list );
	}

	/**
	 * Gets a random 6-digit numeric email verification code.
	 *
	 * @param string $email The subscriber's email address.
	 * @param string $mailing_list The desired mailing list.
	 *
	 * @return string The 6-digit code.
	 */
	private static function get_email_verification_code(
		string $email,
		string $mailing_list
	) : string {

		$transient_key = "ptc_email_verification_code_{$email}_{$mailing_list}";

		$code = (string) get_transient( $transient_key );
		if ( empty( $code ) || 6 !== strlen( $code ) ) {
			$code = (string) rand(100000, 999999);
			set_transient( $transient_key, $code, 15 * \MINUTE_IN_SECONDS );
		}

		return $code;
	}

	/**
	 * Sends an email verification request to confirm a subscriber.
	 *
	 * If a $tick value is provided, then a 6-digit verification
	 * code will be sent to the user. The user must then provide
	 * the 6-digit code manually. This is used in UX flows where
	 * the user must provide a verification code before proceeding.
	 *
	 * If a $tick value is NOT provided, the a verification link
	 * will be sent to the user. The user must then click the link
	 * to verify their email. This is used in UX flows where
	 * immediate verification from the user is not required.
	 *
	 * @link https://documentation.mailgun.com/en/latest/api-sending.html#sending
	 *
	 * @param string $email The subscriber's email address.
	 * @param string $mailing_list The desired mailing list.
	 * @param int    $tick Optional. The verification code tick
	 * value. Default -1 to use verification token link instead.
	 *
	 * @return int The error code or 0 on success.
	 */
	private static function send_email_verification_request(
		string $email,
		string $mailing_list,
		string $verification_type = 'token'
	) : int {

		// Check API request balance, return error code.
		if ( static::start_api_request() < 1 ) {
			static::end_api_request( 0 );
			return static::ERROR_LIMIT_EXCEEDED;
		}

		// Prepare to send verification email.
		$response = null;

		if ( 'code' === $verification_type ) {

			// Prepare email verification code.
			$email_verification_code = static::get_email_verification_code(
				$email,
				$mailing_list
			);

			// Send verification request email.
			$response = wp_remote_post(
				sprintf(
					'https://api.mailgun.net/v3/%s/messages',
					\PTC_MAILGUN_DOMAIN
				),
				array(
					'headers' => array(
						'Authorization' => 'Basic ' . base64_encode( 'api:' . \PTC_MAILGUN_API_KEY ),
					),
					'body'    => array(
						'from'                => sprintf(
							'Purple Turtle Creative <noreply@%s>',
							\PTC_MAILGUN_DOMAIN
						),
						'to'                  => $email,
						'subject'             => 'Please confirm your email',
						'template'            => 'email verification',
						't:version'           => 'verification-code',
						'v:subscriber_email'  => $email,
						'v:verification_code' => $email_verification_code,
						'o:tracking'          => 'yes',
						'o:tag'               => 'verify-email',
					),
				)
			);
		} else {

			// Prepare email verification link.
			$email_verification_url = add_query_arg(
				array(
					'subscriber' => $email,
					'list_id'    => static::MAILING_LIST_IDS[ $mailing_list ],
					'token'      => static::get_email_verification_token(
						$email,
						$mailing_list
					),
				),
				HTML_Routes::get_url( '/mailing-lists/email-verification' )
			);

			// Send verification request email.
			$response = wp_remote_post(
				sprintf(
					'https://api.mailgun.net/v3/%s/messages',
					\PTC_MAILGUN_DOMAIN
				),
				array(
					'headers' => array(
						'Authorization' => 'Basic ' . base64_encode( 'api:' . \PTC_MAILGUN_API_KEY ),
					),
					'body'    => array(
						'from'                     => sprintf(
							'Purple Turtle Creative <noreply@%s>',
							\PTC_MAILGUN_DOMAIN
						),
						'to'                       => $email,
						'subject'                  => 'Please confirm your subscription',
						'template'                 => 'email verification',
						't:version'                => 'initial',
						'v:subscriber_email'       => $email,
						'v:email_verification_url' => $email_verification_url,
						'o:tracking'               => 'yes',
						'o:tag'                    => 'confirm-subscription',
					),
				)
			);
		}

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

	/**
	 * Subscribes a member to the mailing list if valid.
	 *
	 * @link https://documentation.mailgun.com/en/latest/api-mailinglists.html#mailing-lists
	 *
	 * @param string $email The subscriber's email.
	 * @param string $list_id The mailing address ID.
	 * @param string $token The provided email verification token.
	 *
	 * @return bool If the member was successfully subscribed.
	 */
	public static function process_email_verification(
		string $email,
		string $list_id,
		string $token
	) : bool {

		// Get the actual mailing list.
		$mailing_list = static::get_mailing_list_by_id( $list_id );
		if ( ! $mailing_list ) {
			return false;
		}

		if ( 6 === strlen( $token ) && is_numeric( $token ) ) {
			// Confirm the verification code.
			if (
				static::get_email_verification_code(
					$email,
					$mailing_list
				) !== $token
			) {
				return false;
			}
		} else {
			// Confirm the token.
			if (
				static::get_email_verification_token(
					$email,
					$mailing_list
				) !== $token
			) {
				return false;
			}
		}

		// Check the record exists and lock for update.
		$email_verification = static::get_and_lock_email_verification_record(
			$email,
			$mailing_list
		);

		if ( null === $email_verification ) {
			return false;
		}

		if ( 'verified' === $email_verification['status'] ) {
			// Already verified, so avoid the API request.
			return true;
		}

		// Seems legit.
		// Actually subscribe the email to the mailing list.
		$response = wp_remote_post(
			sprintf(
				'https://api.mailgun.net/v3/lists/%s/members',
				$mailing_list
			),
			array(
				'headers' => array(
					'Authorization' => 'Basic ' . base64_encode( 'api:' . \PTC_MAILGUN_API_KEY ),
				),
				'body'    => array(
					'address'    => $email_verification['email'],
					'subscribed' => 'yes',
					'upsert'     => 'yes',
				),
			)
		);

		// Check HTTP response to handle error.
		if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
			trigger_error(
				"Failed to subscribe email verification ID {$email_verification['ID']}: " . print_r( $response, true ),
				\E_USER_WARNING
			);
			return false;
		}

		// Record successful verification status.

		global $wpdb;

		$now_sql = Util::unix_as_sql_timestamp( time() );

		$res = $wpdb->update(
			static::DATABASE_EMAIL_VERIFICATION_TABLE,
			array(
				'status'    => 'verified',
				'last_seen' => $now_sql,
			),
			array(
				'ID'           => $email_verification['ID'],
				'email'        => $email_verification['email'],
				'mailing_list' => $email_verification['mailing_list'],
			),
			array(
				'%s', // status.
				'%s', // last_seen.
			),
			array(
				'%d', // ID.
				'%s', // email.
				'%s', // mailing_list.
			)
		);

		if ( false === $res ) {
			// Ignoring this error because the subscriber has already
			// been successfully subscribed at this point. This is
			// just for bookkeeping and preventing API usage abuse.
			// Note it since this error is unexpected, though, and
			// can be manually resolved later.
			trigger_error(
				"Failed to update email verification ID {$email_verification['ID']} to 'verified' status and '{$now_sql}' last_seen.",
				\E_USER_WARNING
			);
		}

		// Success!
		return true;
	}
}
