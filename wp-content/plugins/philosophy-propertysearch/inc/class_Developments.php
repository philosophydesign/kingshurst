<?php
class Developments {
	var $fields;
	function __construct() {
		
	}
	function getFields () {
		$PS = new PropertySearch();
		$saved_heirachy = $PS->propsrch_heiropt();
		
		$fields[] =	['Status',				'status',		0,	'select',	['past'=>'Past','current'=>'Current','coming'=>'Coming Soon','reserved'=>'Reserved']];
		if (!empty($saved_heirachy['building'])) {
			$fields[] = array('Collection',		'collection', 	0, 'select', collection_list());
		}
		if (!empty($saved_heirachy['location'])) {
			$fields[] = array('Location',		'location', 	0, 'select', locations_list());
		}
		$fields[] =	array('List Description', 	'list_desc', 	0);
		$fields[] =	array('Website', 			'website', 		0);
		$fields[] = array('LatLng Coordinates',			'latlng',		0);
		
		$extra = get_linked_post_type_fields();
		$fields = array_merge($fields, $extra);
		
		return($fields);
		
	}
	function getDevelopments($saletype='') {
		global $wpdb;
		$wpdb->show_errors();
		$query = 'SELECT dev.*
			FROM '.$wpdb->posts.' prop
			LEFT JOIN '.$wpdb->postmeta.' prop_meta_dev on prop.ID = prop_meta_dev.post_id and prop_meta_dev.meta_key = "prop_development"
			LEFT JOIN '.$wpdb->postmeta.' prop_meta_sale on prop.ID = prop_meta_sale.post_id and prop_meta_sale.meta_key = "sale_type"
			LEFT JOIN '.$wpdb->posts.' dev on prop_meta_dev.meta_value = dev.ID
			WHERE prop.post_type = "properties" and prop.post_status = "publish"';
		if (!empty($saletype)) {
			$query .= 'and prop_meta_sale.meta_value = "'.$saletype.'"';
		}
			$query .= 'GROUP BY dev.ID';
			
		$result = $wpdb->get_results($query,OBJECT );
		return($result);
	}
	function getDevelopment($development_post_name) {
		/*
		 global $wpdb;
		 $wpdb->show_errors();
		 $query = 'SELECT * FROM '.$wpdb->posts.' WHERE post_name = "'.$development_post_name.'" AND post_type = "developments" AND post_status = "publish"';
		 $result = $wpdb->get_row($query,OBJECT );
		 return($result);
		 */
	
	
		$args = array(
				'name'=>$development_post_name,
				'post_type'=>"developments",
				'post_status'=>'publish'
		);
		$post = get_posts($args);
		return(reset($post));
	}
}