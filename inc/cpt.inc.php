<?php

if ( ! function_exists('pabru_map') ) {
	// Register Custom Post Type
	function pabru_map() {
		$labels = array(
			'name'                  => _x( 'pabru_maps', 'Post Type General Name', 'pabru-plugin' ),
			'singular_name'         => _x( 'pabru_map', 'Post Type Singular Name', 'pabru-plugin' ),
			'menu_name'             => __( 'pabru_map', 'pabru-plugin' ),
			'name_admin_bar'        => __( 'pabru_map', 'pabru-plugin' ),
			'parent_item_colon'     => __( '', 'pabru-plugin' ),
			'all_items'             => __( 'All maps', 'pabru-plugin' ),
			'add_new_item'          => __( 'Add a new map', 'pabru-plugin' ),
			'add_new'               => __( 'Add new', 'pabru-plugin' ),
			'new_item'              => __( 'New map', 'pabru-plugin' ),
			'edit_item'             => __( 'Edit map', 'pabru-plugin' ),
			'update_item'           => __( 'Update map', 'pabru-plugin' ),
			'view_item'             => __( 'View map', 'pabru-plugin' ),
			'search_items'          => __( 'Search map', 'pabru-plugin' ),
			'not_found'             => __( 'Not found', 'pabru-plugin' ),
			'not_found_in_trash'    => __( 'Not found in Trash', 'pabru-plugin' ),
			'items_list'            => __( 'Maps list', 'pabru-plugin' ),
			'items_list_navigation' => __( 'Maps list navigation', 'pabru-plugin' ),
			'filter_items_list'     => __( 'Filter maps list', 'pabru-plugin' ),
		);
		$args = array(
			'label'                 => __( 'pabru_map', 'pabru-plugin' ),
			'description'           => __( 'Used to manage your maps.', 'pabru-plugin' ),
			'labels'                => $labels,
			'supports'              => array( 'title', ),
			'hierarchical'          => false,
			'public'                => true,
			'show_ui'               => true,
			'show_in_menu'          => true,
			'menu_position'         => 80,
			'menu_icon'             => 'dashicons-admin-site',
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
