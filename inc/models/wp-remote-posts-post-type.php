<?php

class WP_Remote_Posts_Post_Type_Model {

	private $_title;

	/**
	 * The name ( slug ) of the post type. 
	 * @var string
	 */
	public $post_type_name;

	/**
	 * 
	 * @param int $post_type_id The wp remote post type ID. 
	 */
	public function __construct( $post_type_id, $args = array() ) {
		$post = get_post( $post_type_id );

		$this->_title = $post->post_title;
		$this->post_type_name = 'remote_' . $post->post_name;

		$name = $this->_title . 's';
		$singular_name = $this->_title;

		//@TODO:  This needs to be cleaned up and organized a bit better.   WP Core register_post_type is a a little messy. 
		// Args prefixed with an underscore are reserved for internal use.
		$defaults = array(
		    'name' => $this->post_type_name,
		    'labels' => array(),
		    'description' => '',
		    'public' => false,
		    'hierarchical' => false,
		    'exclude_from_search' => null,
		    'publicly_queryable' => null,
		    'show_ui' => null,
		    'show_in_menu' => null,
		    'show_in_nav_menus' => null,
		    'show_in_admin_bar' => null,
		    'menu_position' => null,
		    'menu_icon' => null,
		    'capability_type' => 'post',
		    'capabilities' => array(),
		    'map_meta_cap' => null,
		    'supports' => array(),
		    'register_meta_box_cb' => null,
		    'taxonomies' => array(),
		    'has_archive' => false,
		    'rewrite' => true,
		    'query_var' => true,
		    'can_export' => true,
		    'delete_with_user' => null,
		    '_builtin' => false,
		    '_edit_link' => 'admin.php?page=edit-' . $this->post_type_name . '&post=%d',
		);

		$args = array_merge( $defaults, $args );
		$args = (object) $args;
		$args->labels = array(
		    'name' => $name,
		    'singular_name' => sprintf( _x( '%s', 'post type singular name', 'wp-remote-posts' ), $singular_name ),
		    'menu_name' => $name,
		    'name_admin_bar' => $singular_name,
		    'add_new' => _x( 'Add New', 'remote type', 'wp-remote-posts' ),
		    'add_new_item' => sprintf( __( 'Add New %s', 'wp-remote-posts' ), $singular_name ),
		    'new_item' => sprintf( __( 'New %s', 'wp-remote-posts' ), $singular_name ),
		    'edit_item' => sprintf( __( 'Edit %s', 'wp-remote-posts' ), $singular_name ),
		    'view_item' => sprintf( __( 'View %s', 'wp-remote-posts' ), $singular_name ),
		    'all_items' => sprintf( __( 'All %s', 'wp-remote-posts' ), $name ),
		    'search_items' => sprintf( __( 'Search %s', 'wp-remote-posts' ), $name ),
		    'parent_item_colon' => sprintf( __( 'Parent %s:', 'wp-remote-posts' ), $name ),
		    'not_found' => sprintf( __( 'No %s found.', 'wp-remote-posts' ), strtolower( $name ) ),
		    'not_found_in_trash' => sprintf( __( 'No %s found in Trash.', 'wp-remote-posts' ), strtolower( $name ) )
		);

		foreach ( $args as $arg => $v ) {
			$this->$arg = $v;
		}

		$this->labels = get_post_type_labels( $args );


		$caps = array(
		    'create_posts' => 'administrator'
		);

		$this->cap = (object) $caps;

		
		$this->links = new stdClass();
		$this->links->url_base = 'http://local.wordpress.com/wp-json/wp/v2';
		$this->links->url_items = $this->links->url_base . '/posts';
		$this->links->url_item = $this->links->url_base . '/posts/%d';
		
	}

	/** Helpers for WP_List_Tables * */
	public function get_table_columns() {
		
	}

	/** Helpers for Post Type Supports * */

	/**
	 * Determine if the post type supports the feature. 
	 * @param string $feature
	 * @todo Make this actually look up if we have support for the feature. 
	 */
	public function supports( $feature ) {
		return true;
	}

}
