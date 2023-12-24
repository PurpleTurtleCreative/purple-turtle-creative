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

$end_date       = get_field( 'ptc_project_end' ) ?: 'Present';
$project_client = get_field( 'ptc_project_client' ) ?: get_field_object( 'ptc_project_client' )['default_value'];
$project_url    = get_field( 'ptc_project_url' );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header wave-trim-bottom">
		<div class="content-width">
			<div class="entry-header-inner">

				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

				<ul class="entry-meta">

					<li class="project-dates">
						<strong>Completed:</strong>
						<?php echo wp_kses_post( $end_date ); ?>
					</li>

					<li class="project-client">
						<strong>Requestor:</strong>
						<?php echo wp_kses_post( $project_client ); ?>
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

			</div>
		</div>
	</header><!-- .entry-header -->

	<div class="entry-content-container content-width">
		<div class="entry-content">
			<?php the_content(); ?>
		</div><!-- .entry-content -->
	</div>

</article><!-- #post-<?php the_ID(); ?> -->
