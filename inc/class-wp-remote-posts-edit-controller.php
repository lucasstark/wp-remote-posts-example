<?php

class WP_Remote_Posts_Edit_Controller {

	/**
	 * Internal store for the remote post type object. 
	 * @var \WP_Remote_Posts_Post_Type_Model 
	 */
	private $_remote_post_type;

	/**
	 *
	 * @var WP_Remote_Posts_Client 
	 */
	private $client;

	public function __construct( $remote_post_type_object ) {
		$this->_remote_post_type = $remote_post_type_object;
		$this->client = new WP_Remote_Posts_Client( $remote_post_type_object );

		add_action( 'admin_init', array($this, 'maybe_handle_request') );
	}

	/**
	 * Handles POST requests. 
	 */
	public function maybe_handle_request() {
		if ( !empty( $_POST ) && isset( $_GET['page'] ) && $_GET['page'] == "edit-{$this->_remote_post_type->post_type_name}" ) {

			$form_action = isset( $_POST['form_action'] ) ? $_POST['form_action'] : '';
			switch ( $form_action ) {
				case 'editpost' :
					$this->handle_update_post();
					break;
				default :
					break;
			}
		}
	}

	public function setup_menu() {
		$slug = add_menu_page( $this->_remote_post_type->labels->name, $this->_remote_post_type->labels->name, 'administrator', 'edit-' . $this->_remote_post_type->post_type_name, array($this, 'handle_route') );
		add_submenu_page('edit-' . $this->_remote_post_type->post_type_name, $this->_remote_post_type->labels->add_new_item, $this->_remote_post_type->labels->add_new, 'administrator', 'add-new-' . $this->_remote_post_type->post_type_name, array($this, 'handle_route') );
	}

	public function handle_route() {

		$action = isset( $_GET['action'] ) ? $_GET['action'] : 'table';
		switch ( $action ) {
			case 'create' :
				$this->handle_create_page();
				break;
			case 'edit' :
				$this->handle_edit_page();
				break;
			case 'trash' :
				$this->handle_trash();
				break;
			case 'delete' :
				$this->handle_delete();
				break;
			case 'untrash' :
				$this->handle_untrash();
				break;
			default :
				$this->handle_table_page();
				break;
		}
	}

	public function handle_table_page() {
		$exampleListTable = new WP_Remote_Posts_List_Table( $this->_remote_post_type );
		$exampleListTable->prepare_items();

		$_edit_link = $this->_remote_post_type->_edit_link;
		$post_new_file = admin_url( sprintf( $_edit_link . '&amp;action=create', 'new' ) );
		?>

		<div class="wrap">
			<h1><?php
		echo esc_html( $this->_remote_post_type->labels->name );
		if ( current_user_can( $this->_remote_post_type->cap->create_posts ) ) :
			echo ' <a href="' . esc_url( ( $post_new_file ) ) . '" class="page-title-action">' . esc_html( $this->_remote_post_type->labels->add_new ) . '</a>';
		endif;

		if ( !empty( $_REQUEST['s'] ) ) :
			printf( ' <span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;' ) . '</span>', get_search_query() );
		endif;
		?>
			</h1>
				<?php $exampleListTable->display(); ?>
		</div>
			<?php
		}

		public function handle_create_page() {
			$post_type_object = false;
			include 'views/edit/post-new.php';
		}

		public function handle_edit_page() {
			$post_type_object = $this->_remote_post_type;

			$post = $this->client->get_item( absint( $_GET['post'] ), 'edit' );

			include 'views/edit/post.php';
		}

		public function handle_trash() {
			return;
		}

		public function handle_delete() {
			return;
		}

		public function handle_untrash() {
			return;
		}

		public function handle_update_post() {
			$post_id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : -1;
			if ( false === wp_verify_nonce( $_POST['_wpnonce'], 'update-post_' . $post_id ) ) {
				wp_die( __( 'Error while saving', 'wp-remote-posts' ) );
			}


			$this->client->update( $post_id, array(
			    'title' => $_POST['post_title']
			) );
		}

	}
	