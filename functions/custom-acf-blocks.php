<?php
/**
 * Theme custom Gutenberg blocks registered via ACF Pro.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

add_action( 'acf/init', __NAMESPACE__ . '\register_acf_blocks', 10 );
if ( class_exists( '\WP_Block_Editor_Context' ) ) {
	add_filter( 'block_categories_all', __NAMESPACE__ . '\filter_block_categories', 10, 2 );
} else {
	// Backwards compatibility before WordPress 5.8.
	add_filter( 'block_categories', __NAMESPACE__ . '\filter_block_categories', 10, 2 );
}

/**
 * @link https://developer.wordpress.org/block-editor/reference-guides/filters/block-filters/#block_categories_all
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
