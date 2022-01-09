<?php
/**
 * Registers custom post types and related fields and taxonomies.
 *
 * @package Purple_Turtle_Creative
 */

namespace PTC_Theme;

add_action( 'init', __NAMESPACE__ . '\register_custom_post_types', 10 );

/**
 * Registers the custom post types.
 */
function register_custom_post_types() {

	$labels = [
		'name'                     => 'Portfolio',
		'singular_name'            => 'Project',
		'add_new'                  => 'Add New',
		'add_new_item'             => 'Add New Project',
		'edit_item'                => 'Edit Project',
		'new_item'                 => 'New Project',
		'view_item'                => 'View Project',
		'view_items'               => 'View Portfolio',
		'search_items'             => 'Search Portfolio',
		'not_found'                => 'No projects found.',
		'not_found_in_trash'       => 'No projects found in Trash.',
		'parent_item_colon'        => 'Parent Project:',
		'all_items'                => 'All Projects',
		'archives'                 => 'Project Archives',
		'attributes'               => 'Project Attributes',
		'insert_into_item'         => 'Insert into project',
		'uploaded_to_this_item'    => 'Uploaded to this project',
		'featured_image'           => 'Featured image',
		'set_featured_image'       => 'Set featured image',
		'remove_featured_image'    => 'Remove featured image',
		'use_featured_image'       => 'Use as featured image',
		'filter_items_list'        => 'Filter projects list',
		'filter_by_date'           => 'Filter by date',
		'items_list_navigation'    => 'Projects list navigation',
		'items_list'               => 'Projects list',
		'item_published'           => 'Project published.',
		'item_published_privately' => 'Project published privately.',
		'item_reverted_to_draft'   => 'Project reverted to draft.',
		'item_scheduled'           => 'Project scheduled.',
		'item_updated'             => 'Project updated.',
		'item_link'                => 'Project Link',
		'item_link_description'    => 'A link to a project.',
	];

	register_post_type(
		'ptc-portfolio',
		[
			'label' => 'Portfolio',
			'labels' => $labels,
			'description' => 'Check out the latest projects that I\'ve been working on.',
			'public' => true,
			'hierarchical' => false,
			'exclude_from_search' => false,
			'show_in_rest' => true, // Make available in Block Editor.
			'menu_position' => 21, /* Pages menu item is priority 20, see https://developer.wordpress.org/reference/functions/add_menu_page/#default-bottom-of-menu-structure */
			'menu_icon' => 'dashicons-index-card',
			'register_meta_box_cb' => null,
			'taxonomies' => [ 'skill' ],
			'has_archive' => 'portfolio',
			'rewrite' => [
				'slug' => 'portfolio',
				'with_front' => false,
			],
			'delete_with_user' => false,
		]
	);

	$labels = [
		'name'                       => 'Skills',
		'singular_name'              => 'Skill',
		'search_items'               => 'Search Skills',
		'popular_items'              => 'Popular Skills',
		'all_items'                  => 'All Skills',
		'parent_item'                => 'Parent Skill',
		'parent_item_colon'          => 'Parent Skill:',
		'edit_item'                  => 'Edit Skill',
		'view_item'                  => 'View Skill',
		'update_item'                => 'Update Skill',
		'add_new_item'               => 'Add New Skill',
		'new_item_name'              => 'New Skill Name',
		'separate_items_with_commas' => 'Separate skills with commas',
		'add_or_remove_items'        => 'Add or remove skills',
		'choose_from_most_used'      => 'Choose from the most used skills',
		'not_found'                  => 'No skills found.',
		'no_terms'                   => 'No skills',
		'filter_by_item'             => 'Filter by skill',
		'items_list_navigation'      => 'Skills list navigation',
		'items_list'                 => 'Skills list',
		'most_used'                  => 'Most Used',
		'back_to_items'              => '&larr; Go to Skills',
		'item_link'                  => 'Skill Link',
		'item_link_description'      => 'A link to a skill.',
	];

	register_taxonomy(
		'skill',
		'ptc-portfolio',
		[
			'label' => 'Skills',
			'labels' => $labels,
			'description' => '',
			'public' => true,
			'hierarchical' => false,
			'show_in_rest' => true, // Make available in Block Editor.
		]
	);
}
