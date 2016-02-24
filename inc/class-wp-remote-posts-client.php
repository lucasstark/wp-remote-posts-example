<?php

class WP_Remote_Posts_Client {

	private $post_type_object;

	/**
	 * 
	 * @param \WP_Remote_Posts_Post_Type_Model $post_type_object
	 */
	public function __construct( $post_type_object ) {
		$this->post_type_object = $post_type_object;
	}

	/**
	 * 
	 * @param int $id
	 * @return \WP_Remote_Posts_Post A remote post object. 
	 */
	public function get_item( $id, $context = 'view' ) {
		$_post = wp_cache_get( $id, "remote-posts-{$context}" );
		if ( empty( $_post ) ) {
			$url = sprintf( $this->post_type_object->links->url_item, $id );
			$post_data = $this->json_decode( $this->request($url, array('context' => $context) ) );
			if ( $post_data ) {
				$_post = new WP_Remote_Posts_Post( $post_data );
				wp_cache_add( $_post->id, $_post, "remote-posts-{$context}" );
			} else {
				$_post = false;
			}
		}

		return $_post;
	}

	public function get_items( $args = array(), $context = 'view' ) {

		$url = $this->post_type_object->links->url_items;
		$data = $this->json_decode( $this->request( $url ) );

		foreach ( $data as $post_data ) {
			$_post = new WP_Remote_Posts_Post( $post_data );
			wp_cache_add( $_post->id, $_post, "remote-posts-{$context}" );
			$results[] = $_post;
		}

		return $results;
	}

	/**
	 * 
	 * @param int $id the id of the item to update. 
	 * @param array $changed An array of key=>values to change. 
	 */
	public function update( $id, $changed = array() ) {
		$url = sprintf( $this->post_type_object->links->url_item, $id );
		$response = $this->request( $url, $changed, 'POST' );
	}

	private function request( $url, $data = array(), $type = 'GET', $headers = array() ) {
		
		$default_headers = array(
		    'Authorization' => 'Basic ' . base64_encode( 'admin' . ':' . 'oicu812k' ),
		    'X-WP-Nonce' => wp_create_nonce( 'wp_rest' )
		);

		$options = array(
		    'headers' => array_merge( $default_headers, $headers ),
		    'method' => $type
		);
		
		if ( ( $type == 'GET' || $type == 'HEAD') && !empty($data)){
			$url = add_query_arg($data, $url);
		} else {
			$options['body'] = $data;
		}

		$response = wp_remote_request( $url, $options );

		if ( is_wp_error( $response ) ) {
			wp_die( sprintf( __( 'Server responded with: %s (%d). Data may not be usable.', 'wp-remote-posts' ), $response->get_error_message(), $response->get_error_code() ) );
		}

		if ( $response['response']['code'] != '200' ) {
			wp_die( sprintf( __( 'Server responded with: %s (%d). Data may not be usable.', 'wp-remote-posts' ), $response['response']['message'], $response['response']['code'] ) );
		}

		return $response;
	}

	private function json_decode( $response ) {
		$json_response = wp_remote_retrieve_body( $response );
		if ( empty( $json_response ) ) {
			return false;
		}

		$data = json_decode( $json_response );
		$has_error = ( function_exists( 'json_last_error' ) && json_last_error() !== JSON_ERROR_NONE );
		if ( (!$has_error && $data === null ) || $has_error ) {
			throw new Exception( $json_response );
		}

		return $data;
	}

}
