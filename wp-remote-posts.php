<?php

/*
 * Plugin Name:  WP Remote Posts
 * Description:  Core building blocks for how to incorporate entities from a remote site into your WP Site.  
 * Author:  Lucas Stark
 */

class WP_Remote_Posts {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WP_Remote_Posts();
		}
	}

	private function __construct() {
		include 'inc/wp-remote-posts-data-api.php';
		
		include 'inc/wp-remote-posts-rewrite.php';
		include 'inc/wp-remote-posts-templates.php';
		
		include 'inc/models/wp-remote-posts-item.php';
		
		WP_Remote_Posts_Rewrite::register();
		WP_Remote_Posts_Templates::register();
	}
	
	public static function get_template_directory() {
		return 'wp-remote-posts/';
	}
	
	public static function get_plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
	
}

WP_Remote_Posts::register();
