<?php
class Collections {
	var $fields;
	function __construct() {
		
	}
	function getFields () {
		
		$fields =  array();
 		
		$fields[] = array('Location',			'location', 	0, 'select', locations_list());
 		return($fields);
		
	}
	function getCollections () {
		$args = array(
				'post_type'=>'collections',
				'post_status'=>'publish',
				'orderby'=>'post_title',
				'order'=>'ASC'
		);
		$buildings = get_posts($args);
		return($buildings);
	}
}