 	<?php
if (empty($_SESSION)) {
	session_start();
}

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
/**
 Plugin Name: Philosophy's Points Of Interest
 Plugin URI: http://www.philosophydesign.com
 Description: A home-grown all solution for showing points of interest that relate to a property or post
 Author: Simon Little (simon@philosophydesign.com)
 Version: 1.0
 Author SL
 **/
/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Global Variables
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
define("PHILOSPOI_LOC", plugin_dir_path(__FILE__));
define("PHILOSPOI_URI", plugin_dir_url(__FILE__));

$GESFGEN = array();

/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Includes
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
require_once PHILOSPOI_LOC."inc/common.php";
require_once PHILOSPOI_LOC."inc/class_PointsOfInterest.php";


/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Hooks and Filters definitions 
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */

register_activation_hook( __FILE__, 		'PHILOSPOI_activate' );
register_deactivation_hook( __FILE__, 		'PHILOSPOI_deactivate' );

/* General --------------------------------------------------------------------------------------------------------- */

add_action('init', 							'PHILOSPOI_init');
// add_action('get_header', 					'PHILOSPOI_getbydistance');
add_action('wp_enqueue_scripts',			'PHILOSPOI_enqueue');

/* Admin ----------------------------------------------------------------------------------------------------------- */

add_action('admin_menu', 					'PHILOSPOI_admin_menu');
add_action("admin_menu", 					"PHILOSPOI_admin_init");
add_action('save_post', 					'PHILOSPOI_save_post');

/* Shortcodes ------------------------------------------------------------------------------------------------------ */

add_shortcode( 'philospoi-show-map', 		'PHILOSPOI_SHORTCODE_map' );




/*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*~~~*/
##### The Hook Functions
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
function PHILOSPOI_activate () {
	PHILOSPOI_check_dependants();
	include(PHILOSPOI_LOC."inc/install.php");
}
function PHILOSPOI_deactivate () {
	include(PHILOSPOI_LOC."inc/uninstall.php");
}
function PHILOSPOI_check_dependants() {
	if (!function_exists('philosophybasics')) {
		deactivate_plugins(plugin_basename( __FILE__ ));
		$url = admin_url( 'plugins.php?deactivate=true' );
		header( "Location: $url" );
		die();
	}
}
function PHILOSPOI_init () {
	$post_type = "points_of_interest";
	PHILOSPOI_check_dependants();
	
	
	if (!post_type_exists($post_type)) {
		$l = array(
				"Point of Interest",
				"Points of Interest",
		);
		$args = array(
				"label" => "Collections",
				"labels"=>array(
						'name'=>$l[1],
						'add_new'=>'Add '.$l[0],
						'edit_item'=>'Edit '.$l[0],
						'singular_name'=>$l[0],
						'add_new_item'=>$l[0],
				),
				'public' => true,
				'show_in_nav_menus' => false,
				'exclude_from_search' => true,
				"supports" => array(
						"title",
						"editor",
						"page-attributes",
						//"thumbnail",
						"revisions",
						//"comments"
				),
				"hierarchical" => false,
		);
		
		register_post_type($post_type, $args);
	}
	register_taxonomy(
			'poi-type',
			$post_type,
			array(
					'label' => __( 'Types Of Places' ),
					'rewrite' => array( 'slug' => 'poi-type' ),
			)
	);
}

function PHILOSPOI_options_display () {
	
}
function PHILOSPOI_admin_init () {
	if ( function_exists( 'add_meta_box' ) ) {
		add_meta_box(
				'points-of-interest-fields',
				'Point Of Interest Data',
				'PHILOSPOI_displayfields',
				array('points_of_interest'),
				'normal',
				'high'
				);
	}
	/* Register our stylesheet. */
	wp_register_style( 'philospoi-admin-css',  PHILOSPOI_URI.'/assets/css/philospoi-admin.css', __FILE__);
	wp_enqueue_style( 'philospoi-admin-css' );
	/* Register our stylesheet. */
	wp_register_script( 'philospoi-admin-js',  PHILOSPOI_URI.'/assets/js/philospoi-admin.js', __FILE__);
	wp_add_inline_script('philospoi-admin-js', 'var philospoi_uri = "'.PHILOSPOI_URI.'";');
	wp_enqueue_script( 'philospoi-admin-js' );
	
}

function PHILOSPOI_admin_menu () {
	
}

function PHILOSPOI_enqueue () {
	global $wp_scripts;
	if (!is_admin()) {
		
		
		wp_register_script('philospoi-front-js',	PHILOSPOI_URI. '/assets/js/philospoi-front.js', __FILE__);
		wp_enqueue_script('philospoi-front-js');
		wp_add_inline_script('philospoi-front-js', 'googlemapcallbacks.push("philospoi_InitMap");');
		wp_add_inline_script('philospoi-front-js', 'var philospoi_uri = "'.PHILOSPOI_URI.'";');
		wp_add_inline_script('philospoi-front-js', 'var philospoiaj = "'.PHILOSPOI_URI.'philospoiaj.php";');
		
		
		wp_register_style('philospoi-front-css',	PHILOSPOI_URI. '/assets/css/philospoi-front.css', __FILE__);
		wp_enqueue_style('philospoi-front-css');
		
	}
}
function PHILOSRI_my_async_scripts( $tag, $handle, $src ) {
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

function PHILOSPOI_displayfields () {
	$POI = new PointsOfInterest();
	$GF = new GesForms();
	$stucture = $GF->makeStructObj($POI->getFields());
	global $post;
	foreach ($stucture as $s) {
		$pm = get_post_meta($post->ID, 'philospoi_'.$s->ref);
		if (!empty($pm[0])) {
			$GF->values[$s->ref] = $pm[0];
		}
	}
	echo $GF->outputFields($stucture);
}

function PHILOSPOI_save_post($post_id) {
	if (!empty($_POST)) {
		$post = get_post($post_id);
		if (!empty($post)) {
			$POI = new PointsOfInterest();
			foreach ($POI->getFields() as $f) {
				if ((isset($f[1])) && (isset($_POST[$f[1]]))) {
					update_post_meta($post_id, 'philospoi_'.$f[1], $_POST[$f[1]]);
				}
			}
		}
	}
}
function PHILOSPOI_SHORTCODE_map ($args) {

}



