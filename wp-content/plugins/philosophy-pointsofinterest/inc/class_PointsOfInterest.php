<?php
if (empty($_SESSION)) {
	session_start();
}
class PointsOfInterest {
	var $poi_fields;
	function __construct() {
		
	}
	function getFields () {
		$this->poi_fields= [
				['Address',			'address', 		false, 'body'],
				['Map Pin Text',	'pintext', 		false, 'body'],
				['Coordinates',		'coordinates', 	false],
		];
		return ($this->poi_fields);
	}
	function getPOIList($typesel=array()) {
		$a = [
				'post_type'=>'points_of_interest',
				'post_status'=>'publish',
				'limit'=>-1,
				'numberposts'=>-1,
				'posts_per_page'=>-1
		];
		if (!empty($typesel)) {
			$a['tax_query'] = array(
					array(
							'taxonomy' => 'poi-type',
							'field' => 'slug',
							'terms' => $typesel,
					)
			);
		}
		return(get_posts($a));
	}
}