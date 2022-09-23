<?php
/**
 * PTC Icon Cards Slider block rendering.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

// Get block data.
$icon_cards = get_field( 'ptc_slider_icon_cards' ) ?: [];

// Display slides.
if ( count( $icon_cards ) > 0 ) :
	?>
	<div class="ptc-block ptc-block-icon-cards-slider">
		<?php
		foreach ( $icon_cards as $card ) {
			$icon = explode( '/', $card['icon'] );
			?>
			<div class="icon-card">
				<div class="icon"><?php fa( $icon[1], $icon[0] ); ?></div>
				<div class="card">
					<h3 class="title"><?php echo esc_html( $card['title'] ); ?></h3>
					<div class="content"><?php echo wp_kses_post( $card['content'] ); ?></div>
				</div>
			</div>
		<?php }//end foreach ?>
		<button class="slick-prev" type="button"><?php fa( 'angle-left', 'solid' ); ?></button>
		<div class="slick-dots"></div>
		<button class="slick-next" type="button"><?php fa( 'angle-right', 'solid' ); ?></button>
	</div>
	<?php
endif;
