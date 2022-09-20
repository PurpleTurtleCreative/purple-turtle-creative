<?php
/**
 * Shortcode [ptc-bio-card]
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

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
			<div class="contact">
				<?php if ( $user->user_email ) : ?>
					<a class="referral-badge email" href="mailto:<?php echo esc_attr( $user->user_email ); ?>">
						<?php fa( 'envelope', 'solid' ); ?>
						<div class="brand-text">Email</div>
					</a>
				<?php endif; ?>
				<?php if ( $user->linkedin ) : ?>
					<a class="referral-badge linkedin" href="<?php echo esc_attr( $user->linkedin ); ?>">
						<?php fa( 'linkedin', 'brands' ); ?>
						<div class="brand-text">LinkedIn</div>
					</a>
				<?php endif; ?>
				<?php if ( $user->user_url ) : ?>
					<a class="referral-badge url" href="<?php echo esc_attr( $user->user_url ); ?>">
						<?php fa( 'hand-holding-dollar', 'solid' ); ?>
						<div class="brand-text">Hire Me</div>
					</a>
				<?php endif; ?>
			</div>
		</div>
	</div>
	<?php
endif;
