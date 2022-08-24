<?php
/**
 * Custom shortcodes.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

add_shortcode(
	'ptc-work-status-badge',
	__NAMESPACE__ . '\display_work_status_badge'
);

add_shortcode(
	'ptc-bio-card',
	__NAMESPACE__ . '\display_bio_card'
);

add_action(
	'wp_enqueue_scripts',
	__NAMESPACE__ . '\enqueue_shortcode_scripts'
);

/**
 * Registers shortcode assets for on-demand enqueue.
 */
function enqueue_shortcode_scripts() {
	wp_register_style(
		'ptc-shortcode-bio-card',
		STYLES_URI . '/shortcode_bio-card.css',
		[],
		THEME_VERSION,
		'all'
	);
}

/**
 * Displays a user's details.
 *
 * @param array  $atts User defined attributes in shortcode tag.
 * @param string $content Optional. Inner content of the shortcode tag.
 * Defaults to null if no content.
 * @param string $shortcode_tag The shortcode tag name.
 */
function display_bio_card( $atts, $content = null, $shortcode_tag = '' ) {

	// Set parameter defaults.
	$atts = shortcode_atts(
		[
			'user_id' => 0,
		],
		$atts,
		$shortcode_tag
	);

	// Sanitize and cast values.
	$atts['user_id'] = (int) filter_var( $atts['user_id'], FILTER_SANITIZE_NUMBER_INT );

	// Retrieve calculated defaults, if necessary.
	if ( 0 === $atts['user_id'] ) {
		// If no user_id provided, use the current author.
		$atts['user_id'] = get_the_author_meta( 'ID', false );
	}

	// Load display.
	wp_enqueue_style( 'ptc-shortcode-bio-card' );

	ob_start();
	include THEME_PATH . '/template-parts/shortcodes/bio-card.php';
	return ob_get_clean();
}

/**
 * Displays a badge indicating the specified user's work status.
 */
function display_work_status_badge( $atts ) {
	$atts = shortcode_atts(
		[
			'user' => false,
			'show_message' => true,
		],
		$atts
	);

	if ( ! is_numeric( $atts['user'] ) ) {
		// Default to the current post author.
		$atts['user'] = get_the_author_meta( 'ID', false );
	}

	$atts['user'] = (int) $atts['user'];
	$atts['show_message'] = (bool) filter_var( $atts['show_message'], FILTER_VALIDATE_BOOLEAN );

	include THEME_PATH . '/template-parts/shortcode-work_status_badge.php';
}
