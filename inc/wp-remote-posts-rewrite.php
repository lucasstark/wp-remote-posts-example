<?php

class WP_Remote_Posts_Rewrite {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WP_Remote_Posts_Rewrite();
		}
	}

	public function __construct() {
		add_action( 'init', array($this, 'add_rewrite_rules') );
		add_filter( 'query_vars', array($this, 'add_query_vars') );
	}

	public function add_query_vars( $vars ) {
		$vars[] = 'wp_remote_posts_id';
		return $vars;
	}

	public function add_rewrite_rules() {
		add_rewrite_rule( '^remote-posts/?$', 'index.php?wp_remote_posts_id=all', 'top' );
		add_rewrite_rule( '^remote-posts/([^/]*)$', 'index.php?wp_remote_posts_id=$matches[1]', 'top' );
		
		add_rewrite_tag( '%wp_remote_posts_id%', '([^/]*)' );
	}

}
