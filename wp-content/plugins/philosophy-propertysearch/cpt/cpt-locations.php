<?php

$post_type = "locations";
$l = array(
	"Location",
	"Locations",
);
$args = array(
	"label" => "Locations",
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
	"hierarchical" => true,
);

register_post_type($post_type, $args);
        
if (function_exists('pti_set_post_type_icon')) {
	pti_set_post_type_icon( $post_type, 'globe' );
}



global $wp_rewrite;
add_rewrite_rule(
		'^developments/(.+)/properties/(.+)/?$',
		'index.php?&post_type=properties&name=$matches[2]',
		'top'
		);
add_rewrite_rule(
		'^developments/(.+)/(.+)/?$',
		'index.php?&post_type=developments&name=$matches[1]&tab=$matches[2]',
		'top'
		);
add_rewrite_tag('%tab%','([^&]+)');


add_action("admin_menu", "locations_fields");



function locations_fields() {
	
	if ( function_exists( 'add_meta_box' ) ) {
		add_meta_box(
				'locations_fields',
				'Location Data',
				'locations_displayfields',
				array('locations'),
				'normal', 
				'high' 
		);
	}
}
function locations_displayfields () {
	propsrch_metabox("fields_locations");
}
