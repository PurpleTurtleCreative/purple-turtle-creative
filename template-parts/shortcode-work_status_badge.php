<?php

$acf_target = "user_{$atts['user']}";

$work_status = get_field( 'ptc_work_status', $acf_target );
$work_url = get_field( 'ptc_work_hyperlink', $acf_target );
$work_message = get_field( 'ptc_work_message', $acf_target );
?>
<div class="ptc-shortcode ptc-work-status-badge" data-user="<?php echo esc_attr( $atts['user'] ); ?>" data-status="<?php echo esc_attr( $work_status['value'] ); ?>">

	<a href="<?php echo esc_url( $work_url ); ?>" target="_blank">
		<div class="indicator"></div>
		<p><small>Availability</small><?php echo esc_html( $work_status['label'] ); ?></p>
	</a>

	<?php if ( true === $atts['show_message'] ) : ?>
	<p><small><?php echo esc_html( $work_message ); ?></small></p>
	<?php endif; ?>
</div>
