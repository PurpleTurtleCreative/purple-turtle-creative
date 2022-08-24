<?php

$acf_target = "user_{$atts['user']}";

$work_status = get_field( 'ptc_work_status', $acf_target ) ?? null;
$work_url = get_field( 'ptc_work_url', $acf_target ) ?? '#';
$work_message = get_field( 'ptc_work_message', $acf_target ) ?? '';

// Don't display shortcode if settings weren't saved for user.
if ( ! empty( $work_status ) ) :
	$secondary_label = ( 'open' === $work_status['value'] ) ? 'Contact Now' : 'See Schedule';
	?>
	<div class="ptc-shortcode ptc-work-status-badge" data-user="<?php echo esc_attr( $atts['user'] ); ?>" data-status="<?php echo esc_attr( $work_status['value'] ); ?>">

		<a href="<?php echo esc_url( $work_url ); ?>" target="_blank">
			<div class="indicator indicator-<?php echo esc_attr( $work_status['value'] ); ?>"></div>
			<p><?php echo esc_html( $work_status['label'] ); ?><small><?php echo esc_html( $secondary_label ); ?></small></p>
		</a>

		<?php if ( true === $atts['show_message'] && ! empty( $work_message ) ) : ?>
		<p><small><?php echo esc_html( $work_message ); ?></small></p>
		<?php endif; ?>
	</div>
	<?php
endif;
