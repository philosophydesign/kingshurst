<?php 
add_action('after_setup_theme', 'custom_image_setup');
add_action('wp_enqueue_scripts', 'register_theme_scripts_styles');
add_action( 'after_setup_theme', 'custom_theme_setup' );

function custom_theme_setup() {
	add_theme_support( 'post-thumbnails', array( 'post', 'page' ) );
	add_theme_support( 'menus' );
	
}

function custom_image_setup() {
    add_image_size("homeslide", 960, 690, true);
    
    add_image_size("gallery-mobile", 450, 300, true);
    add_image_size("gallery-tablet", 1200, 800, true);
    
    
    add_image_size("gallery-col-md-1", 75, 300, true);
    add_image_size("gallery-col-md-2", 170, 300, true);
    add_image_size("gallery-col-md-3", 265, 300, true);
    add_image_size("gallery-col-md-4", 360, 300, true);
    add_image_size("gallery-col-md-5", 455, 300, true);
    add_image_size("gallery-col-md-6", 550, 300, true);
    add_image_size("gallery-col-md-7", 645, 300, true);
    add_image_size("gallery-col-md-8", 740, 300, true);
    add_image_size("gallery-col-md-9", 835, 300, true);
    add_image_size("gallery-col-md-10", 930, 300, true);
    add_image_size("gallery-col-md-11", 1025, 300, true);
    add_image_size("gallery-col-md-12", 1120, 300, true);
    
    add_image_size("destination", 262, 174, true);
    
    
}

function register_theme_scripts_styles() {
	global $wp_scripts;

// 	wp_deregister_script('jquery'); //we're loading our own jquery

	if (!is_admin()) { //these are only for the front end of the site, not the admin
		// wp_enqueue_style('calibri', get_stylesheet_directory_uri() . '/assets/css/calibri.css', array(), '1.0', 'all');
		wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css', array(), '3.0.0', 'all');
// 		wp_enqueue_script('jquery', get_stylesheet_directory_uri() . '/assets/js/jquery-1.10.1.min.js');
// 		wp_enqueue_script('flexslider', get_stylesheet_directory_uri() . '/assets/js/jquery.flexslider-min.js');
		wp_enqueue_script('modernizr', get_stylesheet_directory_uri() . '/assets/js/modernizr-2.6.2-respond-1.1.0.min.js');
// 		wp_enqueue_script('modernizr-input', get_stylesheet_directory_uri() . '/assets/js/modernizr.custom.87694.js');
		wp_enqueue_style('font-berling', get_stylesheet_directory_uri() . '/assets/css/berling.css');
		wp_enqueue_style('main', get_stylesheet_directory_uri() . '/assets/css/main.css');
		wp_enqueue_script('main-js', get_stylesheet_directory_uri() . '/assets/js/main.js');
		wp_add_inline_script('main-js', 'var themeuri = "'.get_stylesheet_directory_uri().'/"; ');
		
		//wp_enqueue_script('googlemaps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyAd5jZ4juungDInWpYkBqJnvZd8g1UAC4k&callback=googlemaploaded', null, null, true );
		wp_add_inline_script('main-js', 'googlemapcallbacks.push("philostheme_InitMap");');
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

function cc_mime_types($mimes) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter('upload_mimes', 'cc_mime_types');
?>