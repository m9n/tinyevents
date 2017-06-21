<?php


if ( !function_exists('register_tinyevent') ) :
add_action( 'init', 'register_tinyevent');
function register_tinyevent() {
	register_post_type( // Reference: https://codex.wordpress.org/Function_Reference/register_post_type
		'tinyevent',
		array( 'labels' => array(
			'name' => __( 'Events', 'tinyevent' ),
			'singular_name' => __( 'Event', 'tinyevent' ),
			'all_items' => __( 'All Events', 'tinyevent' ),
			'add_new' => __( 'Add New', 'tinyevent' ),
			'add_new_item' => __( 'Add New Event', 'tinyevent' ),
			'edit' => __( 'Edit', 'tinyevent' ),
			'edit_item' => __( 'Edit Event', 'tinyevent' ),
			'new_item' => __( 'New Event', 'tinyevent' ),
			'view_item' => __( 'View Event', 'tinyevent' ),
			'search_items' => __( 'Search Events', 'tinyevent' ),
			'not_found' =>  __( 'No Events found here.', 'tinyevent' ),
			'not_found_in_trash' => __( 'No Events found here', 'tinyevent' ),
			'parent_item_colon' => ''
		),
		'description' => __( 'This is the example custom Event', 'tinyevent' ),
		'public' => true,
		'publicly_queryable' => true,
		'exclude_from_search' => false,
		'show_ui' => true,
		'query_var' => true,
		'menu_position' => 21,
		'menu_icon' => 'dashicons-calendar-alt',
		'rewrite'	=> array( 'slug' => 'events', 'with_front' => false ),
		'has_archive' => false,
		'capability_type' => 'post',
		'delete_with_user' => false,
		'hierarchical' => false,
		'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'trackbacks', 'excerpt', 'custom-fields', 'comments', 'revisions', 'sticky')
	) );
	
	register_taxonomy_for_object_type( 'category', 'tinyevent' );
	register_taxonomy_for_object_type( 'post_tag', 'tinyevent' );
	
}
endif;