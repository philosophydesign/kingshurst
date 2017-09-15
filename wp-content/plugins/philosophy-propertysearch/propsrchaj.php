<?php
session_start();
$filename = basename($_SERVER['SCRIPT_FILENAME']);
$t[] = microtime(true);
include( $_SERVER['DOCUMENT_ROOT']. '/wp-load.php' );

		
define("PROPSRCH_LOC", str_replace($filename,'',$_SERVER['SCRIPT_FILENAME']));
define("PROPSRCH_URI", str_replace($filename,'',$_SERVER['REQUEST_URI']));

require_once PROPSRCH_LOC."inc/common.php";
require_once PROPSRCH_LOC."inc/class_PropertySearch.php";
require_once PROPSRCH_LOC."inc/class_PropTaxonomy.php";

global $PROPSRCH_results;
if (isset($_GET['getlatlng'])) {
	
	$lines = preg_split('/\n/',urldecode($_GET['getlatlng']));
	foreach ($lines as $l) {
		$l = trim($l);
		if	 ( 
				(strlen($l) <= 8)
				&& 
				(preg_match('/[a-z]{1,2}[0-9]{1,2}(\s[a-z]{1,2}[0-9]{1,2})?/i', $l))
			) {
				
				$google_data = getLatLngFromGoogle($l);
// 				break;
			}
	}
	
	if (empty($google_data)) {
		$google_data = getLatLngFromGoogle(implode(',',$lines));
	}
	if (!empty($google_data)) {
		$coords = $google_data->results[0]->geometry->location->lat . "," . $google_data->results[0]->geometry->location->lng;
		update_post_meta( $post_id, "propsrch_latlng",  $coords);
		die($coords);	
	}
	exit;
} else {
	$_SESSION['GET_CACHE'] = $_GET;
	$PS = new PropertySearch();
	$PS->makeResults();
	$data = $PS->getResultJSON(true);
	die(json_encode($data));
	
}