<?php
if (empty($_SESSION)) {
	session_start();
}

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
/**
 Plugin Name: Philosophy's Property Search Plugin
 Plugin URI: http://www.philosophydesign.com
 Description: A home-grown all in one solution for property search
 Author: Simon Little (simon@philosophydesign.com)
 Version: 1.0
 Author SL
 **/
/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Global Variables
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
define("PROPSRCH_LOC", plugin_dir_path(__FILE__));
define("PROPSRCH_URI", plugin_dir_url(__FILE__));
define("PROPSRCH_DATEFORMAT", 'd/m/Y @ H:i');
define("RATIO_SQUARE_FEET_METRES", 10.7639104);

$GESFGEN = array();
global $GESF_KEEP_NONCE;
$GESF_KEEP_NONCE = false;
/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Includes
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
require_once PROPSRCH_LOC."inc/common.php";
require_once PROPSRCH_LOC."inc/class_PropertySearch.php";
require_once PROPSRCH_LOC."inc/class_Developments.php";
require_once PROPSRCH_LOC."inc/class_Properties.php";
require_once PROPSRCH_LOC."inc/class_PropertiesTemplates.php";
require_once PROPSRCH_LOC."inc/class_Locations.php";
require_once PROPSRCH_LOC."inc/class_Collections.php";
require_once PROPSRCH_LOC."inc/class_FieldCache.php";
require_once PROPSRCH_LOC."inc/class_NumericVals.php";
require_once PROPSRCH_LOC."inc/class_PropTaxonomy.php";
require_once PROPSRCH_LOC."inc/class_SrchCache.php";
if (!class_exists('GesForms')) {
	require_once PROPSRCH_LOC."inc/gesforms_helper.php";
}


/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Hooks
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
register_activation_hook( __FILE__, 		'PROPSRCH_activate' );
register_deactivation_hook( __FILE__, 		'PROPSRCH_deactivate' );

add_action('init', 							'PROPSRCH_init'); 
if (!empty($_GET['generatecache'])) {
	add_action('init', 						'PROPSRCH_generatecache'); 
}
// add_action('init', 							'PROPSRCH_refreshresults'); 
add_action('wp', 							'PROPSRCH_wp'); 
add_action('admin_menu', 					'PROPSRCH_menu'); 
add_action('admin_init', 					'PROPSRCH_admin_init');
add_action('save_post', 					'PROPSRCH_save_post');
add_action('get_header', 					'PROPSRCH_turnonnoncecache');
add_action('get_header', 					'PROPSRCH_run_search');
add_action('wp_enqueue_scripts',			'PROPSRCH_enqueue');

add_shortcode( 'property-search-form', 		'PROPSRCH_SHORTCODE_searchform' );
add_shortcode( 'property-search-results', 	'PROPSRCH_SHORTCODE_searchresults' );
add_shortcode( 'property-search-extras', 	'PROPSRCH_SHORTCODE_searchextras' );
add_shortcode( 'property-search-detail', 	'PROPSRCH_SHORTCODE_detail' );
add_shortcode( 'property-search-function',	'PROPSRCH_SHORTCODE_function' );
add_shortcode( 'property-search-back', 		'PROPSRCH_SHORTCODE_back' );
add_shortcode( 'property-search-related', 	'PROPSRCH_SHORTCODE_related' );
add_shortcode( 'property-search-singlemap',	'PROPSRCH_SHORTCODE_singlemap' );

add_filter( 'script_loader_tag', 'my_async_scripts', 10, 3 );




/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Hook Functions
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
function PROPSRCH_activate () {
	include(PROPSRCH_LOC."inc/install.php");
}
function PROPSRCH_deactivate () {
	include(PROPSRCH_LOC."inc/uninstall.php");
}
function PROPSRCH_init () {
	
	//ppr($_SESSION['GET_CACHE']);	exit;
	$PS = new PropertySearch();
	$saved_heirachy = $PS->propsrch_heiropt();
	include (PROPSRCH_LOC.'cpt/cpt-properties.php');
	if (
			($saved_heirachy['development'] == 1) || 
			($saved_heirachy['collection'] == 1) 
		) {
		include (PROPSRCH_LOC.'cpt/cpt-property-templates.php');
	}
	
	foreach ($PS->heirachy as $k => $v) {
		$cptfile = PROPSRCH_LOC.'cpt/cpt-'.$v['cpt'].'.php';
		#echo '&quot;'.$cptfile.'&quot;<br>';
		if ((file_exists($cptfile)) && ($saved_heirachy[$k] == 1)) {
			#echo 'Found<br>';
			include ($cptfile);
		} else {
			#echo 'Lost<br>';
		}
	}
	
	#exit;
	
}
function PROPSRCH_wp () {
	$pagesel = get_option('propsrch_searchaction');
	global $post;
 	if ($post->ID == $pagesel) {
 		$_SESSION['GET_CACHE'] = array();
 	}
}
/*
function PROPSRCH_refreshresults () {
	if (isset($_GET['getnewlist'])) {
		global $PROPSRCH_results;
// 		ppr($_GET);
		$PS = new PropertySearch();
		$PS->makeResults();
		$data = $PS->outputResults(true);
		die(json_encode($data));
		exit;
	}
}
*/
function PROPSRCH_menu () {
	add_options_page(
			"Property Search Settings",
			"Property Search",
			"manage_options",
			"property-search",
			"PROPSRCH_options_display"
			);
}
function PROPSRCH_admin_init () {
	// Register the settings
	register_setting("propsrch", "propsrch_heirachy", "");
	register_setting("propsrch", "propsrch_buyingoptions", "");
	register_setting("propsrch", "propsrch_posttypes", "");
	register_setting("propsrch", "propsrch_tenureoption", "");
	register_setting("propsrch", "propsrch_typeslist", "");
	register_setting("propsrch", "propsrch_searchoptions", "");
	register_setting("propsrch", "propsrch_deactivatedfields", "");
	register_setting("propsrch", "propsrch_resulttemplate", "");
	register_setting("propsrch", "propsrch_noresulttemplate", "");
	register_setting("propsrch", "propsrch_searchaction", "");
	register_setting("propsrch", "propsrch_admin_colour", "");
	register_setting("propsrch", "propsrch_func", "");
	
	$PS = new PropertySearch();
	
	// Save settings if they've been submitted
	if (isset($_POST['propsrch_saveoptions'])) {
		
		// Heirachy
		$list = [];
		$ph = (!empty($_POST['heirachy'])) ? $_POST['heirachy'] : [];
		foreach ($PS->heirachy as $k => $v) {
			$list[$k] = (in_array($k, $ph)) ? 1 : 0;
		}
		$json = json_encode($list);
		update_option( 'propsrch_heirachy', $json);
		
		// Buying options
		$list = [];
		foreach ($PS->buyingopts as $k => $v) {
			$list[$k] = (in_array($k, $_POST['buyingopts'])) ? 1 : 0;
		}
		$json = json_encode($list);
		update_option( 'propsrch_buyingoptions', $json);
		
		
		// Area Metric 
		if (!empty($_POST['propsrch_areainputunits'])) {
			update_option( 'propsrch_areainputunits', $_POST['propsrch_areainputunits']);
		} else {
			update_option( 'propsrch_areainputunits', 'metric');
		}
		
		
		// Post Types
		if (!empty($_POST['linkedposttypes'])) {
			$post_types = getFilteredPostTypes();
// 			ppr($post_types);
// 			ppr($_POST);
			$list = [];
			foreach ($post_types as $k => $v) {
				$list[$v] =  array(
					'linked'=>(in_array($v, $_POST['linkedposttypes'])) ? 1 : 0,
					'multiple'=>(isset($_POST['multiple'][$v])) ? 1 : 0,
					'searchable'=>(isset($_POST['searchable'][$v])) ? 1 : 0
				);
			}
		}
		//die('TEST');
		$json = json_encode($list);
		update_option( 'propsrch_posttypes', $json);
		
		// Tenure
		if (!empty($_POST['tenureoption'])) {
			update_option('propsrch_tenureoption', $_POST['tenureoption']);
		}
		// Status
		if (!empty($_POST['statusoption'])) {
			update_option('propsrch_statusoption', $_POST['statusoption']);
		}
		// Search options
		$list = [];
		foreach ($PS->searchoptions as $k => $v) {
			$list[$k] = (in_array($k, $_POST['searchoptions'])) ? 1 : 0;
		}
		$json = json_encode($list);
		update_option( 'propsrch_searchoptions', $json);

		// Search Field Mechanisms
		update_option( 'propsrch_fieldmech_totalarea', $_POST['fm-totalarea']);
		
		
		
		// Deactivate options
		$list = [];
		foreach ($PS->deactivatable as $k => $v) {
			$list[$k] = (in_array($k, $_POST['deactivate'])) ? 1 : 0;
		}
		$json = json_encode($list);
		update_option( 'propsrch_deactivatedfields', $json);
		
		// Result action
		if (!empty($_POST['searchpage'])) {
			update_option( 'propsrch_searchaction', $_POST['searchpage']);
		}
		// Featured Image
		if (!empty($_POST['propsrch_featured_image'])) {
			update_option( 'propsrch_featured_image', $_POST['propsrch_featured_image']);
		}
		// Result template
		if (!empty($_POST['resulttemplate'])) {
			update_option( 'propsrch_resulttemplate', $_POST['resulttemplate']);
		}
		// No Result template
		if (!empty($_POST['noresulttemplate'])) {
			update_option( 'propsrch_noresulttemplate', $_POST['noresulttemplate']);
		}
		// Related template
		if (!empty($_POST['relatedtemplate'])) {
			update_option( 'propsrch_relatedtemplate', $_POST['relatedtemplate']);
		}
		// Admin Colour
		if (!empty($_POST['propsrch_admin_colour'])) {
			update_option( 'propsrch_admin_colour', $_POST['propsrch_admin_colour']);
		} else {
			update_option( 'propsrch_admin_colour', '#0000FF');
		}
		
		$PT = new PropTaxonomy();
		if (isset($_POST['termadd'])) {
			foreach ($_POST['termadd'] as $n => $t) {
				$cat = (isset($_POST['termadd_type'][$n])) ? $_POST['termadd_type'][$n] : ''; 
				$PT->insertterm($cat, $t);
			}
		}
		if (isset($_POST['termupdate'])) {
			foreach ($_POST['termupdate'] as $id => $t) {
				$PT->updateterm($id, $t);
			}
		}
		if (isset($_POST['termremove'])) {
			foreach ($_POST['termremove'] as $t) {
				$PT->deleteterm($t);
			}
		}
		
		
		wp_redirect('?page=property-search&saved');
		exit;
	}
	
	/* Register our stylesheet. */
	wp_register_style( 'property-search-admin-css',  PROPSRCH_URI.'assets/css/property-search-admin.css', __FILE__);
	wp_enqueue_style( 'property-search-admin-css' );
	/* Register our stylesheet. */
	wp_register_script( 'property-search-admin-js',  PROPSRCH_URI.'assets/js/property-search-admin.js', __FILE__);
	wp_add_inline_script('property-search-admin-js', 'var propsrch_uri = "'.PROPSRCH_URI.'";');
	wp_enqueue_script( 'property-search-admin-js' );
	
}


function PROPSRCH_save_post ($post_id) {
	if (!empty($_POST)) {
		$post = get_post($post_id);
		if (!empty($post)) {
			$PS = new PropertySearch();
			$FC = new FieldCache();
			$NV = new NumericVals();
			$PT = new PropTaxonomy();
			$SC = new SrchCache();
			
			$fields = array();
			if ($post->post_type == 'properties') {
				$O = new Properties();
			} else if ($post->post_type == 'developments') {
				$O = new Developments();
			} else if ($post->post_type == 'propertytemplates') {
				$O = new PropertiesTemplates();
			} else if ($post->post_type == 'collections') {
				$O = new Collections();
			} else {
				return;
				//die("test");			
			}
			$fields = $O->getFields();
			if (method_exists($O, 'getCacheList')) {
				$cachelist = $O->getCacheList();
			} else {
				$cachelist = array();
			}
			if (method_exists($O, 'getNumericVals')) {
				$numericvals = $O->getNumericVals();
			} else {
				$numericvals = array();
			}
			if (method_exists($O, 'getTaxonomyFields')) {
				$taxfields = $O->getTaxonomyFields();
			} else {
				$taxfields = array();
			}
			$saved_posttypes = $PS->propsrch_posttypes();
			$saved_areainputunits= get_option('propsrch_areainputunits');
			$skipfields = [];
			foreach ($saved_posttypes as $k => $v) {
				if ($v->linked) {
					$fk = 'propsrch_linked_'.$k;
					if (!empty($_POST[$fk])) {
						if ($v->multiple) {
							foreach ($_POST[$fk] as $l_post_id) {
								addPostLink($post_id, $l_post_id, $k);
							}
						} else {
							addPostLink($post_id, $_POST[$fk], $k);
						}
						$skipfields[] = 'linked_'.$k;
					} 
				}
			}
			foreach ($fields as $f) {
				if (in_array($f[1], $skipfields)) {
					continue;
				}
				$pmk = 'propsrch_'.$f[1];
 				$pk = $PS->admin_prefix.$f[1];
 				if (
 						(in_array($f[1], $cachelist)) 
 					&& (
 						(isset($_POST[$pmk.'_choose'])) && 
 						(($_POST[$pmk.'_choose'] != '+ add new') && ($_POST[$pmk.'_choose'] != ''))
 						)
 					) {
					$val = (isset($_POST[$pk.'_choose'])) ? $_POST[$pk.'_choose'] : '';
 				} else {
					$val = (isset($_POST[$pk])) ? $_POST[$pk] : '';
 				}
//  				echo $pmk.' '.$val.'<Br>';
				$old_val = get_post_meta($post_id, $pmk);
				if (in_array($f[1],$numericvals)) {
					if (($saved_areainputunits == 'imperial') && ($f[1] == 'total_area')) {
						#echo $val.' |&gt;| '.($val / RATIO_SQUARE_FEET_METRES).'<Br>'; exit;
						update_numericval($post_id, $f[1], round($val / RATIO_SQUARE_FEET_METRES, 4));
					} else {
						#ppr($f);
						#echo $val.' NA<Br>'; 
						update_numericval($post_id, $f[1], $val);
					}
				} else if (in_array($f[1],$taxfields)) {
 					#echo $post_id.' '.$f[1].' '.$val.'<br>';
					update_proptaxval($post_id, $f[1], $val);
				} else {
 					#echo $post_id.' '.$pmk.' '.$val.'<br>';
					update_post_meta($post_id, $pmk, $val);
				}

			}
			$FC->syncFieldCache($fields, $cachelist, $post->post_type);
			
			$SC->buildCacheValues($post);
			
			
			$NV->clean();
		}
	}
}
function PROPSRCH_turnonnoncecache () {
	global $GESF_KEEP_NONCE;
	$GESF_KEEP_NONCE = true;
}
$hasrun = false;
function PROPSRCH_run_search ($wpquery) {
	global $post;
	global $hasrun;
	$pagesel = get_option('propsrch_searchaction');
	
	
// 	echo '<h1>A</h1>';
	if (
			($post->ID == $pagesel) && 
			(is_main_query()) &&
			(!$hasrun)
		){
// 		echo '<h1>B</h1>';
		$_SESSION['GET_CACHE'] = $_GET;
		$PS = new PropertySearch();
		global $searchresults; 
		$hasrun = true;
		$searchresults = $PS->makeResults();		
		
	}
}
function PROPSRCH_enqueue () {
	global $wp_scripts;
	
	wp_enqueue_script('jquery', 	PROPSRCH_URI. '/lib/jquery-1.7.min.js');
	wp_enqueue_script('jquery-ui',	PROPSRCH_URI. '/lib/jquery-ui-1.12.1.custom/jquery-ui.js');
	wp_enqueue_style('jquery-ui-css',	PROPSRCH_URI. '/lib/jquery-ui-1.12.1.custom/jquery-ui.css');
	$pagesel = get_option('propsrch_searchaction');
	global $post;
	if ((!is_admin()) && ((!empty($_GET['dopropsrch'])) || ($post->ID == $pagesel))) {
		
		wp_enqueue_script('bootstrap-js',	PROPSRCH_URI. '/lib/bootstrap-3.3.7-dist/js/bootstrap.js');
		wp_enqueue_script('bootstrap-css',	PROPSRCH_URI. '/lib/bootstrap-3.3.7-dist/css/bootstrap.css');
		
// 		wp_enqueue_script('chosen-js',	PROPSRCH_URI. '/lib/chosen_v1.6.2/chosen.jquery.js');
// 		wp_enqueue_script('prism-js',	PROPSRCH_URI. '/lib/chosen_v1.6.2/docsupport/prism.js');
// 		wp_enqueue_style('chosen-css',	PROPSRCH_URI. '/lib/chosen_v1.6.2/chosen.css');
 		wp_enqueue_script('select-js',	PROPSRCH_URI. '/lib/select2-4.0.3/js/select2.full.min.js');
 		wp_enqueue_style('select-css',	PROPSRCH_URI. '/lib/select2-4.0.3/css/select2.min.css');
 		
//  		wp_enqueue_script( 'nouislider', PROPSRCH_URI. '/assets/js/noUiSlider.9.2.0/nouislider.min.js');
 		
		wp_enqueue_style('propsrch-front-css',	PROPSRCH_URI. '/assets/css/property-search-front.css');
		
		wp_enqueue_script('touch-punch-js',	PROPSRCH_URI. '/assets/js/jquery.ui.touch-punch.min.js');
		wp_enqueue_script('propsrch-front-js',	PROPSRCH_URI. '/assets/js/property-search-front.js');
		$template = get_option('propsrch_resulttemplate');
		wp_add_inline_script('propsrch-front-js', 'var propsrch_resulttemplate = "'.preg_replace('/[\t\r\n]/','',$template).'";');
		$rangetype = get_option('propsrch_fieldmech_totalarea');
		if ($rangetype == 'prange') {
			$ranges = json_decode(get_option('propsrch_total_area_ranges'));
			$mults['sqm'] = 1; 
			$mults['sqf'] = RATIO_SQUARE_FEET_METRES;
			$mults['acr'] = 0.00024711;
			$mults['hec'] = 0.0001;
			$conversiontable = array();
			foreach ($mults as $k => $m) {
				foreach ($ranges as $r) {
					$conversiontable[$k][] = array(($r[0] * $m),($r[1] * $m));
				}
			}
				
			
			wp_add_inline_script('propsrch-front-js', 'var arearangeconversions = JSON.parse("'.addslashes(json_encode($conversiontable)).'");');
		}
		
		
		
		
		global $GESF_KEEP_NONCE; 
		$template = addslashes(do_shortcode(stripslashes(get_option('propsrch_noresulttemplate'))));
		
		
		wp_add_inline_script('propsrch-front-js', 'var propsrch_noresulttemplate = "'.preg_replace('/[\t\r\n]/','',$template).'";');
		
		if ((!empty($_GET['psv'])) && ($_GET['psv'] == 'map')) {
			$PS = new PropertySearch();
			wp_add_inline_script('propsrch-front-js', 'var mapmode = true;');
			wp_add_inline_script('propsrch-front-js', 'var mapmarkers = [];');
// 			wp_add_inline_script('propsrch-front-js', 'getNewList(true);');
			 //');//."\n".$PS->makeResultsMapData());
 			wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?callback=propsrch_InitMap', null, null, true );
		} else {
			wp_add_inline_script('propsrch-front-js', 'var mapmode = false;');
		}
		wp_add_inline_script('propsrch-front-js', 'var propsrchaj = "'.PROPSRCH_URI.'propsrchaj.php";');
		$sizeunits = (isset($_GET['un'])) ? $_GET['un'] : 'sqm';
		wp_add_inline_script('propsrch-front-js', 'var sizeunits = "'.$sizeunits.'"');
		
		
		$NV = new NumericVals();
		$maxvals = $NV->getAllMax();
		
		$js = '';
		foreach ($maxvals as $mv) {
			if ($mv->field_name == 'total_area') {
				$js .= 'var max_'.$mv->field_name.'_metres = '.$mv->max_value.';'."\n";
			}
			$js .= 'var max_'.$mv->field_name.' = '.$mv->max_value.';'."\n";
		}
		wp_add_inline_script('propsrch-front-js', $js);
		
		
	}
	if ((is_single()) && ($post->post_type == 'properties')) {
		wp_enqueue_script('property-search-single-front-js',	PROPSRCH_URI. '/assets/js/property-search-single-front.js');
		wp_enqueue_script( 'googlemaps', 'https://maps.googleapis.com/maps/api/js?callback=propsrch_InitMap_single', null, null, true );
		$latlng = reset(get_post_meta($post->ID, 'propsrch_latlng'));
		if (empty($latlng)) {
			$latlng = "0, 0";
		}
		wp_add_inline_script('property-search-single-front-js', 'var single_property = new Array('.$latlng.', "'.$post->post_title.'", "'.get_permalink($post->ID).'",  "'.urlencode($r->address).'");');
		
				
	}
	
}
function my_async_scripts( $tag, $handle, $src ) {
    // the handles of the enqueued scripts we want to async
    $async_scripts = array( 'google-maps');

    if ( in_array( $handle, $async_scripts ) ) {
        return '<script type="text/javascript" src="' . $src . '" async="async"></script>' . "\n";
    }

    return $tag;
}
/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Callback Functions
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
function PROPSRCH_options_display () {
	include(PROPSRCH_LOC."html/options.php");
}
function PROPSRCH_SHORTCODE_searchform ($args) {
	if (isset($args['exclude'])) {
		$exclude = explode(',', $args['exclude']);
	}
	$PS = new PropertySearch();
	$form_html = $PS->outputSearchForm($exclude);
	global $GESFGEN;
	$GESFGEN = $PS->gesf_search_fields;
	include(PROPSRCH_LOC."html/sc-searchform.php");
}
function PROPSRCH_SHORTCODE_searchresults ($args) {
	$PS = new PropertySearch();
	if ((isset($_GET['psv']))  && ($_GET['psv'] == 'map')) {
		echo '<div id="propsrch_map"></div>';
	} else {
		echo '<div id="propsrch_results">';
		echo $PS->makeResultsHTML();
		echo '</div>';
	}
}
function PROPSRCH_SHORTCODE_searchextras ($args) {
	include(PROPSRCH_LOC."html/form-extras.php");
}
function PROPSRCH_SHORTCODE_detail ($args) {
	if (!empty($args['variable'])) {
		$PS = new PropertySearch();
		if (isset($PS->result_variables[$args['variable']])) {
			global $property_post_id;
			
			$v = $PS->result_variables[$args['variable']];
			$new = $PS->generate_var($args['variable'], $v, $property_post_id);
			return($new);
		}
	}
}
function PROPSRCH_SHORTCODE_function ($args) {
	if (!empty($args['func'])) {
		$func = 'PROPSRCH_resfunc_'.$args['func'];
		if (function_exists($func)) {
			global $post;
			return($func($post));
		} else {
			return('no_func');
		}
	}
}
function PROPSRCH_SHORTCODE_back ($args) {
	$pagesel = $pagesel = get_permalink(get_option('propsrch_searchaction'));
	if (isset($_SESSION['GET_CACHE'])) {
		$text = (isset($args['text'])) ? $args['text'] : 'Back to results';
		$class = (isset($args['class'])) ? $args['class'] : '';
		$g = [];
		foreach ($_SESSION['GET_CACHE'] as $k => $v) {
			if (is_array($v)) {
				foreach ($v as $sv) {
					$g[] = $k.'[]='.$sv;
				}
			} else {
				$g[] = $k.'='.$v;
			}
		}
		
		$g = '?'.implode('&', $g);
		// onclick="window.history.back();"
	} else {
		$g = '';	
	}
	$html = '<a href="'.$pagesel.$g.'" class="'.$class.'">'.$text.'</a>';
	echo $html;
}

function PROPSRCH_SHORTCODE_related ($args) {
	// size and region
	global $post;
	$latlng = reset(get_post_meta($post->ID,'propsrch_latlng'));
	$NV = new NumericVals();
	$size = $NV->get_value($post->ID, 'total_area');
	
	$p = get_post_ids_from_meta('propsrch_latlng');
	$bydistance = [];
	/*
	echo '<table><tr>
				<th>Distance</th>
				<th>Size Dif</th>
				</tr>';
				*/
	
	$ur_dis = 150; 
	$ur_siz = $size / 2;  
	
	foreach ($p as $prop) {
		if ($prop->post_id == $post->ID) {
			continue;
		}
		$ll = reset(get_post_meta($prop->post_id,'propsrch_latlng'));
		$sz = $NV->get_value($prop->post_id, 'total_area');
		
		$szdif = abs($size->value - $sz->value);
		$size_score = $szdif / $ur_siz; 
		
		$distance = calculatedistance($latlng,$ll);
		$dist_score = $distance / $ur_dis;
		
		$prop->distance = $distance;
		$bydistance[($dist_score+$size_score)] = $prop;
		
		
		/*
		echo '<tr>
				<td style="padding: 10px 5px;">'.$distance.'</td>
				<td style="padding: 10px 5px;">'.$szdif.'</td>
				</tr>';
				*/
	}
	/*echo '</table>';*/
	ksort($bydistance);
	$max = (isset($args['count'])) ? $args['count'] : 3;
	$x = 1;
	
	
	$template = get_option('propsrch_relatedtemplate');
	$results = '';
	$x = 1;
	$return = '';
	$funcVals = getFuncVals();
	
	$PS = new PropertySearch();
	$saved_heirachy = $PS->propsrch_heiropt();
	$get = [];
	foreach ($PS->result_variables as $k => $v) {
		$f = '*|'.$k.'|*';;
		if (strstr($template, $f)) {
			$get[$k] = $v; 
		} 
	}
	foreach ($bydistance as $p) {
		if ($x > $max) {
			break;
		}
		$resulthtml = stripslashes($template);
		
		
		preg_match_all('/(\*{[a-zA-Z]+}\*)/', $resulthtml, $funcmatch);
		
		$foundfields[$x]['funcvals'] = array();
		if (!empty($funcmatch[1])) {
			foreach($funcmatch[1] as $f) {
				$fnc = preg_replace('/[\*{}]/','',$f);
				$fn = 'PROPSRCH_resfunc_'.$fnc;
				if (function_exists($fn)) {
					$pass = new stdClass();
					$pass->ID = $p->post_id;
					$html = $fn($pass);
				} else {
					$html = $fn;
				}
// 				echo $f.'<Br>'.$html.'<br>';
				$resulthtml = str_replace($f, $html, $resulthtml);
				//$foundfields[$x]['funcvals'][] = [$fnc,$html];
		
			}
		}
		
		
		foreach ($get as $k => $v) {
			$f = '*|'.$k.'|*';
			
			if (!empty($v['gen'])) {
				$new = $PS->generate_var($k, $v, $p->post_id, $saved_heirachy);
			} else if (isset($p->$k)) {
				$new = $p->$k;
			} else {
				$new = reset(get_post_meta($p->post_id, $v['var']));
			}
				
			if ($v['var'] == 'total_area') {
				$mult = $this->whatMult();
				$new = number_format(ceil($new * $mult));
				if (($_GET['un'] == 'sqm') || (empty($_GET['un']))) 		{$new .= 'M&sup2;';}
				else if ($_GET['un'] == 'sqf') 								{$new .= 'FT&sup2;';}
				else if ($_GET['un'] == 'acr') 								{$new .= 'AC';}
				else if ($_GET['un'] == 'hec') 								{$new .= 'ha';}
			}
			$resulthtml = str_replace($f,$new,$resulthtml);
		}
		
		echo $resulthtml;
		$x++;
	}
// 	ppr(get_post_meta($post->ID));
	
}
function getFilteredPostTypes () {
	$PS = new PropertySearch();
	$saved_heirachy = $PS->propsrch_heiropt();
	$post_types = get_post_types();
	
	unset($post_types['properties']);
	unset($post_types['propertytemplates']);
	
	foreach ($PS->heirachy as $k => $v) {
		unset($post_types[$v['cpt']]);
	}
	return($post_types);
}
/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### Random Extras
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */

function get_tax_data ($where) {

	global $wpdb;

	$q = "SELECT * FROM `gewp_terms` WHERE ".$where;
	$terms = $wpdb->get_results($q);
	/*
	 * 13 Freehold
	 * 14 Leasehold
	 * 32 Long-Leasehold
	 */
	$term_ids = [];
	foreach ($terms as $t) {
		$term_ids[] = $t->term_id;
	}


	$q = "SELECT t1.*, t2.* FROM `gewp_term_taxonomy` t1 LEFT JOIN `gewp_terms` t2 ON t1.term_id = t2.term_id WHERE t1.`term_id` IN (".implode(',', $term_ids).")";
	// 	echo $q.'<br>';
	$termsdet = $wpdb->get_results($q);
	$term_tax_ids = [];
	$data = [];
	foreach ($termsdet as $t) {
		$data['tax_ids'][] = $t->term_taxonomy_id;
		$data['tax_names'][$t->term_taxonomy_id] = $t->slug;
	}
	return($data);
}
function PROPSRCH_SHORTCODE_singlemap () {
	return('<div id="propsrch_mapsingle"></div>');
};
function regenerate_caches () {
	ini_set('max_execution_time', 120);
	$P = new Properties();
	$SC = new SrchCache();
	$search_result =  $P->getProperties();
	if (!empty($search_result)) {
		if (is_array($search_result)) {
			$x = 1;
			foreach ($search_result as $r) {
				// 					ppr($r);
				$SC->buildCacheValues($r);
				$x++;
			}
		}
	}
	
	$NV = new NumericVals();
	update_option('propsrch_total_area_ranges', json_encode($NV->generateRanges('total_area')));

	
	
	
	#echo $endtime - $starttime.' seconds<Br>';
	
	// Attempt to cache all permutations
	/*
	$NV = new NumericVals();
	$PS = new PropertySearch();
	$struct = $PS->get_searchform_struct();
	// 139 different options
	$c = 0;
	$options = [];
	foreach ($struct as $ak => $as) {
		if (isset($as[4])) {
			$opt = [];
			foreach ($as[4] as $bo) {
				$opt[$as[1]] = $bo;
				foreach ($struct as $bk => $bs) {
					if ((isset($bs[4])) && ($ak != $bk)) {
						foreach ($bs[4] as $bok => $bov) {
							$opt[$bk][]`` = $bov;
						}
					}
					ppr($opt);
				}
				exit;
				ksort($opt);
				$options[] = $opt;
			}
		}
	}
	ppr($options);
	exit;*/
	
}
function PROPSRCH_generatecache () {
	regenerate_caches ();	
	die('END');
}