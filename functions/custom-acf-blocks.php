<?php
/**
 * Theme custom Gutenberg blocks registered via ACF Pro.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

add_action( 'acf/init', __NAMESPACE__ . '\register_acf_blocks', 10 );
add_action( 'acf/load_field/key=field_632a8942e8de8', __NAMESPACE__ . '\populate_icon_select_options' );
add_action( 'acf/load_field/key=field_632df30088333', __NAMESPACE__ . '\populate_icon_select_options' );
if ( class_exists( '\WP_Block_Editor_Context' ) ) {
	add_filter( 'block_categories_all', __NAMESPACE__ . '\filter_block_categories', 10, 2 );
} else {
	// Backwards compatibility before WordPress 5.8.
	add_filter( 'block_categories', __NAMESPACE__ . '\filter_block_categories', 10, 2 );
}

/**
 * Adds a block category for this theme.
 *
 * @link https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#block_categories_all
 *
 * @param array  $block_categories An array of registered block category data.
 * @param string $editor_context The current editor context.
 */
function filter_block_categories( $block_categories, $editor_context ) {
	$block_categories[] = [
		'slug'  => THEME_BASENAME,
		'title' => THEME_NAME,
		'icon'  => null,
	];
	return $block_categories;
}

/**
 * Registers the blocks via ACF Pro.
 */
function register_acf_blocks() {
	if ( function_exists( '\acf_register_block_type' ) ) {
		\acf_register_block_type(
			[
				'name' => 'ptc_block_accordion',
				'title' => 'PTC Accordion',
				'description' => 'A list of headings and associated text.',
				'category' => THEME_BASENAME,
				'icon' => 'editor-ul',
				'render_template' => THEME_PATH . '/template-parts/acf-blocks/accordion.php',
				'post_types' => [ 'page' ],
				'enqueue_style' => STYLES_URI . '/block_accordion.css',
				'supports' => [
					'align' => false,
					'align_text' => false,
					'align_content' => false,
					'full_height' => false,
					'multiple' => true,
				],
			]
		);
		\acf_register_block_type(
			[
				'name' => 'ptc_block_icon_cards_slider',
				'title' => 'PTC Icon Cards Slider',
				'description' => 'Displays a slider of cards with associated icons.',
				'category' => THEME_BASENAME,
				'icon' => 'slides',
				'render_template' => THEME_PATH . '/template-parts/acf-blocks/icon-cards-slider.php',
				'post_types' => [ 'page' ],
				'enqueue_assets' => function() {
					wp_enqueue_style(
						'ptc-block-icon-cards-slider',
						STYLES_URI . '/block_icon-cards-slider.css',
						[],
						THEME_VERSION
					);
					wp_enqueue_style(
						'slick',
						STYLES_URI . '/vendor/slick.css',
						[],
						'1.8.1'
					);
					wp_enqueue_script(
						'slick',
						SCRIPTS_URI . '/vendor/slick.min.js',
						[ 'jquery-core' ],
						'1.8.1',
						true
					);
					wp_enqueue_script(
						'ptc-block-icon-cards-slider',
						SCRIPTS_URI . '/block-icon-cards-slider.min.js',
						[ 'slick' ],
						THEME_VERSION,
						true
					);
				},
				'supports' => [
					'align' => false,
					'align_text' => false,
					'align_content' => false,
					'full_height' => false,
					'multiple' => true,
				],
			]
		);
		\acf_register_block_type(
			[
				'name' => 'ptc_block_icon_buttons',
				'title' => 'PTC Icon Buttons',
				'description' => 'Displays buttons with an icon.',
				'category' => THEME_BASENAME,
				'icon' => 'button',
				'render_template' => THEME_PATH . '/template-parts/acf-blocks/icon-buttons.php',
				'post_types' => [ 'page' ],
				// 'enqueue_style' => STYLES_URI . '/block_icon-buttons.css',
				'supports' => [
					'align' => true,
					'align_text' => false,
					'align_content' => false,
					'full_height' => false,
					'multiple' => true,
				],
			]
		);
		\acf_register_block_type(
			[
				'name' => 'ptc_block_post_previews',
				'title' => 'PTC Post Previews',
				'description' => 'Displays post preview cards.',
				'category' => THEME_BASENAME,
				'icon' => 'admin-page',
				'render_template' => THEME_PATH . '/template-parts/acf-blocks/post-previews.php',
				'post_types' => [ 'page' ],
				'enqueue_style' => STYLES_URI . '/block_post-previews.css',
				'supports' => [
					'align' => false,
					'align_text' => false,
					'align_content' => false,
					'full_height' => false,
					'multiple' => true,
				],
			]
		);
	}
}

/**
 * Sets the provided field's "choices" with "solid" family icons available
 * within this theme.
 *
 * @link https://www.advancedcustomfields.com/resources/dynamically-populate-a-select-fields-choices/
 *
 * @param array $field The ACF field object data.
 */
function populate_icon_select_options( $field ) {

	// Clear existing options.
	$field['choices'] = [];

	// Find icons for each FontAwesome family.
	$icon_families = [ 'brands', 'solid' ];
	foreach ( $icon_families as $family ) {
		$icon_files = glob( THEME_PATH . "/assets/icons/{$family}/*.svg" ) ?: [];
		if ( count( $icon_files ) > 0 ) {
			foreach ( $icon_files as $icon_file ) {
				$icon_name = basename( $icon_file, '.svg' );
				$field['choices'][ "{$family}/{$icon_name}" ] = ucwords( $family . ' - ' . str_replace( '-', ' ', $icon_name ) );
			}
		}
	}

	return $field;
}
