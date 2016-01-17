<?php

/**
 * Represents a single remote post item. 
 */
class WP_Remote_Posts_Item {

	/**
	 * Gets an instance for a single remote post. 
	 * @param int $remote_item_id
	 * @return WP_Remote_Posts_Item
	 */
	public static function get_instance( $remote_item_id ) {
		$data = self::get_data( $remote_item_id );
		return !empty( $data ) ? new WP_Remote_Posts_Item( $data ) : null;
	}

	/**
	 * Gets a an array containing all the data required to create an WP_Remote_Posts_Item instance;
	 * @param int $remote_item_id
	 */
	private static function get_data( $remote_item_id ) {
		$local_data = array();

		//TODO:   This is where you might grab the item from your XML feed. 
		$remote_data = WP_Remote_Posts_Data_Api::get_post_data( $remote_item_id );
		if ( !empty( $remote_data ) ) {
			$local_data['ID'] = $remote_data['ID'];
			$local_data['title'] = apply_filters( 'the_title', $remote_data['title'] );
			$local_data['content'] = apply_filters( 'the_content', $remote_data['content'] );
			$local_data['permalink'] = get_site_url() . '/remote-posts/' . $remote_data['ID'];
		}
		
		return apply_filters( 'wp_remote_posts_get_model_data', $local_data, $remote_item_id );
	}

	/**
	 *
	 * @var int 
	 */
	public $ID;

	/**
	 *
	 * @var string 
	 */
	public $title;

	/**
	 *
	 * @var string 
	 */
	public $content;

	/**
	 *
	 * @var string
	 */
	public $permalink;

	public function __construct( $data = array() ) {
		$this->ID = $data['ID'];
		$this->title = $data['title'];
		$this->content = $data['content'];
		$this->permalink = $data['permalink'];
	}

}
