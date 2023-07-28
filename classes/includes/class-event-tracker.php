<?php
/**
 * Event Tracker class
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

/**
 * Tracks events for analytics reporting.
 *
 * Currently, Google Analytics 4 (GA4) is supported and implemented.
 * The Measurement Protocol API credentials and GA4 property information
 * must be defined as the following global constants:
 * - string PTC_GA4_API_SECRET
 * - string PTC_GA4_MEASUREMENT_ID
 * - string PTC_GA4_CLIENT_ID
 *
 * @link https://developers.google.com/analytics/devguides/collection/protocol/ga4/sending-events?client_type=gtag#required_parameters
 */
class Event_Tracker {

	/**
	 * The Google Analytics 4 Measurement Protocol API endpoint.
	 *
	 * Debugging can be enabled by simply changing this URL. See the
	 * included @link in this docblock. This is helpful for validating
	 * events during testing.
	 *
	 * @link https://developers.google.com/analytics/devguides/collection/protocol/ga4/validating-events?hl=en&client_type=gtag#sending_events_for_validation
	 *
	 * @var string GA4_ENDPOINT
	 */
	private const GA4_ENDPOINT = 'https://www.google-analytics.com/mp/collect';

	/**
	 * Records an event in a Google Analytics 4 property.
	 *
	 * Note that event_category will always be this plugin's slug name.
	 *
	 * @link https://developers.google.com/analytics/devguides/collection/protocol/ga4/sending-events?client_type=gtag#sending_events_2
	 *
	 * @param string $event_name The event name. Only alphanumeric characters
	 * and underscores are allowed.
	 * @param array  $params An associative array of event dimension keys
	 * and their respective string or integer values.
	 * @param string $client_id Optional. The client attribution
	 * identifier. Default '' to use the configured PTC_GA4_CLIENT_ID.
	 */
	public static function record_ga4_event(
		string $event_name,
		array $params,
		string $client_id = ''
	) {
		if (
			defined( '\PTC_GA4_API_SECRET' ) &&
			defined( '\PTC_GA4_MEASUREMENT_ID' ) &&
			defined( '\PTC_GA4_CLIENT_ID' )
		) {

			// Determine client identifier.
			if ( empty( $client_id ) ) {
				$client_id = \PTC_GA4_CLIENT_ID;
			}

			// Prepare request location.
			$request_url = add_query_arg(
				array(
					'api_secret'     => \PTC_GA4_API_SECRET,
					'measurement_id' => \PTC_GA4_MEASUREMENT_ID,
				),
				static::GA4_ENDPOINT
			);

			// Use DebugView when testing.
			if ( 'production' !== wp_get_environment_type() ) {
				$params['debug_mode'] = true;
			}

			// Prepare event data.
			$event_data = array(
				'name'   => $event_name,
				'params' => $params,
			);

			// Prepare request body.
			$body_json = wp_json_encode(
				array(
					'client_id'            => $client_id,
					'events'               => array( $event_data ),
					'non_personalized_ads' => true,
				)
			);
			if ( empty( $body_json ) || ! is_string( $body_json ) ) {
				trigger_error(
					'Failed to record_ga4_event(). The request body could not be encoded into JSON.',
					\E_USER_WARNING
				);
				return;
			}

			// Send the event to be recorded.
			$response = wp_remote_post(
				$request_url,
				array(
					'headers' => array(
						'Content-Type' => 'application/json',
					),
					'body'    => $body_json,
				)
			);

			// Check HTTP response.
			if ( (int) wp_remote_retrieve_response_code( $response ) > 204 ) {
				trigger_error(
					'Failed to record_ga4_event(). Received HTTP response error: ' . print_r( $response, true ),
					\E_USER_WARNING
				);
			}
		} else {
			trigger_error(
				'Failed to record_ga4_event(). Missing one or more required global constants: PTC_GA4_API_SECRET, PTC_GA4_MEASUREMENT_ID, PTC_GA4_CLIENT_ID',
				\E_USER_WARNING
			);
		}
	}
}//end class
