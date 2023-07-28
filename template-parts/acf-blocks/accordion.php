<?php
/**
 * PTC Accordions block rendering.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

// Get block data.
$accordion_items = get_field( 'ptc_accordion_items' ) ?: [];

if ( count( $accordion_items ) > 0 ) :
	?>
	<div class="ptc-block ptc-block-accordion">
		<ul>
			<?php
			foreach ( $accordion_items as $item ) {
				printf(
					'<li><h3 class="item-heading h4">%1$s</h3><div class="item-content">%2$s<div></li>',
					esc_html( $item['heading'] ),
					wp_kses_post( $item['content'] )
				);
			}
			?>
		</ul>
	</div>
	<?php
endif;
