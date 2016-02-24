<?php

/**
 * Controller to register post types and taxonomies. 
 */
class WP_Remote_Posts_Taxonomy {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WP_Remote_Posts_Taxonomy();
		}
	}

	private function __construct() {
		add_action( 'init', array($this, 'register_post_types'), 99 );
	}

	/**
	 * Registers the main post type for storing remote post type configurations. 
	 */
	public function register_post_types() {
		$labels = array(
		    'name' => _x( 'Remote Types', 'post type general name', 'wp-remote-posts' ),
		    'singular_name' => _x( 'Remote Type', 'post type singular name', 'wp-remote-posts' ),
		    'menu_name' => _x( 'Remote Types', 'admin menu', 'wp-remote-posts' ),
		    'name_admin_bar' => _x( 'Remote Type', 'add new on admin bar', 'wp-remote-posts' ),
		    'add_new' => _x( 'Add New', 'remote type', 'wp-remote-posts' ),
		    'add_new_item' => __( 'Add New Remote Type', 'wp-remote-posts' ),
		    'new_item' => __( 'New Remote Type', 'wp-remote-posts' ),
		    'edit_item' => __( 'Edit Remote Type', 'wp-remote-posts' ),
		    'view_item' => __( 'View Remote Type', 'wp-remote-posts' ),
		    'all_items' => __( 'All Remote Types', 'wp-remote-posts' ),
		    'search_items' => __( 'Search Remote Types', 'wp-remote-posts' ),
		    'parent_item_colon' => __( 'Parent Remote Types:', 'wp-remote-posts' ),
		    'not_found' => __( 'No remote types found.', 'wp-remote-posts' ),
		    'not_found_in_trash' => __( 'No remote types found in Trash.', 'wp-remote-posts' )
		);

		$args = array(
		    'labels' => $labels,
		    'description' => __( 'Description.', 'wp-remote-posts' ),
		    'public' => false,
		    'publicly_queryable' => false,
		    'show_ui' => true,
		    'show_in_menu' => true,
		    'query_var' => false,
		    'rewrite' => false,
		    'capability_type' => 'post',
		    'has_archive' => false,
		    'hierarchical' => false,
		    'menu_position' => null,
		    'supports' => array('title')
		);

		register_post_type( 'wp-remote-posts-type', $args );
	}

}
