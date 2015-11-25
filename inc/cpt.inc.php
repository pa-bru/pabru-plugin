<?php

if ( ! function_exists('pabru_map') ) {
	// Register Custom Post Type
	function pabru_map() {

		$labels = array(
			'name'                  => _x( 'pabru_maps', 'Post Type General Name', 'text_domain' ),
			'singular_name'         => _x( 'pabru_map', 'Post Type Singular Name', 'text_domain' ),
			'menu_name'             => __( 'pabru_map', 'text_domain' ),
			'name_admin_bar'        => __( 'pabru_map', 'text_domain' ),
			'parent_item_colon'     => __( '', 'text_domain' ),
			'all_items'             => __( 'all maps', 'text_domain' ),
			'add_new_item'          => __( 'Add a new map', 'text_domain' ),
			'add_new'               => __( 'Add new', 'text_domain' ),
			'new_item'              => __( 'New map', 'text_domain' ),
			'edit_item'             => __( 'Edit map', 'text_domain' ),
			'update_item'           => __( 'Update map', 'text_domain' ),
			'view_item'             => __( 'view map', 'text_domain' ),
			'search_items'          => __( 'Search map', 'text_domain' ),
			'not_found'             => __( 'Not found', 'text_domain' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
			'items_list'            => __( 'maps list', 'text_domain' ),
			'items_list_navigation' => __( 'maps list navigation', 'text_domain' ),
			'filter_items_list'     => __( 'Filter maps list', 'text_domain' ),
		);
		$args = array(
			'label'                 => __( 'pabru_map', 'text_domain' ),
			'description'           => __( 'used to manage your maps', 'text_domain' ),
			'labels'                => $labels,
			'supports'              => array( 'title', ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 80,
			'show_in_admin_bar'     => true,
			'show_in_nav_menus'     => true,
			'can_export'            => true,
			'has_archive'           => true,		
			'exclude_from_search'   => false,
			'publicly_queryable'    => true,
			'capability_type'       => 'page',
		);
		register_post_type( 'pabru_map', $args );

	}
add_action( 'init', 'pabru_map', 0 );
}
