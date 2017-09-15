<?php
session_start();
$filename = basename($_SERVER['SCRIPT_FILENAME']);
$t[] = microtime(true);
include( $_SERVER['DOCUMENT_ROOT']. '/wp-load.php' );

		
define("PHILOSPOI_LOC", str_replace($filename,'',$_SERVER['SCRIPT_FILENAME']));
define("PHILOSPOI_URI", str_replace($filename,'',$_SERVER['REQUEST_URI']));

require_once PHILOSPOI_LOC."inc/common.php";
require_once PHILOSPOI_LOC."inc/class_PointsOfInterest.php";

/*
 * TODO: Dynamic image size
 * TODO: Dynamic mapping of coordinates
 */


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
		//update_post_meta( $post_id, "propsrch_latlng",  $coords);
		die(preg_replace('/\s/','', $coords));	
	}
	exit;
} else if (isset($_GET['poifor'])) {
	$poifor = explode('/',preg_replace('/[^\w\/\-]/','',$_GET['poifor']));
	if ((isset($poifor[0])) && (isset($poifor[1]))) {
		$typesel = explode(',',trim($_GET['poit'],','));
		
		$a = [
			'post_type'=>$poifor[0],
			'name'=>$poifor[1],
			'post_status'=>'publish',
			'limit'=>1
		];
		//$maxdistance = 100;
		$maxdistance = 10;
		$p = get_posts($a);
		if (!empty($p[0])) {
			$post = $p[0];
			$coords = get_post_meta($post->ID, 'propsrch_latlng');
			if (!empty($coords[0])) {
				$coords = $coords[0];
				$POI = new PointsOfInterest();
				$allpoi = $POI->getPOIList($typesel);
				$poibydistance = array();
				foreach  ($allpoi as $i) {
					$pc = get_post_meta($i->ID, 'philospoi_coordinates');
					$text = get_post_meta($i->ID, 'philospoi_pintext');
					$img = wp_get_attachment_image_src(get_post_thumbnail_id($i->ID), 'thumbnail');
					if (empty ($img)) {
						$img = ['',0,0];
					}
					if (!empty($pc[0])) {
						$pc = $pc[0];
						$distance = calculatedistance($coords, $pc);
						if ($maxdistance > $distance) {
							
							$text = $text[0];
							$key = $distance;
							$terms = wp_get_post_terms($i->ID, 'poi-type');
							$poitypes = array();
							foreach ($terms as $t) {
								$poitypes[] = $t->slug;
							}
							preg_match_all('/(www\.[a-z\-_]+\.[a-z\.]+)/',$text,$m);
							if (!empty($m[0])) {
								foreach ($m[0] as $w) {
									$text = str_replace($w, '<a target="blank" href="http://'.$w.'">'.$w.'</a>', $text);
								}
							}
							
							$text = nl2br($text);
							
							
							if (!strstr('.',$key)) {
								$key .= '.';
							}
							$key .= '0000'.rand(10000,99999);
							
							$poibydistance[$key] = [
									$i->post_title, 
									$distance, 
									explode(',',$pc), 
									$text, 
									$img,
									$poitypes
							];
						}
					}
				}
				
				ksort($poibydistance);
				$x = 0;
				$new = array();
				foreach ($poibydistance as $k => $v) {
					unset($poibydistance);
					$new[$x] = $v;
					$x++;
				}
				die(json_encode(['mapcenter'=>explode(',',$coords), 'centertitle'=>$post->post_title, 'nhmm'=>$new]));
			}
		}
	}

}