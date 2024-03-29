<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

defined( 'ABSPATH' ) || die();

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header wave-trim-bottom">
		<div class="content-width">
			<div class="entry-header-inner">

				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

				<ul class="entry-meta">
					<li class="entry-date">
						<?php
						if ( is_page( array( 'privacy-policy', 'terms-conditions' ) ) ) {
							echo 'Modified <strong>' . esc_html( get_the_modified_date() ) . '</strong>';
						} else {
							the_published_or_modified_date();
						}
						?>
					</li>
					<?php if ( 'page' != get_post_type() ) : ?>
					<li class="entry-categories"><?php the_category( ' ' ); ?></li>
					<?php endif; ?>
				</ul><!-- .entry-meta -->

				<?php the_short_description(); ?>

			</div>
		</div>
	</header><!-- .entry-header -->

	<div class="entry-content-container content-width">
		<div class="entry-content">

			<?php the_content(); ?>

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
			?>
		</div><!-- .entry-content -->
	</div>

</article><!-- #post-<?php the_ID(); ?> -->
