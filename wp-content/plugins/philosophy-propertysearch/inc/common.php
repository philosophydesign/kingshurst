<?php
/*
function search_form () {

	$p = 100000;
	$prices = array();
	while ($p <= 1000000) {
		$prices[] = '&pound;'.number_format($p);
		$p = $p + 25000;
	}

	$d = 0;
	$distances = array();
	while ($d <= 50) {
		$distances[] = $d.' km';
		$d = $d + 5;
	}

	$structure =  [
			['Area',			'a',				0,	'select', ['London', 'Everywhere Else']],
			['Developments',	'd',				0,	'select', development_list()],
			['Buying Options',	'buying_options',	0,	'select', ['Private', 'Shared Ownership']],
			['Distance',		'distance',			0,	'select', $distances],
			['Price',			'price_min',		0,	'select', $prices],
			['',				'price_max',		0,	'select', $prices],
			['Type',			'type',				0,	'select', ['Studio','Apartment','House']],
			['Bedrooms',		'bedrooms',			0,	'select', [1,2,3,4,5,6,7,8,9,10]],
				
	];

	$fb = new GesForms();
	$fb->fieldClasses = array();
	$fb->unique = '-'.rand(100,999);
	$fb->placeholder = array(
			'area'				=>	'Select from list',
			'development'		=>	'Select from list',
			'buying_options'	=>	'Select from list',
			'price_min'			=>	'Minium',
			'price_max'			=>	'Maximum',
	);


	$html = '
		<block class="search_form">
			<form action="">
				'.$fb->outputFields($fb->makeStructObj($structure)).'
				<button>Search</button>
			</form>
		</block>
			';
		
	return($html);
}
*/
function getDevelopments($saletype='') {
	$DEV = new Developments();
	$developments = $DEV->getDevelopments($saletype);
	return($developments);
}
function getDevelopment($development_post_name) {
	$DEV = new Developments();
	$development = $DEV->getDevelopment($development_post_name);
	return($development);
}



function getPropertiesBySaleType ($saletype) {
	$PRO = new Properties();
	$properties = $PRO->getPropertiesBySaleType($saletype);
	return($properties);
}

function getPropertiesByDevelopment ($t_id, $rooms = array()) {
	$PRO = new Properties();
	$properties = $PRO->getPropertiesByDevelopment($t_id, $rooms);
	return($properties);
}

function  getNumberofBedrooms($t_id, $rooms = array()) {
	$PRO = new Properties();
	$result = $PRO->getNumberofBedrooms($t_id, $rooms);
	return($result);
}


function getPropertyURL ($development_name, $property_name) {
	$PRO = new Properties();
	$result = $PRO->getPropertyURL($development_name, $property_name);
	return($result);
}


function development_list ($byname=true) {
	$result = getDevelopments();
	$list = [];
	foreach ($result as $r) {
		if ($byname) {
			$list[$r->post_name] = $r->post_title;
		} else {
			$list[$r->ID] = $r->post_title;
		}
	}
	return($list);
}
function tenure_list ($saved_tenureoption=0) {
	$PS = new PropertySearch();
	if (empty($saved_tenureoption)) {
		$saved_tenureoption = get_option('propsrch_tenureoption');
	}
	$list = [];
	if ($saved_tenureoption == 'standard') {
		foreach ($PS->tenureopts as $k => $v) {
			$list[$k] = $v['label']; 
		}
	} elseif ($saved_tenureoption == 'friendly') {
		foreach ($PS->tenureopts_friendly as $k => $v) {
			$list[$k] = $v['label']; 
		}
	}  
	return($list);
}
function property_template_list ($byname=true) {
	$templates = get_posts(array(
		'post_type'=>'propertytemplates',
		'post_status'=>'publish',
	));
	$list = [];
	foreach ($templates as $r) {
		if ($byname) {
			$list[$r->post_name] = $r->post_title;
		} else {
			$list[$r->ID] = $r->post_title;
		}
	}
	return($list);
}
function property_status_list() {
	$saved_statusoption= get_option('propsrch_statusoption');
	$PS = new PropertySearch();
	$list = [];
	foreach ($PS->status_list as $k=>$l) {
		if (
				($l['sor'] == $saved_statusoption) 
				|| 
				($saved_statusoption == 'both') 
				|| 
				($l['sor'] == 'both')
			) {
			$list[$k] = $l['label'];
		}
	}
	
	return($list);
}
function buying_options_list () {
	$PS = new PropertySearch();
	$saved_buyingoptions = $PS->propsrch_buyingoptions();
	$list = [];
	foreach ($PS->buyingopts as $k => $v) {
		if ((isset($saved_buyingoptions[$k])) && ($saved_buyingoptions[$k] == 1)) {
			$list[$k] = $v['label'];
		}
	}
	return($list);
	
}
function locations_list ($byname=true) {
	$LOC = new Locations();
	$locations = $LOC->getLocations();
	$list = [];
	foreach ($locations as $v) {
		if ($byname) {
			$list[$v->post_name] = $v->post_title;
		} else {
			$list[$v->ID] = $v->post_title;
		}
	}
	
	return($list);
	
}
function collection_list ($byname=true) {
	$BUI = new Collections();
	$buildings = $BUI->getCollections();
	$list = [];
	foreach ($buildings as $v) {
		if ($byname) {
			$list[$v->post_name] = $v->post_title;
		} else {
			$list[$v->ID] = $v->post_title;
		}
	}
	
	return($list);
	
}
function post_list ($post_type, $byname=true) {
	echo  $byname;
	$args = [
		'post_type'			=>	$post_type,
		'post_status'		=>	'publish',
		'orderby'			=>	'post_name',
		'order'				=>	'ASC',
		'posts_per_page'	=>	-1,
		'limit'				=>	-1
	];
	$posts = get_posts($args);
	$list = [];
	foreach ($posts as $v) {
		if ($byname) {
			$list[$v->post_name] = $v->post_title;
		} else {
			$list[$v->ID] = $v->post_title;
		}
	}
	
	return($list);
	
}
function property_type_list ($adminmode=false) {
	
	$PT = new PropTaxonomy();
	$r = $PT->getTermsByCat('property_type');
	$list = [];
	foreach ($r as $l) {
		if ($adminmode) {
			$list[$l->term_id] = $l->term_value;
		} else {
			$list[$l->term_value] = $l->term_value;
		}
	}
	
	return($list);
}
function number_bedrooms_list () {
	return(["Studio", 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]);
}
function propsrch_metabox ($inc) {
	
	$c = get_option("propsrch_admin_colour");
	$c =  (!empty($c)) ? $c : '#0000FF';
	echo '<div class="propsrch_metabox">
			<style>
			#development_fields .hndle {
				background-color: '.$c.';
				color: white;	
			}
			</style>
			';
	$inc = include(PROPSRCH_LOC."html/".$inc.".php");
	if (file_exists($inc)) {
		include($inc);
	}
	echo '<br class="clear"/></div>';
}
function fieldcache_list ($pt, $fn) {
	$FC = new FieldCache();
	return($FC->getVals($pt, $fn, true));
}
function get_linked_post_type_fields () {
	$PS = new PropertySearch();
	$saved_posttypes = $PS->propsrch_posttypes();
	$fields = array();
	$fields[] = ["<h2>Related Items</h2>",'related_items',0,'html'];
	foreach ($saved_posttypes as $k => $v) {
		if ($v->linked) {
			$pt = get_post_type_object($k);
			$make = $v->multiple ? 'checkrefset' : 'ref_select';
 			$fields[] =  [$pt->labels->name,	'linked_'.$k,		0,	$make,	post_list($k, FALSE)];
		}
	}
	
	return($fields);
}



function getPostLinks($post_id, $post_type='') {
	global $wpdb;
	$q = 'select * from propsrch_postlinks l left join '.$wpdb->posts.' pl ON pl.ID = l.linked_post_id where property_post_id = '.$post_id;
	$r = $wpdb->get_results($q);
	$list = [];
	foreach ($r as $p) {
		$list[$p->post_type][] = $p;
	}
	return($list);

}
function getPIDsFromLink($linked_post_id) {
	global $wpdb;
	$q = 'select * from propsrch_postlinks where linked_post_id = '.$linked_post_id;
	$r = $wpdb->get_results($q);
	$list = [];
	foreach ($r as $p) {
		$list[] = $p->property_post_id;
	}
	return($list);

}

function addPostLink($property_post_id, $linked_post_id, $post_type='') {
	global $wpdb;
	$q = 'select * from propsrch_postlinks where property_post_id = '.$property_post_id.' and linked_post_id	= '.$linked_post_id;
	$r = $wpdb->get_row($q);
	if (empty($r)) {
		$q = 'insert into propsrch_postlinks (property_post_id, linked_post_id, post_type) VALUES ('.$property_post_id.', '.$linked_post_id.', "'.$post_type.'")';
		$wpdb->query($q);
		$wpdb->show_errors();
	}
}
function get_meta_values( $key = '', $type = 'post', $status = 'publish' ) {
	global $wpdb;
	if(empty($key)) {
		return;
	}
	$q = "SELECT pm.meta_value FROM {$wpdb->postmeta} pm
	LEFT JOIN $wpdb->posts p ON p.ID = pm.post_id
	WHERE pm.meta_key = '$key'
			AND p.post_status = '$status'
			AND p.post_type = '$type'";
	#echo $q.'<br>';
	$r = $wpdb->get_results($q);
				
	return $r;
}
function get_numericval($post_id, $field_name) {
	$NV = new NumericVals();
	$val = $NV->get_value($post_id, $field_name);
	return($val->value);
}
function update_numericval($post_id, $field_name, $value) {
	$NV = new NumericVals();
	$NV->smart_update($post_id, $field_name, $value);
}
function update_proptaxval($post_id, $field_name, $value) {
	$PT = new PropTaxonomy();
	$PT->smartUpdateTerm($post_id, $field_name, $value);
}
function get_acf_fields() {
	global $wpdb;
	
	$q = 'select * from '.$wpdb->posts.' where post_type="acf"';
	$r = $wpdb->get_results($q);
	$acf = [];
	
	foreach ($r as $a) {
		$qm = 'select * from '.$wpdb->postmeta.' where post_id = '.$a->ID.' and meta_key LIKE "field_%"';
		$rm = $wpdb->get_results($qm);
		$s = reset($rm);
		if (!empty($s)) {
			$acf[] = unserialize($s->meta_value); 
		}
	}
	return($acf);
}
function get_acf_key($field_name, $post_type = '') {
	global $wpdb;

	$q = "
	SELECT `meta_key`, `meta_value`
	FROM $wpdb->postmeta pm
	LEFT JOIN $wpdb->posts p on p.ID = pm.post_id
	WHERE pm.`meta_value` LIKE 'field_%' AND pm.`meta_key` LIKE '%$field_name%'
	GROUP by meta_key
	";
	#echo $q;
	$r = $wpdb->get_row($q,OBJECT );
	if (!empty($r)) {
		return($r->meta_value);
	} 
	
}
if (!function_exists('ppr')) {
	function ppr ($var,$return = false) {
		if (empty($var)) {
			$var = '[EMPTY]';
		}
		$o = '<pre class="ppr">'.print_r($var,1).'</pre>';
		if (empty($return)) {
			echo $o;
		} else {
			return($o);
		}
	}
}
function get_psterms($cat) {
	global $wpdb;
	if(empty($cat)) {
		return;
	}
	
	$PT = new PropTaxonomy();
	$list = $PT->getTermsByCat($cat);
	
	return $list;
}
if (!function_exists("getLatLngFromGoogle")) {
	function getLatLngFromGoogle ($q) {
		$url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode( $q ) . "&sensor=false&region=gb";
	// 	echo 'TRY: '.$url.'<br>';
		$response = wp_remote_retrieve_body( wp_remote_get( $url ) );
		
		if (is_wp_error($response)) {
			return;
		}
		
		$google_data = json_decode( $response );
		
		if( $google_data->status != "OK" ) {
			return;
		} else {
			return($google_data);
		}
	}
}
function getFuncVals() {
	$fv = get_option('propsrch_func');
	$fv = (array) json_decode($fv);
	if (empty($fv)) {
		$fv = array();
	}
	return($fv);
}
function propsrch_register_funcval ($name) {
	$funcvals = getFuncVals();
	foreach ($name as $f => $n) {
		if (!isset($funcvals[$f])) {
			$funcvals[$f] = $n;
		}
	}
	update_option('propsrch_func', json_encode($funcvals));
}
function get_post_ids_from_meta($meta_key) {
	global $wpdb;
	$wpdb->show_errors();
	$q = "
	SELECT `post_id`, `post_title`, `meta_key`, `meta_value`
	FROM $wpdb->postmeta pm
	LEFT JOIN $wpdb->posts p on p.ID = pm.post_id
	WHERE pm.`meta_key` = '$meta_key' and p.post_status = 'publish'
	
	";
	$r = $wpdb->get_results($q,OBJECT );
	if (!empty($r)) {
		return($r);
	} 
}
if (!function_exists("calculatedistance")) {
	function calculatedistance ($lnglat1, $lnglat2, $unit="K") {
		$lnglat1 = explode(",", $lnglat1);
		$lon1 = $lnglat1[0];
		$lat1 = $lnglat1[1];
		
		$lnglat2 = explode(",", $lnglat2);
		$lon2 = $lnglat2[0];
		$lat2 = $lnglat2[1];
		
		$theta = $lon1 - $lon2;
		$dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
		$dist = acos($dist);
		$dist = rad2deg($dist);
		$miles = $dist * 60 * 1.1515;
		$unit = strtoupper($unit);
	
		if ($unit == "K") {
	    	return ($miles * 1.609344);
		} else if ($unit == "N") {
			return ($miles * 0.8684);
	    } else {
			return $miles;
		}
	}
}