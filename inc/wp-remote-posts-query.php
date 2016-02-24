<?php

/**
 * A query object which mimics functionality of the WP_Query
 */
class WP_Remote_Posts_Query {

	private $_endpoint;

	/**
	 * The total number of posts found matching the current query parameters
	 * @var int 
	 */
	public $found_posts;

	/**
	 * The number of posts being displayed.
	 * @var int 
	 */
	public $post_count;

	/**
	 * The total number of pages. Is the value of the X-WP-TotalPages
	 * @var int
	 */
	public $max_num_pages;

	public function __construct( $endpoint, $query = '' ) {
		$this->_endpoint = $endpoint;

		if ( !empty( $query ) ) {
			$this->query( $query );
		}
	}

	/**
	 * Initiates object properties and sets default values.
	 *
	 * @since 1.5.0
	 * @access public
	 */
	public function init() {
		unset( $this->posts );
		unset( $this->query );
		$this->query_vars = array();
		$this->post_count = 0;
		$this->current_post = -1;

		unset( $this->request );
		unset( $this->post );

		$this->found_posts = 0;
		$this->max_num_pages = 0;
	}

	public function query( $query ) {
		$this->init();
		$this->query = $this->query_vars = wp_parse_args( $query );
		return $this->get_posts();
	}

	public function get_posts() {
		
		//$this->parse_query();
		
		// Shorthand.
		$q = $this->query_vars;
		unset( $q['post_type'] );
		$endpoint_args = array();
		foreach ( $q as $k => $v ) {
			if ( !empty( $v ) ) {
				$endpoint_args[$k] = $v;
			}
		}

		$endpoint_url = add_query_arg( $endpoint_args, $this->_endpoint );
		$response = wp_remote_get( $endpoint_url, array('timeout' => 20) );

		if ( is_wp_error( $response ) ) {
			//$this->debug( sprintf( __( 'HTTP request returned an error: %s (%s).', 'json-shortcode' ), $response->get_error_message(), $response->get_error_code() ) );
			echo $response->get_error_message();
			return;
		}

		if ( $response['response']['code'] != '200' ) {
			wp_die( sprintf( __( 'Server responded with: %s (%d). Data may not be usable.', 'wp-remote-posts' ), $response['response']['message'], $response['response']['code'] ) );
		}

		$json_response = wp_remote_retrieve_body( $response );

		if ( empty( $json_response ) ) {
			return array();
		}


		$this->found_posts = wp_remote_retrieve_header( $response, 'x-wp-total' );
		$this->max_num_pages = wp_remote_retrieve_header( $response, 'x-wp-totalpages' );

		$results = array();
		$data = json_decode( $json_response );

		foreach ( $data as $post_data ) {
			$results[] = new WP_Remote_Posts_Post( $post_data );
		}

		return $results;
	}

}
	