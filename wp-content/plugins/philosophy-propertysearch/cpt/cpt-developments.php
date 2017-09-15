<?php

$post_type = "developments";
$l = array(
	"Development",
	"Developments",
);
$args = array(
	"label" => "Developments",
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
        
if (function_exists('pti_set_post_type_icon')) {
	pti_set_post_type_icon( 'developments', 'wrench' );
}


add_action('add_meta_boxes', 'developments_meta_boxes', 10, 2 );

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


add_action("admin_menu", "development_fields");


function developments_meta_boxes () {
	add_meta_box('property_list','Property List','property_list_meta_box',array('developments'),'side','core');
}
function property_list_meta_box () {
	global $post;
	$properties = get_posts(array(
		'post_type'=>'properties',
		'meta_query'=>array(
			array(
				'key'=>'prop_development',
				'value'=>$post->ID
			)
		)
	));
	echo '<p><a href="/wp-admin/post-new.php?post_type=properties&development='.$post->ID.'">Add New Property</a></p>';
	echo '<table class="quicklinks">';
	foreach ($properties as $p) {
		echo '<tr>
				<td>'.$p->post_title.'</td>
				<td>
						<a target="_blank" href="'.get_permalink($p->ID).'">VIEW</a><Br>
						<a href="'.get_edit_post_link($p->ID).'">EDIT</a>
				</td>
			</tr>';
	}
	echo '</table>';
}




function development_fields() {
	
	if ( function_exists( 'add_meta_box' ) ) {
		add_meta_box(
				'development_fields',
				'Development Data',
				'development_displayfields',
				array('developments'),
				'normal', 
				'high' 
		);
	}
}
function development_displayfields () {
	propsrch_metabox("fields_developments");
}
