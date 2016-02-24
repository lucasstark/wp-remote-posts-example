<?php

/**
 * Get's a list of remote post types. 
 * @return \WP_Remote_Posts_Post_Type_Model[]
 */
function wp_remote_posts_get_post_types() {

	$post_types = get_posts( array(
	    'post_type' => WPRP()->settings->post_type_remote_type,
	    'post_status' => 'publish',
	    'nopaging' => true,
	    'fields' => 'ids'
	) );

	if ( empty( $post_types ) ) {
		return array();
	}
	
	$results = array();
	foreach ( $post_types as $post_type_id ) {
		$results[] = new WP_Remote_Posts_Post_Type_Model( $post_type_id );
	}
	
	return $results;
}


function wp_remote_posts_post_type_supports($post_type, $feature) {
	
}