<?php
if ( !class_exists( 'WP_List_Table' ) ) :
	require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
endif;

class WP_Remote_Posts_List_Table extends WP_List_Table {

	protected $post_type;

	/**
	 *
	 * @var WP_Remote_Posts_Post_Type_Model
	 */
	protected $post_type_object;

	/**
	 * 
	 * @param \WP_Remote_Posts_Post_Type_Model $remote_post_type
	 * @param array $args
	 */
	public function __construct( $remote_post_type, $args = array() ) {
		$this->post_type_object = $remote_post_type;

		$options = wp_parse_args( $args, array(
		    'plural' => $remote_post_type->labels->name,
		    'singular' => $remote_post_type->labels->singular_name,
		    'ajax' => false,
		    'screen' => 'wp-remote-posts',
			) );

		parent::__construct( $options );
	}

	public function get_columns() {
		$posts_columns = array();
		$posts_columns['cb'] = '<input type="checkbox" />';
		/* translators: manage posts column name */
		$posts_columns['title'] = _x( 'Title', 'column name' );
		$posts_columns['date'] = __( 'Date' );

		return $posts_columns;
	}

	public function get_hidden_columns() {
		return array();
	}

	public function get_sortable_columns() {
		return array(
		    'title' => array('title', false),
		    'date' => array('date', true)
		);
	}

	public function prepare_items() {
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$q = $_GET;

		$order = isset( $q['order'] ) ? strtolower( $q['order'] ) : 'asc';
		$orderby = isset( $q['orderby'] ) ? $q['orderby'] : 'date';
		$s = isset( $q['s'] ) ? $q['s'] : '';

		$per_page = $this->get_items_per_page( 'edit_' . $this->post_type_object->post_type_name . '_per_page' );
		$per_page = apply_filters( 'edit_posts_per_page', $per_page, $this->post_type_object->post_type_name );

		$current_page = $this->get_pagenum();

		$this->_column_headers = array($columns, $hidden, $sortable);

		$query = new WP_Remote_Posts_Query( $this->post_type_object->links->url_items, array(
		    'post_type' => $this->post_type_object->post_type_name,
		    'per_page' => $per_page,
		    'page' => $current_page,
		    
		    's' => $s
		) );

		$this->items = $query->get_posts();

		$this->set_pagination_args( array(
		    'total_items' => $query->found_posts,
		    'per_page' => $per_page
		) );
	}

	/**
	 * Handles the checkbox column output.
	 *
	 * @since 4.3.0
	 * @access public
	 *
	 * @param WP_Remote_Posts_Post $post The current WP_Remote_Posts_Post object.
	 */
	public function column_cb( $post ) {
		$title = $post->title();
		if ( empty( $title ) ) {
			$title = __( '(no title)' );
		}
		//@TODO:  Check permissions for column_cb
		if ( true ):
			?>
			<label class="screen-reader-text" for="cb-select-<?php echo esc_attr( $post->id ) ?>"><?php
				printf( __( 'Select %s', 'wp-remote-posts' ), $title );
				?></label>
			<input id="cb-select-<?php echo esc_attr( $post->id ); ?>" type="checkbox" name="post[]" value="<?php echo esc_attr( $post->id ); ?>" />
			<div class="locked-indicator"></div>
			<?php
		endif;
	}

	/**
	 * 
	 * @param WP_Remote_Posts_Post $post The current WP_Remote_Posts_Post object.
	 */
	public function column_title( $post ) {
		$pad = '';
		
		$_edit_link = $this->post_type_object->_edit_link;
		$edit_link = admin_url( sprintf( $_edit_link . '&amp;action=edit', $post->id ) );
		
		echo '<strong>';
		echo '<a class="row-title" href="' . $edit_link . '" title="' . esc_attr( sprintf( __( 'Edit &#8220;%s&#8221;' ), $post->title() ) ) . '">' . $pad . $post->title() . '</a>';
		echo '</strong>';
	}

	public function column_default( $item, $column ) {
		return $item->$column;
	}

	/**
	 * Generates and displays row action links.
	 *
	 * @since 4.3.0
	 * @access protected
	 *
	 * @param object $post        Post being acted upon.
	 * @param string $column_name Current column name.
	 * @param string $primary     Primary column name.
	 * @return string Row actions output for posts.
	 */
	protected function handle_row_actions( $post, $column_name, $primary ) {
		if ( $primary !== $column_name ) {
			return '';
		}

		$post_type_object = $this->post_type_object;
		$can_edit_post = true;
		$can_delete_post = true;

		$actions = array();
		
		$_edit_link = $post_type_object->_edit_link;
		
		$edit_link = admin_url( sprintf( $_edit_link . '&amp;action=edit', $post->id ) );
		$trash_link = admin_url( sprintf( $_edit_link . '&amp;action=trash', $post->id ) );
		$delete_link = admin_url( sprintf( $_edit_link . '&amp;action=delete', $post->id ) );
		$restore_link = admin_url( sprintf( $_edit_link . '&amp;action=untrash', $post->id ) );

		if ( $can_edit_post && 'trash' != $post->post_status ) {
			$actions['edit'] = '<a href="' . $edit_link . '" title="' . esc_attr__( 'Edit this item' ) . '">' . __( 'Edit' ) . '</a>';
			$actions['inline hide-if-no-js'] = '<a href="#" class="editinline" title="' . esc_attr__( 'Edit this item inline' ) . '">' . __( 'Quick&nbsp;Edit' ) . '</a>';
		}

		if ( $can_delete_post ) {
			if ( 'trash' === $post->post_status ) {
				$actions['untrash'] = "<a title='" . esc_attr__( 'Restore this item from the Trash' ) . "' href='" . wp_nonce_url( $restore_link, 'untrash-post_' . $post->id ) . "'>" . __( 'Restore' ) . "</a>";
			} elseif ( EMPTY_TRASH_DAYS ) {
				$actions['trash'] = "<a class='submitdelete' title='" . esc_attr__( 'Move this item to the Trash' ) . "' href='" . wp_nonce_url( $trash_link, "'trash-post_{$post->id}" ) . "'>" . __( 'Trash' ) . "</a>";
			}

			if ( 'trash' === $post->post_status || !EMPTY_TRASH_DAYS ) {
				$actions['delete'] = "<a class='submitdelete' title='" . esc_attr__( 'Delete this item permanently' ) . "' href='" . wp_nonce_url( $delete_link, "'trash-post_{$post->id}" ) . "'>" . __( 'Delete Permanently' ) . "</a>";
			}
		}

		return $this->row_actions( $actions );
	}

}
