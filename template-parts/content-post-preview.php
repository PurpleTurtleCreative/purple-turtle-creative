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

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<a href="<?php echo esc_url( get_permalink() ); ?>">
			<?php the_post_thumbnail( 'large' ); ?>
		</a>
	</header><!-- .entry-header -->

	<div class="entry-content">

		<?php the_primary_category(); ?>

		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

		<p class="entry-date"><?php the_published_or_modified_date(); ?></p>

		<?php the_short_description(); ?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<p class="entry-readmore"><a href="<?php echo esc_url( get_permalink() ); ?>">Read More <?php fa( 'angle-double-right' ); ?></a></p>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
