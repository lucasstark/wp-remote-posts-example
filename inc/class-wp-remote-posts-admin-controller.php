<?php

class WP_Remote_Posts_Admin_Controller {

	private static $instance;

	public static function register() {
		if ( self::$instance == null ) {
			self::$instance = new WP_Remote_Posts_Admin_Controller();
		}
	}

	private $__edit_controllers;

	private function __construct() {
		
		//Include things required for the admin which are not included already. 
		require_once 'class-wp-remote-posts-edit-controller.php';
		require_once 'class-wp-remote-posts-list-table.php';

		add_action( 'admin_menu', array($this, 'on_admin_menu') );
	}

	public function on_admin_menu() {

		$remote_types = wp_remote_posts_get_post_types();
		foreach ( $remote_types as $rt ) {
			$this->__edit_controllers[$rt->post_type_name] = new WP_Remote_Posts_Edit_Controller( $rt );
			$this->__edit_controllers[$rt->post_type_name]->setup_menu();
		}
	}

	public function view_list_table_page() {
		$exampleListTable = new WP_Remote_Posts_List_Table();
		$exampleListTable->prepare_items();
		?>
		<div class="wrap">
			<div id="icon-users" class="icon32"></div>
			<h2>Example List Table Page</h2>
			<?php $exampleListTable->display(); ?>
		</div>
		<?php
	}

}
