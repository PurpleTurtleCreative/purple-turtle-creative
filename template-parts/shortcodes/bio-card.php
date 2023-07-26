<?php
/**
 * Shortcode [ptc-bio-card]
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

if ( ! empty( $atts['user_id'] ) ) :
	$user = get_userdata( $atts['user_id'] );
	?>
	<div class="ptc-shortcode ptc-shortcode-bio-card">
		<div class="user-avatar">
			<?php echo get_avatar( $user, 150, 'mystery', $user->display_name ); ?>
		</div>
		<div class="user-details">
			<p class="name h3 heading"><?php echo esc_html( $user->display_name ?? '' ); ?></p>
			<p class="description"><?php echo wp_kses_post( $user->description ?? '' ); ?></p>
			<div class="contact button-group left">
				<?php if ( $user->user_email ) : ?>
				<div class="button">
					<a class="icon-button email" href="mailto:<?php echo esc_attr( $user->user_email ); ?>"><?php fa( 'envelope', 'solid' ); ?>Email
					</a>
				</div>
				<?php endif; ?>
				<?php if ( $user->linkedin ) : ?>
				<div class="button">
					<a class="icon-button linkedin" href="<?php echo esc_attr( $user->linkedin ); ?>"><?php fa( 'linkedin', 'brands' ); ?>LinkedIn
					</a>
				</div>
				<?php endif; ?>
				<?php if ( $user->user_url ) : ?>
				<div class="button">
					<a class="icon-button url" href="<?php echo esc_attr( $user->user_url ); ?>"><?php fa( 'hand-holding-dollar', 'solid' ); ?>Hire Me
					</a>
				</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
endif;
