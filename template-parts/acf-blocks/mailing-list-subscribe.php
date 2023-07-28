<?php
/**
 * PTC Mailing List Subscribe block rendering.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

// Ensure dependency is present.
if ( ! class_exists( 'Mailing_Lists' ) ) {
	require_once \PTC_Theme\THEME_PATH . '/classes/public/class-mailing-lists.php';
}

// Render HTML.

echo '<div class="ptc-block-mailing-list-subscribe">';

\PTC_Theme\Mailing_Lists::render_subscription_form(
	get_field( 'ptc_mailing_list' ),
	get_field( 'ptc_mailing_list_title' ),
	get_field( 'ptc_mailing_list_body' ),
	get_field( 'ptc_mailing_list_captcha_action_id' ),
	get_field( 'ptc_mailing_list_submit_label' )
);

echo '</div>';
