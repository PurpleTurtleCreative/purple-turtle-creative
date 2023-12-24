<?php
/**
 * Shortcode [ptc-work-status-badge]
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

if ( ! empty( $atts['user_id'] ) ) :

	$acf_target  = "user_{$atts['user_id']}";
	$work_status = get_field( 'ptc_work_status', $acf_target ) ?? array();

	// Don't display shortcode if settings weren't saved for user.
	if ( ! empty( $work_status ) ) :
		$work_url        = get_field( 'ptc_work_url', $acf_target ) ?? '#';
		$secondary_label = ( 'open' === $work_status['value'] ) ? 'Contact Now' : 'See Schedule';
		?>
		<a class="ptc-shortcode ptc-shortcode-work-status-badge" href="<?php echo esc_url( $work_url ); ?>">
			<div class="indicator indicator-<?php echo esc_attr( $work_status['value'] ); ?>"></div>
			<p><?php echo esc_html( $work_status['label'] ); ?><small><?php echo esc_html( $secondary_label ); ?></small></p>
		</a>
		<?php
	endif;
endif;
