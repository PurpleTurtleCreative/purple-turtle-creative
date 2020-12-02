<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<header id="header" class="site-header">
		<div class="content-width">
			<div class="site-logo hide-decal-small">
				<a href="<?php echo esc_url( home_url() ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					<?php svg( 'decal-duo-opacity.svg' ); ?>
					<?php svg( 'type-duo-opacity.svg' ); ?>
				</a>
			</div><!-- .site-logo -->

			<nav id="site-navigation" class="main-navigation">
				<button class="menu-toggle" aria-controls="menu-header" aria-expanded="false">Menu</button>
				<?php
				wp_nav_menu(
					[
						'theme_location' => 'menu-header',
						'menu_id'        => 'menu-header',
					]
				);
				?>
			</nav><!-- #site-navigation -->
		</div>
	</header><!-- #header -->
