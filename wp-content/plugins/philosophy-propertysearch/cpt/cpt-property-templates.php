<?php

 $post_type = "propertytemplates";

  	$l = array(
    			"Property Template",
    			"Property Templates",
    	);
        $args = array(
            "label" => "Property Templates",
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
            	//"editor", 
            	"page-attributes", 
            	"thumbnail", 
            	"revisions",
            	//"comments"
            ),
            "hierarchical" => false,
       		'show_in_nav_menus' => false,
       		'exclude_from_search' => true,
       		'publicly_queryable'=>false,
        );

        register_post_type($post_type, $args);
        
        if (function_exists('pti_set_post_type_icon')) {
        	pti_set_post_type_icon( 'propertytemplates', 'copy' );
        }
        add_action("admin_menu", "propertytemplates_fields");
        
        
        function propertytemplates_fields() {
        	if ( function_exists( 'add_meta_box' ) ) {
        		
        		add_meta_box(
        				'propertytemplates_fields',
        				'Property Template Data',
        				'propertytemplates_displayfields',
        				array('propertytemplates'),
        				'normal', 
						'high' 
        				);
//         		die("Testingagain");
        	}
        }
        
        function propertytemplates_displayfields () {
        	propsrch_metabox("fields_propertytemplates");
        }
        