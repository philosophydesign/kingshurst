<?php 
add_action('after_setup_theme', 'custom_image_setup');
add_action('wp_enqueue_scripts', 'register_theme_scripts_styles');
add_action( 'after_setup_theme', 'custom_theme_setup' );

function custom_theme_setup() {
	add_theme_support( 'post-thumbnails', array( 'post', 'page' ) );
}

function custom_image_setup() {
    add_image_size("homeslide", 960, 690, true);
}

function register_theme_scripts_styles() {
	global $wp_scripts;

// 	wp_deregister_script('jquery'); //we're loading our own jquery

	if (!is_admin()) { //these are only for the front end of the site, not the admin
		// wp_enqueue_style('calibri', get_stylesheet_directory_uri() . '/assets/css/calibri.css', array(), '1.0', 'all');
		wp_enqueue_style('bootstrap', get_stylesheet_directory_uri() . '/assets/css/bootstrap.min.css', array(), '3.0.0', 'all');
		wp_enqueue_script('jquery', get_stylesheet_directory_uri() . '/assets/js/jquery-1.10.1.min.js');
// 		wp_enqueue_script('flexslider', get_stylesheet_directory_uri() . '/assets/js/jquery.flexslider-min.js');
		wp_enqueue_script('modernizr', get_stylesheet_directory_uri() . '/assets/js/modernizr-2.6.2-respond-1.1.0.min.js');
// 		wp_enqueue_script('modernizr-input', get_stylesheet_directory_uri() . '/assets/js/modernizr.custom.87694.js');
		wp_enqueue_style('font-berling', get_stylesheet_directory_uri() . '/assets/css/berling.css');
		wp_enqueue_style('main', get_stylesheet_directory_uri() . '/assets/css/main.css');
	}
}

?>