<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

$end_date = get_field( 'ptc_project_end' ) ?: '<span class="pill-badge">Present</span>';

$project_client = get_field( 'ptc_project_client' ) ?: get_field_object( 'ptc_project_client' )['default_value'];

$skills = get_the_terms( get_post(), 'skill' );
$skill_tag_list_items_string = '';
if ( is_array( $skills ) ) {
	$queried_taxonomy = $GLOBALS['wp_query']->get( 'taxonomy' );
	$queried_term = $GLOBALS['wp_query']->get( 'term' );
	foreach ( $skills as &$skill ) {
		$skill_tag_list_items_string .= sprintf(
			'<li><a href="%1$s" class="%3$s">%2$s</a></li>',
			get_category_link( $skill ),
			$skill->name,
			( $queried_taxonomy === $skill->taxonomy && $queried_term === $skill->slug ) ? 'active' : ''
		);
	}
}

$project_url = get_field( 'ptc_project_url' );

$is_portfolio_archive = ( is_post_type_archive() || is_tax( 'skill' ) );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<div class="entry-content">

		<p class="project-client"><?php echo wp_kses_post( $project_client ); ?></p>

		<?php
		if ( true === $is_portfolio_archive ) {
			if ( $project_url ) {
				$external_link_icon = get_fa( 'square-arrow-up-right', 'solid' );
				the_title( '<h2 class="entry-title"><a href="' . esc_url( $project_url ) . '" target="_blank">', $external_link_icon . '</a></h2>' );
			} else {
				the_title( '<h2 class="entry-title">', '</h2>' );
			}
		} else {
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		}
		?>

		<p class="entry-date"><?php echo wp_kses_post( $end_date ); ?></p>

		<?php ( true === $is_portfolio_archive ) ? the_content() : the_short_description(); ?>

	</div><!-- .entry-content -->

	<?php if ( $skill_tag_list_items_string ) : ?>
	<footer class="entry-footer">
		<ul class="project-skill-tags" role="list">
			<?php echo wp_kses_post( $skill_tag_list_items_string ); ?>
		</ul>
	</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
