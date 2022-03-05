<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

$project_dates = get_field( 'ptc_project_dates' );
$from_date = $project_dates['ptc_project_from'];
$to_date = $project_dates['ptc_project_to'] ?? 'Present';
if ( ! $to_date ) {
	$to_date = 'Present';
}

$billable_hours = get_field( 'ptc_project_billable_hours' );
$project_dates_string = "{$from_date} &mdash; {$to_date}";
if ( $billable_hours ) {
	$project_dates_string .= " ({$billable_hours} hours)";
}

$project_client = get_field( 'ptc_project_client' );
if ( ! $project_client ) {
	$project_client = get_field_object( 'ptc_project_client' )['default_value'];
}

$skills = get_the_terms( get_post(), 'skill' );
$skill_tag_list_items_string = '';
if ( is_array( $skills ) ) {
	foreach ( $skills as &$skill ) {
		$skill_tag_list_items_string .= "<li>{$skill->name}</li>";
	}
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
		<a href="<?php echo esc_url( get_permalink() ); ?>" tabindex="-1">
			<?php the_post_thumbnail( 'large' ); ?>
		</a>
	</header><!-- .entry-header -->

	<div class="entry-content">

		<p class="project-client"><?php echo wp_kses_post( $project_client ); ?></p>

		<?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' ); ?>

		<p class="project-dates"><?php echo wp_kses_post( $project_dates_string ); ?></p>

		<?php the_short_description(); ?>

	</div><!-- .entry-content -->

	<?php if ( $skill_tag_list_items_string ) : ?>
	<footer class="entry-footer">
		<ul class="project-skill-tags" role="list">
			<?php echo wp_kses_post( $skill_tag_list_items_string ); ?>
		</ul>
	</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
