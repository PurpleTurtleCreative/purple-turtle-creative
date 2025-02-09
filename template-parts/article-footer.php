<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

?>

<footer class="article-footer content-width">
	<?php

	if ( has_term( array( 'completionist', 'completionist-users' ), 'category' ) ) {
		// Release Notes and usage posts about Completionist.
		require_once THEME_PATH . '/classes/public/class-mailing-lists.php';
		Mailing_Lists::render_subscription_form_block(
			'completionist@purpleturtlecreative.com',
			'Get the Latest Updates',
			'Join the <a href="' . esc_url( home_url( '/completionist/' ) ) . '">Completionist</a> mailing list to know when exciting new features and critical updates are released to supercharge your productivity with&nbsp;Asana&nbsp;and&nbsp;WordPress!',
			'completionist-blog-post-end',
			'Subscribe'
		);
	}

	if ( ! is_page( array( 'privacy-policy', 'terms-conditions' ) ) ) {
		echo '<div style="margin-top: var(--wp--preset--spacing--40);margin-bottom: var(--wp--preset--spacing--40);">';
		echo do_shortcode( '[ptc-bio-card]' );
		echo '</div>';
	}

	if ( ! is_singular( 'page' ) ) {
		the_post_navigation(
			array(
				'prev_text' => '<div class="nav-icon">' . get_fa( 'angle-left' ) . '</div><div class="nav-label"><span class="nav-subtitle">Back</span><br/>%title</div>',
				'next_text' => '<div class="nav-label"><span class="nav-subtitle">Next</span><br/>%title</div><div class="nav-icon">' . get_fa( 'angle-right' ) . '</div>',
			)
		);
	}
	?>
</footer>
