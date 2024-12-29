<?php
/**
 * Manual_Downloads Class
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
 * Offers manual downloads of ptc-resources-server resources.
 *
 * @link https://documentation.mailgun.com/en/latest/api-mailinglists.html
 */
class Manual_Downloads {

	/**
	 * Hooks code into WordPress.
	 */
	public static function register() {
		add_action( 'html_routes_init', __CLASS__ . '::register_html_routes' );
		add_action( 'rest_api_init', __CLASS__ . '::register_rest_routes' );
	}

	/**
	 * Registers HTML route endpoints.
	 */
	public static function register_html_routes() {
		HTML_Routes::register_route(
			'/download',
			THEME_PATH . '/html-routes/manual-download.php'
		);
	}

	/**
	 * Registers REST API route endpoints.
	 */
	public static function register_rest_routes() {
		register_rest_route(
			REST_API_NAMESPACE_V1,
			'/download',
			array(
				array(
					'methods'             => 'GET',
					'callback'            => __CLASS__ . '::handle_get_download',
					'permission_callback' => '__return_true',
					'args'                => array(
						'resource_path'         => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'email'                 => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'license_key'           => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'cf-turnstile-action'   => array(
							'type'              => 'string',
							'required'          => true,
							'sanitize_callback' => 'sanitize_text_field',
						),
						'cf-turnstile-response' => array(
							'type'              => 'string',
							'required'          => true,
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
	}

	/**
	 * Handles a request to download a premium resource.
	 *
	 * @param \WP_REST_Request $request The request.
	 *
	 * @return \WP_REST_Response The response.
	 */
	public static function handle_get_download(
		\WP_REST_Request $request
	) : \WP_REST_Response {

		// Gather variables from request.
		$resource_path       = $request['resource_path'];
		$resource_path_parts = explode( '/', $request['resource_path'] );
		$email               = $request['email'];
		$license_key         = $request['license_key'];

		// Default response that should always be overridden.
		$res = array(
			'status'  => 'success',
			'code'    => 500,
			'message' => 'Sorry, but your request could not be processed. Something went wrong on our end.',
			'data'    => null,
		);

		if ( empty( $resource_path_parts ) || 2 !== count( $resource_path_parts ) ) {
			$res = array(
				'status'  => 'error',
				'code'    => 400,
				'message' => 'Invalid resource requested.',
				'data'    => null,
			);
		} else {

			// Process the request.
			if ( 'plugins' === $resource_path_parts[0] ) {

				$plugin_slug = $resource_path_parts[1];

				$plugins_server = new \PTC_Resources_Server\Plugins\Server();
				$plugin_zip = $plugins_server->find_latest( $plugin_slug );
				if ( empty( $plugin_zip ) ) {
					$res = array(
						'status'  => 'error',
						'code'    => 404,
						'message' => 'Failed to find file for download.',
						'data'    => null,
					);
				} else {

					$res = array(
						'status'  => 'error',
						'code'    => 403,
						'message' => 'That file is not available for download.',
						'data'    => null,
					);

					if ( $plugins_server->is_premium_plugin( $plugin_slug ) ) {
						$plugin_metadata = $plugins_server->get_metadata( $plugin_slug );
						if ( 'lemon_squeezy' === $plugin_metadata['license_provider'] ) {

							try {

								\PTC_Resources_Server\Lemon_Squeezy_API::get_lemon_squeezy_license_key_error(
									\PTC_Resources_Server\Lemon_Squeezy_API::validate_license( $license_key, null ),
									$email,
									$plugin_metadata['product_id'],
									$license_key,
									true
								);

								Event_Tracker::record_ga4_event(
									'request_manual_download',
									array(
										'event_category'     => 'manual_download',
										'event_label'        => $resource_path,
										'user_id'            => wp_hash( "{$email}_{$license_key}" ),
										'http_response_code' => 200,
									)
								);

								$plugins_server->load_download_zip_response( $plugin_zip );
								exit;
							} catch ( \Exception $err ) {
								$res = array(
									'status'  => 'error',
									'code'    => $err->getCode(),
									'message' => $err->getMessage(),
									'data'    => null,
								);
							}
						}
					}
				}
			} else {
				$res = array(
					'status'  => 'error',
					'code'    => 403,
					'message' => 'That resource type is not currently supported.',
					'data'    => null,
				);
			}
		}

		// Record GA4 event.
		Event_Tracker::record_ga4_event(
			'request_manual_download',
			array(
				'event_category'     => 'manual_download',
				'event_label'        => $resource_path,
				'user_id'            => wp_hash( "{$email}_{$license_key}" ),
				'http_response_code' => $res['code'],
			)
		);

		// Format response.
		return new \WP_REST_Response( $res, $res['code'] );
	}

	/**
	 * Displays the manual download form for a ptc-resources-server resource.
	 *
	 * @param string $resource_path Optional. The ptc-resources-server resource path. (eg. plugins/completionist-pro)
	 * @param string $email Optional. The customer email.
	 * @param string $license_key Optional. The license key.
	 */
	public static function render_manual_download_form(
		string $resource_path = '',
		string $email = '',
		string $license_key = '',
	) {

		$resource_path_options = array(
			'plugins/completionist-pro' => 'Completionist PRO',
		);

		$form_action_url = rest_url( REST_API_NAMESPACE_V1 . '/download' );

		?>
		<div class="ptc-manual-download-form">
			<form method="GET" action="<?php echo esc_url( $form_action_url ); ?>">
				<label>
					<span>Product</span>
					<select name="resource_path" required>
					<?php
					foreach ( $resource_path_options as $value => $label ) {
						printf(
							'<option value="%s"%s>%s</option>',
							esc_attr( $value ),
							( $resource_path === $value ) ? ' selected="selected"' : '',
							esc_html( $label )
						);
					}
					?>
					</select>
				</label>
				<label>
					<span>Email</span>
					<input name="email" type="email" value="<?php echo esc_attr( $email ); ?>" placeholder="michelle@purpleturtlecreative.com" required />
				</label>
				<label>
					<span>License Key</span>
					<input name="license_key" type="text" value="<?php echo esc_attr( $license_key ); ?>" placeholder="XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX" required />
				</label>
				<div class="form-row-captcha-submit">
					<?php Captcha::render( 'ptc-resource-manual-download' ); ?>
					<button type="submit">Download</button>
				</div>
			</form>
		</div>
		<?php
	}
}
