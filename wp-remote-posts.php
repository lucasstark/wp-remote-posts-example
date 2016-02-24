<?php

/*
 * Plugin Name:  WP Remote Posts
 * Description:  Core building blocks for how to incorporate entities from a remote site into your WP Site.  
 * Author:  Lucas Stark
 */

class WP_Remote_Posts {

	private static $instance;

	/**
	 * Registers the single instance of the plugin. 
	 */
	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WP_Remote_Posts();
		}
	}

	/**
	 * The single instance of the plugin. 
	 * @return WP_Remote_Posts
	 */
	public static function instance() {
		return self::$instance;
	}

	/**
	 * Settings manager for the plugin. 
	 * @var WP_Remote_Posts_Settings
	 */
	public $settings;

	/**
	 * The main client API used to access remote services. 
	 * @var WP_Remote_Posts_Data_Api 
	 */
	public $api;

	private function __construct() {


		require_once 'inc/functions.php';

		require_once 'inc/class-wp-remote-posts-client.php';
		require_once 'inc/class-wp-remote-posts-settings.php';

		require_once 'inc/models/wp-remote-posts-post-type.php';
		require_once 'inc/models/wp-remote-posts-post.php';

		require_once 'inc/wp-remote-posts-query.php';



		require_once 'inc/class-wp-remote-posts-taxonomy.php';
		require_once 'inc/wp-remote-posts-rewrite.php';
		require_once 'inc/wp-remote-posts-templates.php';

		require_once 'inc/models/wp-remote-posts-item.php';

		WP_Remote_Posts_Taxonomy::register();
		WP_Remote_Posts_Rewrite::register();
		WP_Remote_Posts_Templates::register();


		//Include and boot up the remote posts admin controller. 
		include 'inc/class-wp-remote-posts-admin-controller.php';
		WP_Remote_Posts_Admin_Controller::register();

		add_action( 'plugins_loaded', array($this, 'on_plugins_loaded'), 0 );
	}

	public function on_plugins_loaded() {
		//Set up some plugin properties.  Done in plugins loaded to prevent any race conditions. 
		$this->settings = new WP_Remote_Posts_Settings();
		
		if ( isset( $_GET['test'] ) ) {
			require ABSPATH . '/' . PLUGINDIR . '/client-php/library/WPAPI.php';
			require ABSPATH . '/' . PLUGINDIR . '/client-php/vendor/autoload.php';

			WPAPI::register_autoloader();
			$api = new WPAPI( 'http://local.wordpress.com/wp-json/wp/v2' );
			$posts_collection = new WPAPI_Posts( $api, 'admin', 'oicu812k' );

			$post = $posts_collection->get(1000);
			$post->title = 'New Post Title ' . date('Y-m-d H:i:s');
			$post->update();
			
		}
		
		return;
	}

	public static function get_template_directory() {
		return 'wp-remote-posts/';
	}

	public static function get_plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

}

WP_Remote_Posts::register();

/**
 * The main instance of the plugin. 
 * @return WP_Remote_Posts
 */
function WPRP() {
	return WP_Remote_Posts::instance();
}
