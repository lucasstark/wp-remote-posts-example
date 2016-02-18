<?php

class WP_Remote_Posts_Templates {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WP_Remote_Posts_Templates();
		}
	}

	private function __construct() {
		add_filter( 'template_include', array($this, 'on_template_include') );
	}

	public function on_template_include( $template ) {
		global $wp_query;

		if ( isset( $wp_query->query_vars['wp_remote_posts_id'] ) && !empty( $wp_query->query_vars['wp_remote_posts_id'] ) ) {



			if ( $wp_query->query_vars['wp_remote_posts_id'] == 'all' ) {
				$file = 'archive-remote-post.php';
			} else {
				$item = WP_Remote_Posts_Item::get_instance( (int) get_query_var( 'wp_remote_posts_id' ) );
				if ( empty( $item ) ) {
					$file = 'single-remote-post-404.php';
				} else {
					$file = 'single-remote-post.php';
				}
			}

			//Look in your theme for single-remote-post.php and wp-remote-posts/single-remote-post.php
			$template = locate_template( array_unique( array($file, WP_Remote_Posts::get_template_directory() . $file) ) );
			if ( !$template ) {
				$template = WP_Remote_Posts::get_plugin_path() . '/templates/' . $file;
			}
		}

		return $template;
	}

}
