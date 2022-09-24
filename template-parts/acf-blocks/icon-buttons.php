<?php
/**
 * PTC Icon Buttons block rendering.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

// Get block data.
$icon_buttons_color = get_field( 'ptc_icon_buttons_color' ) ?: '';
$icon_buttons = get_field( 'ptc_icon_buttons' ) ?: [];

// Display slides.
if ( count( $icon_buttons ) > 0 ) :
	$class_list = 'ptc-block ptc-block-icon-buttons button-group';
	if ( ! empty( $block['className'] ) ) {
		$class_list .= " {$block['className']}";
	}
	if ( ! empty( $block['align'] ) ) {
		$class_list .= " {$block['align']}";
	}
	?>
	<div class="<?php echo esc_attr( $class_list ); ?>">
		<?php
		foreach ( $icon_buttons as $button ) {
			$icon = explode( '/', $button['icon'] );
			$class_list = "icon-button {$icon_buttons_color} --is-icon-{$icon[0]}-{$icon[1]}";
			if ( false === $button['icon_position_before'] ) {
				$class_list .= ' --is-icon-position-after';
			}
			?>
			<div class="button h5">
				<a class="<?php echo esc_attr( $class_list ); ?>" href="<?php echo esc_url( $button['url'] ); ?>"><?php fa( $icon[1], $icon[0] ); ?><?php echo esc_html( $button['label'] ); ?></a>
			</div>
		<?php }//end foreach ?>
	</div>
	<?php
endif;
