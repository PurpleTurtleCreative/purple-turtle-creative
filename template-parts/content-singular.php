<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'content-width' ); ?>>

	<header class="entry-header">

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<ul class="entry-meta">
			<li class="entry-date"><?php the_published_or_modified_date(); ?></li>
			<?php if ( 'page' != get_post_type() ) : ?>
			<li class="entry-categories"><?php the_category( ' ' ); ?></li>
			<?php endif; ?>
		</ul><!-- .entry-meta -->

	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->