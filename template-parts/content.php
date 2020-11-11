<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Purple_Turtle_Creative
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'content-width' ); ?>>
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;

		if ( 'post' === get_post_type() ) :
			$published_date = get_the_date();
			$modified_date = get_the_modified_date();
			$entry_date = ( $modified_date !== $published_date ) ?
				"Updated <strong>{$modified_date}</strong>" : "Published <strong>{$published_date}</strong>";
			?>
			<ul class="entry-meta">
				<li class="entry-date"><?php echo wp_kses( $entry_date, 'strong' ); ?></li>
				<li class="entry-categories"><?php the_category( ' ' ); ?></li>
			</ul><!-- .entry-meta -->
		<?php endif; ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php // purple_turtle_creative_entry_footer(); ?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
