<?php

class WP_Remote_Posts_Data_Api {

	private static $_cache;

	public static function get_posts_data() {

		if ( empty( self::$_cache ) ) {
			//Just mocking up some data here.  This is where you'd grab and parse your XML feed. 
			for ( $i = 0; $i < 100; $i++ ) {
				self::$_cache[$i] = array(
				    'ID' => $i,
				    'title' => 'Post ' . $i,
				    'content' => 'Post ' . $i . ' Content'
				);
			}
		}
		
		return self::$_cache;
	}
	
	public static function get_post_data($remote_post_id) {
		self::get_posts_data();
		return isset(self::$_cache[$remote_post_id]) ? self::$_cache[$remote_post_id] : null;
	}

}
