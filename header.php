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

defined( 'ABSPATH' ) || die();

?>

<!doctype html>
<html <?php language_attributes(); ?>>
<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="#3c56f5">

	<!-- Google tag (gtag.js) -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=G-X52D1SE1L3"></script>
	<script>
		window.dataLayer = window.dataLayer || [];
		function gtag(){dataLayer.push(arguments);}
		gtag('js', new Date());

		<?php if ( 'production' === wp_get_environment_type() ) : ?>
		gtag('config', 'G-X52D1SE1L3');
		<?php else : ?>
		gtag('config', 'G-X52D1SE1L3', { "debug_mode": true });
		<?php endif; ?>
	</script>

	<link rel="preload" href="/wp-content/themes/purple-turtle-creative/assets/fonts/poppins_v15_700.woff2" as="font" type="font/woff2" crossorigin="anonymous">
	<link rel="preload" href="/wp-content/themes/purple-turtle-creative/assets/fonts/roboto_v20_300.woff2" as="font" type="font/woff2" crossorigin="anonymous">
	<link rel="preload" href="/wp-content/themes/purple-turtle-creative/assets/fonts/roboto_v20_500.woff2" as="font" type="font/woff2" crossorigin="anonymous">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php wp_body_open(); ?>

	<div id="overlay"></div>

	<div id="page" class="site">
		<header id="header" class="site-header">
			<div class="content-width">

				<div class="site-logo hide-decal-small">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
						<?php svg( 'decal-duo-opacity.svg' ); ?>
						<?php svg( 'type-duo-opacity.svg' ); ?>
					</a>
				</div>

				<nav id="site-navigation" class="main-navigation">
					<button class="menu-toggle" title="Menu"><?php fa( 'bars', 'solid' ); ?></button>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'menu-header',
							'menu_id'        => 'menu-header',
						)
					);
					?>
				</nav>

			</div>
		</header><!-- #header -->
