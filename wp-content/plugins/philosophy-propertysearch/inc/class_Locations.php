<?php
class Locations {
	var $fields;
	function __construct() {
		
	}
	function getFields () {
		
		$fields =  array();
		$fields[] = array('LatLng Coordinates',			'latlng',		0);
 		return($fields);
		
	}
	function getLocations () {
		$args = array(
				'post_type'=>'locations',
				'post_status'=>'publish'
		);
		
		$locations = get_posts($args);
		return($locations);
	}
}