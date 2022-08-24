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

	<link rel="preconnect" href="https://fonts.gstatic.com">
	<link rel="preload" href="https://fonts.gstatic.com/s/poppins/v15/pxiByp8kv8JHgFVrLCz7Z1xlFd2JQEk.woff2" as="font" type="font/woff2" crossorigin="anonymous">
	<link rel="preload" href="https://fonts.gstatic.com/s/roboto/v20/KFOlCnqEu92Fr1MmSU5fBBc4AMP6lQ.woff2" as="font" type="font/woff2" crossorigin="anonymous">

	<meta name="theme-color" content="#3c56f5">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php wp_body_open(); ?>

	<div id="overlay"></div>

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

				<?php do_shortcode( '[work_status_badge show_message="false"]' ); ?>
			</div>
		</header><!-- #header -->
