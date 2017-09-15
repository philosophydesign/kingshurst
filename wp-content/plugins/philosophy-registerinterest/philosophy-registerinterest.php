<?php
if(session_id() == '') {
	session_start();
}
/**
Plugin Name: Philosophy's Register Interest Plugin
Plugin URI: http://www.philosophydesign.com
Description: A home-grown all in one solution for register interest 
Author: Simon Little (simon@philosophydesign.com)
Version: 1.0
Author SL
**/

define("REGINT_LOC", plugin_dir_path(__FILE__));
define("REGINT_URI", plugin_dir_url(__FILE__));
define("REGINT_DATEFORMAT", 'd/m/Y @ H:i');

/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Includes
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
require_once REGINT_LOC."inc/common.php";
require_once REGINT_LOC."inc/class_RegisterInterest.php";
require_once REGINT_LOC."inc/class_Browser.php";
require_once REGINT_LOC."inc/PHPMailer-master/PHPMailerAutoload.php";

/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Hooks
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
register_activation_hook( __FILE__, 'REGINT_activate' );
//add_action('admin_init', 'REGINT_checks');
add_action('init', 					'REGINT_init');
add_action('wp_enqueue_scripts', 	'REGINT_enqueue');


add_action('admin_menu', 'REGINT_menu');
add_action('admin_init', 'REGINT_admin_init' );
if (isset($_POST['RI_runearly'])) {
	$func = 'REGINT_runearly_'.$_POST['RI_runearly'];
	add_action('init', $func);
}

add_shortcode( 'register-interest-form', 'REGINT_SHORTCODE_registerinterestform' );
if (isset($_POST['philosri_nonce'])) {
	add_action('init', 'REGINT_handleSubmission');
}
if ((isset($_POST['submission'])) && (!empty($_POST['action']))) {
	if  ($_POST['action'] == 'getsinglesubmission') {
		add_action( 'wp_ajax_getsinglesubmission', 'getsinglesubmission_callback' );
	} else if  ($_POST['action'] == 'getemailreadlog') {
		add_action( 'wp_ajax_getemailreadlog', 'getemaillog_callback' );
	}
}

if (isset($_GET['export'])) {
	add_action( 'init', 'REGINT_export' );
}


// hook add_query_vars function into query_vars
add_filter('query_vars', 'REGINT_add_query_vars');
add_action( 'init', 'REGINT_rewrite_rules' );
add_action( 'wp', 'REGINT_philosriimgtrck');

/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/ 
##### The Hook Functions 
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
function REGINT_init () {
	
	REGINT_check_dependants();
// 	flush_rewrite_rules();
}
function REGINT_check_dependants() {
	if (!function_exists('philosophybasics')) {
		require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		
		deactivate_plugins(plugin_basename( __FILE__ ));
		$url = admin_url( 'plugins.php?deactivate=true' );
		header( "Location: $url" );
		die();
	}
}
function REGINT_checks () {
	$RI = new RegisterInterest();
	$RI->hook_checks();
}

function REGINT_activate () {
	REGINT_check_dependants();
	$RI = new RegisterInterest();
	$RI->hook_activate();
	if ($RI->dependency_check()) {
		$RI->database_check();
	}
	flush_rewrite_rules();
}

function REGINT_menu () {
	$RI = new RegisterInterest();
	$RI->hook_menu();
}
function REGINT_admin_page () {
	$RI = new RegisterInterest();
	$data = $RI->adminpage_common(array());
	
	if (empty($_GET['ri-action'])) {
		reset($data['list_utils']);
		$action = key($data['list_utils']);
	} else {
		$action = $_GET['ri-action'];
	}
	
	include("html/header.php");
	
	$clean = preg_replace('/\W/','',$action);
	$inc = REGINT_LOC.'html/action-'.$clean.'.php';
	$func = 'adminpage_'.$clean;
	if (method_exists($RI, $func)) {
		$data = $RI->$func($data);	
	} else {
		RIDB('No func: '.$func);
	}
	if (file_exists($inc)) {
		include($inc);
	} else {
		RIDB('No inc: '.$inc);
	}
	
	//ppr($data);
}
function REGINT_enqueue () {
	global $wp_scripts;
	
	global $post;
	if (!is_admin()) {
		wp_enqueue_script('regint-js',	REGINT_URI. '/assets/js/register-interest-front.js');
		wp_add_inline_script('regint-js', 'var philosriaj = "'.REGINT_URI.'philosriaj.php";');
		
	}
}
function REGINT_admin_init() {
	/* Register our javascript . */
	wp_register_script( 'register-interest-admin-js',  REGINT_URI.'assets/js/register-interest-admin.js');
	
	wp_enqueue_script( 'register-interest-admin-js' );
	if (
			(isset($_GET['ri-action']))
			&& 
			($_GET['ri-action'] == "editform")
			){
		
		
		$RI = new RegisterInterest();
		
		wp_add_inline_script('register-interest-admin-js', "var types_field = ['".implode("','", $RI->fb->availableFields)."']; ");
		//wp_register_script( 'react-js',  'https://unpkg.com/react@15/dist/react.min.js');
		//wp_register_script( 'react-dom-js',  'https://unpkg.com/react-dom@15/dist/react-dom.min.js');
		/* DEVELOPMENT REACT */
		wp_register_script( 'react-js',  'https://unpkg.com/react@15/dist/react.js');
		wp_register_script( 'react-dom-js',  'https://unpkg.com/react-dom@15/dist/react-dom.js');
		
		wp_register_script( 'react-babel-js',  'https://cdnjs.cloudflare.com/ajax/libs/babel-core/5.8.23/browser.min.js');
		//wp_register_script( 'form-react-js', REGINT_URI.'assets/js/form.react.js' );
		
		if (!empty($_GET['form'])) {
			$RI = new RegisterInterest();
			$form = $RI->get_complete_form($_GET['form']);
			
			$js_fielddata = "var start_fielddata = [];\n";
			foreach ($form->fields as $f) {
				$rowindex = 'l'.rand(1000000000,9999999999);
				
				$options = "";
				$x = 0;
				$f->options = json_decode($f->options);
				if (is_object($f->options)) {
					foreach ($f->options as $k => $o) {
						if ($k !== $x) {
							$options = $options.$k.' : ';
						}
						$options = $options . $o."\\n";
						$x++;
					}
				}
				$js_fielddata  = $js_fielddata.'start_fielddata["'.$rowindex.'"] = {
					field_id	: '.$f->id.',
					index		: "'.$rowindex.'",
					group		: "'.addslashes($f->field_group).'",
					label		: "'.addslashes($f->label).'",
					reference	: "'.addslashes($f->ref).'",
					mandatory	: '.$f->mandatory.',
					type		: "'.addslashes($f->type).'",
					options		: "'.addslashes($options).'",
					value		: "'.addslashes($f->value).'",
					readonly	: '.$f->readonly.',
					sortorder	: '.$f->rowindex.',
					minimizebox	: 1,
					deleted: 0
				};'."\n";
			}
		}
		wp_add_inline_script('register-interest-admin-js', preg_replace('/\n\r\t/','',$js_fielddata));
		
		
		wp_enqueue_script( 'react-js' );
		wp_enqueue_script( 'react-dom-js' );
		wp_enqueue_script( 'react-babel-js' );
 		wp_enqueue_script( 'form-react-js' );
	}
	
	
	
	
	/* Register our stylesheet. */
	wp_register_style( 'register-interest-admin-css',  REGINT_URI.'assets/css/register-interest-admin.css', __FILE__);
	wp_enqueue_style( 'register-interest-admin-css' );
}
function REGINT_runearly_addform() {
	$RI = new RegisterInterest();
	$form_id = $RI->runearly_admin_addform();
	if ($form_id) {
		header("Location: /wp-admin/admin.php?page=register-interest&ri-action=editform&form=".$form_id);
	} else {
		header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
	}
	exit;
}
function REGINT_runearly_editsettings() {
	$RI = new RegisterInterest();
	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	if ($RI->runearly_editsettings()) {
		$actual_link .= '&added=true';
	}
	header("Location: ".$actual_link);
	exit;
}
function REGINT_runearly_editform() {
	$RI = new RegisterInterest();
	$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	if ($RI->runearly_admin_editform()) {
		$actual_link .= '&added=true';
	}
	header("Location: ".$actual_link);
	exit;
}
function REGINT_SHORTCODE_registerinterestform($args) {
	// This is the sho
 	$RI = new RegisterInterest();
	//$GF = new GesForms();
	if (!empty($args['form'])) {
		$action = (!empty($args['action'])) ? $args['action'] : '';
		$html = $RI->make_form_html($args['form'], $args);
		if (!empty($_GET['philosri_submitted'])) {
			/* TODO: Make success message dynamic */
			$html = '<p class="philosri_submitted sucess">You have successfully registered your interest.</p>'.$html;
		}
		return($html);
	}
}
function REGINT_handleSubmission () {
	PHILOSRI_handleSubmit();
}
function getemaillog_callback () {
	$failed = false;
	$response = array();
	if (!empty($_POST['submission'])) {
		$RI = new RegisterInterest();
		$email_id = $RI->get_email_id_from_submission($_POST['submission']);
		if (!empty($email_id)) {
			$emails = $RI->get_emails_readlog($email_id);
			if (empty($emails)) {
				$failed = true;
			} else {
				$response = array();
				foreach ($emails as $e) {
					$response[] = [
							'ip'	=>	$e->ipaddress,
							'date'	=>	$e->datetime
					];
				}
			}
		} else {
			$failed = true;
		}
	}
	if ($failed) {
		wp_die(json_encode(['data'=>'no_log']));
	} else {
		wp_die(json_encode(['data'=>$response]));
	}
	exit;
}
function getsinglesubmission_callback() {
	global $wpdb;
	if (!empty($_POST['submission'])) {
		$browsers = listOfBrowsers();
		$RI = new RegisterInterest();
		$meta = $RI->get_form_submission($_POST['form'], $_POST['submission']);
		if (!empty($meta)) {
			
			$bdetect = new Browser($meta->ua);
			
			$meta->platform = $bdetect->isMobile() ? 'Mobile' : 'Desktop';
			$meta->browser = $bdetect->getBrowser();
			$meta->browser_class = str_replace(' ','-',strtolower($bdetect->getBrowser()));
			$meta->date = date(REGINT_DATEFORMAT, strtotime($meta->date));
			
			$submission_data = array(
				'meta'=>$meta,
				'data'=>$RI->get_submission_data($_POST['form'], $_POST['submission'])
			);
		} else {
			$submission_data = ['meta'=>[], 'data'=>[]];
		}
		wp_die(json_encode($submission_data));
	}
	wp_die();
}


function REGINT_philosriimgtrck () {
	$i = get_query_var('philosritrk');
	if (!empty($i)) {
		//echo $i.'<br>';
		$RI = new RegisterInterest();
		$RI->use_philosritrk($i);
		$c = file_get_contents(REGINT_LOC.'assets/img/pixel.gif');
		//die("<Br>THE END");

		header('Content-Type: image/gif');
		die($c);
	} else {
// 		die("DAsdasd");
	}
}

function REGINT_rewrite_rules () {
	add_rewrite_tag('%philosritrk%','([^&]+)');
	add_rewrite_rule('^philosritrk/(\w+).gif', 'index.php?philosritrk=$matches[1]', 'top');
  	
}
function REGINT_add_query_vars($aVars) {
	$aVars[] = "philosri_submitted"; // represents the name of the product category as shown in the URL
	$aVars[] = "philosritrk";
	return $aVars;
}
function REGINT_export () {
	if ($_GET['export'] == 'csv') {
		$fields = array();
		$RI = new RegisterInterest();		
		$submissions = $RI ->get_form_submissions($_GET['form']);
		foreach ($submissions as $s) {
			$bdetect = new Browser($s->ua);
			$submission_data = $RI->get_submission_data($_GET['form'], $s->id);
			
			$d = array(
				'Date'=>date(REGINT_DATEFORMAT, strtotime($s->date)),
				'Interest'=> ($s->post_id) ? $s->post_title : 'N/A',
				'IP Address'=>$s->ip_address,
				'Browser'=>ucwords($bdetect->getBrowser()),
				'Platform' => ($bdetect->isMobile()) ? 'Mobile' : 'Desktop',
				
			);
			foreach ($submission_data as $sd) {
				$d[$sd->label] = $sd->value;
			}
			$fields[] = $d;
		}
		foreach (reset($fields) as $k => $v) {
			$headers[$k] = $k;
		}
		$fields = array_merge([$headers], $fields);
		ob_start();
		$df = fopen("php://output", 'w');
		foreach ($fields as $f) {
			fputcsv($df, $f);
		}
		fclose($df);
		$output = ob_get_clean();
		
		$now = gmdate("D, d M Y H:i:s");
		header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
		header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
		header("Last-Modified: {$now} GMT");
		
		// force download
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Type: application/download");
		
		// disposition / encoding on response body
		header("Content-Disposition: attachment;filename=export-".date("Y-m-d-H-i").".csv");
		header("Content-Transfer-Encoding: binary");
		die($output);
	}
}