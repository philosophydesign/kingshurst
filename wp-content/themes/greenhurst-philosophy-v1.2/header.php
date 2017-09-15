<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php wp_head(); ?>
</head>
<?php 
global $post;
?>
<body <?php body_class($post->post_name); ?>>
	<div class="container">
		<div class="row" id="page-1">
			<?php 
			if ($post->post_name == 'home') {?>
			<div id="graphics-background" class="col-md-12 col-sm-12 col-xs-12">
				<div id="flower-left-top"></div>
				<div id="flower-left-bot"></div>
				<div id="flower-right-top"></div>
				<div id="flower-right-bot"></div>
			</div>
			<?php } ?>
			<div class="col-md-12 col-sm-12 col-xs-12">
				<div id="header" >
					<a href="/" class="site-logo">Greenhurst</a>
					<?php
		            	wp_nav_menu( array( 'menu' => 'Main Menu', 'menu_class'=>'' ) );
					?>
				</div>