<?php
/**
 Plugin Name: Philosophy's Basics
 Plugin URI: http://www.philosophydesign.com
 Description: Common functions required for the Philosophy Suite
 Author: Simon Little (simon@philosophydesign.com)
 Version: 1.0
 Author SL
 **/
define("PHILOSBAS_LOC", plugin_dir_path(__FILE__));
define("PHILOSBAS_URI", plugin_dir_url(__FILE__));
add_filter( 'script_loader_tag', 'PHILOS_my_async_scripts', 10, 3 );
add_action('wp_enqueue_scripts',			'PHILOSBAS_enqueue');
add_action('admin_init', 					'PHILOSBAS_admin_enqueue' );

function philosophybasics() {
	// I just exist to to provide a test
}

function PHILOSBAS_enqueue () {
	global $wp_scripts;
	if (!is_admin()) {
		wp_deregister_script('jquery'); //we're loading our own jquery
		wp_register_script('jquery', PHILOSBAS_URI.'/assets/js/jquery-1.10.1.min.js');
		wp_enqueue_script('jquery');
		
		wp_register_script(	'philosbasics-front-js',	PHILOSBAS_URI. '/assets/js/philosbasics-front.js', __FILE__);
		wp_enqueue_script(	'philosbasics-front-js');
		wp_add_inline_script('philosbasics-front-js', 'var googlemapcallbacks = new Array();');
		wp_enqueue_script(	'googlemaps', 				'https://maps.googleapis.com/maps/api/js?key=AIzaSyAd5jZ4juungDInWpYkBqJnvZd8g1UAC4k&callback=googlemaploaded', null, null, true );
	}
}
function PHILOSBAS_admin_enqueue () {
	/* Register our stylesheet. */
 
	
	wp_register_script( 'philosbasics-admin-js',  PHILOSBAS_URI.'/assets/js/philosbasics-admin.js', array( 'jquery' ), '1.0.0');
	wp_enqueue_script( 'philosbasics-admin-js' );

}

function PHILOS_my_async_scripts( $tag, $handle, $src ) {
	// the handles of the enqueued scripts we want to async
	$async_scripts = array( 'google-maps');

	if ( in_array( $handle, $async_scripts ) ) {
		return '<script type="text/javascript" src="' . $src . '" async="async"></script>' . "\n";
	}

	return $tag;
}
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
function ajaxrespond ($success = false, $response = array()) {
	Header('Content-Type: application/json; charset=UTF8');
	header("content-type: text/plain;");
	$response = array(
			'success'=>$success,
			'response'=>$response
	);
	die(json_encode($response));
	exit;
}
require_once("gesforms_helper.php");