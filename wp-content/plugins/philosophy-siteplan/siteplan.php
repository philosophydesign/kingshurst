<?php 
/**
 Plugin Name: Philosophy's Siteplan
 Plugin URI: http://www.philosophydesign.com
 Description: A plugin for making siteplans using Google Maps API
 
 Author: Simon Little (simon@philosophydesign.com)
 Version: 1.0
 Author SL
 **/
/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Global Variables
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
define("PHILOSSP_LOC", plugin_dir_path(__FILE__));
define("PHILOSSP_URI", plugin_dir_url(__FILE__));
define("PHILOSP_folder", 'philosophy-siteplan-data');
define("PHILOSP_datafilename", 'siteplan.json');

/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Hooks and Filters definitions
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
add_action('wp_enqueue_scripts',				'PHILOSSP_enqueue');
add_action('admin_enqueue_scripts',				'PHILOSSP_enqueueadmin');
add_action('admin_menu', 						'PHILOSP_admin_menu');
add_action('admin_init', 						'PHILOSP_admin_init');
register_activation_hook( __FILE__, 			'PHILOSP_activate' );

add_shortcode('philosophy-siteplan', 			'PHILOSP_SHORTCODE_output' );


function PHILOSSP_enqueueadmin () {
	global $wp_scripts;
		wp_register_script('philoso-siteplan-admin-js',	PHILOSSP_URI. '/assets/js/philoso-siteplan-admin.js', __FILE__);
		wp_enqueue_script('philoso-siteplan-lealet-js','https://unpkg.com/leaflet@1.0.3/dist/leaflet.js');
		wp_enqueue_script('philoso-siteplan-admin-js');
		
		//wp_add_inline_script('philoso-siteplan-front-js', 'googlemapcallbacks.push("philossp_InitMap");');
		
// 		wp_add_inline_script('googlemaps', 'USGSOverlay.prototype = new google.maps.OverlayView();');
		wp_enqueue_style('philoso-siteplan-lealet-css',	'https://unpkg.com/leaflet@1.0.3/dist/leaflet.css');
		
		wp_register_style('philoso-siteplan-admin-css',	PHILOSSP_URI. '/assets/css/philoso-siteplan-admin.css', __FILE__);
		wp_enqueue_style('philoso-siteplan-admin-css');
		
}

function PHILOSSP_enqueue () {
	global $wp_scripts;
	if (!is_admin()) {
		wp_register_script('philoso-siteplan-front-js',	PHILOSSP_URI. '/assets/js/philoso-siteplan-front.js', __FILE__);
		
		wp_enqueue_script('philoso-siteplan-lealet-js','https://unpkg.com/leaflet@1.0.3/dist/leaflet.js');
		wp_enqueue_script('philoso-siteplan-front-js');
		
		//wp_add_inline_script('philoso-siteplan-front-js', 'googlemapcallbacks.push("philossp_InitMap");');
		
// 		wp_add_inline_script('googlemaps', 'USGSOverlay.prototype = new google.maps.OverlayView();');
		wp_enqueue_style('philoso-siteplan-lealet-css',	'https://unpkg.com/leaflet@1.0.3/dist/leaflet.css');
		
		wp_register_style('philoso-siteplan-front-css',	PHILOSSP_URI. '/assets/css/philoso-siteplan-front.css', __FILE__);
		wp_enqueue_style('philoso-siteplan-front-css');
		
	}
}

function PHILOSP_SHORTCODE_output () {
	#echo '<h3>Siteplan Map</h3>';
	echo '<div id="siteplan-map"></div>';
}
function PHILOSP_admin_menu () {
	add_menu_page(
			'Siteplans',
			'Siteplans',
			'edit_posts',
			'siteplans',
			'PHILOSP_admin_page',
			'dashicons-admin-multisite',
			5
			);
	
}
function PHILOSP_admin_page() {
	include("html/header.php");
	if (empty($_GET['spid'])) {
		include("html/list.php");
	} else {
		include("html/edit.php");
	}
}
function PHILOSP_activate () {
	mkdir(wheresmyplanstuff());
}
function wherestheplanstuff ($urlmode = false) {
	$dir = wp_upload_dir();
	if ($urlmode) {
		$dir = $dir['baseurl'];
	} else {
		$dir = $dir['basedir'];
	}
	$dir .= '/'.PHILOSP_folder;
	return($dir);
}
function wheresmyplanstuff ($siteplan="", $urlmode = false) {
	$dir = wherestheplanstuff ($urlmode);
	if ($siteplan) {
		$dir .= "/".$siteplan;
		if ((!file_exists($dir)) && (!$urlmode)) {
			mkdir($dir);
			saveDataFile($siteplan);
		}
	}
	return($dir);
}
function saveDataFile($siteplan, $data=array()) {
	$dir = wheresmyplanstuff($siteplan);
	$fn = $dir."/".PHILOSP_datafilename;
	
	if ((empty($data)) || (!file_exists($fn))) {
		$data['date_created'] = date('Y-m-d H:i:s');
	} else {
		$json = json_decode(file_get_contents($fn));
		if (isset($json->date_created)) {
			$data['date_created'] = $json->date_created;
		}
	}
	$f = fopen($fn, "w");	
	$data['date_saved'] = date('Y-m-d H:i:s');
	
	fwrite($f, json_encode($data));
	fclose($f);
	#print_r($data);
	#exit;
}
function getsiteplanadminformstruct () {
	$struct = [
		['Title', 		'title', 		0],
		['Siteplan',	'siteplan', 	0, 	'file']
	];
	return($struct);
}
function getSiteplanList () {
	$dir = wherestheplanstuff();
	$list = scandir($dir);
	array_shift($list);
	array_shift($list);
	$new = array();
	foreach ($list as $l) {
		$row = [];
		$row['title'] = $l;
		$row['folder'] = $l;
		$saved = getDataForSiteplan($l);
		if (!empty($saved['title'])) {
			$row['title'] = $saved['title'];
		}
		$new[] = $row;
	}
	return($new);
}
function getDataForSiteplan ($siteplan) {
	$dir = wherestheplanstuff();
	$p = $dir.'/'.$siteplan.'/'.PHILOSP_datafilename;
	if (file_exists($p)) {
		$json = file_get_contents($p);
		$saved = json_decode($json, true);
		return($saved);
	} else {
		return([]);
	}
	
}
function get_properties() {
	$properties = get_posts([
			'post_type'=>'properties',
			'post_status'=>'publish',
			'posts_per_page'=> -1,
			'orderby'=>'menu_order',
			'order'		=>'ASC'
	]);
	return($properties);
}
function PHILOSP_admin_init () {
	if (isset($_GET['spid'])) {
		if ($_GET['spid'] == 'new') {
			$id = time();
			header("location: ?page=siteplans&spid=".$id);
			exit;
		} else {
			$siteplan = $_GET['spid'];
			$folder = wheresmyplanstuff ($siteplan);
			$response = [];
			$error = false;
			if (!empty($_GET['spdo'])) {
				if ($_GET['spdo'] == 'save') {
					$properties = get_properties();
					foreach ($properties as $p) {
						if (isset($_POST['shape'][$p->ID])) {
							update_post_meta($p->ID, 'siteplan_coordinates', $_POST['shape'][$p->ID]);
						}
					}
					
					
					if (!empty($_FILES["siteplan"]["tmp_name"])) {
						$check = getimagesize($_FILES["siteplan"]["tmp_name"]);
						if ($check) {
							if (move_uploaded_file($_FILES["siteplan"]["tmp_name"], $folder. '/'.$_FILES["siteplan"]["name"])) {
								$response[] = "The file ". basename( $_FILES["siteplan"]["name"]). " has been uploaded.";
							} else {
								$response[] = "Sorry, there was an error uploading your file.";
								$error = true;
							}
						} else {
							$response[] = "The file ". basename( $_FILES["siteplan"]["name"]). " is not an image.";
							$error = true;
						}
					}
					$ed = getDataForSiteplan ($siteplan);
					$struct = getsiteplanadminformstruct();
					$d = array();
					foreach ($struct as $s) {
						if ($s[1] == 'siteplan') {
							if (!empty($_FILES["siteplan"]["name"])) {
								$d[$s[1]] = $_FILES["siteplan"]["name"];
							} else {
								$d[$s[1]] = $ed['siteplan'];
							}
						} else 
						if (isset($_POST[$s[1]])) {
							$d[$s[1]] = $_POST[$s[1]];
						}
					}
					saveDataFile($siteplan, $d);
				}
				if (!$error) {
					header("location: ?page=siteplans&spid=".$siteplan);
					exit;
				}
			}
		}
	}
}
?>