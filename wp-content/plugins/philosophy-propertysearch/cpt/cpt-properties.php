<?php
$post_type = "properties";

        $l = array(
                "Property",
                "Properties",
        );
        $args = array(
            "label" => "Properties",
            "labels"=>array(
                'name'=>$l[1],
                'add_new'=>'Add '.$l[0],
                'edit_item'=>'Edit '.$l[0],
                'singular_name'=>$l[0],
                'add_new_item'=>$l[0],
            ),
            "rewrite" => array(
                "with_front" => false,
                "slug" => "properties"
            ),
            'public' => true,
            'show_in_nav_menus' => false,
            'exclude_from_search' => true,
            "supports" => array(
                "title", 
                //"editor", 
                "page-attributes", 
                //"thumbnail", 
                "revisions",
                //"comments"
            ),
            "hierarchical" => false,
            
        );

        register_post_type($post_type, $args);
        
        if (function_exists('pti_set_post_type_icon')) {
            pti_set_post_type_icon( 'properties', 'home' );
        }


// add_action( 'admin_enqueue_scripts', 'property_edit_js' );
add_action("admin_menu", "property_fields");

/*
function property_edit_js ($hook) {
    if (current_user_can( 'manage_options'))  {
        $screen = get_current_screen();
        if  
            (
                    ($hook == 'post.php') ||
                    ($hook == 'post-new.php') 
                ) {
                    
                
                if ($screen->post_type == 'properties') {
                    wp_enqueue_script('properties-edit', '/wp-content/mu-plugins/assets/js/properties-edit.js');
                }
        }
    }
}
*/
function property_fields() {
    if ( function_exists( 'add_meta_box' ) ) {
        add_meta_box(
                'properties_fields',
                'Property Data',
                'property_displayfields',
                array('properties'),
                'normal', 
                'high' 
                );
    }
}
function property_displayfields () {
    propsrch_metabox("fields_properties");
}





