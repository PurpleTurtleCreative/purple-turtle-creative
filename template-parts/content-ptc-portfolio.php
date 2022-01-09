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

$project_role = get_field( 'ptc_project_role' );
$project_url = get_field( 'ptc_project_url' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'content-width' ); ?>>

	<header class="entry-header">

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

		<ul class="entry-meta">

			<li class="project-dates">
				<strong>Project Dates:</strong>
				<?php echo wp_kses_post( $project_dates_string ); ?>
			</li>

			<li class="project-client">
				<strong>Requestor:</strong>
				<?php echo wp_kses_post( $project_client ); ?>
			</li>

			<li class="project-role">
				<strong>Work Role:</strong>
				<?php echo wp_kses_post( $project_role ); ?>
			</li>

			<li class="entry-categories project-skills">
				<strong>Skills Used:</strong><?php the_terms( get_the_ID(), 'skill', '', '', '' ); ?>
			</li>

			<?php if ( $project_url ) : ?>
			<li class="project-link">
				<a class="button" href="<?php echo esc_url( $project_url ); ?>" target="_blank">View Project <?php fa( 'long-arrow-alt-right' ); ?></a>
			</li>
			<?php endif; ?>

		</ul><!-- .entry-meta -->

	</header><!-- .entry-header -->

	<div class="entry-content">
		<?php the_content(); ?>
	</div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->
