<?php
class Properties {
	var $fields;
	function __construct() {
		
	}
	function getCacheList () {
		$list = array();
		$PS = new PropertySearch();
		$saved_heirachy = $PS->propsrch_heiropt();
		if (
				(empty($saved_heirachy['location'])) &&
				(empty($saved_heirachy['development'])) &&
				(empty($saved_heirachy['collection']))
				) {
			$list[] = 'location';
		}
		return($list);
	}
	function getNumericVals() {
		$list = array('price','total_area');
		return($list);
	}
	function getTaxonomyFields () {
		$list = array('property_type');
		return($list);
	}
	function getFields () {
		$PS = new PropertySearch();
		$saved_heirachy = $PS->propsrch_heiropt();
		$saved_buyingoptions = $PS->propsrch_buyingoptions();
 		$bo_list = buying_options_list();
		
		$fields =  array();
		$fields[] = ['List Description', 	'list_desc', 	0];
 		if ((!empty($saved_heirachy['development'])) || (!empty($saved_heirachy['collection']))) {
			$fields[] = ['Property Template',				'template',		0,	'select',	property_template_list()];
 		} else {
 			
 			$fields[] = ['Property Type(s)',		'property_type',	0,	'checkrefset',	property_type_list(true)];
 		}
		$fields[] = ['Status',				'status',		0,	'ref_select', property_status_list()];
 		if (count($bo_list ) > 1) {
 			$fields[] = ['Sale Type',			'saletype', 	0, 'select', $bo_list];
 		}
		if (empty($saved_heirachy['development']))  {
 			$fields[] = ['Tenure',			'tenure', 	0, 'ref_select', tenure_list('standard')];
 			$fields[] = ['Bedrooms',			'no_bedrooms',		0,	'ref_select',	number_bedrooms_list()];
		}
		if ((empty($saved_heirachy['development'])) && (!empty($saved_heirachy['collection']))) {
			$fields[] = ['Collection',		'collection', 	0, 'ref_select', collection_list()];
		}
		if (
				(empty($saved_heirachy['location'])) && 
				(empty($saved_heirachy['development'])) && 
				(empty($saved_heirachy['collection']))
			) {
			$fields[] = ['Price',			'price', 		0];
			$fields[] = ['Location',		'location', 	0];
		} elseif (
				(!empty($saved_heirachy['location'])) && 
				(empty($saved_heirachy['development'])) && 
				(empty($saved_heirachy['collection']))
			) {
			$fields[] = ['Location',		'location_post', 	0, 'ref_select', locations_list()];
		} else { 
			$fields[] = ['Price',			'price', 	0];
		}
 		if ((empty($saved_heirachy['development'])) && (empty($saved_heirachy['collection']))) {
			$fields[] = ['Address',			'address',		0, 'body'];
			$saved_areainputunits= get_option('propsrch_areainputunits');
			if ($saved_areainputunits == 'metric') {
 				$fields[] = ['Total Area (Square Metres)',			'total_area',		0];
			} else if ($saved_areainputunits == 'imperial') {
 				$fields[] = ['Total Area (Square Feet)',			'total_area',		0];
				
			}
			$fields[] = ['LatLng Coordinates',			'latlng',		0];
			
			$extra = get_linked_post_type_fields();
			$fields = array_merge($fields, $extra);
		} 
 		return($fields);
		
	}
	function getProperties ($saletype='') {
		$args = array(
				'post_type'=>'properties',
				'post_status'=>'publish',
				'posts_per_page'=>-1
		);
		
	
		$properties = get_posts($args);
		return($properties);
	}
	function getPropertiesBySaleType ($saletype='') {
		$args = array(
				'post_type'=>'properties',
				'post_status'=>'publish'
		);
		if (!empty($saletype)) {
			$args['meta_query'][] =
			array(
					'key'=>'sale_type',
					'value'=>$saletype
			);
		}
	
		$properties = get_posts($args);
		return($properties);
	}
	function getPropertiesByDevelopment ($dev_post_id, $saletype='') {
		$args = array(
				'post_type'=>'properties',
				'post_status'=>'publish'
		);
		if (!empty($dev_post_id)) {
			$args['meta_query'][] =
			array(
					'key'=>'prop_development',
					'value'=>$dev_post_id
			);
		}
		if (!empty($saletype)) {
			$args['meta_query'][] =
			array(
					'key'=>'sale_type',
					'value'=>$saletype
			);
		}
	
		$properties = get_posts($args);
		return($properties);
	}
	function getNumberofBedrooms($t_id, $rooms = array()) {
		if (empty($rooms)) {
			$rooms = get_field('room_details', $t_id);
		}
		$nob = 0;
		foreach ($rooms as $r) {
			if ($r['bedroom']) {
				$nob++;
			}
		}
		return($nob);
	}
	function getPropertyURL($development_name, $property_name) {
		$righturl = '/developments/'.$development_name.'/properties/'.$property_name.'/';
		return($righturl);
	}
}