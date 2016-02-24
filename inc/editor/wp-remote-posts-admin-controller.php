<?php

class WP_Remote_Posts_Admin_Controller {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WP_Remote_Posts_Admin_Controller();
		}
	}

	private function __construct() {
		add_action( 'admin_menu', array($this, 'on_admin_menu') );
	}

	public function on_admin_menu() {
		
	}

}
